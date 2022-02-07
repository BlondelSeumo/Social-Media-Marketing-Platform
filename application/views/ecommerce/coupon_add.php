<section class="section section_custom pt-1">
  <div class="section-header d-none">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce/coupon_list'); ?>"><?php echo $this->lang->line("Coupon"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("Add Coupon"); ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">

      <form class="form-horizontal" action="<?php echo site_url().'ecommerce/add_coupon_action';?>" method="POST">
        <input type="hidden" id="store_id" name="store_id" value="<?php echo $this->session->userdata("ecommerce_selected_store"); ?>">
        <div class="card no_shadow">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="name"> <?php echo $this->lang->line("Store")?> *</label>
                  <?php echo form_dropdown('', $store_list,$this->session->userdata("ecommerce_selected_store"),'disabled class="form-control select2"'); ?>
                  <span class="red"><?php echo form_error('store_id'); ?></span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="name"> <?php echo $this->lang->line("Products")?> <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Products"); ?>" data-content="<?php echo $this->lang->line("Choose products you want to apply this coupon, selecting no product means it will apply for all."); ?>"><i class='fa fa-info-circle'></i> </a></label>
                  <div id="product_con">
                    <?php echo form_dropdown('product_ids[]',array(), '','multiselect class="form-control select2" id="product_ids"'); ?>
                  </div>
                  <span class="red"><?php echo form_error('product_ids');?></span>
                </div>
              </div>
            </div>  

            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="coupon_type" > <?php echo $this->lang->line('Coupon Type');?> *</label>
                    <div class="custom-switches-stacked mt-2">
                      <div class="row">
                        <?php
                        $i=0;
                        foreach ($coupon_type_list as $key => $value) 
                        { 
                          $i++;
                          if(set_value('coupon_type')!='' && $value==set_value('coupon_type')) $checked="checked";
                          else if($i==1)  $checked="checked";
                          else $checked="";
                          ?>
                          <div class="col-4">
                            <label class="custom-switch">
                              <input type="radio" name="coupon_type" value="<?php echo $value; ?>" <?php echo $checked; ?> class="coupon_type custom-switch-input">
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description"><?php echo ucfirst($value);?></span>
                            </label>
                          </div>
                        <?php
                        }
                        ?>
                      </div>                                  
                    </div>
                    <span class="red"><?php echo form_error('coupon_type'); ?></span>
                </div> 
              </div>
            </div>

             <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="coupon_code"> <?php echo $this->lang->line("Coupon Code")?> *</label>
                  <input name="coupon_code" value="<?php echo set_value('coupon_code');?>"  class="form-control" type="text">
                  <span class="red"><?php echo form_error('coupon_code'); ?></span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="coupon_amount"><?php echo $this->lang->line("Coupon Amount")?> *</label>              
                  <input name="coupon_amount" value="<?php echo set_value('coupon_amount');?>"  class="form-control" type="text">
                  <span class="red"><?php echo form_error('coupon_amount'); ?></span>               
                </div>
              </div>
            </div>

            <div class="row" id="hidden">              
              <div class="col-12 col-md-6">
                <?php $expired_date_default = '';
                ?>
                <div class="form-group">
                  <label for="expiry_date"> <?php echo $this->lang->line("Expiry Date")?> *</label>
                  <input name="expiry_date" value="<?php echo (set_value('expiry_date')!="") ? set_value('expiry_date') : $expired_date_default;?>" class="form-control datepicker_x" type="text">
                  <span class="red"><?php echo form_error('expiry_date'); ?></span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="max_usage_limit"><?php echo $this->lang->line("Max Usage Limit")?> </label>              
                  <input name="max_usage_limit" value="<?php echo set_value('max_usage_limit');?>"  class="form-control" type="number" min='0'>
                  <span class="red"><?php echo form_error('max_usage_limit'); ?></span>               
                </div>
              </div>
            </div>


            <?php 

            $checked2="checked";
            if(validation_errors())
            {
              if(set_value('status')=='1') $checked2="checked";
              else $checked2="";
            }
            $checked3="";
            if(validation_errors())
            {
              if(set_value('free_shipping_enabled')=='1') $checked3="checked";
              else $checked3="";
            }
            ?>

            <div class="row">             
              <div class="col-6">
                <div class="form-group">
                  <label for="status" > <?php echo $this->lang->line('Status');?> *</label><br>
                  <label class="custom-switch mt-2">
                    <input type="checkbox" name="status" value="1" class="custom-switch-input" <?php echo $checked2;?>>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
                    <span class="red"><?php echo form_error('status'); ?></span>
                  </label>
                </div>
              </div>
               <div class="col-6">
                <div class="form-group">
                  <label for="free_shipping_enabled" > <?php echo $this->lang->line('Free Shipping?');?></label><br>
                  <label class="custom-switch mt-2">
                    <input type="checkbox" name="free_shipping_enabled" value="1" class="custom-switch-input" <?php echo $checked3; ?>>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Enabled');?></span>
                    <span class="red"><?php echo form_error('free_shipping_enabled'); ?></span>
                  </label>
                </div>
              </div>
            </div>           


          </div>

          <div class="card-footer p-0">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("ecommerce/coupon_list",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>  
    </div>
  </div>
</section>

          

<?php $xproduct_ids = is_array(set_value('product_ids')) ? set_value('product_ids') : array(); ?>
<script type="text/javascript">
  $(document).ready(function() {
    
    setTimeout(function(){ 
      $("#store_id").change();      
    }, 500);

    var today = new Date();
    $('.datepicker_x').datetimepicker({
      theme:'light',
      format:'Y-m-d H:i:s',
      formatDate:'Y-m-d H:i:s',
      minDate: today
    });

    $("#store_id").change(function(){
      var store_id = $("#store_id").val();
      if(store_id=='') store_id='0';
      $.ajax({
          context: this,
          type:'POST' ,
          url:"<?php echo base_url('ecommerce/get_product_list/')?>"+store_id+"/1/1",
          success:function(response)
          { 
              $("#product_con").html(response);
              var product_ids = [<?php echo '"'.implode('","',  $xproduct_ids ).'"' ?>];
              $('#product_ids').val(product_ids).trigger('change');
          }
      });
    });
  });
</script>