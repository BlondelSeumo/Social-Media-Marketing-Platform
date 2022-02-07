  <?php
  $subscriber_id=$this->session->userdata($product_data['store_id']."ecom_session_subscriber_id");
  if($subscriber_id=="")  $subscriber_id = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
  $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';

  $currency = isset($ecommerce_config['currency']) ? $ecommerce_config['currency'] : "USD";
  $currency_icon = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
  $currency_position = isset($ecommerce_config['currency_position']) ? $ecommerce_config['currency_position'] : "left";
  $decimal_point = isset($ecommerce_config['decimal_point']) ? $ecommerce_config['decimal_point'] : 0;
  $thousand_comma = isset($ecommerce_config['thousand_comma']) ? $ecommerce_config['thousand_comma'] : '0';
  $buy_button_title = isset($ecommerce_config['buy_button_title']) ? $ecommerce_config['buy_button_title'] : $this->lang->line("Buy Now");
  $hide_add_to_cart = isset($ecommerce_config['hide_add_to_cart']) ? $ecommerce_config['hide_add_to_cart'] : "0";
  $hide_buy_now = isset($ecommerce_config['hide_buy_now']) ? $ecommerce_config['hide_buy_now'] : "0";
  $map_array = array();
  $currency_left = $currency_right = "";
  if($currency_position=='left') $currency_left = $currency_icon;
  if($currency_position=='right') $currency_right = $currency_icon;
  foreach ($attribute_price_map as $key => $value) {
    $x = $value["amount"]==0 && $value["price_indicator"]=='x' ? 'x' : '';
    $ammount_formatted = $currency_left.mec_number_format($value["amount"],$decimal_point,$thousand_comma).$currency_right;
    $map_array[$value["attribute_id"]][$value["attribute_option_name"]] = $value["amount"]!=0 ? $value["price_indicator"].$ammount_formatted : $x;
  }

  $product_link = base_url("ecommerce/product/".$product_data['id']); 
  $product_link = mec_add_get_param($product_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

  $current_cart_id = isset($current_cart['cart_id']) ? $current_cart['cart_id'] : 0;
  $cart_count = isset($current_cart['cart_count']) ? $current_cart['cart_count'] : 0;
  $current_cart_url = base_url("ecommerce/cart/".$current_cart_id);
  $current_cart_url = mec_add_get_param($current_cart_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

  $current_cart_data = (isset($current_cart["cart_data"]) && is_array($current_cart["cart_data"])) ? $current_cart["cart_data"] : array();

  $have_attributes = false;
  $product_attributes = array_filter(explode(',', $product_data['attribute_ids']));
  if(is_array($product_attributes) && !empty($product_attributes)) $have_attributes = true;

  $quantity_in_cart = 0;
  if(!$have_attributes) $quantity_in_cart = isset($current_cart_data[$product_data['id']]["quantity"]) ? $current_cart_data[$product_data['id']]["quantity"] : 0;
  else if(isset($_GET['quantity']))  $quantity_in_cart = $_GET['quantity'];

  $carousel = true;
  if($product_data['featured_images']=="" && $product_data['thumbnail']=="") $carousel = false;
  ?>

  <div class="row bg-white pb-3 margin_md"> 
    <div class="<?php echo $carousel ? 'col-12 col-sm-12 col-md-6 col-lg-4' : 'col-12';?>">
      <?php if($carousel) : ?>
      <article class="article article-style-c mt-3 mb-0 remove-margin no_shadow">            
        <?php $featured_images_array = ($product_data['featured_images']!="") ? explode(',', $product_data['featured_images']) : array(); ?>
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="3000">
          <ol class="carousel-indicators mb-0">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <?php 
            if($product_data['featured_images']!="")
            {          
              $slide=0;
              foreach ($featured_images_array as $key => $value) 
              {
                $slide++;
                echo '<li data-target="#carouselExampleIndicators" data-slide-to="'.$slide.'"></li>';
              }
            }
            ?>
          </ol>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <?php 
                $imgSrc = ($product_data['thumbnail']!='') ? base_url('upload/ecommerce/'.$product_data['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                if(isset($product_data["woocommerce_product_id"]) && !is_null($product_data["woocommerce_product_id"]) && $product_data['thumbnail']!='')
                $imgSrc = $product_data['thumbnail'];
              ?>
              <img class="d-block w-100" style="height: 345px;" src="<?php echo $imgSrc; ?>">
              <?php echo $badge = mec_display_price($product_data['original_price'],$product_data['sell_price'],$currency_icon,'4',$currency_position,$decimal_point,$thousand_comma);?>
              <div class="carousel-caption">
                  <h4><?php echo $product_data['product_name'];?></h4>
                  <!-- <p></p> -->
              </div>
            </div>
            <?php 
            if($product_data['featured_images']!="")
            {
              foreach ($featured_images_array as $key => $value)
              { ?>
              <div class="carousel-item">
                <?php
                $imgSrc = base_url('upload/ecommerce/'.$value);
                if(isset($product_data["woocommerce_product_id"]) && !is_null($product_data["woocommerce_product_id"]))
                $imgSrc = $value;
                ?>
                <img class="d-block w-100" style="height: 345px;" src="<?php echo $imgSrc; ?>">
                <?php echo $badge; ?>
                <div class="carousel-caption">
                    <h4><?php echo $product_data['product_name'];?></h4>
                    <!-- <p></p> -->
                </div>
              </div> 
              <?php 
              }?>
            <?php 
            } ?>
          </div>
          <?php if($product_data['featured_images']!="")
          { ?>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only"><?php echo $this->lang->line("Previous");?></span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only"><?php echo $this->lang->line("Next");?></span>
          </a>
        <?php } ?>
        </div>
      </article>
      <?php endif; ?>      
    </div>

    <div class="col-12 col-sm-12 <?php if($carousel) echo 'col-md-6 col-lg-8';?>">
      <div class="card no_shadow mt-3 mb-0 remove-margin">
        <div class="card-header p-2">
           <h4 class="full_width pr-0 text-dark">
            <?php echo $product_data['product_name'];?>
            <span class="float-right" id="calculated_price_basedon_attribute"><?php echo mec_display_price($product_data['original_price'],$product_data['sell_price'],$currency_icon,'1',$currency_position,$decimal_point,$thousand_comma);?></span>          
          </h4>
        </div>
        <?php
        if($this->ecommerce_review_comment_exist && isset($review_data[0])) : 
          $rating = mec_average_rating($review_data[0]['total_point'],$review_data[0]['total_review']);
          $review_star = mec_display_rating_starts($rating);
          if(!empty($rating)) echo '<div class="pr-2 text-right">'.$review_star.' <b>'.number_format($rating,2).'</b></div>';
        endif;
        ?>
      </div>

      <div class="hero p-2" style="border-radius: 0 0 3px 3px;">
        <div class="hero-inner">
          <ul class="nav nav-tabs" id="myTab2" role="tablist">
            <?php if($have_attributes): ?>
            <li class="nav-item">
              <a class="nav-link active show" id="details-tab2" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="false"><?php echo $this->lang->line("Options"); ?></a>
            </li>
            <?php endif; ?>
            <?php if(!empty($product_data['product_description']) || !empty($product_data['product_video_id'])): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo (!$have_attributes) ? 'active show' : '';?>" id="description-tab2" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="false"><?php echo $this->lang->line("Details"); ?></a>
            </li>
            <?php endif; ?>   

            <?php if($this->ecommerce_review_comment_exist): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo (!$have_attributes && empty($product_data['product_description'])) ? 'active show' : '';?>"  id="reviews-tab2" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false"><?php echo $this->lang->line("Reviews"); ?></a>
            </li>
            <?php endif; ?>
            <?php if(!empty($product_data['purchase_note'])): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo (!$have_attributes && empty($product_data['product_description']) && !$this->ecommerce_review_comment_exist) ? 'active show' : '';?>"  id="purchase_note-tab2" data-toggle="tab" href="#purchase_note" role="tab" aria-controls="purchase_note" aria-selected="false"><?php echo $this->lang->line("Note"); ?></a>
            </li>
            <?php endif; ?>
          </ul>
          <div class="tab-content tab-bordered mb-2" id="myTab3Content">
            <?php if($have_attributes): ?>
            <div class="tab-pane fade active show p-2 pb-0" id="details" role="tabpanel" aria-labelledby="details-tab2">
       
               <?php if($have_attributes) 
               { ?>
                <div class="col-12">
                  <!-- <ul class="list-group mb-2"> -->
                    <?php                      
                    $attr_count = 0;
                    foreach ($attribute_list as $key => $value) 
                    {
                      if(in_array($value["id"], $product_attributes))
                      {
                        $attr_count++;
                        $name = "attribute_".$attr_count;
                        $options_array = json_decode($value["attribute_values"],true);
                        $url_option = "option".$value["id"];
                        $selected = isset($_GET[$url_option]) ? $_GET[$url_option] : "";
                        $selected = explode(',', $selected);                        
                        
                        $star = ($value['optional']=='0') ? '*' : '';
                        $options_print = "";
                        $count = 0;
                        foreach ($options_array as $key2 => $value2)
                        {
                          $selected_attr = in_array($value2, $selected) ? "checked" : "";
                          $count++;
                          $temp_id = $name.$count;
                          $tempu = isset($map_array[$value["id"]][$value2]) ? $map_array[$value["id"]][$value2] : "";
                          $continue = false;
                          if($tempu!='')
                          {
                            $first_char = substr($tempu, 0, 1);
                            if($first_char=='x') $continue = true;
                          }
                          if($continue) continue;
                          if($value['multiselect']=='1')
                          {
                            $options_print.='
                            <div class="custom-control custom-checkbox d-block">
                              <input type="checkbox" data-attr="'.$value["id"].'" name="'.$name.'"   value="'.$value2.'" class="custom-control-input options" id="'.$temp_id.'" data-optional="'.$value["optional"].'" '.$selected_attr.'>
                              <label class="custom-control-label" for="'.$temp_id.'">'.$value2.' <b class="text-dark text-small">'.$tempu.'</b></label>
                            </div>';
                          }
                          else 
                          {
                            $options_print.='
                            <label class="custom-switch d-block">
                              <input type="radio" data-attr="'.$value["id"].'"  name="'.$name.'"  value="'.$value2.'" class="custom-switch-input options" data-optional="'.$value["optional"].'" '.$selected_attr.'>
                              <span class="custom-switch-indicator"></span>
                              <span class="custom-switch-description">'.$value2.' <sub class="text-dark text-small">'.$tempu.'</sub></span>
                            </label>';
                          }

                        }              

                        echo '
                          <div class="card no_shadow mb-2">
                            <div class="card-header p-1 border-0">
                              <h6 style="font-size:15px" class="text-primary">'.$value["attribute_name"].$star.'</h6>
                            </div>
                            <div class="card-body p-1">
                             '.$options_print.'   
                            </div>
                          </div>'; 
                      }
                    }
                    ?>
               <?php 
               } ?>
              </div>
            </div>
            <?php endif; ?>
            <?php if(!empty($product_data['product_description'])): ?>
            <div class="tab-pane fade p-2 pb-0 <?php echo (!$have_attributes) ? 'active show' : '';?>" id="description" role="tabpanel" aria-labelledby="description-tab2">
              <div><?php echo $product_data['product_description']; ?></div>
              <?php if(!empty($product_data['product_video_id'])) : ?>
              <div class="mt-3">
                <iframe width="100%" height="350" src="https://www.youtube.com/embed/<?php echo $product_data['product_video_id']; ?>" frameborder="0"></iframe> 
              </div>     
              <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($this->ecommerce_review_comment_exist): ?>
            <div class="tab-pane fade p-3 pb-0 <?php echo (!$have_attributes && empty($product_data['product_description'])) ? 'active show' : '';?>" id="reviews" role="tabpanel" aria-labelledby="reviews-tab2">
              <?php 
              if(empty($review_list_data)) 
              echo '
                <div class="card no_shadow m-0" id="nodata" style="">
                <div class="card-body pb-2">
                  <div class="empty-state p-0">
                    <h6 class="mt-0">'.$this->lang->line("We could not find any review.").'</h6>
                  </div>
                </div>
              </div>';
              else
              {
                foreach ($review_list_data as $key => $value) 
                {
                    
                    $profile_pic = ($value['profile_pic']!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".$value["profile_pic"]."'>" :  "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
                    $image_path=($value["image_path"]!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url($value["image_path"])."'>" : $profile_pic;
                    $review_url = base_url("ecommerce/review/".$value["id"]);
                    $review_url = mec_add_get_param($review_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
                    $reply_button = $hide_button = $reply_block = $review_reply_show = '';
                    if($this->user_id !='')
                    {
                      if($value['review_reply']=='') $reply_button = ' <a class="collpase_link d-inline float-right" data-toggle="collapse" href="#collapsereview'.$value["id"].'" role="button" aria-expanded="false" aria-controls="collapsereview'.$value["id"].'"><i class="fas fa-comment"></i> '.$this->lang->line("Reply").'</a>';
                      $hide_button = '<a data-id="'.$value["id"].'" class="d-inline float-right pr-3 hide-review text-muted" href="#"><i class="fas fa-eye-slash"></i> '.$this->lang->line("Hide").'</a>';
                      if($value['review_reply']=='') $reply_block ='
                      <div class="input-group collapse pt-2" id="collapsereview'.$value["id"].'">
                        <textarea class="form-control review_reply" name="review_reply" style="height:50px !important;"></textarea>
                        <button class="btn btn-primary btn-lg leave_review_comment no_radius" parent-id="'.$value['id'].'"><i class="fas -reply"></i> '.$this->lang->line("Reply").'</button>              
                      </div>';
                    }
                    $review_reply_text =  preg_replace("/(https?:\/\/[a-zA-Z0-9\-._~\:\/\?#\[\]@!$&'\(\)*+,;=]+)/", '<a target="_BLANK" href="$1">$1</a>', $value["review_reply"]); // find and replace links with ancor tag
                    if($value['review_reply']!='')
                    {
                      $store_favicon_src = isset($social_analytics_codes['store_favicon']) ? base_url("upload/ecommerce/".$social_analytics_codes['store_favicon']) : base_url('assets/img/avatar/avatar-1.png');
                      $storeName = isset($social_analytics_codes['store_name']) ? $social_analytics_codes['store_name'] : $this->lang->line("Admin");
                      $review_reply_show = '
                      <div class="media mt-3 w-100">
                            <img class="rounded-circle mr-3" style="height:50px;width:50px;" src="'.$store_favicon_src.'">
                            <div class="media-body">
                              <h6 class="mt-1 mb-0">'.$storeName.' <i class="fas fa-user-circle text-primary"></i></h6>
                              <p style="font-size:11px;" class="m-0 text-muted d-inline">'.date("d M,y H:i",strtotime($value['replied_at'])).'                    
                              <p class="mb-0">'.nl2br($review_reply_text).'</p>
                            </div>
                        </div>';
                    }
                    $review_star_single = mec_display_rating_starts($value['rating'],'text-medium');
                    $review_text =  preg_replace("/(https?:\/\/[a-zA-Z0-9\-._~\:\/\?#\[\]@!$&'\(\)*+,;=]+)/", '<a target="_BLANK" href="$1">$1</a>', $value["review"]); // find and replace links with ancor tag
                    echo '
                    <div class="media mb-4 mt-2 w-100 p-2" id="review-'.$value["id"].'">
                      '.$image_path.'
                      <div class="media-body">
                        <h6 class="mt-1 mb-0 w-100">'.$value["first_name"].' '.$value["last_name"].'<span class="pl-2 text-medium">'.$review_star_single.' '.number_format($value['rating'],1).'</span></h6>
                        <p style="font-size:11px;" class="m-0 d-inline"><a target="_BLANK" href="'.$review_url.'">'.date("d M,y H:i",strtotime($value['inserted_at'])).'</a></p>
                        '.$reply_button.$hide_button.'
                        <p class="mb-0 mt-2 text-justify"><b>'.$value["reason"].'</b> : '.nl2br($review_text).'</p>                  
                        '.$reply_block.$review_reply_show.'
                      </div>                        
                    </div>';
                }
                
              }
            ?> 
            </div>
            <?php endif; ?>
            <?php if(!empty($product_data['purchase_note'])): ?>
            <div class="tab-pane fade p-2 pb-0 <?php echo (!$have_attributes && empty($product_data['product_description']) && !$this->ecommerce_review_comment_exist) ? 'active show' : '';?>" id="purchase_note" role="tabpanel" aria-labelledby="purchase_note-tab2">
              <?php echo $product_data['purchase_note']; ?>
            </div>
            <?php endif; ?>
          </div>

          <?php if($hide_buy_now=='0'): ?>
          <a href="" id="single_buy_now" class="btn btn-outline-primary add_to_cart buy_now btn-lg btn-block no_radius <?php echo ($product_data['attribute_ids']=='')?'':'d-none'; ?>" data-attributes="<?php echo $product_data['attribute_ids'];?>" data-product-id="<?php echo $product_data['id'];?>" data-action='add'><i class="fas fa-credit-card"></i> <?php echo $this->lang->line($buy_button_title); ?></a>
          <?php endif; ?>
        
          <ul class="list-group">        
            <?php if($this->ecommerce_review_comment_exist && $subscriber_id!="" && $this->user_id =='' && !empty($has_purchase_array)) : ?>
            <li class="list-group-item d-flex justify-content-between align-items-center no_radius">
              <a id="rate_modal_button" href="" class="w-100" data-id="<?php echo isset($xreview[0]['id']) ? $xreview[0]['id'] : 0;?>">
                <?php echo $this->lang->line("Rate this item"); ?>
                <?php if(isset($xreview[0]['rating'])) 
                {
                  $review_star_given = mec_display_rating_starts($xreview[0]['rating']);
                  echo '<span class="float-right">'.$review_star_given.'</span>';
                }?>
                
              </a>
            </li>
            <?php endif; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center no_radius">
              <?php echo $this->lang->line("Category"); ?>
              <span class="badge badge-primary badge-pill">
                <?php echo isset($category_list[$product_data['category_id']]) ? $category_list[$product_data['category_id']] : $this->lang->line("Uncategorised");?>
              </span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center no_radius">
              <?php echo $this->lang->line("Sales"); ?>
              <span class="badge badge-primary badge-pill"><?php echo $product_data['sales_count'];?></span>
            </li>
            <?php if($product_data['stock_display']=='1') : ?>
            <li class="list-group-item d-flex justify-content-between align-items-center no_radius">
              <?php echo $this->lang->line("Stock"); ?>
              <span class="badge badge-primary badge-pill"><?php echo $product_data['stock_item'];?></span>
            </li>
            <?php endif; ?>
          </ul>
          
        </div>
        

        <?php if($hide_add_to_cart=='0'): ?>
        <article class="article article-style-c m-0 mt-2" id="cart_actions">    
          <div class="article-details p-0">
            <div class="form-group m-0">
              <div class="input-group">
                <div class="input-group-append">
                  <button class="btn btn-dark add_to_cart" data-product-id="<?php echo $product_data['id'];?>" data-attributes="<?php echo $product_data['attribute_ids'];?>" data-action="remove" type="button" style="min-width: 120px;" data-toggle="tooltip" title="<?php echo $this->lang->line('Remove 1 from Cart'); ?>"><i class="fas fa-minus-circle"></i> <?php echo $this->lang->line('Remove'); ?> <?php echo $this->lang->line('(-1)');?></button>
                </div>
                <input type="text" class="form-control text-center bg-white" data-toggle="tooltip" title="<?php echo $this->lang->line('Currently added to cart');?>" id="item_count" readonly value="<?php echo $quantity_in_cart;?>">
                <div class="input-group-append">
                  <button style="min-width: 120px;" class="btn btn-primary add_to_cart no_radius" data-product-id="<?php echo $product_data['id'];?>"  data-attributes="<?php echo $product_data['attribute_ids'];?>" data-action="add" type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('Add 1 to Cart');?>"><i class="fas fa-cart-plus"></i> <?php echo $this->lang->line('Add');?> <?php echo $this->lang->line('(+1)');?></button>
                </div>
              </div>
            </div>  
          </div>
        </article>
      <?php endif; ?>

      </div>
              
    </div>
  </div>

  <?php if($this->is_ecommerce_related_product_exist) : ?>

    <?php if(!empty($upsell_product_lists)) : ?>
      <?php 
        $upsell_imgSrcs = ($upsell_product_lists[0]['thumbnail']!='') ? base_url('upload/ecommerce/'.$upsell_product_lists[0]['thumbnail']) : base_url('assets/img/products/product-1.jpg');
         if(isset($upsell_product_lists[0]["woocommerce_product_id"]) && !is_null($upsell_product_lists[0]["woocommerce_product_id"]) && $upsell_product_lists[0]['thumbnail']!='')
        $upsell_imgSrcs = $upsell_product_lists[0]['thumbnail'];

        $upsell_product_url = base_url("ecommerce/product/".$upsell_product_lists[0]['id']);
        $upsell_product_url = mec_add_get_param($upsell_product_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

        $downsell_product_url = base_url("ecommerce/product/".$downsell_product_id);
        $downsell_product_url = mec_add_get_param($downsell_product_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

        $display_upsell_product_lists_product_price = mec_display_price($upsell_product_lists[0]['original_price'],$upsell_product_lists[0]['sell_price'],$currency_icon,'1',$currency_position,$decimal_point,$thousand_comma);
        $display_upsell_product_lists_product_discount = mec_display_price($upsell_product_lists[0]['original_price'],$upsell_product_lists[0]['sell_price'],$currency_icon,'4',$currency_position,$decimal_point,$thousand_comma);

      ?>
      <div class="row section" id="upsell_product" style="display: none;">
        <div class="col-12 p-0">
          <div class="section-title mt-3 mb-3"><?php echo $this->lang->line("You May Also Like") ?></div>
        </div>
        <div class="col-12 col-md-3 p-0">
          <article class="article article-style-c mb-3 mt-1" style="width:250px;">
            <div class="article-header">
              <a href="<?php echo $upsell_product_url; ?>">
                <div class="article-image" data-background="<?php echo $upsell_imgSrcs;?> " style="background-image: url('<?php echo $upsell_imgSrcs;?>');"></div>
              </a>
              <?php echo $display_upsell_product_lists_product_discount; ?>

            </div>
            <div class="article-details pt-0 pb-0 pl-1 pr-1">
              <div class="article-category mt-1 mb-0"><?php echo $display_upsell_product_lists_product_price; ?></div>
              <div class="article-title mb-2">
                <a href="<?php echo $upsell_product_url; ?>" class="text-dark text-small"><?php echo $upsell_product_lists[0]['product_name']; ?></a>
              </div>

              <p class="d-none"><?php echo strip_tags($upsell_product_lists[0]['product_description']); ?></p>
              <p class="d-none"><?php echo isset($category_list[$upsell_product_lists[0]['category_id']]) ? $category_list[$upsell_product_lists[0]['category_id']] : ''; ?></p>

              <div>
                <?php if($downsell_product_id != 0): ?>
                  <a href="<?php echo $downsell_product_url; ?>" class="btn btn-outline-primary" data-toggle="tooltip" data-title="<?php echo $this->lang->line("Cancel"); ?>"><i class="far fa-times-circle"></i></a>
                  <?php else : ?>
                  <a href="#" class="btn btn-outline-secondary disabled" data-toggle="tooltip" data-title="<?php echo $this->lang->line("Cancel"); ?>"><i class="far fa-times-circle"></i></a>
                <?php endif; ?>
                <a href="<?php echo $upsell_product_url; ?>" class="btn btn-primary float-right" data-toggle="tooltip" data-title="<?php echo $this->lang->line("Continue"); ?>"><i class="far fa-arrow-alt-circle-right"></i></a>  
              </div>
              &nbsp;   
            </div>
          </article>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="row mt-2 bg-white">
    <div class="col-12 always_padded">     

      <?php 
      if($this->ecommerce_review_comment_exist):      
        $js_store_id = isset($social_analytics_codes['store_id']) ? $social_analytics_codes['store_id'] : $social_analytics_codes['id'];
        $js_user_id = isset($social_analytics_codes['user_id']) ? $social_analytics_codes['user_id'] : $social_analytics_codes['user_id'];  
        $subscriberId=$this->session->userdata($js_store_id."ecom_session_subscriber_id");
        if($subscriberId=="")  $subscriberId = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
        if($subscriberId=='') $subscriberId = $this->uri->segment(4);
        ?>
        <div class="card mb-0 no_shadow">
          <div class="card-header p-0 pt-3 pb-3">
            <h4><i class="fas fa-paper-plane"></i>  <?php echo $this->lang->line("Leave a comment");?></h4>
          </div>
          <div class="card-body p-0">
            <textarea id="new_comment" class="form-control comment_reply" placeholder="<?php echo $this->lang->line('Write comment here');?>"></textarea>
            <button class="btn btn-primary btn-lg leave_comment mt-2" parent-id='' id="leave_comment"><i class="fas fa-comment"></i> <?php echo $this->lang->line("Comment"); ?></button>
          </div>
        </div>
        <div class="card mt-2 mb-2 no_shadow" id="comment_section">
          <div class="card-header p-0 pt-3 pb-3">
            <h4><i class="fas fa-comment"></i> <?php echo $this->lang->line("Comments");?></h4>
          </div>
          <div class="card-body p-0">

            <div id="load_data"></div>

            <div class="text-center" id="waiting" style="width: 100%;margin: 30px 0;">
              <i class="fas fa-spinner fa-spin blue" style="font-size:60px;"></i>
            </div>  

            <div class="card no_shadow m-0" id="nodata" style="display: none">
              <div class="card-body">
                <div class="empty-state p-0">
                  <h6 class="mt-0"><?php echo $this->lang->line("We could not find any comment.") ?></h6>
                </div>
              </div>
            </div>            

            <button class="btn btn-outline-primary m-3 mb-4" style="display: none;" id="load_more" data-limit="10" data-start="0"><i class="fas fa-book-reader"></i> <?php echo $this->lang->line("Load More"); ?></button>

          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if($this->is_ecommerce_related_product_exist) : ?>
    <?php if(!empty($related_product_lists)) : ?>
      <div class="row mt-2 section">
        <div class="col-12 p-0">
          <div class="section-title mt-3 mb-1"><?php echo $this->lang->line("Related Items") ?></div>
        </div>
        <div class="col-12 p-0 mb-3">
          <div class="owl-carousel owl-theme" id="featured-products-carousel">
            <?php foreach($related_product_lists as $featured) : ?>
              <?php 
                  $imgSrcs = ($featured['thumbnail']!='') ? base_url('upload/ecommerce/'.$featured['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                   if(isset($featured["woocommerce_product_id"]) && !is_null($featured["woocommerce_product_id"]) && $featured['thumbnail']!='')
                  $imgSrcs = $featured['thumbnail'];

                  $product_url = base_url("ecommerce/product/".$featured['id']);
                  $product_url = mec_add_get_param($product_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

                  $display_featured_product_price = mec_display_price($featured['original_price'],$featured['sell_price'],$currency_icon,'1',$currency_position,$decimal_point,$thousand_comma);
                  $display_featured_product_discount = mec_display_price($featured['original_price'],$featured['sell_price'],$currency_icon,'4',$currency_position,$decimal_point,$thousand_comma);
              ?>
              <article class="article article-style-c mb-1 mt-1">
                <div class="article-header">
                  <a href="<?php echo $product_url; ?>">
                    <div class="article-image" data-background="<?php echo $imgSrcs;?> " style="background-image: url('<?php echo $imgSrcs;?>');"></div>
                  </a>
                  <?php echo $display_featured_product_discount; ?>

                </div>
                <div class="article-details pt-0 pb-0 pl-1 pr-1">
                  <div class="article-category mt-1 mb-0"><?php echo $display_featured_product_price; ?></div>
                  <div class="article-title mb-0">
                    <a href="<?php echo $product_url; ?>" class="text-dark text-small"><?php echo $featured['product_name'] ?></a>
                  </div>
                  <p class="d-none"><?php echo strip_tags($featured['product_description']); ?></p>
                  <p class="d-none"><?php echo isset($category_list[$featured['category_id']]) ? $category_list[$featured['category_id']] : ''; ?></p>
                  &nbsp;   
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
   
<div class="sticky-height"></div>
    
              

<?php include(APPPATH."views/ecommerce/cart_js.php"); ?>
<?php include(APPPATH."views/ecommerce/cart_style.php"); ?>
<?php include(APPPATH."views/ecommerce/common_style.php"); ?>


<script>
  var counter=0;
  var current_product_id = "<?php echo $current_product_id; ?>";
  var current_store_id = "<?php echo $current_store_id; ?>";
  var currency_icon = "<?php echo $currency_icon; ?>";
  var currency_position = "<?php echo $currency_position; ?>";
  var decimal_point = "<?php echo $decimal_point; ?>";
  var thousand_comma = "<?php echo $thousand_comma; ?>";
  var store_favicon = "<?php echo isset($social_analytics_codes['store_favicon'])?$social_analytics_codes['store_favicon']:'';?>";
  var store_name = "<?php echo isset($social_analytics_codes['store_name'])?$social_analytics_codes['store_name']:'';?>";
  var product_name = "<?php echo isset($product_data['product_name'])?$product_data['product_name']:'';?>";
  var ecommerce_review_comment_exist = '<?php echo $this->ecommerce_review_comment_exist;?>';
  var ecommerce_related_product_exist = '<?php echo $this->is_ecommerce_related_product_exist;?>';
 

  $(document).ready(function() {
    if(ecommerce_review_comment_exist=='1')
    {
      setTimeout(function() {          
        var start = $("#load_more").attr("data-start");   
        load_data(start,false,false,"");
      }, 1000);

      $(document).on('click', '#load_more', function(e) {
        var start = $("#load_more").attr("data-start");
        load_data(start,false,true,"");
      });

      $(document).on('click', '#rate_modal_button', function(e) {
        e.preventDefault();
        $("#ReviewModal").modal();
      });
    }

    if(ecommerce_related_product_exist) {
      $("#featured-products-carousel").owlCarousel({
        items: 3,
        margin: 10,
        autoplay: true,
        autoplayTimeout: 4000,
        loop: true,
        responsive: {
          0: {
            items: 4
          },
          768: {
            items: 5
          },
          1200: {
            items: 6
          }
        }
      });
    }
  });
</script>

<?php include(APPPATH."views/ecommerce/attribute_value.php"); ?>
<?php if($this->ecommerce_review_comment_exist) include(APPPATH."views/ecommerce/comment_js.php"); ?>

<style type="text/css">
  .article-title a{font-size: 10px !important;font-weight: 500 !important;}
  .article-category{font-size: 9px;}
  .article .article-details,.article .article-details a{line-height: 12px !important;}
  .article.article-style-c .article-header {height: 160px !important; } 
  #upsell_product .article { width: 250px !important }
  .custom-control.custom-checkbox{margin-bottom:10px;}
  .custom-control-label{line-height: 2rem;padding-left: 20px}
  .custom-control-label::before,.custom-control-label::after{height: 1.5rem;width: 1.5rem;}
  .custom-switch{margin-bottom: 10px;}
  .media-body h6{font-weight: 700;font-size: 17px;}
  @media (max-width: 978px) {
    .sticky-height{height: 35px !important;}
    #cart_actions{position: fixed;border-radius: 0;z-index: 99;bottom:78px;left:0;width: 100%;background:#fff;}
    .col-12:not(.always_padded) {
      padding:0;
    }   
   .remove-margin{margin:0 !important;}    
  }
  @media (min-width: 768px) { 
    .margin_md{margin-top:10px;}
  }
  .hero p{font-size: 14px;line-height: 25px;}
  .text-medium{font-size: 12px !important;}
</style>

<?php if($this->ecommerce_review_comment_exist) : ?>
<div class="modal fade" id="ReviewModal" tabindex="-1" role="dialog" aria-labelledby="ReviewModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ReviewModalLabel"><i class="fas fa-star"></i> <?php echo $this->lang->line("Rate Item"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body text-justify" id="ReviewModalBody">
              <input type="hidden" id="insert_id" name="insert_id" value="<?php echo isset($xreview[0]['id'])?$xreview[0]['id']:""; ?>">
              <div class="form-group">
                <label for=""><?php echo $this->lang->line("Reason");?>*</label>
                <div class="input-group">
                    <input type="text" value="<?php echo isset($xreview[0]['reason'])?$xreview[0]['reason']:""; ?>" class="form-control" placeholder="<?php echo $this->lang->line("Example : Quick Delivery"); ?>" id="reason" name="reason">
                </div>
                <div class="invalid-feedback"></div>
              </div>


              <div class="form-group">
                <label for=""><?php echo $this->lang->line("Rating");?>*</label>
                <div class="selectgroup selectgroup-pills">
                  <?php
                  $select_rating = isset($xreview[0]['rating']) ? $xreview[0]['rating'] : 5;
                  for($i=1;$i<=5;$i++)
                  {
                    $checked=($select_rating==$i) ? 'checked'  : '';
                    echo '
                      <label class="selectgroup-item">
                        <input type="radio" name="rating" value="'.$i.'" class="selectgroup-input" '.$checked.'>
                        <span class="selectgroup-button border">'.$i.' <i class="fas fa-star orange"></i> </span>
                      </label>';
                  }?>                  
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <label for=""><?php echo $this->lang->line("Review");?></label>
                <div class="input-group">
                    <textarea class="form-control" style="height: 200px !important" placeholder="<?php echo $this->lang->line("Write a few words"); ?>" id="review" name="review"><?php echo isset($xreview[0]['review'])?$xreview[0]['review']:""; ?></textarea>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <label for=""><?php echo $this->lang->line("Related Order");?> *</label>
                <div class="input-group">
                    <?php 
                    $related_order = array();
                    $default_related_order = isset($xreview[0]['cart_id'])?$xreview[0]['cart_id']:"";
                    foreach ($has_purchase_array as $key => $value) {
                      $related_order[$value["cart_id"]] = $this->lang->line("Order")." #".$value["cart_id"];
                    }
                    echo form_dropdown('cart_id', $related_order, $default_related_order,'class="form-control" id="cart_id"');
                    ?>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group mt-2">
                <a href="" id="rate_now" class="btn btn-primary btn-lg btn-block" tabindex="4">
                  <?php echo $this->lang->line("Submit Review");?>
                </a>
              </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
