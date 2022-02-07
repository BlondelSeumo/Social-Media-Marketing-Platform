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

    if(!isset($xvalue["is_category_wise_product_view"])) $xvalue["is_category_wise_product_view"] = "0";
    if(!isset($xvalue["product_listing"])) $xvalue["product_listing"] = "list";
    if(!isset($xvalue["theme_color"])) $xvalue["theme_color"] = "var(--blue)";
    if($xvalue["theme_color"]=="") $xvalue["theme_color"] = "var(--blue)";

    $colors = array('var(--blue)','#2d88ff','#1261a0','#545096','#e55053','#fc4444','#ff8342','#ffc156','#00c9b8','#00a65a','#164a41','#293745');
  ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
          <form action="<?php echo base_url("ecommerce/appearance_settings_action"); ?>" method="POST">
          <div class="card no_shadow">
            <div class="card-body p-0">


              <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group mb-0">                      
                      <label><?php echo $this->lang->line("Choose Theme color"); ?></label>
                    </div>
                    <?php $select_front_theme = $xvalue["theme_color"]; ?>
                    
                    <div class="row gutters-xs mb-3">
                      <?php foreach ($colors as $key => $value) : ?>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input name="theme_front" type="radio" value="<?php echo $value; ?>" class="colorinput-input" <?php if ($select_front_theme == $value) echo "checked"; ?>/>
                            <span class="colorinput-color" style="background: <?php echo $value; ?>"></span>
                          </label>
                        </div>
                      <?php endforeach; ?>   
                  </div>
                </div>

                <div class="col-12 col-md-6">                  
                  <div class="form-group mb-0 ">                      
                    <label><?php echo $this->lang->line(""); ?></label>
                  </div>
                  <?php echo "<input type='color' name='theme_color' id='theme_color' class='form-control border-right' value='".$select_front_theme."'>"; ?>    
                </div>

                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label>
                      <?php echo $this->lang->line("Product grouping"); ?>
                    </label>
                    <div class="selectgroup d-block">
                      <label class="selectgroup-item">
                        <input type="radio" name="is_category_wise_product_view" value="1" class="selectgroup-input" <?php if(isset($xvalue["is_category_wise_product_view"]) && $xvalue["is_category_wise_product_view"]=='1') echo 'checked'; ?>>
                        <span class="selectgroup-button"> <?php echo $this->lang->line("Category-wise") ?></span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="is_category_wise_product_view" value="0" class="selectgroup-input" <?php if(isset($xvalue["is_category_wise_product_view"]) && $xvalue["is_category_wise_product_view"]=='0') echo 'checked'; ?>>
                        <span class="selectgroup-button"> <?php echo $this->lang->line("None") ?></span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label>
                      <?php echo $this->lang->line("Product viewing"); ?>
                    </label>
                    <div class="selectgroup d-block">
                      <label class="selectgroup-item">
                        <input type="radio" name="product_listing" value="grid" class="selectgroup-input" <?php if(isset($xvalue["product_listing"]) && $xvalue["product_listing"]=='grid') echo 'checked'; ?>>
                        <span class="selectgroup-button"> <?php echo $this->lang->line("Grid view") ?></span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="product_listing" value="list" class="selectgroup-input" <?php if(isset($xvalue["product_listing"]) && $xvalue["product_listing"]=='list') echo 'checked'; ?>>
                        <span class="selectgroup-button"> <?php echo $this->lang->line("List view") ?></span>
                      </label>
                    </div>
                  </div>
                </div>
              
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label><?php echo $this->lang->line("Product sorting"); ?></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                      <span class="input-group-text"><?php echo $this->lang->line("Order by"); ?></span>
                      </div>
                      <?php
                      $product_sort =isset($xvalue['product_sort'])?$xvalue['product_sort']:"name";
                      $product_sort_order =isset($xvalue['product_sort_order'])?$xvalue['product_sort_order']:"asc";
                      $arr = array
                      (
                        'name'=>$this->lang->line('Product Title'),
                        'new'=>$this->lang->line('New Product'),
                        'price'=>$this->lang->line('Price'),
                        'sale'=>$this->lang->line('Total Sales'),
                        'random'=>$this->lang->line('Random')
                      );
                      $arr2 = array
                      (
                        'asc'=>$this->lang->line('Ascending'),
                        'desc'=>$this->lang->line('Descending')
                      );
                      ?>
                      <?php echo form_dropdown('product_sort', $arr,$product_sort,"class='form-control'"); ?>
                      <?php echo form_dropdown('product_sort_order', $arr2,$product_sort_order,"class='form-control'"); ?>
                    </div>
                  </div>
                </div>

                
                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label><?php echo $this->lang->line("Font"); ?></label>
                    <?php
                    $default_font =isset($xvalue['font'])?$xvalue['font']:'"Trebuchet MS",Arial,sans-serif';
                    if($default_font=='') $default_font = '"Trebuchet MS",Arial,sans-serif';
                    ?>
                    <?php echo form_dropdown('font', $font_list, $default_font,"class='form-control id='font'"); ?>
                  </div>
                </div>
                
                <div class="col-12 <?php if($xdata2['store_type'] == 'physical') echo 'col-md-6'; ?>">
                  <div class="form-group">
                    <label>
                      <?php echo $this->lang->line("Buy now button title"); ?>
                    </label>
                    <div class="input-group mb-2">
                        <input type="text" name="buy_button_title" id="buy_button_title" class="form-control" value="<?php echo isset($xvalue['buy_button_title']) ? $xvalue['buy_button_title'] : "Buy Now";?>">
                    </div>                                      
                  </div>
                </div>

                <?php if($xdata2['store_type'] == 'physical') : ?>
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label>
                        <?php echo $this->lang->line("Store pickup title"); ?>
                      </label>
                      <div class="input-group mb-2">
                          <input type="text" name="store_pickup_title" id="store_pickup_title" class="form-control" value="<?php echo isset($xvalue['store_pickup_title']) ? $xvalue['store_pickup_title'] : "Store Pickup";?>">
                      </div>                                      
                    </div>
                  </div>
                <?php endif; ?>

                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label for=""><?php echo $this->lang->line('Hide Add to Cart Button');?></label>
                    <br>
                    <?php 
                    $hide_add_to_cart =isset($xvalue['hide_add_to_cart'])?$xvalue['hide_add_to_cart']:"0";
                    ?>
                    <label class="custom-switch mt-2">
                      <input type="checkbox" name="hide_add_to_cart" value="1" class="custom-switch-input"  <?php if($hide_add_to_cart=='1') echo 'checked'; ?>>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"><?php echo $this->lang->line('Hide');?></span>
                      <span class="red"><?php echo form_error('hide_add_to_cart'); ?></span>
                    </label>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label for=""><?php echo $this->lang->line('Hide Buy Now Button');?></label>
                    <br>
                    <?php 
                    $hide_buy_now =isset($xvalue['hide_buy_now'])?$xvalue['hide_buy_now']:"0";
                    ?>
                    <label class="custom-switch mt-2">
                      <input type="checkbox" name="hide_buy_now" value="1" class="custom-switch-input"  <?php if($hide_buy_now=='1') echo 'checked'; ?>>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"><?php echo $this->lang->line('Hide');?></span>
                      <span class="red"><?php echo form_error('hide_buy_now'); ?></span>
                    </label>
                  </div>
                </div>  
              </div>         


              <?php 
                if($this->basic->is_exist("modules",array("id"=>310))) :
                if($this->session->userdata('user_type') == 'Admin' || in_array(310,$this->module_access)) :
              ?>
                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for=""><?php echo $this->lang->line('Whatsapp Send Order Button');?></label>
                      <br>
                      <?php 
                      $whatsapp_send_order_button =isset($xvalue['whatsapp_send_order_button'])?$xvalue['whatsapp_send_order_button']:"0";
                      ?>
                      <label class="custom-switch mt-2">
                        <input type="checkbox" id="whatsapp_send_order_button" name="whatsapp_send_order_button" value="1" class="custom-switch-input"  <?php if($whatsapp_send_order_button=='1') echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Hide');?></span>
                        <span class="red"><?php echo form_error('whatsapp_send_order_button'); ?></span>
                      </label>
                    </div>
                  </div> 

                  <div class="col-12 col-md-6 whatsapp_phone_number_div" <?php if($whatsapp_send_order_button == '1') echo 'style="display:block"'; else echo 'style="display:none"' ?>>
                    <div class="form-group">
                      <label for=""><?php echo $this->lang->line('Whatsapp Phone Number');?> <span class="red">*</span></label>
                      <br>
                      <?php 
                      $whatsapp_phone_number =isset($xvalue['whatsapp_phone_number'])?$xvalue['whatsapp_phone_number']:"";
                      ?>
                      <input type="text" name="whatsapp_phone_number" id="whatsapp_phone_number" value="<?php echo $whatsapp_phone_number; ?>" class="form-control" placeholder="<?php echo $this->lang->line('Type phone number with country code'); ?>">
                      <span class="red"><?php echo form_error('whatsapp_phone_number'); ?></span>
                    </div>
                  </div> 

                  <div class="col-12 whatsapp_message_div" <?php if($whatsapp_send_order_button == '1') echo 'style="display:block"'; else echo 'style="display:none"' ?>>
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Whatsapp Send Order text'); ?></label>
                          <a id="variables" class="float-right text-warning pointer"><i class="fas fa-circle"></i> <?php echo  $this->lang->line("Variables"); ?></a>
                      
                      <textarea name="whatsapp_send_order_text" id="whatsapp_send_order_text" cols="30" rows="10" class="form-control whatsapp_send_order_text" style="height:250px !important;"><?php echo !empty($xvalue['whatsapp_send_order_text']) ? $xvalue['whatsapp_send_order_text']: $default_whatsapp_send_order_text; ?></textarea>
                    </div>
                  </div> 
                </div>   

              <?php endif; ?>
              <?php endif; ?>         


            <div class="card-footer p-0">
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
            ['font', ['bold', 'underline','italic','clear']],
            // ['fontname', ['fontname']],
            // ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ]
      });

    $(document).on('click','[name="theme_front"]',function(e){
      theme_front = $(this).val();
      $("#theme_color").val(theme_front);
    });



    $(document).on('change', 'input[name=whatsapp_send_order_button]', function(event) {
      event.preventDefault();
      var whatsapp_send_order_button = $("input[name=whatsapp_send_order_button]:checked").val();

      if(typeof(whatsapp_send_order_button)=="undefined") {
        $(".whatsapp_phone_number_div").css('display','none');
        $(".whatsapp_message_div").css('display','none');
      } else {
        $(".whatsapp_phone_number_div").css('display','block');
        $(".whatsapp_message_div").css('display','block');
      }
    });

    $(document).on('click','#variables',function(e){
      e.preventDefault();          

      var success_message= '{{order_no}}<br/>{{customer_info}}<br/>{{product_info}}<br/>{{order_status}}<br/>{{order_url}}<br/>{{payment_method}}<br/>{{tax}}<br/>{{total_price}}<br/>{{delivery_address}}';
      var span = document.createElement("span");
      span.innerHTML = success_message;
      swal({ title:'<?php echo $this->lang->line("Variables"); ?>', content:span,icon:'info'});     
    }); 


  });
</script>