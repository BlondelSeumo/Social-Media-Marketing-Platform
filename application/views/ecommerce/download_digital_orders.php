<?php 
$pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
$store_unique_id =  isset($store_data['store_unique_id']) ? $store_data['store_unique_id'] : "";
$currency = isset($ecommerce_config['currency']) ? $ecommerce_config['currency'] : "USD";
$currency_icon = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";

$form_action = base_url('ecommerce/store/'.$store_data['store_unique_id']);
$subscriber_id=$this->session->userdata($store_id."ecom_session_subscriber_id");
if($subscriber_id=="")$subscriber_id = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
$form_action = mec_add_get_param($form_action,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

$currency_position = isset($ecommerce_config['currency_position']) ? $ecommerce_config['currency_position'] : "left";
$decimal_point = isset($ecommerce_config['decimal_point']) ? $ecommerce_config['decimal_point'] : 0;
$thousand_comma = isset($ecommerce_config['thousand_comma']) ? $ecommerce_config['thousand_comma'] : '0';
?>
<?php $this->load->view('admin/theme/message'); ?>

<style type="text/css">
	.activities .activity .activity-detail:before{ content: ''; }
  @media (max-width: 575.98px) {
    #search_store_id{width: 75px;}
    #search_status{width: 80px;}
    #select2-search_store_id-container,#select2-search_status-container,#search_value{padding-left: 8px;padding-right: 5px;}
  }
</style>

<div class="row pt-3 pl-3 pr-3 pb-0">

	<div class="col-12">
		<div class="card bg-light no-shadow mb-0">
			<div class="card-body p-0">
				<?php foreach ($product_list as $value): ?>
					<?php 
						$payment_amount = $value['currency']." ".mec_number_format($value['unit_price'],$decimal_point,$thousand_comma);
						if($value["action_type"]=="checkout") $invoice =  base_url("ecommerce/order/".$value['cart_id']);
						else $invoice =  base_url("ecommerce/cart/".$value['cart_id']);
						$invoice = mec_add_get_param($invoice,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

						$payment_status = $value['status'];
						if($payment_status=='pending') $payment_status_badge = "<span class='text-danger'>".$this->lang->line("Pending")."</span>";
						else if($payment_status=='approved') $payment_status_badge = "<span class='text-primary'>".$this->lang->line("Approved")."</span>";
						else if($payment_status=='rejected') $payment_status_badge = "<span class='text-danger'>".$this->lang->line("Rejected")."</span>";
						else if($payment_status=='shipped') $payment_status_badge = "<span class='text-info'>".$this->lang->line("Shipped")."</span>";
						else if($payment_status=='delivered') $payment_status_badge = "<span class='text-info'>".$this->lang->line("Delivered")."</span>";
						else if($payment_status=='completed') $payment_status_badge = "<span class='text-success'>".$this->lang->line("Completed")."</span>";

						$payment_status_note = ($value['status_changed_note']!='') ? htmlspecialchars($value['status_changed_note']) : "";
					?>
					<div class="activities">
						<div class="activity">
							<div class="activity-detail w-100 mb-2">
								<div class="mb-2">
									<span class="text-job"><?php echo date("M j,y H:i",strtotime($value['updated_at'])) ?></span>
									<span class="bullet"></span>
									<a class="text-job text-primary" href="<?php echo $invoice; ?>"> #<?php echo $value['cart_id'].' ('.$payment_amount.')' ?></a>
									<div class="float-right dropdown ml-3">
										<a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></a>
										<div class="dropdown-menu">
											<div class="dropdown-title"><?php echo $this->lang->line('Options'); ?></div>
											<a href="<?php echo base_url("ecommerce/download_item/".$value['id']."?subscriber_id=".$value['subscriber_id']); ?>" class="dropdown-item has-icon"><i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line('Download'); ?></a>
										</div>
									</div>
									<span class="float-right text-small"><?php echo $payment_status_badge; ?></span>
								</div>
								<p><?php echo $payment_status_note; ?></p>
							</div>
						</div>
					</div>
				<?php endforeach ?>
			</div>
		</div>

	</div>
  <div class="col-12 p-0">
    <div class="card bg-light no_shadow mb-0">
      <div class="card-body data-card p-0">
<!--         <div class="row">
          <div class="col-6 col-md-4">
            <?php
            $status_list[''] = $this->lang->line("Status");                
            echo 
            '<div class="input-group mb-3" id="searchbox">
              <div class="input-group-prepend d-none">
                <input type="text" value="'.$store_id.'" name="search_store_id" id="search_store_id">
                <input type="text" value="'.$subscriber_id.'" name="search_subscriber_id" id="search_subscriber_id">
                <input type="text" value="'.$pickup.'" name="search_pickup" id="search_pickup">
              </div>
              <div class="input-group-prepend d-none">
                '.form_dropdown('search_status',$status_list,'','class="form-control select2" id="search_status"').'
              </div>
              <input type="text" class="form-control rounded-left" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
              </div>
            </div>'; ?>                                          
          </div>

          <div class="col-6 col-md-8 text-right">

        	<?php
	          echo $drop_menu ='<a href="javascript:;" id="search_date_range" class="btn btn-outline-primary btn-lg  icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="search_date_range_val">';
	        ?>

                                     
          </div>
        </div> -->

        <div class="table-responsive2">
            <input type="hidden" id="put_page_id">
            <table class="table table-bordered" id="mytable">
              <thead class="d-none">
                <tr>
                  <th>#</th>      
                  <th style="vertical-align:middle;width:20px">
                      <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                  </th>
                  <th><?php echo $this->lang->line("Order ID")?></th>
                  <th><?php echo $this->lang->line("Coupon")?></th>                
                  <th><?php echo $this->lang->line("Transaction ID")?></th>               
                  <th><?php echo $this->lang->line("My Data")?></th>               
              	</tr>
              </thead>
            </table>
        </div>
      </div>
    </div>
  </div>       
    
</div>

<?php include(APPPATH."views/ecommerce/common_style.php"); ?>