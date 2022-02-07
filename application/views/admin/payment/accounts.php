<section class="section section_custom">
  <div class="section-header">
    <h1><i class="far fa-credit-card"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
          <form action="<?php echo base_url("payment/accounts_action"); ?>" method="POST">
          <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
          <div class="card">
            <div class="card-body">

                <div class="row">
                  <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for=""><i class="fas fa-at"></i> <?php echo $this->lang->line("Paypal Email");?> </label>
                        <input name="paypal_email" value="<?php echo isset($xvalue['paypal_email']) ? $xvalue['paypal_email'] :""; ?>"  class="form-control" type="email">              
                        <span class="red"><?php echo form_error('paypal_email'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group">
                      <label for="paypal_payment_type" ><i class="fas fa-hand-holding-usd"></i>  <?php echo $this->lang->line('Paypal Recurring Payment');?></label>
                        
                        <div class="form-group">
                          <?php 
                          $paypal_payment_type =isset($xvalue['paypal_payment_type'])?$xvalue['paypal_payment_type']:"";
                          if($paypal_payment_type == '') $smtp_type='manual';
                          ?>
                          <label class="custom-switch mt-2">
                            <input type="checkbox" name="paypal_payment_type" value="recurring" class="custom-switch-input"  <?php if($paypal_payment_type=='recurring') echo 'checked'; ?>>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                            <span class="red"><?php echo form_error('paypal_payment_type'); ?></span>
                          </label>
                        </div>
                    </div> 
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('Paypal Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $paypal_mode =isset($xvalue['paypal_mode'])?$xvalue['paypal_mode']:"";
                      if($paypal_mode == '') $paypal_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="paypal_mode" value="sandbox" class="custom-switch-input"  <?php if($paypal_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('paypal_mode'); ?></span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Stripe Secret Key");?></label>
                      <input name="stripe_secret_key" value="<?php echo isset($xvalue['stripe_secret_key']) ? $xvalue['stripe_secret_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('stripe_secret_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fab fa-keycdn"></i> <?php echo $this->lang->line("Stripe Publishable Key");?></label>
                      <input name="stripe_publishable_key" value="<?php echo isset($xvalue['stripe_publishable_key']) ? $xvalue['stripe_publishable_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('stripe_publishable_key'); ?></span>
                    </div>
                  </div>
                </div>

                 <div class="row">
                  <div class="col-12 col-md-8">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Mollie API Key");?></label>
                      <input name="mollie_api_key" value="<?php echo isset($xvalue['mollie_api_key']) ? $xvalue['mollie_api_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('mollie_api_key'); ?></span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-6 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Razorpay Key ID");?></label>
                      <input name="razorpay_key_id" value="<?php echo isset($xvalue['razorpay_key_id']) ? $xvalue['razorpay_key_id'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('razorpay_key_id'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Razorpay Key Secret");?></label>
                      <input name="razorpay_key_secret" value="<?php echo isset($xvalue['razorpay_key_secret']) ? $xvalue['razorpay_key_secret'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('razorpay_key_secret'); ?></span>
                    </div>
                  </div>
                </div>


                <div class="row">
                  <div class="col-6 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Paystack Secret Key");?></label>
                      <input name="paystack_secret_key" value="<?php echo isset($xvalue['paystack_secret_key']) ? $xvalue['paystack_secret_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('paystack_secret_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Paystack Public Key");?></label>
                      <input name="paystack_public_key" value="<?php echo isset($xvalue['paystack_public_key']) ? $xvalue['paystack_public_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('paystack_public_key'); ?></span>
                    </div>
                  </div>
                </div>


                 <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Mercadopago Public Key");?></label>
                      <input name="mercadopago_public_key" value="<?php echo isset($xvalue['mercadopago_public_key']) ? $xvalue['mercadopago_public_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('mercadopago_public_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Mercadopago Access Token");?></label>
                      <input name="mercadopago_access_token" value="<?php echo isset($xvalue['mercadopago_access_token']) ? $xvalue['mercadopago_access_token'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('mercadopago_access_token'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-globe"></i>  <?php echo $this->lang->line("Mercadopago Country");?></label>                    
                      <select name='marcado_country' class='form-control select2' style='width:100% !important;'>
                        <?php 
                          foreach ($marcadopago_country as $key => $value) {
                            if($key == $xvalue['marcadopago_country']) $selected = 'selected';
                            else $selected = '';
                            echo '<option value="'.$key.'" '.$selected.'> '.$value.'</option>';
                          }
                         ?>
                      </select>
                      <span class="red"><?php echo form_error('country'); ?></span>
                    </div>
                  </div>

                </div>


                <!-- SSLCMMERZ -->

                 <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("SSLCommerz Store ID");?></label>
                      <input name="sslcommerz_store_id" value="<?php echo isset($xvalue['sslcommerz_store_id']) ? $xvalue['sslcommerz_store_id'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('sslcommerz_store_id'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("SSLCommerz Store Password");?></label>
                      <input name="sslcommerz_store_password" value="<?php echo isset($xvalue['sslcommerz_store_password']) ? $xvalue['sslcommerz_store_password'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('sslcommerz_store_password'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('SSLCommerz Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $sslcommers_mode =isset($xvalue['sslcommers_mode'])?$xvalue['sslcommers_mode']:"";
                      if($sslcommers_mode == '') $sslcommers_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="sslcommers_mode" value="sandbox" class="custom-switch-input"  <?php if($sslcommers_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('sslcommerz_mode'); ?></span>
                      </label>
                    </div>
                  </div>

                </div>

                <!-- Senangpay  -->

                <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="form-group mb-0">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("SenangPay Merchant Id");?></label>
                      <input name="senangpay_merchent_id" value="<?php echo isset($xvalue['senangpay_merchent_id']) ? $xvalue['senangpay_merchent_id'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('senangpay_merchent_id'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group mb-0">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("SenangPay Secret Key");?></label>
                      <input name="senangpay_secret_key" value="<?php echo isset($xvalue['senangpay_secret_key']) ? $xvalue['senangpay_secret_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('senangpay_secret_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group mb-0">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('SenangPay Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $senangpay_mode =isset($xvalue['senangpay_mode'])?$xvalue['senangpay_mode']:"";
                      if($senangpay_mode == '') $senangpay_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="senangpay_mode" value="sandbox" class="custom-switch-input"  <?php if($senangpay_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('senangpay_mode'); ?></span>
                      </label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <?php $red_url = base_url('stripe_action/senangpay_action'); ?>
                        <small class="text-dark mt-2 d-block"><b><?php echo $this->lang->line('Senangpay return url :'); ?></b> <?php echo "<span class='badge badge-danger p-1 pl-2 pr-2'>".$red_url."</span>";?> <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Senangpay return url"); ?>" data-content="<?php echo $this->lang->line("please put this redirect url in your senangpay profile`s return url field."); ?>"><i class='fa fa-info-circle'></i> <?php echo $this->lang->line("Details"); ?></a></small>
                    </div>
                 </div>

                </div>

                <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Instamojo Private Api Key");?></label>
                      <input name="instamojo_api_key" value="<?php echo isset($xvalue['instamojo_api_key']) ? $xvalue['instamojo_api_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('instamojo_api_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Instamojo Private Auth Token");?></label>
                      <input name="instamojo_auth_token" value="<?php echo isset($xvalue['instamojo_auth_token']) ? $xvalue['instamojo_auth_token'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('instamojo_auth_token'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('Instamojo Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $instamojo_mode =isset($xvalue['instamojo_mode'])?$xvalue['instamojo_mode']:"";
                      if($instamojo_mode == '') $instamojo_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="instamojo_mode" value="sandbox" class="custom-switch-input"  <?php if($instamojo_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('instamojo_mode'); ?></span>
                      </label>
                    </div>
                  </div>

                </div>

                <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Toyyibpay Secret Key");?></label>
                      <input name="toyyibpay_secret_key" value="<?php echo isset($xvalue['toyyibpay_secret_key']) ? $xvalue['toyyibpay_secret_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('toyyibpay_secret_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Toyyibpay Category code");?></label>
                      <input name="toyyibpay_category_code" value="<?php echo isset($xvalue['toyyibpay_category_code']) ? $xvalue['toyyibpay_category_code'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('toyyibpay_category_code'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('Toyyibpay Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $toyyibpay_mode =isset($xvalue['toyyibpay_mode'])?$xvalue['toyyibpay_mode']:"";
                      if($toyyibpay_mode == '') $toyyibpay_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="toyyibpay_mode" value="sandbox" class="custom-switch-input"  <?php if($toyyibpay_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('toyyibpay_mode'); ?></span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Xendit Secret Api Key");?></label>
                      <input name="xendit_secret_api_key" value="<?php echo isset($xvalue['xendit_secret_api_key']) ? $xvalue['xendit_secret_api_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('xendit_secret_api_key'); ?></span>
                    </div>
                  </div>

                </div>


              
                <div class="row">
                  <div class="col-6 col-md-8">
                    <div class="form-group">
                       <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Myfatoorah Api Key");?>
                      <a href="#" data-placement="top" data-toggle="popover" title="" data-content="Myfatoorah only Supports KWD, SAR, BHD, AED, QAR, OMR, JOD, EGP  Currency" data-original-title="<?php echo $this->lang->line('Myfatoorah Supported Currency');  ?>" ><i class="fa fa-info-circle"></i> </a>
                    </label>
                      <input name="myfatoorah_api_key" value="<?php echo isset($xvalue['myfatoorah_api_key']) ? $xvalue['myfatoorah_api_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('myfatoorah_api_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('Myfatoorah Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $myfatoorah_mode =isset($xvalue['myfatoorah_mode'])?$xvalue['myfatoorah_mode']:"";
                      if($myfatoorah_mode == '') $myfatoorah_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="myfatoorah_mode" value="sandbox" class="custom-switch-input"  <?php if($myfatoorah_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('myfatoorah_mode'); ?></span>
                      </label>
                    </div>
                  </div>
                </div>

                 <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Paymaya Public Key");?></label>
                      <input name="paymaya_public_key" value="<?php echo isset($xvalue['paymaya_public_key']) ? $xvalue['paymaya_public_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('paymaya_public_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-6 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Paymaya Secret Key");?></label>
                      <input name="paymaya_secret_key" value="<?php echo isset($xvalue['paymaya_secret_key']) ? $xvalue['paymaya_secret_key'] :""; ?>" class="form-control" type="text">  
                      <span class="red"><?php echo form_error('paymaya_secret_key'); ?></span>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-group">
                      <label for=""><i class="fas fa-vial"></i> <?php echo $this->lang->line('Paymaya Sandbox Mode');?></label>
                      <br>
                      <?php 
                      $paymaya_mode =isset($xvalue['paymaya_mode'])?$xvalue['paymaya_mode']:"";
                      if($paymaya_mode == '') $paymaya_mode='live';
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="paymaya_mode" value="sandbox" class="custom-switch-input"  <?php if($paymaya_mode=='sandbox') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('paymaya_mode'); ?></span>
                      </label>
                    </div>
                  </div>
                </div>




                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-coins"></i>  <?php echo $this->lang->line("Currency");?></label>
                      <?php $default_currency = isset($xvalue['currency']) ? $xvalue['currency'] : "USD"; ?>               
                      <select name='currency' class='form-control select2' style='width:100% !important;'>
                      <?php
                      foreach ($currecny_list_all as $key => $value)
                      {
                        $paypal_supported = in_array($key, $currency_list) ? " - PayPal & Stripe" : "";
                        if($default_currency==$key) $selected_curr = "selected='selected'";
                        else $selected_curr = '';
                        echo '<option value="'.$key.'" '.$selected_curr.' >'.str_replace('And', '&', ucwords($value)).$paypal_supported.'</option>';
                      }
                      ?>
                      </select>
                      <span class="red"><?php echo form_error('currency'); ?></span>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for=""><i class="fas fa-file-invoice-dollar"></i> <?php echo $this->lang->line('Enable manual payment');?></label>
                      <br>
                      <?php 
                        $manual_payment =isset($xvalue['manual_payment'])?$xvalue['manual_payment']:"";
                        if($manual_payment == "") $manual_payment = "no";
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="manual_payment" class="custom-switch-input" value="yes" <?php if ($manual_payment == "yes") echo "checked"; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                        <span class="red"><?php echo form_error('manual_payment'); ?></span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div id="manual-payins" class="col-12">
                    <div class="form-group">
                      <label><i class="fa fa-info"></i> <?php echo $this->lang->line('Manual payment instructions'); ?></label>
                      <textarea name="manual_payment_instruction" class="summernote form-control" style="height: 200px !important"><?php echo set_value('manual_payment_instruction', isset($xvalue['manual_payment_instruction']) ? $xvalue['manual_payment_instruction'] : ""); ?></textarea>
                      <span class="red"><?php echo form_error('manual_payment_instruction'); ?></span>
                    </div>
                  </div>                    
                </div>
            </div>

            <div class="card-footer bg-whitesmoke">
              <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
