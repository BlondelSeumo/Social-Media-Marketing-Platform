<?php $this->load->view('admin/theme/message'); ?>
<style>
    #search_page_id{min-width: 150px !important;}
    .list-unstyled-border li{border-bottom: none;margin-bottom: 10px;padding-top: 0;}
    .tickets-list .ticket-item{border-bottom: none;padding:0;margin-bottom: 14px;}
    .tickets-list .ticket-item h4{margin-bottom: 9px;}
    .media{margin-bottom: 12px;}
    .media .media-title {margin-bottom: 0;font-size: 14px;}
    .ticket-title h4{color:#000 !important;margin-bottom: 3px !important;font-weight:500 !important;}
    .website{font-size: 20px;}
    .website i{font-size: 20px !important;}
    .website h4{font-size: 20px !important;}
</style>

<style type="text/css">
  .button-outline
  {
    background: #fff;
    border: .5px dashed #ccc;
  }
  .button-outline:hover
  {
    border: 1px dashed var(--blue) !important;
    cursor: pointer;
  }
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  /*.multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;margin-top: 15px}*/
  .multi_layout .list-group li{padding: 15px 10px 12px 25px;}
  .multi_layout{border:.5px solid #dee2e6;}
  .multi_layout .collef,.multi_layout .colmid,.multi_layout .colrig{padding-left: 0px; padding-right: 0px;}
  .multi_layout .collef,.multi_layout .colmid{border-right: .5px solid #dee2e6;}
  .multi_layout .main_card{box-shadow: none;}
  /*.multi_layout .collef .makeScroll{max-height: 500px;overflow:auto;}*/
  /*.multi_layout .colrig .makeScroll{max-height:750px;overflow:auto;}*/
  .multi_layout .list-group{padding-top:6px;}
  .multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;cursor: pointer;z-index: 0;padding:21px;}
  .multi_layout .list-group .list-group-item:first-child{border-top:none;}
  .multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
  .multi_layout .list-group .list-group-item.active{border:.5px solid var(--blue);}
  .multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout .card-statistic-1{border-radius: 0;}
  .multi_layout h6.page_name{font-size: 14px;margin:10px 0;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .card .card-header h4 a{font-weight: 700 !important;}  
  .product-item .product-name{font-weight: 500;}
  .badge-status{border-color:#eee;}
  /* #right_column_title i{font-size: 17px;} */
  #cart_activities{height: 710px;overflow:auto;}
  ::placeholder {
    color: #ccc !important;
  } 

  #right_column_bottom_content .no_shadow .card-header,#right_column_bottom_content .no_shadow .card-body{padding:20px 0;}  
  /*.multi_layout .card-statistic-1 .card-icon{border: .5px solid #dee2e6;}*/
  .multi_layout .card.card-statistic-1 .card-icon, .card.card-statistic-2 .card-icon{margin:0;border-radius: 4px 0 0 4px;background: transparent;}
  .multi_layout .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;margin-bottom: 5px;}
  .multi_layout .card.card-statistic-1 .card-header, .multi_layout  .card.card-statistic-2 .card-header{padding:0;padding-top: 20px;}
  .multi_layout .card.card-statistic-1 .card-body, .multi_layout  .card.card-statistic-2 .card-body{padding: 0 ;}
  /*#right_column_bottom_content div[class^='col'], #right_column_bottom_content  div[class*=' col']{padding-left: 5px;padding-right: 5px;}*/

</style>



<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-store"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <a title="<?php echo $this->lang->line('Create Store');?>" data-toggle="tooltip" class="btn btn-primary iframed" href="<?php echo base_url("ecommerce/add_store");?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Store"); ?>
      </a> 
    </div>    
  </div>

  <div class="section-body">
    <?php if(!empty($store_data)){ ?>
    <div class="card" id="store_date_range">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-sm-12 col-md-12 col-lg-8">
            <div class="breadcrumb-item">
              <form method="POST" action="<?php echo base_url('ecommerce/store_list') ?>">          
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-calendar"></i>
                    </div>
                  </div>
                  <input type="hidden" name="store_id" id="store_id">
                  <input type="text" class="form-control datepicker_x" value="<?php echo $this->session->userdata("ecommerce_from_date"); ?>" id="from_date" name="from_date" style="width:115px"> 
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      -
                    </div>
                  </div>
                  <input type="text" class="form-control datepicker_x" value="<?php echo $this->session->userdata("ecommerce_to_date"); ?>" id="to_date" name="to_date" style="width:115px">                 
                  <select name='currency' id='currency' class='form-control select2' style="width: 85px;">
                  <?php
                  foreach ($currecny_list_all as $key => $value)
                  {
                    if($this->session->userdata("ecommerce_currency")==$key) $selected_curr = "selected='selected'";
                    else $selected_curr = '';
                    echo '<option value="'.$key.'" '.$selected_curr.' >'.$key.'</option>';
                  }
                  ?>
                  </select>
                  <button class="btn btn-outline-primary" style="margin-left:1px" id="search_submit" type="submit"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php }?>


    <?php if(empty($store_data))
    { ?>       
    <div class="card" id="nodata">
      <div class="card-body">
        <div class="mt-3">
          <iframe src="" frameborder="0" style="display: none;" width="100%" onload="resizeIframe(this)"></iframe>
        </div>
        <div class="empty-state hide_in_iframe">
          <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
           <h2 class="mt-0"><?php echo $this->lang->line("We could not find any ecommerce store.");?></h2>
          <p class="lead"><?php echo $this->lang->line("You have to create a store first."); ?></p>
          <a href="<?php echo base_url('ecommerce/add_store'); ?>" title="<?php echo $this->lang->line('Create Store');?>" data-toggle="tooltip" class="btn btn-primary iframed" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Create Store");?></a>
        </div>
      </div>
    </div>

    <?php 
    }
    else
    { 
      $summary_earning = 0;
      $summary_recovered_cart = 0;
      $summary_reminder_cart = 0;
      $summary_checkout_cart = 0;
      $earning_chart_labels = array();
      $earning_chart_values = array();
      // $top_buyers = array();
   
      $from_date = strtotime($this->session->userdata("ecommerce_from_date")); 
      $to_date = strtotime($this->session->userdata("ecommerce_to_date")); 
      do 
      {
         $temp = date("Y-m-d",$from_date);
         $temp2 = date("j M",$from_date);;
         $earning_chart_values[$temp] = 0;
         $earning_chart_labels[] = $temp2;
         $from_date = strtotime('+1 day',$from_date); 
      } 
      while ($from_date <= $to_date);

      foreach ($cart_data as $key => $value) 
      {
        if($value['action_type']=='checkout') $summary_checkout_cart++;   

        if($value["last_completed_hour"]>0)
        {
          $summary_reminder_cart++;
          if($value['action_type']=='checkout') $summary_recovered_cart++;          
        }

        if($value["status"]!='pending' && $value["status"]!='rejected')
        {
          $summary_earning += $value["payment_amount"];
          $updated_at_formatted = date("Y-m-d",strtotime($value['updated_at']));
          if(isset($earning_chart_values[$updated_at_formatted])) $earning_chart_values[$updated_at_formatted] += $value["payment_amount"];
          else $earning_chart_values[$updated_at_formatted] = $value["payment_amount"];

          // if($value['buyer_country']!='')
          // {
          //   if(isset($top_buyers[$value['buyer_country']])) $top_buyers[$value['buyer_country']] += $value["payment_amount"];
          //   else $top_buyers[$value['buyer_country']] = $value["payment_amount"];
          // }
        }
      }
      // arsort($top_buyers);   
      ?>
      <div class="row multi_layout">

        <div class="col-12 col-md-3 col-lg-2 collef">
          <div class="card main_card">
            <div class="card-header">
              <!-- <div class="col-6 padding-0"> -->
                <h4><i class="fas fa-store-alt"></i> <?php echo $this->lang->line("Stores"); ?></h4>
              <!-- </div> -->
              <!-- <div class="col-6 padding-0">             -->
                <!-- <input type="text" class="form-control float-right" id="search_page_list" onkeyup="search_in_ul(this,'page_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>"> -->
              <!-- </div> -->
            </div>
            <div class="card-body padding-0">
              <ul class="list-group" id="page_list_ul" style="margin-top: 20px;">
                <?php $i=0; 
                $current_store_data =  array();
                foreach($store_data as $value) 
                { 
                  if($value['id']==$this->session->userdata("ecommerce_selected_store")) $current_store_data = $value;
                  
                  ?> 
                  <li class="list-group-item <?php if($value['id']==$this->session->userdata("ecommerce_selected_store")) echo 'active'; ?> page_list_item" page_table_id="<?php echo $value['id']; ?>">
                    <h6 class="page_name"><?php if($value['store_type'] == 'digital') echo '<i class="fas fa-cloud-download-alt"></i>'; else echo '<i class="fas fa-store"></i>'; ?> <?php echo str_replace(array('https://','http://'), '', $value['store_name']); ?></h6>
                  </li>
                  <?php $i++; 
                } ?>                
              </ul>
            </div>
          </div>          
        </div>

        <?php 
        $config_currency  = isset($ecommerce_config['currency']) ? $ecommerce_config['currency'] : "USD";
        if($this->session->userdata("ecommerce_currency")=='')
        $store_currency = isset($currency_icons[$config_currency]) ? $currency_icons[$config_currency] : "$";
        else $store_currency = isset($currency_icons[$this->session->userdata("ecommerce_currency")]) ? $currency_icons[$this->session->userdata("ecommerce_currency")] : "$";
        $currency_position = isset($ecommerce_config['currency_position']) ? $ecommerce_config['currency_position'] : "left";
        $decimal_point = isset($ecommerce_config['decimal_point']) ? $ecommerce_config['decimal_point'] : 0;
        $thousand_comma = isset($ecommerce_config['thousand_comma']) ? $ecommerce_config['thousand_comma'] : '0';
        $currency_left = $currency_right = "";
        if($currency_position=='left') $currency_left = $store_currency;
        if($currency_position=='right') $currency_right = $store_currency;

        $menu_array = array
        (
          0 => array
          (
            'class'=>'',
            'href'=>base_url('ecommerce'),
            'title'=>$this->lang->line('Dashboard'),
            'icon'=>'fas fa-tachometer-alt',
            'attr'=>''
          ),
          5 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/edit_store/".$current_store_data['id'],
            'title'=>$this->lang->line('Store Settings'),
            'icon'=>'fas fa-cog',
            'attr'=>'campaign_id='.$current_store_data['id']
          ),
          8 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/payment_accounts/".$current_store_data['id'],
            'title'=>$this->lang->line('Checkout Settings'),
            'icon'=>'fas fa-credit-card',
            'attr'=>''
          ),
          9 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/appearance_settings/".$current_store_data['id'],
            'title'=>$this->lang->line('Appearance Settings'),
            'icon'=>'fas fa-palette',
            'attr'=>''
          ),
          10 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/business_hour_settings",
            'title'=>$this->lang->line('Business Hour Settings'),
            'icon'=>'fas fa-calendar-check',
            'attr'=>''
          ),
          14 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/category_list'),
            'title'=>$this->lang->line('Categories'),
            'icon'=>'fas fa-columns',
            'attr'=>''
          ),          
          17 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/attribute_list'),
            'title'=>$this->lang->line('Attributes'),
            'icon'=>'fas fa-palette',
            'attr'=>''
          ),
          20 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/product_list'),
            'title'=>$this->lang->line('Products'),
            'icon'=>'fas fa-box-open',
            'attr'=>''
          ),
          21 => array
          (
            'class'=>'',
            'href'=>base_url('ecommerce/store/'.$current_store_data['store_unique_id']),
            'title'=>$this->lang->line('Visit Store'),
            'icon'=>'fas fa-newspaper',
            'attr'=>'target="_BLANK"'
          ),
          23 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/pickup_point_list'),
            'title'=>$this->lang->line('Delivery Points'),
            'icon'=>'fas fa-map-marker-alt',
            'attr'=>''
          ),          
          24 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/qr_code/".$current_store_data['id'],
            'title'=>$this->lang->line('QR Menu'),
            'icon'=>'fas fa-qrcode',
            'attr'=>'campaign_id='.$current_store_data['id']
          ),
          26 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/coupon_list'),
            'title'=>$this->lang->line('Coupons'),
            'icon'=>'fas fa-gifts',
            'attr'=>''
          ),
          28 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/customer_list'),
            'title'=>$this->lang->line('Signed-up Customers'),
            'icon'=>'fas fa-users',
            'attr'=>''
          ),
          29 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/order_list'),
            'title'=>$this->lang->line('Orders'),
            'icon'=>'fas fa-cart-plus',
            'attr'=>''
          ),
          36 => array
          (
            'class'=>'iframed',
            'href'=>base_url('ecommerce/copy_url/'.$this->session->userdata("ecommerce_selected_store")),
            'title'=>$this->lang->line('Copy URL'),
            'icon'=>'fas fa-copy',
            'attr'=>''
          ),

          39 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/notification_settings/".$current_store_data['id'],
            'title'=>$this->lang->line('Order Status Notification'),
            'icon'=>'fas fa-bell',
            'attr'=>'campaign_id='.$current_store_data['id']
          ),
          42 => array
          (
            'class'=>'iframed',
            'href'=>base_url()."ecommerce/reminder_settings/".$current_store_data['id'],
            'title'=>$this->lang->line('Confirmation & Reminder'),
            'icon'=>'fas fa-bullhorn',
            'attr'=>'campaign_id='.$current_store_data['id']
          ),
          45 => array
          (
            'class'=>'reminder_report',
            'href'=>'',
            'title'=>$this->lang->line('Reminder Report'),
            'icon'=>'fas fa-eye',
            'attr'=>'campaign_id='.$current_store_data['id']
          ),
          48 => array
          (
            'class'=>'delete_campaign',
            'href'=>'#',
            'title'=>$this->lang->line('Delete Store'),
            'icon'=>'fas fa-trash-alt',
            'attr'=>'campaign_id='.$current_store_data['id']
          )
        );

        ?>

        <div class="col-12 col-md-9 col-lg-8 colrig" id="right_column">

          <div class="card main_card">
            <div class="card-header">
              <div class="col-8 p-0">
                <h4 id="right_column_title"><i class="fas fa-tachometer-alt"></i> <a title="<?php echo $this->lang->line("Visit Store"); ?>" data-toggle="tooltip" target="_BLANK" href="<?php echo base_url('ecommerce/store/'.$current_store_data['store_unique_id']); ?>"><?php echo str_replace(array('https://','http://'), '', $current_store_data['store_name']); ?></a> (
                    <?php if($current_store_data['page_id'] != 0) : 
                      echo '<a title="'.$this->lang->line("Visit Page").'" data-toggle="tooltip" target="_BLANK" href="https://facebook.com/'.$current_store_data['fb_page_id'].'">'.$current_store_data['page_name'].'</a>';
                     else :
                        echo $this->lang->line('No Page');
                      ?>
                    <?php endif; ?>
                  ) 
                  <span id='iframe_title'> : <?php echo $this->lang->line("Dashboard"); ?></span></h4>
              </div>
              <div class="col-4 p-0 d-lg-none">
                <div class="card-header-action dropleft float-right">
                  <a href="#" data-toggle="dropdown" class="btn btn-lg btn-outline-primary dropdown-toggle"><?php echo $this->lang->line("Actions"); ?></a>
                  <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="width: 250px !important">
                    <?php
                    foreach ($menu_array as $key => $value) {

                      if($current_store_data['store_type'] == 'digital' && $value['href'] == base_url('ecommerce/business_hour_settings')) continue;

                      if($current_store_data['store_type'] == 'digital' && $value['href'] == base_url('ecommerce/pickup_point_list')) continue;

                      echo '<li><a data-original-title="'.$value['title'].'" class="dropdown-item '.$value['class'].'" href='.$value['href'].' '.$value['attr'].'><i class="'.$value['icon'].'" ></i> &nbsp; '.$value['title'].'</a></li>';
                    }
                    ?>
                  </ul>
                </div> 
              </div>
            </div>

            <div class="card-body" >
              <iframe src="" frameborder="0" style="display: none;" width="100%" onload="resizeIframe(this)"></iframe>
              <div class="row hide_in_iframe mt-1">
                <div class="col-12">

                  <div id="right_column_content">              

                    <div id="right_column_bottom_content">

                      <div class="row">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-cart-plus text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Total Order"); ?></h4>
                              </div>
                              <div class="card-body">
                                <?php echo count($cart_data); ?>
                              </div>
                            </div>
                          </div>
                        </div>
                       <!--  <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-bell text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Reminded Cart"); ?></h4>
                              </div>
                              <div class="card-body">
                                <?php echo $summary_reminder_cart; ?>
                              </div>
                            </div>
                          </div>
                        </div> -->
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-shopping-bag text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Recovered Cart"); ?></h4>
                              </div>
                              <div class="card-body">
                                <?php echo $summary_recovered_cart; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-credit-card text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Checkout Order"); ?></h4>
                              </div>
                              <div class="card-body">
                               <?php echo $summary_checkout_cart ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                          <div class="card card-statistic-1">
                            <div class="card-icon">
                              <i class="fas fa-coins text-primary"></i>
                            </div>
                            <div class="card-wrap">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line("Total Earnings"); ?></h4>
                              </div>
                              <div class="card-body">
                               <?php //echo $store_currency.custom_number_format($summary_earning); ?>
                               <?php echo $currency_left.mec_number_format($summary_earning,$decimal_point,$thousand_comma).$currency_right; ?>
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>

                      <div class="row">

                        <div class="col-12 col-md-12 col-lg-8">
                          <div class="card no_shadow">
                            <div class="card-header">
                              <h4><i class="fas fa-coins"></i> <?php echo $this->lang->line("Earnings"); ?></h4>
                            </div>
                            <div class="card-body">
                              <canvas id="myChart" height="182"></canvas>
                            </div>
                          </div>
                          <div class="card no_shadow">
                            <div class="card-header">
                              <h4><i class="fas fa-star"></i> <?php echo $this->lang->line("Top Products"); ?></h4>
                            </div>
                            <div class="card-body">
                              <div class="owl-carousel owl-theme" id="products-carousel">
                                  <?php                                 
                                  $product_list_assoc = array();                         
                                  foreach ($product_list as $key => $value) 
                                  {
                                    $product_list_assoc[$value["id"]] = $value;
                                  }
                                  foreach ($top_products as $key => $value) 
                                  { 
                                      $pro_id = $value["product_id"];

                                      $thumb = (isset($product_list_assoc[$pro_id]["thumbnail"]) && !empty($product_list_assoc[$pro_id]["thumbnail"])) ? base_url('upload/ecommerce/'.$product_list_assoc[$pro_id]["thumbnail"]) : base_url('assets/img/example-image.jpg');

                                      if(isset($product_list_assoc[$pro_id]["woocommerce_product_id"]) && !is_null($product_list_assoc[$pro_id]["woocommerce_product_id"]) && isset($product_list_assoc[$pro_id]["thumbnail"]) && !empty($product_list_assoc[$pro_id]["thumbnail"]))
                                      $thumb = $product_list_assoc[$pro_id]["thumbnail"];

                                      $pro_name  = isset($product_list_assoc[$pro_id]["product_name"]) ? $product_list_assoc[$pro_id]["product_name"] : "";
                                      ?>
                                      <div>
                                        <div class="product-item">
                                          <div class="product-image">                                  
                                            <a target="_BLANK" href="<?php echo base_url('ecommerce/product/'.$pro_id);?>" ><img style="width:80px;height:80px;border:1px solid #eee;"  src="<?php echo $thumb; ?>" class="img-fluid rounded-circle"></a>
                                          </div>
                                          <div class="product-details">
                                            <div class="product-name"><a target="_BLANK" href="<?php echo base_url('ecommerce/product/'.$pro_id);?>" ><?php echo $pro_name; ?></a></div>
                                            <div class="text-muted text-small"><?php echo $value["sales_count"];?> <?php echo $this->lang->line("Sales"); ?></div>
                                            <!-- <div class="product-cta">
                                              <a target="_BLANK" href="<?php echo base_url('ecommerce/product/'.$pro_id);?>" class="btn btn-outline-primary"><?php echo $this->lang->line("Details"); ?></a>
                                            </div> -->
                                          </div>
                                        </div>
                                      </div>
                                  <?php
                                  } ?>                          
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-4">
                          
                          <div class="card no_shadow">
                            <div class="card-header">
                                <h4><i class="fas fa-tasks"></i> <?php echo $this->lang->line("Cart Activities"); ?></h4>                             
                            </div>
                            <div class="card-body nicescroll" id="cart_activities" style="padding-right: 10px">

                             
                              <ul class="list-unstyled list-unstyled-border">
                                <?php
                                if(empty($cart_data)) echo '<div class="alert alert-light">'.$this->lang->line("No activity found").'</div>';                               
                                foreach ($cart_data as $key => $value) 
                                { 
                                  $hook_ago = date_time_calculator($value['updated_at'],true);
                                  if($value['action_type']=='add') 
                                  {
                                    $hook_icon ='fas fa-cart-plus';
                                    $hook_color = 'text-primary';
                                    $hook_activity = $this->lang->line("New item added to cart");
                                  }
                                  else if($value['action_type']=='remove') 
                                  {
                                    $hook_icon ='fas fa-cart-arrow-down';
                                    $hook_color = 'text-danger';
                                    $hook_activity = $this->lang->line("Item removed from cart");
                                  }
                                  else 
                                  {
                                    $hook_icon = 'fas fa-shopping-bag';
                                    $hook_color = 'text-success';
                                    $currency_icon = isset($currency_icons[strtoupper($value['currency'])]) ? $currency_icons[strtoupper($value['currency'])] : '';
                                    $hook_activity = $this->lang->line("Successful checkout").' <span class="">('.$currency_icon.$value['payment_amount'].')</span>';
                                  }
                                 
                                  $hook_class = $hook_icon.' '.$hook_color;
                                  
                                  $hook_user = ($value['first_name']!='') ? $value['first_name']." ".$value['last_name'] : $value['full_name'];

                                  $profile_pic = ($value['profile_pic']!="") ? "<img class='mr-3 rounded-circle' style='height:40px;width:40px;' src='".$value["profile_pic"]."'>" :  "<img class='mr-3  rounded-circle' style='height:40px;width:40px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";

                                  $path = ($value["image_path"]!="") ? "<img class='mr-3 rounded-circle' style='height:40px;width:40px;' src='".base_url($value["image_path"])."'></a>" : $profile_pic;

                                  echo
                                  ' 
                                  <li class="media webhook_data pointer" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$value['email']." (".$value['subscriber_id'].')">
                                      '.$path.'
                                      <div class="media-body">
                                        <div class="float-right text-primary text-small pr-1 ltr">'.$hook_ago.'</div>
                                        <div class="media-title ltr"><i class="'.$hook_class.'"></i> '.$hook_user.'</div>
                                        <span class="text-small">'.$hook_activity.'</span>
                                      </div>
                                  </li>';                         
                                } 
                                ?>                               
                              </ul>
                            </div>
                          </div>
                        </div>                     

                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
        
          </div>
        </div>

        <div class="col-lg-2 collef d-none d-lg-block" style="border:.5px solid #dee2e6;">
          <div class="card main_card" >
            <div class="card-header">
                <h4><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Actions"); ?></h4>
            </div>
            <div class="card-body padding-0">
              <ul class="nav nav-pills flex-column settings_menu" style="margin-top: 20px;">
                 <?php
                    $count_menu=0;
                    foreach ($menu_array as $key => $value) {
                      $count_menu++;
                      $active_class = ($count_menu==1) ? 'active' : '';

                      if($current_store_data['store_type'] == 'digital' && $value['href'] == base_url('ecommerce/business_hour_settings')) continue;

                      if($current_store_data['store_type'] == 'digital' && $value['href'] == base_url('ecommerce/pickup_point_list')) continue;

                      echo ' <li class="nav-item"><a  data-original-title="'.$value['title'].'" href="'.$value['href'].'" class="no_radius nav-link '.$value['class'].' '.$active_class.'" '.$value['attr'].'><i class="'.$value['icon'].'"></i> '.$value['title'].'</a></li>';
                    }
                 ?>
              </ul>
            </div>
          </div>
        </div>


      </div>

    <?php 
    } ?>

  </div>
