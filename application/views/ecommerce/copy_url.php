<?php
$store_code = array(0=>array("title"=>$this->lang->line("Store Page"),"url"=>base_url("ecommerce/store/".$current_store_data['store_unique_id'])));                         $category_copy = array();
$order_code = array(0=>array("title"=>$this->lang->line("Buyer's Orders Page"),"url"=>base_url("ecommerce/my_orders/".$current_store_data['id'])));
$product_copy = array();
foreach ($category_list as $key => $value)
{
   $store_code[] = array("title"=>$this->lang->line("Store Page")." - ".$this->lang->line("Category")." : ".$value,"url"=>base_url("ecommerce/store/".$current_store_data['store_unique_id']."?category=".$key));
}
$product_list_assoc = array();                         
foreach ($product_list as $key => $value) 
{
  $product_copy[] = array("title"=>$this->lang->line("Product Page")." : ".$value["product_name"],"url"=>base_url("ecommerce/product/".$value['id']));  
}
?>


<section class="section">
  <div class="section-body">
    <div class="modal-body">
      <ul class="nav nav-tabs" id="myTab2" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home" aria-selected="true"><?php echo $this->lang->line("Store URL"); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#profile2" role="tab" aria-controls="profile" aria-selected="false"><?php echo $this->lang->line("Order URL"); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="contact-tab2" data-toggle="tab" href="#contact2" role="tab" aria-controls="contact" aria-selected="false"><?php echo $this->lang->line("Product URL"); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="legal-tab2" data-toggle="tab" href="#legal2" role="tab" aria-controls="legal" aria-selected="false"><?php echo $this->lang->line("Legal URL"); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="embed-tab2" data-toggle="tab" href="#embed2" role="tab" aria-controls="embed" aria-selected="false"><?php echo $this->lang->line("Widget"); ?></a>
        </li>
      </ul>
      <div class="tab-content tab-bordered" id="myTab3Content">

        <div class="tab-pane fade show active bg-body" id="home2" role="tabpanel" aria-labelledby="home-tab2">
          <?php 
           foreach ($store_code as $key => $value)
           { ?>
             <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-circle"></i> 
                  <a href="<?php echo $value["url"];?>" target="_BLANK"><?php echo $value['title'];?></a>
                </h4>
              </div>
              <div class="card-body">
                <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $value["url"];?></span></code></pre>
              </div>
            </div>
           <?php
          } 
          ?>
        </div>
        <div class="tab-pane fade bg-body" id="profile2" role="tabpanel" aria-labelledby="profile-tab2">
          <?php 
           foreach ($order_code as $key => $value)
           { ?>
             <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-circle"></i> 
                 <a href="<?php echo $value["url"];?>" target="_BLANK"><?php echo $value['title'];?></a>
                </h4>
              </div>
              <div class="card-body">
                <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $value["url"];?></span></code></pre>
              </div>
            </div>
           <?php
           } 
          ?>
        </div>
        <div class="tab-pane fade bg-body" id="contact2" role="tabpanel" aria-labelledby="contact-tab2">
         <?php 
         foreach ($product_copy as $key => $value)
         { ?>
           <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-circle"></i> 
                <a href="<?php echo $value["url"];?>" target="_BLANK"><?php echo $value['title'];?></a>
              </h4>
            </div>
            <div class="card-body">
              <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $value["url"];?></span></code></pre>
            </div>
          </div>
         <?php
         } 
         ?>
        </div>
        <div class="tab-pane fade bg-body" id="legal2" role="tabpanel" aria-labelledby="legal-tab2">         
           <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-circle"></i>
                <?php $legal_url1 =  base_url("ecommerce/terms_of_service/".$current_store_data['store_unique_id']);?>
                <a href="<?php echo $legal_url1;?>" target="_BLANK"><?php echo $this->lang->line("Terms of service");?></a>
              </h4>
            </div>
            <div class="card-body">
              <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $legal_url1;?></span></code></pre>
            </div>
          </div>
          <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-circle"></i>
                <?php $legal_url2 =  base_url("ecommerce/refund_policy/".$current_store_data['store_unique_id']);?>
                <a href="<?php echo $legal_url2;?>" target="_BLANK"><?php echo $this->lang->line("Refund policy");?></a>
              </h4>
            </div>
            <div class="card-body">
              <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $legal_url2;?></span></code></pre>
            </div>
          </div>
        </div>
        <div class="tab-pane fade bg-body" id="embed2" role="tabpanel" aria-labelledby="embed-tab2">         
           <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-circle"></i>
                <?php $embed_url =  isset($store_code[0]['url'])?$store_code[0]['url']:'';?>
                <a href="<?php echo $embed_url;?>" target="_BLANK"><?php echo $this->lang->line("Embed Code");?></a>
              </h4>
            </div>
            <div class="card-body">
              <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo htmlspecialchars('<iframe width="100%" height="800" src="'.$embed_url.'" frameborder="0"  gesture="media" allow="encrypted-media" allowfullscreen></iframe>');?></span></code></pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>