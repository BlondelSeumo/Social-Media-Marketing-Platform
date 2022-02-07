  <?php
  $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
  $product_data = $review_data[0];
  $subscriber_id=$this->session->userdata($product_data['store_id']."ecom_session_subscriber_id");
  if($subscriber_id=="")  $subscriber_id = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";

  $product_link = base_url("ecommerce/product/".$product_data['id']); 
  $product_link = mec_add_get_param($product_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

  $current_cart_id = isset($current_cart['cart_id']) ? $current_cart['cart_id'] : 0;
  $cart_count = isset($current_cart['cart_count']) ? $current_cart['cart_count'] : 0;
  $current_cart_url = base_url("ecommerce/cart/".$current_cart_id); 
  $current_cart_url = mec_add_get_param($current_cart_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

  $current_cart_data = (isset($current_cart["cart_data"]) && is_array($current_cart["cart_data"])) ? $current_cart["cart_data"] : array();
  ?>

  <div class="row mt-2 bg-white h-100">
    <div class="col-12 always_padded">     

      <?php 
      if($this->ecommerce_review_comment_exist):      
        $js_store_id = isset($social_analytics_codes['store_id']) ? $social_analytics_codes['store_id'] : $social_analytics_codes['id'];
        $js_user_id = isset($social_analytics_codes['user_id']) ? $social_analytics_codes['user_id'] : $social_analytics_codes['user_id'];  
        $subscriberId=$this->session->userdata($js_store_id."ecom_session_subscriber_id");
        if($subscriberId=="")  $subscriberId = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
        if($subscriberId=='') $subscriberId = $this->uri->segment(4);
        ?>
        <div class="card mt-2 mb-2 no_shadow" id="comment_section">
          <div class="card-header p-0 pt-3 pb-3">
            <h4><i class="fas fa-star"></i> <?php echo $this->lang->line("Reviews");?></h4>
          </div>
          <div class="card-body p-0">

            <?php 
            foreach ($review_data as $key => $value) 
            {
                $profile_pic = ($value['profile_pic']!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".$value["profile_pic"]."'>" :  "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
                $image_path=($value["image_path"]!="") ? "<img class='rounded-circle mr-3' style='height:50px;width:50px;' src='".base_url($value["image_path"])."'>" : $profile_pic;
                $review_url = base_url("ecommerce/review/".$value["id"]);
                // if($subscriber_id!="") $review_url .= "?subscriber_id=".$subscriber_id;
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
            } ?>

          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
   
<div class="sticky-height"></div>    
              
<?php include(APPPATH."views/ecommerce/common_style.php"); ?>

<script>
  var counter=0;
  var current_product_id = "<?php echo $current_product_id; ?>";
  var current_store_id = "<?php echo $current_store_id; ?>"; 
  var store_favicon = "<?php echo isset($social_analytics_codes['store_favicon'])?$social_analytics_codes['store_favicon']:'';?>";
  var store_name = "<?php echo isset($social_analytics_codes['store_name'])?$social_analytics_codes['store_name']:'';?>";
  var product_name = "<?php echo isset($product_data['product_name'])?$product_data['product_name']:'';?>";
  var comment_id = "<?php echo isset($comment_id) ? $comment_id : 0;?>";
</script>

<?php include(APPPATH."views/ecommerce/comment_js.php"); ?>

<style type="text/css">  .
  @media (max-width: 978px) {
    .sticky-height{height: 35px !important;}
    #cart_actions{position: fixed;border-radius: 0;z-index: 99;bottom:65px;left:0;width: 100%;background:#fff;}
    .col-12:not(.always_padded) {
      padding:0;
    }   
   .remove-margin{margin:0 !important;}    
  }
  @media (min-width: 768px) { 
    .margin_md{margin-top:20px;}
  }
  .media-body h6{font-weight: 700;font-size: 17px;}
</style>