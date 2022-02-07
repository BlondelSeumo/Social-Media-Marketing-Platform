<section class="section section_custom">
  <div class="section-header d-none">
    <h1><i class="far fa-credit-card"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("Payment Accounts"); ?></div>
    </div>
  </div>

  <?php 
    if($this->session->flashdata('success_message')==1)
    echo "<div class='alert alert-success text-center'><i class='fas fa-check-circle'></i> ".$this->lang->line("Your data has been successfully stored into the database.")."</div><br>";
  ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
          <form action="<?php echo base_url("ecommerce/payment_accounts_action"); ?>" method="POST">
          <div class="card no_shadow">
            <div class="card-body p-0">

                <div class="card">
                  <div class="card-header p-3" style="border:1px solid #e4e6fc;border-bottom: none;">
                    <h4><?php echo $this->lang->line("Payment Integration"); ?></h4><hr>
                  </div>
                  <div class="card-footer p-3" style="border:1px solid #e4e6fc">

                    <div class="row">
                      <div class="col-12 col-md-8">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line("Paypal Email");?> </label>
                            <input name="paypal_email" value="<?php echo isset($xvalue['paypal_email']) ? $xvalue['paypal_email'] :""; ?>"  class="form-control" type="email">              
                            <span class="red"><?php echo form_error('paypal_email'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Paypal Sandbox Mode');?></label>
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
                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""> <?php echo $this->lang->line("Stripe Secret Key");?></label>
                          <input name="stripe_secret_key" value="<?php echo isset($xvalue['stripe_secret_key']) ? $xvalue['stripe_secret_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('stripe_secret_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Stripe Publishable Key");?></label>
                          <input name="stripe_publishable_key" value="<?php echo isset($xvalue['stripe_publishable_key']) ? $xvalue['stripe_publishable_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('stripe_publishable_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Stripe billing address');?></label>
                          <br>
                          <?php 
                          $stripe_billing_address =isset($xvalue['stripe_billing_address'])?$xvalue['stripe_billing_address']:"";
                          if($stripe_billing_address == '') $stripe_billing_address='0';
                          ?>
                          <label class="custom-switch mt-2">
                            <input type="checkbox" name="stripe_billing_address" value="1" class="custom-switch-input"  <?php if($stripe_billing_address=='1') echo 'checked'; ?>>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                            <span class="red"><?php echo form_error('stripe_billing_address'); ?></span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-6 col-md-4">
                        <div class="form-group mb-0">
                          <label for=""><?php echo $this->lang->line("SenangPay Merchant Id");?></label>
                          <input name="senangpay_merchent_id" value="<?php echo isset($xvalue['senangpay_merchent_id']) ? $xvalue['senangpay_merchent_id'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('senangpay_merchent_id'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group mb-0">
                          <label for=""><?php echo $this->lang->line("SenangPay Secret Key");?></label>
                          <input name="senangpay_secret_key" value="<?php echo isset($xvalue['senangpay_secret_key']) ? $xvalue['senangpay_secret_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('senangpay_secret_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group mb-0">
                          <label for=""><?php echo $this->lang->line('SenangPay Sandbox Mode');?></label>
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
                            <?php $red_url = base_url('ecommerce/senangpay_action'); ?>
                              <small class="text-dark mt-2 d-block"><b><?php echo $this->lang->line('Senangpay return url :'); ?></b> <?php echo "<span class='badge badge-danger p-1 pl-2 pr-2'>".$red_url."</span>";?> <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Senangpay return url"); ?>" data-content="<?php echo $this->lang->line("please put this redirect url in your senangpay profile`s return url field."); ?>"><i class='fa fa-info-circle'></i> <?php echo $this->lang->line("Details"); ?></a></small>
                          </div>
                       </div>

                    </div>

                    <div class="row">
                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Instamojo Private Api Key");?></label>
                          <input name="instamojo_api_key" value="<?php echo isset($xvalue['instamojo_api_key']) ? $xvalue['instamojo_api_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('instamojo_api_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Instamojo Private Auth Token");?></label>
                          <input name="instamojo_auth_token" value="<?php echo isset($xvalue['instamojo_auth_token']) ? $xvalue['instamojo_auth_token'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('instamojo_auth_token'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Instamojo Sandbox Mode');?></label>
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
                          <label for=""> <?php echo $this->lang->line("Sslcommerz Store ID");?></label>
                          <input name="sslcommerz_store_id" value="<?php echo isset($xvalue['sslcommerz_store_id']) ? $xvalue['sslcommerz_store_id'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('sslcommerz_store_id'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Sslcommerz Store Password");?></label>
                          <input name="sslcommerz_store_password" value="<?php echo isset($xvalue['sslcommerz_store_password']) ? $xvalue['sslcommerz_store_password'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('sslcommerz_store_password'); ?></span>
                    
                        </div>
                      </div>
                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Sslcommerz Sandbox Mode');?></label>
                          <br>
                          <?php 
                          $sslcommerz_mode =isset($xvalue['sslcommerz_mode'])?$xvalue['sslcommerz_mode']:"";
                          if($sslcommerz_mode == '') $sslcommerz_mode='live';
                          ?>
                          <label class="custom-switch mt-2">
                            <input type="checkbox" name="sslcommerz_mode" value="sandbox" class="custom-switch-input"  <?php if($sslcommerz_mode=='sandbox') echo 'checked'; ?>>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                            <span class="red"><?php echo form_error('sslcommerz_mode'); ?></span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""> <?php echo $this->lang->line("Mercado Pago Public Key");?></label>
                          <input name="mercadopago_public_key" value="<?php echo isset($xvalue['mercadopago_public_key']) ? $xvalue['mercadopago_public_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('mercadopago_public_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Mercado Pago Acceess Token");?></label>
                          <input name="mercadopago_access_token" value="<?php echo isset($xvalue['mercadopago_access_token']) ? $xvalue['mercadopago_access_token'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('mercadopago_access_token'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Mercadopago Country");?></label>                    
                          <select name='marcadopago_country' class='form-control select2' style='width:100% !important;'>
                            <?php 
                              foreach ($marcadopago_country as $key => $value) {
                                if(isset($xvalue['marcadopago_country']) && $key == $xvalue['marcadopago_country']) $selected = 'selected';
                                else $selected = '';
                                echo '<option value="'.$key.'" '.$selected.'> '.$value.'</option>';
                              }
                             ?>
                          </select>
                          <span class="red"><?php echo form_error('country'); ?></span>
                        </div>
                      </div>
                    </div>   

                    <div class="row">
                      <div class="col-12 col-md-6">
                        <div class="form-group">
                          <label for=""> <?php echo $this->lang->line("Mollie API Key");?></label>
                          <input name="mollie_api_key" value="<?php echo isset($xvalue['mollie_api_key']) ? $xvalue['mollie_api_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('mollie_api_key'); ?></span>
                        </div>
                      </div>
                      <div class="col-12 col-md-6">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Xendit Secret Api Key");?></label>
                          <input name="xendit_secret_api_key" value="<?php echo isset($xvalue['xendit_secret_api_key']) ? $xvalue['xendit_secret_api_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('xendit_secret_api_key'); ?></span>
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Toyyibpay Secret Key");?></label>
                          <input name="toyyibpay_secret_key" value="<?php echo isset($xvalue['toyyibpay_secret_key']) ? $xvalue['toyyibpay_secret_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('toyyibpay_secret_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Toyyibpay Category code");?></label>
                          <input name="toyyibpay_category_code" value="<?php echo isset($xvalue['toyyibpay_category_code']) ? $xvalue['toyyibpay_category_code'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('toyyibpay_category_code'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Toyyibpay Sandbox Mode');?></label>
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
                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Paymaya Public Key");?></label>
                          <input name="paymaya_public_key" value="<?php echo isset($xvalue['paymaya_public_key']) ? $xvalue['paymaya_public_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('paymaya_public_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Paymaya Secret Key");?></label>
                          <input name="paymaya_secret_key" value="<?php echo isset($xvalue['paymaya_secret_key']) ? $xvalue['paymaya_secret_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('paymaya_secret_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Paymaya Sandbox Mode');?></label>
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
                      <div class="col-12 col-md-8">
                        <div class="form-group">
                          <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Myfatoorah Api Key");?>
                          <a href="#" data-placement="top" data-toggle="popover" title="" data-content="Myfatoorah only Supports KWD, SAR, BHD, AED, QAR, OMR, JOD, EGP  Currency" data-original-title="<?php echo $this->lang->line('Myfatoorah Supported Currency');  ?>" ><i class="fa fa-info-circle"></i> </a>
                        </label>
                          <input name="myfatoorah_api_key" value="<?php echo isset($xvalue['myfatoorah_api_key']) ? $xvalue['myfatoorah_api_key'] :""; ?>"  class="form-control" type="text">              
                          <span class="red"><?php echo form_error('myfatoorah_api_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-12 col-md-4">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line('Myfatoorah Sandbox Mode');?></label>
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
                      <div class="col-6 col-md-6">
                        <div class="form-group">
                          <label for=""> <?php echo $this->lang->line("Razorpay Key ID");?></label>
                          <input name="razorpay_key_id" value="<?php echo isset($xvalue['razorpay_key_id']) ? $xvalue['razorpay_key_id'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('razorpay_key_id'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-6">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Razorpay Key Secret");?></label>
                          <input name="razorpay_key_secret" value="<?php echo isset($xvalue['razorpay_key_secret']) ? $xvalue['razorpay_key_secret'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('razorpay_key_secret'); ?></span>
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-6 col-md-6">
                        <div class="form-group">
                          <label for=""> <?php echo $this->lang->line("Paystack Secret Key");?></label>
                          <input name="paystack_secret_key" value="<?php echo isset($xvalue['paystack_secret_key']) ? $xvalue['paystack_secret_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('paystack_secret_key'); ?></span>
                        </div>
                      </div>

                      <div class="col-6 col-md-6">
                        <div class="form-group">
                          <label for=""><?php echo $this->lang->line("Paystack Public Key");?></label>
                          <input name="paystack_public_key" value="<?php echo isset($xvalue['paystack_public_key']) ? $xvalue['paystack_public_key'] :""; ?>" class="form-control" type="text">  
                          <span class="red"><?php echo form_error('paystack_public_key'); ?></span>
                        </div>
                      </div>
                    </div>                       

                    <?php if($xdata2['store_type'] == 'physical') : ?>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group mb-0">
                          <label type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" class="pointer text-primary" style="font-size: 14px"><b><i class="fa fa-newspaper"></i> <?php echo $this->lang->line('Write Manual payment instructions'); ?></b></label>
                          <div class="collapse" id="collapseExample">
                            <textarea name="manual_payment_instruction" id="collapseExample" class="form-control visual_editor collapse"><?php echo set_value('manual_payment_instruction', isset($xvalue['manual_payment_instruction']) ? $xvalue['manual_payment_instruction'] : ""); ?></textarea>
                            <span class="red"><?php echo form_error('manual_payment_instruction'); ?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="card" id="payment_options">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Payment Options"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                      <div class="row">
                        <div class="col-12 col-md-3">
                          <div class="form-group">
                            <label>
                              <?php echo $this->lang->line("PayPal checkout"); ?> *
                            </label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="paypal_enabled" value="1" class="selectgroup-input" <?php if($xdata2["paypal_enabled"]=='1') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="paypal_enabled" value="0" class="selectgroup-input" <?php if($xdata2["paypal_enabled"]=='0') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                              </label>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-md-3">
                          <div class="form-group">
                            <label>
                              <?php echo $this->lang->line("Stripe checkout"); ?> *
                            </label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="stripe_enabled" value="1" class="selectgroup-input" <?php if($xdata2["stripe_enabled"]=='1') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="stripe_enabled" value="0" class="selectgroup-input" <?php if($xdata2["stripe_enabled"]=='0') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                              </label>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-md-3">
                          <div class="form-group">
                            <label>
                              <?php echo $this->lang->line("Razorpay checkout"); ?> *
                            </label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="razorpay_enabled" value="1" class="selectgroup-input" <?php if($xdata2["razorpay_enabled"]=='1') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="razorpay_enabled" value="0" class="selectgroup-input" <?php if($xdata2["razorpay_enabled"]=='0') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                              </label>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Paystack checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="paystack_enabled" value="1" class="selectgroup-input" <?php if($xdata2["paystack_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="paystack_enabled" value="0" class="selectgroup-input" <?php if($xdata2["paystack_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Mollie checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="mollie_enabled" value="1" class="selectgroup-input" <?php if($xdata2["mollie_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="mollie_enabled" value="0" class="selectgroup-input" <?php if($xdata2["mollie_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Mercado Pago checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="mercadopago_enabled" value="1" class="selectgroup-input" <?php if($xdata2["mercadopago_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="mercadopago_enabled" value="0" class="selectgroup-input" <?php if($xdata2["mercadopago_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Sslcommerz checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="sslcommerz_enabled" value="1" class="selectgroup-input" <?php if($xdata2["sslcommerz_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="sslcommerz_enabled" value="0" class="selectgroup-input" <?php if($xdata2["sslcommerz_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Senangpay checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="senangpay_enabled" value="1" class="selectgroup-input" <?php if($xdata2["senangpay_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="senangpay_enabled" value="0" class="selectgroup-input" <?php if($xdata2["senangpay_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Instamojo checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="instamojo_enabled" value="1" class="selectgroup-input" <?php if($xdata2["instamojo_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="instamojo_enabled" value="0" class="selectgroup-input" <?php if($xdata2["instamojo_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                          <div class="form-group">
                            <label>
                              <?php echo $this->lang->line("Xendit checkout"); ?> *
                            </label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="xendit_enabled" value="1" class="selectgroup-input" <?php if($xdata2["xendit_enabled"]=='1') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="xendit_enabled" value="0" class="selectgroup-input" <?php if($xdata2["xendit_enabled"]=='0') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                              </label>
                            </div>
                          </div>
                       </div> 

                       <div class="col-12 col-md-3">
                        <div class="form-group">
                          <label>
                            <?php echo $this->lang->line("Myfatoorah checkout"); ?> *
                          </label>
                          <div class="selectgroup w-100">
                            <label class="selectgroup-item">
                              <input type="radio" name="myfatoorah_enabled" value="1" class="selectgroup-input" <?php if($xdata2["myfatoorah_enabled"]=='1') echo 'checked'; ?>>
                              <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                            </label>
                            <label class="selectgroup-item">
                              <input type="radio" name="myfatoorah_enabled" value="0" class="selectgroup-input" <?php if($xdata2["myfatoorah_enabled"]=='0') echo 'checked'; ?>>
                              <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                            </label>
                          </div>
                        </div>
                      </div>


                       <div class="col-12 col-md-3">
                          <div class="form-group">
                            <label>
                              <?php echo $this->lang->line("Toyyibpay checkout"); ?> *
                            </label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="toyyibpay_enabled" value="1" class="selectgroup-input" <?php if($xdata2["toyyibpay_enabled"]=='1') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="toyyibpay_enabled" value="0" class="selectgroup-input" <?php if($xdata2["toyyibpay_enabled"]=='0') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                              </label>
                            </div>
                          </div>
                       </div> 

                        <div class="col-12 col-md-3">
                          <div class="form-group">
                            <label>
                              <?php echo $this->lang->line("Paymaya checkout"); ?> *
                            </label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="paymaya_enabled" value="1" class="selectgroup-input" <?php if($xdata2["paymaya_enabled"]=='1') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="paymaya_enabled" value="0" class="selectgroup-input" <?php if($xdata2["paymaya_enabled"]=='0') echo 'checked'; ?>>
                                <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                              </label>
                            </div>
                          </div>
                       </div> 

                       <input type="hidden" name="store_type" id="store_type" value="<?php echo $xdata2['store_type']; ?>">
                       <?php if($xdata2['store_type'] == 'physical') : ?>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Manual checkout"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="manual_enabled" value="1" class="selectgroup-input" <?php if($xdata2["manual_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="manual_enabled" value="0" class="selectgroup-input" <?php if($xdata2["manual_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                              <label>
                                <?php echo $this->lang->line("Cash on delivery"); ?> *
                              </label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="cod_enabled" value="1" class="selectgroup-input"  <?php if($xdata2["cod_enabled"]=='1') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("Yes") ?></span>
                                </label>
                                <label class="selectgroup-item">
                                  <input type="radio" name="cod_enabled" value="0" class="selectgroup-input"  <?php if($xdata2["cod_enabled"]=='0') echo 'checked'; ?>>
                                  <span class="selectgroup-button"> <?php echo $this->lang->line("No") ?></span>
                                </label>
                              </div>
                            </div>
                        </div>
                      <?php endif; ?>
                     </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Currency & Formatting"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                      <div class="row">
                        <div class="col-12">
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

                        <div class="col-12 col-md-4">   
                          <div class="form-group">                       
                            <label for="">
                              <?php echo $this->lang->line('Right Alignment');?>
                              <a href="#" data-placement="top" data-toggle="tooltip" title="<?php echo $this->lang->line("Right alignment could be helpful for several currencies. Example display : 25৳");?>"><i class="fa fa-info-circle"></i> </a>
                          </label>
                            <br>
                            <?php 
                            $currency_position =isset($xvalue['currency_position'])?$xvalue['currency_position']:"";
                            if($currency_position == '') $currency_position='left';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="currency_position" value="right" class="custom-switch-input"  <?php if($currency_position=='right') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                              <span class="red"><?php echo form_error('currency_position'); ?></span>
                            </label>
                          </div>
                        </div>

                        <div class="col-12 col-md-4">
                          <div class="form-group">
                           <label for="">
                            <?php echo $this->lang->line("Two Decimal");?>
                            <a href="#" data-placement="top" data-toggle="tooltip" title="<?php echo $this->lang->line("If enabled prices will be displayed with two decimal points. Example display : $25.99");?>"><i class="fa fa-info-circle"></i> </a>
                           </label>
                           <?php 
                           $decimal_point =isset($xvalue['decimal_point'])?$xvalue['decimal_point']:"";
                           if($decimal_point == '') $decimal_point='2';
                           ?>
                           <br>
                           <label class="custom-switch mt-2">
                             <input type="checkbox" name="decimal_point" value="2" class="custom-switch-input"  <?php if($decimal_point=='2') echo 'checked'; ?>>
                             <span class="custom-switch-indicator"></span>
                             <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                             <span class="red"><?php echo form_error('decimal_point'); ?></span>
                           </label>            
                           <span class="red"><?php echo form_error('decimal_point'); ?></span>
                          </div>
                        </div>

                        <div class="col-12 col-md-4">
                          <div class="form-group">
                            <label for="">
                              <?php echo $this->lang->line('Display Comma');?>
                              <a href="#" data-placement="top" data-toggle="tooltip" title="<?php echo $this->lang->line("If enabled prices will be displayed comma separated. Example display : $25,000");?>"><i class="fa fa-info-circle"></i> </a>

                           </label>
                            <br>
                            <?php 
                            $thousand_comma =isset($xvalue['thousand_comma'])?$xvalue['thousand_comma']:"";
                            if($thousand_comma == '') $thousand_comma='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="thousand_comma" value="1" class="custom-switch-input"  <?php if($thousand_comma=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                              <span class="red"><?php echo form_error('thousand_comma'); ?></span>
                            </label>
                          </div>
                        </div>                        

                      </div>


                    </div>
                </div>

                <div class="card">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Tax & Delivery Charge"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                        <div class="row">
                          <div class="form-group <?php if($xdata2['store_type'] == 'physical') echo 'col-6 col-md-6'; else echo 'col-12';?> ">
                            <label>
                              <?php echo $this->lang->line("Tax"); ?> % 
                            </label>
                            <div class="input-group mb-2">
                                <input type="number" name="tax_percentage" id="tax_percentage" class="form-control" value="<?php echo $xdata2['tax_percentage']; ?>" min="0" step="0.01">  
                                <div class="input-group-append">
                                  <div class="input-group-text">%</div>
                                </div>
                            </div>                    
                          </div>

                          <?php if($xdata2['store_type'] == 'physical') : ?>
                            <div class="form-group col-6 col-md-6">
                              <label>
                                <?php echo $this->lang->line("Delivery charge"); ?>
                              </label>
                              <div class="input-group mb-2">
                                  <input type="number" name="shipping_charge" id="shipping_charge" class="form-control" value="<?php echo $xdata2['shipping_charge']; ?>" min="0" step="0.01">  
                                  <div class="input-group-append">
                                    <div class="input-group-text">
                                      <?php 
                                        $currency = isset($xvalue['currency']) ? $xvalue['currency'] : "USD";
                                        $currency_icon = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
                                        echo $currency_icon; 
                                      ?>                                      
                                      </div>
                                  </div>
                              </div>                                      
                            </div>
                          <?php endif; ?>

                        </div>                
                    </div>
                </div>

                <?php if($xdata2['store_type'] == 'physical') : ?>
                <div class="card">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Delivery Preference"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                      <div class="row">
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Store Pickup');?></label>
                            <br>
                            <?php 
                            $is_store_pickup =isset($xvalue['is_store_pickup'])?$xvalue['is_store_pickup']:"";
                            if($is_store_pickup == '') $is_store_pickup='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_store_pickup" value="1" class="custom-switch-input"  <?php if($is_store_pickup=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_store_pickup'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Home Delivery');?></label>
                            <br>
                            <?php 
                            $is_home_delivery =isset($xvalue['is_home_delivery'])?$xvalue['is_home_delivery']:"";
                            if($is_home_delivery == '') $is_home_delivery='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_home_delivery" value="1" class="custom-switch-input"  <?php if($is_home_delivery=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_home_delivery'); ?></span>
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="row">                        
                        <div class="col-12 col-md-4">
                          <div class="form-group mb-2">
                            <label for=""><?php echo $this->lang->line('Preparation time');?></label>
                            <br>
                            <?php 
                            $is_preparation_time =isset($xvalue['is_preparation_time'])?$xvalue['is_preparation_time']:"";
                            if($is_preparation_time == '') $is_preparation_time='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_preparation_time" value="1" class="custom-switch-input"  <?php if($is_preparation_time=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_preparation_time'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-12 col-md-8">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                              <span class="input-group-text"><?php echo $this->lang->line("Time"); ?></span>
                              </div>
                              <?php
                              $preparation_time_unit = isset($xvalue['preparation_time_unit'])?$xvalue['preparation_time_unit']:"minutes";
                              $disabled = ($is_preparation_time=='1')?"":"disabled";
                              $arr = array
                              (
                                ''=>$this->lang->line('Time unit'),
                                'minutes'=>$this->lang->line('Minutes'),
                                'hours'=>$this->lang->line('Hours'),
                                'days'=>$this->lang->line('Days')
                              );
                              ?>
                              <input type="text" placeholder="<?php echo $this->lang->line('example : 10 or 10-15');?>" <?php echo $disabled; ?> name="preparation_time" class="form-control" value="<?php echo isset($xvalue['preparation_time'])?$xvalue['preparation_time']:"30" ?>">
                              <?php echo form_dropdown('preparation_time_unit', $arr,$preparation_time_unit,"class='form-control' {$disabled}"); ?>
                            </div>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group mb-2">
                            <label for=""><?php echo $this->lang->line('Scheduled order');?></label>
                            <br>
                            <?php 
                            $is_order_schedule =isset($xvalue['is_order_schedule'])?$xvalue['is_order_schedule']:"";
                            if($is_order_schedule == '') $is_order_schedule='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_order_schedule" value="1" class="custom-switch-input"  <?php if($is_order_schedule=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_order_schedule'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-8">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <?php
                              $order_schedule = isset($xvalue['order_schedule'])?$xvalue['order_schedule']:"any";
                              $disabled = ($is_order_schedule=='1')?"":"disabled";
                              $arr = array
                              (
                                'today'=>$this->lang->line('Only Today'),
                                'tomorrow'=>$this->lang->line('Today & Tomorrow'),
                                'week'=>$this->lang->line('One Week'),
                                'any'=>$this->lang->line('Any')
                              );
                              ?>
                            <div class="input-group mb-2">
                               <?php echo form_dropdown('order_schedule',$arr,$order_schedule,"class='form-control' {$disabled}"); ?>
                            </div>                                      
                          </div>                          
                        </div>
                        

                      </div>
                    </div>
                </div>
                <?php endif; ?>


                <div class="card">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Login Preference"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                      <div class="row">
                        <div class="col-12 col-md-4">
                          <div class="form-group mb-2">
                            <label for=""><?php echo $this->lang->line('Guest Purchase');?></label>
                            <br>
                            <?php 
                            $is_guest_login =isset($xvalue['is_guest_login'])?$xvalue['is_guest_login']:"0";
                            if($is_guest_login == '') $is_guest_login='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_guest_login" value="1" class="custom-switch-input"  <?php if($is_guest_login=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_guest_login'); ?></span>
                            </label>
                          </div>
                        </div>                       

                      </div>
                    </div>
                </div>


                <div class="card <?php if($xdata2['page_id'] == 0) echo 'd-none'; ?>">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Assign Labels"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-group mb-2">
                            <label for=""><?php echo $this->lang->line('Choose Labels (Labels will be assigned to the subscribers after successfull checkout.)');?></label>
                            <select name="assign_labels[]" id="assign_labels" multiple class="form-control w-100 select2">
                              <?php foreach($page_labels as $label) { 
                                $selected_label = '';
                                $xlabel_ids = explode(",",$xdata2['label_ids']);
                                if(in_array($label['id'], $xlabel_ids)) $selected_label = 'selected';
                              ?>

                                <option value="<?php echo $label['id'] ?>" <?php echo $selected_label; ?>><?php echo $label['group_name'] ?></option>
                              <?php } ?>
                            </select>

                          </div>
                        </div>                       

                      </div>
                    </div>
                </div>

                <?php if($xdata2['store_type'] == 'physical') : ?>
                <div class="card">
                    <div class="card-header p-3"  style="border:1px solid #e4e6fc;border-bottom: none;">
                      <h4><?php echo $this->lang->line("Address Preference"); ?></h4><hr>
                    </div>
                    <div class="card-footer p-3" style="border:1px solid #e4e6fc;">
                      <div class="row">                        
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Checkout Country');?></label>
                            <br>
                            <?php 
                            $is_checkout_country =isset($xvalue['is_checkout_country'])?$xvalue['is_checkout_country']:"";
                            if($is_checkout_country == '') $is_checkout_country='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_checkout_country" value="1" class="custom-switch-input"  <?php if($is_checkout_country=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_checkout_country'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Checkout State');?></label>
                            <br>
                            <?php 
                            $is_checkout_state =isset($xvalue['is_checkout_state'])?$xvalue['is_checkout_state']:"";
                            if($is_checkout_state == '') $is_checkout_state='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_checkout_state" value="1" class="custom-switch-input"  <?php if($is_checkout_state=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_checkout_state'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Checkout City');?></label>
                            <br>
                            <?php 
                            $is_checkout_city =isset($xvalue['is_checkout_city'])?$xvalue['is_checkout_city']:"";
                            if($is_checkout_city == '') $is_checkout_city='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_checkout_city" value="1" class="custom-switch-input"  <?php if($is_checkout_city=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_checkout_city'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Checkout Zip');?></label>
                            <br>
                            <?php 
                            $is_checkout_zip =isset($xvalue['is_checkout_zip'])?$xvalue['is_checkout_zip']:"";
                            if($is_checkout_zip == '') $is_checkout_zip='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_checkout_zip" value="1" class="custom-switch-input"  <?php if($is_checkout_zip=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_checkout_zip'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Checkout Email');?></label>
                            <br>
                            <?php 
                            $is_checkout_email =isset($xvalue['is_checkout_email'])?$xvalue['is_checkout_email']:"";
                            if($is_checkout_email == '') $is_checkout_email='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_checkout_email" value="1" class="custom-switch-input"  <?php if($is_checkout_email=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_checkout_email'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Checkout Phone');?></label>
                            <br>
                            <?php 
                            $is_checkout_phone =isset($xvalue['is_checkout_phone'])?$xvalue['is_checkout_phone']:"";
                            if($is_checkout_phone == '') $is_checkout_phone='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_checkout_phone" value="1" class="custom-switch-input"  <?php if($is_checkout_phone=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_checkout_phone'); ?></span>
                            </label>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="form-group">
                            <label for=""><?php echo $this->lang->line('Delivery Note');?></label>
                            <br>
                            <?php 
                            $is_delivery_note =isset($xvalue['is_delivery_note'])?$xvalue['is_delivery_note']:"";
                            if($is_delivery_note == '') $is_delivery_note='0';
                            ?>
                            <label class="custom-switch mt-2">
                              <input type="checkbox" name="is_delivery_note" value="1" class="custom-switch-input"  <?php if($is_delivery_note=='1') echo 'checked'; ?>>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo $this->lang->line('Enable');?></span>
                              <span class="red"><?php echo form_error('is_delivery_note'); ?></span>
                            </label>
                          </div>
                        </div>
                        
                        

                      </div>
                    </div>
                </div>
                <?php endif; ?>

                
            </div>

            <div class="card-footer p-0">

              <?php 
              if($this->session->flashdata('error_message')==1)
              echo "<div class='alert alert-danger text-center'><i class='fas fa-times-circle'></i> ".$this->lang->line("You must select at least one checkout payment option.")."</div><br>";
              ?>
              <?php 
              if($this->session->flashdata('error_message2')==1)
              echo "<div class='alert alert-danger text-center'><i class='fas fa-times-circle'></i> ".$this->lang->line("You cannot disable both store pickup and home delivery.")."</div><br>";
              ?>
              <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            </div>

          </div>

        </form>
      </div>
    </div>
  </div>
</section>


<script type="text/javascript">
  $(document).ready(function($) {

    $('.visual_editor').summernote({
        height: 180,
        minHeight: 180,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline','italic','clear']],
            // ['fontname', ['fontname']],
            // ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ]
      });

    $(document).on('click','[name="is_preparation_time"]',function(e){
     if ($(this).is(':checked'))
     {
        $('[name="preparation_time"]').removeAttr('disabled');
        $('[name="preparation_time_unit"]').removeAttr('disabled');
     }
     else
     {
        $('[name="preparation_time"]').attr('disabled','');
        $('[name="preparation_time_unit"]').attr('disabled','');
     }
    });

    $(document).on('click','[name="is_order_schedule"]',function(e){
     if ($(this).is(':checked'))
     {
        $('[name="order_schedule"]').removeAttr('disabled');
     }
     else
     {
        $('[name="order_schedule"]').attr('disabled','');
     }
    });

  });
</script>