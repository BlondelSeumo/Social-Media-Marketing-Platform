  <?php
  $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
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
  var comment_id = "<?php echo $comment_id;?>";

  $(document).ready(function() {

    setTimeout(function() {          
      var start = $("#load_more").attr("data-start");   
      load_data(start,false,false,comment_id);
    }, 1000);

  });
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