</section>


<?php 
if(!empty($current_store_data) && !empty($store_data)) 
{
  $max = (!empty($earning_chart_values)) ? max($earning_chart_values) : 0;
  $steps = $max/5;
  if($steps==0) $steps = 1;
  ?>

  <script type="text/javascript">
    var statistics_chart = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(statistics_chart, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($earning_chart_labels); ?>,
        datasets: [{
          label: '<?php echo $this->lang->line("Earning"); ?>',
          data: <?php echo json_encode(array_values($earning_chart_values)); ?>,
          borderWidth: 3,
          borderColor: '#0D8BF1',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#0D8BF1',
          pointBorderColor: '#0D8BF1',
          pointRadius: 3
        }]
      },
      options: {
        legend: {
          display: false
        },
        scales: {
          yAxes: [{
            gridLines: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              stepSize: <?php echo $steps; ?>
            }
          }],
          xAxes: [{
            gridLines: {
              display: false,
              drawBorder: false,
              color: '#dee2e6',
              lineWidth: 1
            }
          }]
        },
      }
    });  
  </script>
  <?php 
} ?>

<script>
$(document).ready(function($) {  
    var base_url = '<?php echo base_url(); ?>';
 
    $(document).on('click', '.delete_campaign', function(event) {
        event.preventDefault();
        
        swal({
              title: '<?php echo $this->lang->line("Delete Store"); ?>',
              text: '<?php echo $this->lang->line("Do you really want to delete this store? Deleting store will also delete all related data like cart,purchase,settings etc."); ?>',
              icon: 'warning',
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) 
              {
                  var base_url = '<?php echo site_url();?>';
                  $(this).addClass('btn-danger btn-progress');
                  $(this).removeClass('btn-outline-danger');
                  var that = $(this);
                  var campaign_id = $(this).attr('campaign_id');

                  $.ajax({
                    context: this,
                    type:'POST' ,
                    url:"<?php echo site_url();?>ecommerce/delete_store",
                    dataType: 'json',
                    data:{campaign_id : campaign_id},
                    success:function(response){ 

                      $(that).removeClass('btn-danger btn-progress');
                      $(this).addClass('btn-outline-danger');
                      
                      if(response.status == '1')
                      {
                        iziToast.success({title: '<?php echo $this->lang->line("Deleted Successfully"); ?>', message: response.message,position: 'bottomRight'});
                        $("#search_submit").click();
                      }
                      else
                      iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message ,position: 'bottomRight'});    
                    }
                  });
              } 
            });
    });

    $(document).on('click', '#copy_urls', function(event) {
        event.preventDefault();
        $("#copy_data_modal").modal();
    });

    $(document).on('click', '.page_list_item', function(event) {
        event.preventDefault();
        $('.page_list_item').removeClass('active');
        $(this).addClass('active');
        var store_id = $(this).attr('page_table_id');
        $("#store_id").val(store_id);
        $("#search_submit").click();
    });

    var table2="";
    $(document).on('click', '.reminder_report', function(event) {
        event.preventDefault();
        $("#reminder_data").modal();

        setTimeout(function(){    
        if (table2 == '')
        {
          var perscroll2;
          table2 = $("#mytable2").DataTable({
              serverSide: true,
              processing:true,
              bFilter: true,
              order: [[ 7, "desc" ]],
              pageLength: 10,
              ajax: {
                  url: '<?php echo base_url("ecommerce/reminder_send_status_data"); ?>',
                  type: 'POST',
                  data: function ( d )
                  {
                      d.page_id = $('#hidden_page_id').val();
                  }
              },
              language: 
              {
                url: '<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json');?>'
              },
              dom: '<"top"f>rt<"bottom"lip><"clear">',
              columnDefs: [
                {
                    targets: [1],
                    visible: false
                },
                {
                    targets: [0,5,6,7,8],
                    className: 'text-center'
                }
              ],
              fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                  if(areWeUsingScroll)
                  {
                    if (perscroll2) perscroll2.destroy();
                    perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                  }
              },
              scrollX: 'auto',
              fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                  if(areWeUsingScroll)
                  { 
                    if (perscroll2) perscroll2.destroy();
                    perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                  }
              }
          });
        }
        else table2.draw();
        }, 1000);
    });


    $(document).on('click','.woo_error_log',function(e){
      e.preventDefault();
      $(this).removeClass('btn-outline-primary').addClass("btn-primary").addClass('btn-progress');
      var id = $(this).attr('data-id');

      $.ajax
        ({
           type:'POST',
           url:base_url+'ecommerce/reminder_response',
           data:{id:id},
           context: this,
           success:function(response)
            {
              $(this).addClass('btn-outline-primary').removeClass("btn-primary").removeClass('btn-progress');

              var success_message= response;
              var span = document.createElement("span");
              span.innerHTML = success_message;
              swal({ title:'<?php echo $this->lang->line("API Response"); ?>', content:span,icon:'info'});
            } 
        }); 
    });

    $(document).on('click','.iframed',function(e){
      e.preventDefault();
      var iframe_url = $(this).attr('href');
      var iframe_height = $(this).attr('data-height');
      $("iframe").attr('src',iframe_url).show();
      $(".hide_in_iframe").hide();
      $('.breadcrumb-item').hide();
      $("#store_date_range").hide();
      var title=" : "+$(this).attr("data-original-title");
      $("#iframe_title").html(title);      
    });

    $("#products-carousel").owlCarousel({
      items: 4,
      margin: 10,
      autoplay: true,
      autoplayTimeout: 5000,
      loop: true,
      responsive: {
        0: {
          items: 2
        },
        768: {
          items: 2
        },
        1200: {
          items: 4
        }
      }
    });

    $('.datepicker_x').datetimepicker({
      theme:'light',
      format:'Y-m-d H:i:s',
      formatDate:'Y-m-d H:i:s',
      // minDate: today
    });

     $(".settings_menu a").click(function(){
      $(".settings_menu a").removeClass("active");
      $(this).addClass("active");
    });

});
</script>


<?php include(APPPATH.'views/ecommerce/cart_modal.php'); ?>

<div class="modal fade" id="reminder_data" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-bell"></i> <?php echo $this->lang->line("Reminder Report"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body data-card">
        <div class="table-responsive2">
            <table class="table table-bordered" id="mytable2">
              <thead>
                <tr>
                  <th>#</th>      
                  <th style="vertical-align:middle;width:20px">
                      <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                  </th>
                  <th><?php echo $this->lang->line("First Name"); ?></th>
                  <th><?php echo $this->lang->line("Last Name")?></th>
                  <th><?php echo $this->lang->line("Email"); ?></th>
                  <th><?php echo $this->lang->line("Subscriber ID"); ?></th>
                  <th><?php echo $this->lang->line("Reminder Hour"); ?></th>
                  <th><?php echo $this->lang->line("Sent at"); ?></th>
                  <th><?php echo $this->lang->line("API Response"); ?></th>
                  <th><?php echo $this->lang->line("Order"); ?></th>
                </tr>
              </thead>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>