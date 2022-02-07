<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<?php 
$user_id_url = $this->uri->segment(3);
if(empty($user_id_url)) $user_id_url = 0;

$month_name_array = array(
	'01' => 'January',
	'02' => 'February',
	'03' => 'March',
	'04' => 'April',
	'05' => 'May',
	'06' => 'June',
	'07' => 'July',
	'08' => 'August',
	'09' => 'September',
	'10' => 'October',
	'11' => 'November',
	'12' => 'December'
);
?>


<section class="section">
    <?php 
    if($other_dashboard=='1')
    { ?>
      <div class="section-header">
        <h1><i class="fas fa-fire"></i> <?php echo $this->lang->line('Dashboard for')." ".$user_name." [".$user_email."]"; ?> </h1>
      </div>
    <?php 
    }
   ?>

  <?php if($other_dashboard == 1) : ?>
  <div class="section-body">
  <?php endif; ?>

    <div class="row statistics-box justify-content-md-center">
      <div class="col-lg-4 col-md-6 col-sm-12 pr-md-1">
        <div class="card card-statistic-2 border">
          <div class="card-chart">
            <canvas id="subscribers_chart_1" height="72"></canvas>
          </div>
          <div class="card-stats mb-1">
            <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
            <div class="card-stats-items month_change_middle_content">
              <div class="card-stats-item">
                <div class="card-stats-item-count text-primary gradient" id="fbsub"><?php echo custom_number_format($fbsub); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Facebook'); ?></div>
              </div>
              <div class="card-stats-item">
                <div class="card-stats-item-count text-secondary gradient" id="igsub"><?php echo custom_number_format($igsub); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Instagram'); ?></div>
              </div>
              <div class="card-stats-item">
                <div class="card-stats-item-count text-info gradient" id="esub"><?php echo custom_number_format($esub); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Ecommerce'); ?></div>
              </div>
            </div>
          </div>

          <div class="card-icon shadow-primary bg-primary gradient">
            <i class="fas fa-user-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4 class="text-dark dropleft">
                <?php echo $this->lang->line('Total Subscribers'); ?>
                <a class="dropdown-toggle text-muted float-right text-small"  data-toggle="dropdown" href="#" id="orders-month"><?php echo $month_name_array[$month_number]; ?></a>
                <ul class="dropdown-menu dropdown-menu-sm">
                  <!-- <li class="dropdown-title"><?php echo $this->lang->line('Select Month'); ?></li> -->
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '01') echo 'active'; ?>" month_no="01"><?php echo $this->lang->line('January');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '02') echo 'active'; ?>" month_no="02"><?php echo $this->lang->line('February');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '03') echo 'active'; ?>" month_no="03"><?php echo $this->lang->line('March');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '04') echo 'active'; ?>" month_no="04"><?php echo $this->lang->line('April');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '05') echo 'active'; ?>" month_no="05"><?php echo $this->lang->line('May');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '06') echo 'active'; ?>" month_no="06"><?php echo $this->lang->line('June');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '07') echo 'active'; ?>" month_no="07"><?php echo $this->lang->line('July');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '08') echo 'active'; ?>" month_no="08"><?php echo $this->lang->line('August');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '09') echo 'active'; ?>" month_no="09"><?php echo $this->lang->line('September');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '10') echo 'active'; ?>" month_no="10"><?php echo $this->lang->line('October');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '11') echo 'active'; ?>" month_no="11"><?php echo $this->lang->line('November');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == '12') echo 'active'; ?>" month_no="12"><?php echo $this->lang->line('December');?></a></li>
                  <li><a href="#" class="dropdown-item month_change <?php if($month_number == 'year') echo 'active'; ?>" month_no="year"><?php echo $this->lang->line('This Year');?></a></li>
                </ul>
                
              </h4>
            </div>
            <div class="card-body text-primary gradient" id="total_subscribers"><?php echo custom_number_format($total_sub); ?></div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-12 pl-md-1 pr-md-1">
        <div class="card card-statistic-2 border">
          <div class="card-stats">
            <div class="statistic-details mt-0">
              <div class="statistic-details-item" title="<?php echo $this->lang->line('Comment Reply Campaign Enabled'); ?>" data-toggle="tooltip">
                <div class="detail-value"><?php echo custom_number_format($number_of_auto_comment_reply_campaign);?></div>
                <div class="detail-name"><?php echo $this->lang->line('Comment Reply Campaign Enabled'); ?></div>
              </div>
              <div class="statistic-details-item" title="<?php echo $this->lang->line('FB - Comment Replied (Last 24 Hours)'); ?>" data-toggle="tooltip">
                <div class="detail-value"><?php echo custom_number_format($number_of_auto_comment_report_fb);?></div>
                <div class="detail-name"><?php echo $this->lang->line('FB - Comment Replied (Last 24 Hours)'); ?></div>
              </div>
              <div class="statistic-details-item" title="<?php echo $this->lang->line('IG - Comment Replied (Last 24 Hours)'); ?>" data-toggle="tooltip">
                <div class="detail-value"><?php echo custom_number_format($number_of_auto_comment_report_ig);?></div>
                <div class="detail-name"><?php echo $this->lang->line('IG - Comment Replied (Last 24 Hours)'); ?></div>
              </div>
            </div>

            <ul class="list-unstyled list-unstyled-border mb-0">
              <li class="media">
                <div class="media-body">                  
                  <div class="mt-1">
                    <div class="budget-price">
                      <div class="budget-price-square bg-primary" data-width="100%" style="width: 100%;"></div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
            

            <div class="card-stats-items mb-1">
              <div class="card-stats-item">
                <div class="card-stats-item-count text-primary gradient" id=""><?php echo custom_number_format($total_pages); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Page'); ?></div>
              </div>
              <div class="card-stats-item">
                <div class="card-stats-item-count text-secondary gradient" id=""><?php echo custom_number_format($total_ig_account); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Instagram'); ?></div>
              </div>
              <div class="card-stats-item">
                <div class="card-stats-item-count text-info gradient" id=""><?php echo custom_number_format($number_of_bot_flow); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Flow'); ?></div>
              </div>
            </div>
          </div>
          <div class="card-icon bg-secondary gradient">
            <i class="fab fa-facebook-messenger"></i>
          </div>
          <div class="card-wrap">
            
            <div class="card-header">
              <h4 class="text-dark"><?php echo $this->lang->line('Bot Enabled'); ?></h4>
            </div>
            <div class="card-body text-secondary gradient" id=""><?php echo custom_number_format($number_of_bots); ?></div>
          </div>
        </div>

      </div>

      <div class="col-lg-4 col-md-6 col-sm-12 pl-md-1">
        <div class="card card-statistic-2 border">
          <div class="card-chart">
            <canvas id="ecommerce_chart_1" height="72"></canvas>
          </div>
          <div class="card-stats mb-1">
            <div class="text-center waiting hidden" id="e_loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
            <div class="card-stats-items ecommerce_month_change_middle_content">
              <div class="card-stats-item">
                <div class="card-stats-item-count text-info gradient" id="order_block"><?php echo custom_number_format($total_orders); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Orders'); ?></div>
              </div>
              <div class="card-stats-item">
                <div class="card-stats-item-count text-danger gradient" id="recovered_block"><?php echo custom_number_format($summary_recovered_cart); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Pending'); ?></div>
              </div>
              <div class="card-stats-item">
                <div class="card-stats-item-count text-success gradient" id="checkout_block"><?php echo custom_number_format($summary_checkout_cart); ?></div>
                <div class="card-stats-item-label"><?php echo $this->lang->line('Checkout'); ?></div>
              </div>
            </div>
          </div>
          <div class="card-icon bg-warning shadow-warning">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="card-wrap">

            <div class="card-header">
              <h4 class="text-dark dropleft">
                <?php echo $this->lang->line('Total Earnings'); ?>
                <a class="dropdown-toggle text-muted float-right text-small" data-toggle="dropdown" href="#" id="ecommerce-month"><?php echo $month_name_array[$month_number]; ?></a>
                <ul class="dropdown-menu dropdown-menu-sm">
                  <!-- <li class="dropdown-title"><?php echo $this->lang->line('Select Month'); ?></li> -->
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '01') echo 'active'; ?>" month_no="01"><?php echo $this->lang->line('January');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '02') echo 'active'; ?>" month_no="02"><?php echo $this->lang->line('February');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '03') echo 'active'; ?>" month_no="03"><?php echo $this->lang->line('March');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '04') echo 'active'; ?>" month_no="04"><?php echo $this->lang->line('April');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '05') echo 'active'; ?>" month_no="05"><?php echo $this->lang->line('May');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '06') echo 'active'; ?>" month_no="06"><?php echo $this->lang->line('June');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '07') echo 'active'; ?>" month_no="07"><?php echo $this->lang->line('July');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '08') echo 'active'; ?>" month_no="08"><?php echo $this->lang->line('August');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '09') echo 'active'; ?>" month_no="09"><?php echo $this->lang->line('September');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '10') echo 'active'; ?>" month_no="10"><?php echo $this->lang->line('October');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '11') echo 'active'; ?>" month_no="11"><?php echo $this->lang->line('November');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == '12') echo 'active'; ?>" month_no="12"><?php echo $this->lang->line('December');?></a></li>
                  <li><a href="#" class="dropdown-item ecommerce_month_change <?php if($month_number == 'year') echo 'active'; ?>" month_no="year"><?php echo $this->lang->line('This Year');?></a></li>
                </ul>

                <div class="dropdown d-inline">
                  <a class="dropdown-toggle text-muted text-small" href="#" data-toggle="dropdown">
                    <?php if(in_array($selected_curr,$currency_lists)) echo '('.$selected_curr.')'; else echo $this->lang->line('Select'); ?>
                  </a>
                  <div class="dropdown-menu">
                    <?php foreach ($currency_lists as $single_currency) { ?>
                    <a class="dropdown-item currency_item" store_id="<?php echo $single_currency; ?>" href="#"><?php echo $single_currency; ?></a>
                    <?php } ?>
                  </div>
                </div> 
              </h4>
            </div>
            <div class="card-body text-warning gradient" id="total_earning">
              <?php  echo custom_number_format($summary_earning); ?>     
            </div>
          </div>
        </div>
      </div>
    </div>
  


    <div class="row">
      <div class="col-12">
        <div class="card  mb-2">
          <div class="card-header">
            <h4><i class="fas fa-users"></i> <?php echo $this->lang->line("Messenger Subscriber Gain - 12 Months") ?> </h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 col-md-10">
              <canvas id="subscribers_stats" height="120"></canvas>
                
              </div>
              <div class="col-12 col-md-2">
                <div class="statistic-details mt-sm-4 d-block">
                  <div class="statistic-details-item">
                    <div class="detail-value text-danger gradient">
                      <?php 
                        if($subscribers_gain >= 1000) {
                           echo $subscribers_gain/1000 . "k";
                        }
                        else {
                            echo $subscribers_gain;
                        }
                      ?>
                    </div>
                    <div class="detail-name"><?php echo $this->lang->line("Subscriber Gain"); ?></div>
                  </div>
                  <div class="statistic-details-item">
                    <div class="detail-value text-primary gradient">
                      <?php 
                        if($email_gain >= 1000) {
                           echo $email_gain/1000 . "k";
                        }
                        else {
                            echo $email_gain;
                        }
                      ?>
                    </div>
                    <div class="detail-name"><?php echo $this->lang->line("Email Gain"); ?></div>
                  </div>
                  <div class="statistic-details-item">
                    <div class="detail-value text-secondary gradient">
                      <?php 
                        if($phone_gain >= 1000) {
                           echo $phone_gain/1000 . "k";
                        }
                        else {
                            echo $phone_gain;
                        }
                      ?>
                    </div>
                    <div class="detail-name"><?php echo $this->lang->line("Phone Number Gain"); ?></div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

      <?php if(!empty($latest_subscriber_list)) : ?>
      <div class="col-12">
        <div class="owl-carousel owl-theme my-4" id="products-carousel">
          <?php foreach($latest_subscriber_list as $value) : ?>
          <div>
            <div class="product-item pb-3">
              <div class="product-image">
                <img alt="image" src="<?php echo $value['image_path']; ?>" class="img-fluid rounded-circle" style="width:80px; height: 80px;">
              </div>
              <div class="product-details">
                <div class="product-name"><?php if($value['full_name'] != '') echo $value['full_name']; else echo $value['first_name'].' '.$value['last_name']; ?></div>
                <div class="product-review">
                  <a style="cursor: pointer;" href="https://facebook.com/<?php echo $value['page_id']; ?>" target="_BLANK"><?php echo $value['page_name']; ?></a>
                </div>
                <div class="text-muted text-small"><?php echo $value['subscribed_at']; ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    </div>


    <div class="row">
      <div class="col-12 col-md-12">
        <div class="card mb-2">
          <div class="card-header border-bottom-0">
            <h4><i class="fas fa-user-circle"></i> <?php echo $this->lang->line('Subscribers from Different Sources - 12 Months'); ?></h4>
          </div>
          <div class="card-body">
            <div class="row">             

              <div class="col-12 col-md-7">
                <div class="row mt-3">
                  <div class="col-6">
                    <ul class="list-unstyled list-unstyled-border list-unstyled-noborder mb-0">
                      <li class="media"> 
                        <img class="img-fluid mt-1" src="<?php echo base_url('assets/img/icon/checkbox.png'); ?>" alt="image" width="30">
                        <div class="media-body ml-3">
                          <div class="media-title fs-14"><?php echo $refferer_source_info['checkbox_plugin']['title']; ?></div>
                          <div class="text-small text-muted"><?php echo isset($refferer_source_info['checkbox_plugin']['subscribers']) ? number_format($refferer_source_info['checkbox_plugin']['subscribers']) : 0 ?></div>
                        </div>
                      </li>
                      <li class="media">
                        <img class="img-fluid mt-1" src="<?php echo base_url('assets/img/icon/send_to_messenger.png'); ?>" alt="image" width="30">
                        <div class="media-body ml-3">
                          <div class="media-title fs-14"><?php echo $refferer_source_info['sent_to_messenger']['title']; ?></div>
                          <div class="text-small text-muted"><?php echo isset($refferer_source_info['sent_to_messenger']['subscribers']) ? number_format($refferer_source_info['sent_to_messenger']['subscribers']) : 0 ?></div>
                        </div>
                      </li>
                      <li class="media">
                        <img class="img-fluid mt-1" src="<?php echo base_url('assets/img/icon/customer_chat_plugin.png'); ?>" alt="image" width="30">
                        <div class="media-body ml-3">
                          <div class="media-title fs-14"><?php echo $refferer_source_info['customer_chat_plugin']['title']; ?></div>
                          <div class="text-small text-muted"><?php echo isset($refferer_source_info['customer_chat_plugin']['subscribers']) ? number_format($refferer_source_info['customer_chat_plugin']['subscribers']) : 0 ?></div>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="col-6">
                    <ul class="list-unstyled list-unstyled-border list-unstyled-noborder mb-0">
                      <li class="media">
                        <img class="img-fluid mt-1" src="<?php echo base_url('assets/img/icon/direct.png'); ?>" alt="image" width="30">
                        <div class="media-body ml-3">
                          <div class="media-title fs-14"><?php echo $refferer_source_info['direct']['title']; ?></div>
                          <div class="text-small text-muted"><?php echo isset($refferer_source_info['direct']['subscribers']) ? number_format($refferer_source_info['direct']['subscribers']) : 0 ?></div>
                        </div>
                      </li>
                      <li class="media">
                        <img class="img-fluid mt-1" src="<?php echo base_url('assets/img/icon/auto_reply.png'); ?>" alt="image" width="30">
                        <div class="media-body ml-3">
                          <div class="media-title fs-14"><?php echo $refferer_source_info['comment_private_reply']['title']; ?></div>
                          <div class="text-small text-muted"><?php echo isset($refferer_source_info['comment_private_reply']['subscribers']) ? number_format($refferer_source_info['comment_private_reply']['subscribers']) : 0 ?></div>
                        </div>
                      </li>
                      <li class="media">
                        <img class="img-fluid mt-1" src="<?php echo base_url('assets/img/icon/me_link.png'); ?>" alt="image" width="30">
                        <div class="media-body ml-3">
                          <div class="media-title fs-14"><?php echo $refferer_source_info['me_link']['title']; ?></div>
                          <div class="text-small text-muted"><?php echo isset($refferer_source_info['me_link']['subscribers']) ? number_format($refferer_source_info['me_link']['subscribers']) : 0 ?></div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-5">
                <div class="social_chart_container">
                  <canvas id="social_network_shared_data" height="170"></canvas>
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>   
    </div>

    <!--<div class="row no-gutters mb-4">     
      <div class="col-12 col-md-4 ltr">
        <div class="card card-large-icons card-condensed gradient">
          <div class="card-icon mr-3 ml-3">
            <canvas id="fbsubscribers" height="100"></canvas>
          </div>
          <div class="card-body justify-content-center align-self-center font-weight-bold text-primary gradient"><?php echo $this->lang->line('Facebook Subscribers'); ?> (<?php echo $fsub_chart_data??0; ?>%)</div>
        </div>  
      </div>
      <div class="col-12 col-md-4 ltr">
        <div class="card card-large-icons card-condensed border-right border-left no_radius">
          <div class="card-icon  mr-3 ml-3">
            <canvas id="igsubscribers" height="100"></canvas>
          </div>
          <div class="card-body justify-content-center align-self-center font-weight-bold text-primary gradient"><?php echo $this->lang->line('Instagram Subscribers'); ?> (<?php echo $igsub_chart_data??0; ?>%)</div>
        </div>
      </div>
      <div class="col-12 col-md-4 ltr">
        <div class="card card-large-icons card-condensed">
          <div class="card-icon  mr-3 ml-3">
            <canvas id="esubscribers" height="100"></canvas>
          </div>
          <div class="card-body justify-content-center align-self-center font-weight-bold text-primary gradient"><?php echo $this->lang->line('Ecommerce Customers'); ?> (<?php echo $esub_chart_data??0; ?>%)</div>
        </div>
      </div>

      <div class="col-12">
        <div class="owl-carousel owl-theme mt-2" id="products-carousel">
          <?php foreach($latest_subscriber_list as $value) : ?>
          <div>
            <div class="product-item pb-3">
              <div class="product-image">
                <img alt="image" src="<?php echo $value['image_path']; ?>" class="img-fluid rounded-circle" style="width:80px; height: 80px;">
              </div>
              <div class="product-details">
                <div class="product-name"><?php if($value['full_name'] != '') echo $value['full_name']; else echo $value['first_name'].' '.$value['last_name']; ?></div>
                <div class="product-review">
                  <a style="cursor: pointer;" href="https://facebook.com/<?php echo $value['page_id']; ?>" target="_BLANK"><?php echo $value['page_name']; ?></a>
                </div>
                <div class="text-muted text-small"><?php echo $value['subscribed_at']; ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>-->

    <!-- Ecommerce earning statistics -->
    <div class="row mt-3 <?php if(empty(array_values($fb_earning_chart_values))) echo 'd-none';?>">
      <div class="col-12">
        <div class="card mb-0">
          <div class="card-header border-bottom-0">
            <h4><i class="fas fa-shopping-cart"></i> <?php echo $this->lang->line("Ecommerce Earnings")." (".$selected_curr.") - ".$this->lang->line("30 days"); ?></h4>
          </div>
          <div class="card-body">
            <div class="row no-gutters">
              <div class="col-12 col-md-7 col-lg-8">
                <canvas id="myChart_ecommerce" height="180"></canvas>
              </div>
              <div class="col-9 col-md-3 col-lg-3">
                <div class="nicescroll" id="cart_activities">
                  <ul class="list-unstyled list-unstyled-border">
                    <?php
                    if(empty($cart_data_graph)) echo '<div class="alert alert-light">'.$this->lang->line("No activity found").'</div>';                               
                    foreach ($cart_data_graph as $key => $value) 
                    { 
                      $hook_ago = date_time_calculator($value['updated_at'],true);
                      if($value['action_type']=='add') 
                      {
                        $hook_icon ='fas fa-cart-plus';
                        $hook_color = 'text-primary';
                        $hook_activity = $this->lang->line("Item added");
                      }
                      else if($value['action_type']=='remove') 
                      {
                        $hook_icon ='fas fa-cart-arrow-down';
                        $hook_color = 'text-danger';
                        $hook_activity = $this->lang->line("Item removed");
                      }
                      else 
                      {
                        $hook_icon = 'fas fa-shopping-bag';
                        $hook_color = 'text-success';
                        $currency_icon = isset($currency_icons[strtoupper($value['currency'])]) ? $currency_icons[strtoupper($value['currency'])] : '';
                        $hook_activity = $this->lang->line("Checkout").' <span class="">('.$currency_icon.$value['payment_amount'].')</span>';
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
                            <div class=""><i class="'.$hook_class.'"></i> '.$hook_user.'</div>
                            <span class="text-small">'.$hook_activity.' : '.$hook_ago.'</span>
                          </div>
                      </li>';                         
                    } 
                    ?>                               
                  </ul>
                </div>
              </div>
              <div class="col-3 col-md-2 col-lg-1">
                <div class="nicescroll" id="cart_recent_sales">
                    <?php 
                      foreach($top_products as $product) :

                        $thumb = (isset($product["thumbnail"]) && !empty($product["thumbnail"])) ? base_url('upload/ecommerce/'.$product["thumbnail"]) : base_url('assets/img/products/product-1.jpg');

                        if(isset($product["woocommerce_product_id"]) && !is_null($product["woocommerce_product_id"]) && isset($product["thumbnail"]) && !empty($product["thumbnail"]))
                        $thumb = $product["thumbnail"];
                    ?>

                    <div>
                      <div class="product-item">
                        <div class="product-image">                                  
                          <a target="_BLANK" href="<?php echo base_url('ecommerce/product/'.$product['product_id']);?>" ><img  src="<?php echo $thumb; ?>" class="img-fluid rounded-circle" style="width: 80px;height: 80px;"></a>
                        </div>
                        <div class="product-details mb-2">
                          <div class="product-name mb-0"><a target="_BLANK" href="<?php echo base_url('ecommerce/product/'.$product['product_id']);?>" ><?php echo $product['product_name']; ?></a></div>
                          <div class="text-muted text-small"><?php echo $product["sales_count"];?> <?php echo $this->lang->line("Sales"); ?></div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                  
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row campaign_statistics mt-4">
      <div class="col-12">
        <div class="card mb-0">
          <div class="card-body p-md-0">
            <div class="row no-gutters">
              <div class="col-12 col-md-6">
                <div class="card no_shadow  mb-0">
                  <div class="card-header p-0 border-bottom-0">
                    <h4 class="text-center w-100"><i class="fas fa-envelope"></i> <?php echo $this->lang->line('Email Campaigns Overviews'); ?> </h4>
                  </div>
                  <div class="card-body p-0 pb-4">
                    <div class="row no-gutters">
                      <div class="col-12 col-md-6">
                        <canvas id="email_campaign" height="230"></canvas>                        
                        <div class="pt-4 text-center">
                          <i class="fas fa-circle text-danger"></i> <?php echo $this->lang->line('Pending') ?>
                          <i class="fas fa-circle text-warning"></i> <?php echo $this->lang->line('Processing') ?>
                          <i class="fas fa-circle text-success gradient"></i> <?php echo $this->lang->line('Completed') ?>
                        </div>
                      </div>
                      <div class="col-12 col-md-6">
                        <div class="mt-4 campaign_stats">
                          <table class="table table-borderless">
                            <tbody>
                              <tr>
                                <td class="col-10 p-0">
                                  # <?php echo $this->lang->line('Campaigns Pending'); ?>
                                  <br>
                                  <div class="progress progress-info">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $email_campaigns; ?>%; background-image: linear-gradient(135deg, #130CB7 10%, #52E5E7 100%);" aria-valuenow="<?php echo $email_campaigns; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <br>
                                </td>
                                <td class="col-2 text-center"><?php echo $email_campaigns; ?></td>
                              </tr>
                              <tr>
                                <td class="col-10 p-0">
                                   # <?php echo $this->lang->line('Email Sent'); ?>
                                  <br>
                                  <div class="progress progress-success">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $total_sent; ?>%;background-image: linear-gradient( 135deg, #3CD500 10%, #FFF720 100%);" aria-valuenow="<?php echo $total_sent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <br>
                                </td>
                                <td class="col-2 text-center"><?php echo custom_number_format($total_sent); ?></td>
                              </tr>
                              <tr>
                                <td class="col-10 p-0">
                                  # <?php echo $this->lang->line('Email Pending'); ?>
                                  <br>
                                  <div class="progress progress-warning">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $remaining_threads ?>%;background-image: linear-gradient( 135deg,#FD6585 10%,#FFD3A5 100%);" aria-valuenow="<?php echo $remaining_threads ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <br>
                                </td>
                                <td class="col-2 text-center"><?php echo custom_number_format($remaining_threads) ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>

                  
                  </div>
                </div>          
              </div>

              <div class="col-12 col-md-6">
                <div class="card no_shadow  mb-0">
                  <div class="card-header p-0 border-bottom-0">
                    <h4 class="text-center w-100"><i class="fas fa-sms"></i> <?php echo $this->lang->line('SMS Campaigns Overviews'); ?> </h4>
                  </div>
                  <div class="card-body p-0 pb-4">
                    <div class="row no-gutters">
                      <div class="col-12 col-md-6">                        
                        <canvas id="sms_campaign" height="230"></canvas>
                        <div class="pt-4 text-center">
                          <i class="fas fa-circle text-danger"></i> <?php echo $this->lang->line('Pending') ?>
                          <i class="fas fa-circle text-warning"></i> <?php echo $this->lang->line('Processing') ?>
                          <i class="fas fa-circle text-success gradient"></i> <?php echo $this->lang->line('Completed') ?>
                        </div>
                      </div>
                      <div class="col-12 col-md-6">
                        
                        <div class="mt-4 campaign_stats">
                          <table class="table table-borderless table-sm">
                            <tbody>
                              <tr>
                                <td class="col-10 p-0">
                                  # <?php echo $this->lang->line('Campaigns Pending'); ?>
                                  <br>
                                  <div class="progress progress-info">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $sms_campaigns; ?>%;background-image: linear-gradient(135deg, #130CB7 10%, #52E5E7 100%);" aria-valuenow="<?php echo $sms_campaigns; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <br>
                                </td>
                                <td class="col-2 text-center"><?php echo $sms_campaigns; ?></td>
                              </tr>
                              <tr>
                                <td class="col-10 p-0">
                                  # <?php echo $this->lang->line('SMS Sent'); ?>
                                  <br>
                                  <div class="progress progress-success">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $total_sent2; ?>%;background-image: linear-gradient( 135deg, #3CD500 10%, #FFF720 100%);" aria-valuenow="<?php echo $total_sent2; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <br>
                                </td>
                                <td class="col-2 text-center"><?php echo custom_number_format($total_sent2); ?></td>
                              </tr>
                              <tr>
                                <td class="col-10 p-0">
                                  # <?php echo $this->lang->line('SMS Pending'); ?>
                                  <br>
                                  <div class="progress progress-warning">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $remaining_threads2 ?>%;background-image: linear-gradient( 135deg,#FD6585 10%,#FFD3A5 100%);" aria-valuenow="<?php echo $remaining_threads2 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <br>
                                </td>
                                <td class="col-2 text-center"><?php echo custom_number_format($remaining_threads2) ?></td>
                              </tr>
                            </tbody>
                          </table>
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


    <div class="row dashboard_fullCalendar no-gutters mt-4">
      <div class="col-12">
        <div class="card no_radius">
            <div class="card-header">
              <h4 class="w-100 pr-0">
                <i class="fa fa-calendar"></i> <?php echo $this->lang->line("Activity Calendar") ?>
                <a href="<?php echo $this->user_id==$user_id_url || $user_id_url==0 ? base_url('calendar') : base_url('calendar/user/'.$user_id_url);?>" class="btn btn-outline-primary float-right" target="_BLANK"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line("Monthly Activity");?></a>
              </h4>
            </div>
            <div class="card-body">
                <div id="dashboard_calendar"></div>
            </div>
        </div>
      </div>
    </div>
    
  <?php if($other_dashboard == 1) : ?>
  </div>
  <?php endif; ?>
</section>

<script type="text/javascript">
  
  var subscribers_chart = document.getElementById("subscribers_chart_1").getContext('2d');

  var sevendays_subscriber_chart_bgcolor = subscribers_chart.createLinearGradient(0, 0, 0, 70);
  sevendays_subscriber_chart_bgcolor.addColorStop(0, 'rgba(21, 233, 255, .3)');
  sevendays_subscriber_chart_bgcolor.addColorStop(1, 'rgba(21, 151, 229, 0)');

  var subscribers_myChart = new Chart(subscribers_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_values(array_slice($last_tweleve_month, -6, 6, true)))?>,
      datasets: [{
        label: '<?php echo $this->lang->line("Subscribers");?>',
        data:  <?php echo json_encode(array_values(array_slice($total_subscribers, -6, 6, true)))?>,
        backgroundColor: 'transparent',
        borderWidth: 2.5,
        borderColor: '#0D8BF1',
        pointBorderWidth: 0,
        pointBorderColor: 'transparent',
        pointRadius: 3,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,1)',
      }]
    },
    options: {
      layout: {
        padding: {
          bottom: -1,
          left: -1
        }
      },
      plugins: {
        datalabels: {
            display: false,
        }
      },
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
            beginAtZero: true,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            drawBorder: false,
            display: false,
          },
          ticks: {
            display: false
          }
        }]
      }
    }
  });
    
  var ecommerce_chart = document.getElementById("ecommerce_chart_1").getContext('2d');

  // var sevendays_subscriber_chart_bgcolor = ecommerce_chart.createLinearGradient(0, 0, 0, 70);
  // sevendays_subscriber_chart_bgcolor.addColorStop(0, 'rgba(252, 66, 123, .2)');
  // sevendays_subscriber_chart_bgcolor.addColorStop(1, 'rgba(139, 37, 68, 0)');

  var ecommerce_myChart = new Chart(ecommerce_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_values(array_slice($earning_chart_labels, -6, 6, true)))?>,
      datasets: [{
        label: '<?php echo $this->lang->line("Earnings");?>',
        data: <?php echo json_encode(array_values(array_slice($total_earning_chart_values, -6, 6, true)))?>,
        backgroundColor: 'transparent',
        borderWidth: 2.5,
        // borderColor: '#FC427B',
        borderColor: '#0D8BF1',
        pointBorderWidth: 0,
        pointBorderColor: 'transparent',
        pointRadius: 3,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,1)',
      }]
    },
    options: {
      layout: {
        padding: {
          bottom: -1,
          left: -1
        }
      },
      plugins: {
        datalabels: {
            display: false,
        }
      },
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
            beginAtZero: true,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            drawBorder: false,
            display: false,
          },
          ticks: {
            display: false
          }
        }]
      },
    }
  });


</script>


<script>
  var user_id_url = '<?php echo $user_id_url;?>';
  var ctx = document.getElementById("subscribers_stats").getContext('2d');
  var v1 = '<?php echo $this->lang->line("Subscriber Gain"); ?>';
  var v2 = '<?php echo $this->lang->line("Email Gain"); ?>';
  var v3 = '<?php echo $this->lang->line("Phone Gain"); ?>';
  var gradient_warning = ctx.createLinearGradient(0, 0, 0, 600);
  gradient_warning.addColorStop(0, 'rgba(252, 74, 26)');
  gradient_warning.addColorStop(1, 'rgba(247, 183, 51)'); 

  var gradient_secondary = ctx.createLinearGradient(0, 0, 0, 600);
  gradient_secondary.addColorStop(0, 'rgba(241, 71, 147)');
  gradient_secondary.addColorStop(1, 'rgba(58, 9, 137)'); 

  var gradient_primary = ctx.createLinearGradient(0, 0, 0, 600);
  gradient_primary.addColorStop(0, 'rgba(13, 139, 241)');
  gradient_primary.addColorStop(1, 'rgba(7, 65, 204)'); 

  const labels = <?php echo json_encode(array_values($last_tweleve_month)); ?>;

  const subscribers_data = {
   labels: labels,
   datasets: [{
     label: v1,
     type:'line',
     backgroundColor: 'transparent',
     borderColor: gradient_warning,
     data: <?php echo json_encode(array_values($total_subscribers)) ?>,
     pointBorderWidth: 0,
     pointRadius: 0,
     pointBackgroundColor: 'transparent',
   },{
     label: v2,
     backgroundColor: gradient_primary,
     borderColor: 'transparent',
     data: <?php echo json_encode(array_values($email_subscribers)) ?>,
   },
   {
       label: v3,
       type:'bar',
       backgroundColor: gradient_secondary,
       borderColor: 'transparent',
       data: <?php echo json_encode(array_values($phone_subscribers)) ?>,
     }
   ]
  };

  const config = {
   type: 'bar',
   data: subscribers_data,
   options: {
     responsive: true,
     legend: {
       display: true
     },
     hover: {
      mode: false
     },
     plugins: {
       datalabels: {
           display: false,
       }
     },
     scales: {
       yAxes: [{
         ticks: {
           beginAtZero: true,
           stepSize: <?php echo $stepSize; ?>,
         }
       }],
     },
   }

  };

  const myChart2 = new Chart(ctx,config);

</script>

<script>
    $(document).ready(function() {   

      var stepsize = "<?php echo $step_size; ?>"; 
      var fb_vs_ig_vs_web_earning_chart = document.getElementById('myChart_ecommerce').getContext('2d');

      var gradient_info = fb_vs_ig_vs_web_earning_chart.createLinearGradient(0, 0, 0, 600);
      gradient_info.addColorStop(0, 'rgba(21, 233, 255, .8)');
      gradient_info.addColorStop(1, 'rgba(19, 29, 75, .8)'); 

      var gradient_success = fb_vs_ig_vs_web_earning_chart.createLinearGradient(0, 0, 0, 600);
      gradient_success.addColorStop(0, 'rgba(83, 161, 100,.8)');
      gradient_success.addColorStop(1, 'rgba(19, 29, 75, .8)'); 

      var gradient_primary = fb_vs_ig_vs_web_earning_chart.createLinearGradient(0, 0, 0, 600);
      gradient_primary.addColorStop(0, 'rgba(13, 139, 241, .6)');
      gradient_primary.addColorStop(1, 'rgba(7, 65, 204, .6)'); 

      var gradient_secondary = fb_vs_ig_vs_web_earning_chart.createLinearGradient(0, 0, 0, 600);
      gradient_secondary.addColorStop(0, 'rgba(241, 71, 147, .7)');
      gradient_secondary.addColorStop(1, 'rgba(58, 9, 137, .7)'); 

      var gradient_warning = fb_vs_ig_vs_web_earning_chart.createLinearGradient(0, 0, 0, 600);
      gradient_warning.addColorStop(0, 'rgba(252, 74, 26, .8)');
      gradient_warning.addColorStop(1, 'rgba(247, 183, 51, .8)'); 

      var gradient_danger = fb_vs_ig_vs_web_earning_chart.createLinearGradient(0, 0, 0, 600);
      gradient_danger.addColorStop(0, 'rgba(255, 106, 0, .8)');
      gradient_danger.addColorStop(1, 'rgba(238, 9, 121, .8)'); 

      var myChart1 = new Chart(fb_vs_ig_vs_web_earning_chart, {
        type: 'line',
        data: {
          labels: <?php echo json_encode(array_values($earning_chart_labels));?>,
          datasets: [
          {
            label: '<?php echo $this->lang->line('Facebook'); ?>',
            data: <?php echo json_encode(array_values($fb_earning_chart_values));?>,
            borderWidth: 0,
            backgroundColor: gradient_primary,
            borderWidth: 0,
            borderColor: 'transparent',
            pointBorderWidth: 0 ,
            pointRadius: 0,
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: 'transparent',
          },          
          {
            label: '<?php echo $this->lang->line('Instagram'); ?>',
            data: <?php echo json_encode(array_values($ig_earning_chart_values));?>,
            borderWidth: 0,
            backgroundColor: gradient_secondary,
            borderWidth: 0,
            borderColor: 'transparent',
            pointBorderWidth: 0,
            pointRadius: 0,
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: 'rgba(13, 139, 241, .8)',
          },
          {
            label: '<?php echo $this->lang->line('Web'); ?>',
            data: <?php echo json_encode(array_values($web_earning_chart_values));?>,
            borderWidth: 0,
            backgroundColor: gradient_warning,
            borderWidth: 0,
            borderColor: 'transparent',
            pointBorderWidth: 0 ,
            pointRadius: 0,
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: 'transparent',
          }]          
        },
        options: {
          responsive: true,
          legend: {
            display: true
          },
          plugins: {
            datalabels: {
                display: false,
            }
          },
          scales: {
            yAxes: [{
              gridLines: {
                display: false,
                drawBorder: false,
                color: '#f2f2f2',
              },
              ticks: {
                beginAtZero: true,
                stepSize: stepsize,
                // display: false,
                // callback: function(value, index, values) {
                //   return value;
                // }
              }
            }],
            xAxes: [{
              gridLines: {
                display: false,
                tickMarkLength: 15,
              }
            }]
          },
        }
      });
    });
</script>

<!--
<script>
  var fbsubscriberProgress = document.getElementById("fbsubscribers");
  var fbsubscriberChart = new Chart(fbsubscriberProgress, {
    type: 'doughnut',
    data: {
      datasets: [{
        label: "Facebook",
        // percent: 60,
        percent: <?php echo $fsub_chart_data; ?>,
        backgroundColor: ['rgba(13, 139, 241, 1)']
      }]
    },
    plugins: [{
        beforeInit: (chart) => {
          const dataset = chart.data.datasets[0];
          chart.data.labels = [dataset.label];
          dataset.data = [dataset.percent, 100 - dataset.percent];
        },
        datalabels: {
            display: false,
        }
      },
      {
        beforeDraw: (chart) => {
          var width = chart.chart.width,
          height = chart.chart.height,
          ctx = chart.chart.ctx;
          ctx.restore();
          var fontSize = (height / 100).toFixed(2);
          ctx.font = fontSize + "em sans-serif";
          ctx.fillStyle = "#9b9b9b";
          ctx.textBaseline = "middle";
          var text = chart.data.datasets[0].percent + "%",
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;
          ctx.fillText(text, textX, textY);
          ctx.save();

          ctx.shadowColor = "#eee";
          ctx.shadowBlur = 5;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 5;
        }
      }
    ],
    options: {
      // responsive: true,
      maintainAspectRatio: false,
      cutoutPercentage: 50,
      rotation: Math.PI / 2,
      legend: {
        display: false,
      },
      hover: {
         mode: false
       },
      tooltips: {
        enabled:false,
        filter: tooltipItem => tooltipItem.index == 0
      },
      plugins: {
        datalabels: {
          display: false,
        }
      },
    }
  });
</script>
<script>
  var igsubscriberProgress = document.getElementById("igsubscribers");
  var igsubscriberchart = new Chart(igsubscriberProgress, {
    type: 'doughnut',
    data: {
      datasets: [{
        label: 'Instagram',
        // percent: 70,
        percent: <?php echo $igsub_chart_data; ?>,
        backgroundColor: ['rgba(188, 80, 144, 1)']
      }]
    },
    plugins: [{
        beforeInit: (chart) => {
          const dataset = chart.data.datasets[0];
          chart.data.labels = [dataset.label];
          dataset.data = [dataset.percent, 100 - dataset.percent];
        },
        datalabels: {
            display: false,
        }
      },
      {
        beforeDraw: (chart) => {
          var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
          ctx.restore();
          var fontSize = (height / 100).toFixed(2);
          ctx.font = fontSize + "em sans-serif";
          ctx.fillStyle = "#9b9b9b";
          ctx.textBaseline = "middle";
          var text = chart.data.datasets[0].percent + "%",
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;
          ctx.fillText(text, textX, textY);
          ctx.save();

          ctx.shadowColor = "#eee";
          ctx.shadowBlur = 5;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 5;
        }
      }
    ],
    options: {
      // responsive: true,
      maintainAspectRatio: false,
      cutoutPercentage: 50,
      rotation: Math.PI / 2,
      legend: {
        display: false,
      },
      hover: {
         mode: false
       },
      tooltips: {
        enabled: false,
        filter: tooltipItem => tooltipItem.index == 0
      },
      plugins: {
        datalabels: {
            display: false,
        }
      },
    }
  });
</script>
<script>
  var esubscriberProgress = document.getElementById("esubscribers");
  var esubscriberchart = new Chart(esubscriberProgress, {
    type: 'doughnut',
    data: {
      datasets: [{
        label: 'Ecommerce',
        // percent: 55,
        percent: <?php echo $esub_chart_data; ?>,
        backgroundColor: ['rgba(252, 74, 26, 1)']
      }]
    },
    plugins: [{
        beforeInit: (chart) => {
          const dataset = chart.data.datasets[0];
          chart.data.labels = [dataset.label];
          dataset.data = [dataset.percent, 100 - dataset.percent];
        },
        datalabels: {
          display: false,
        },
      },
      {
        beforeDraw: (chart) => {
          var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
          ctx.restore();
          var fontSize = (height / 100).toFixed(2);
          ctx.font = fontSize + "em sans-serif";
          ctx.fillStyle = "#9b9b9b";
          ctx.textBaseline = "middle";
          var text = chart.data.datasets[0].percent + "%",
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;
          ctx.fillText(text, textX, textY);
          ctx.save();

          ctx.shadowColor = "#eee";
          ctx.shadowBlur = 5;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 5;
        }
      }
    ],
    options: {
      // responsive: true,
      maintainAspectRatio: false,
      cutoutPercentage: 50,
      rotation: Math.PI / 2,
      legend: {
        display: false,
      },
      hover: {
       mode: false
       },
      tooltips: {
        enabled: false,
        filter: tooltipItem => tooltipItem.index == 0
      },
      plugins: {
        datalabels: {
            display: false,
        }
      },
    }
  });
</script>
-->

<!-- Subscribers from different sources chart -->
<script>
  var social_network_shared_config = document.getElementById('social_network_shared_data').getContext('2d');
  var only_keys = <?php echo json_encode(array_keys(isset($subscribers_source_info) ? $subscribers_source_info : array())); ?>;
  var only_values = <?php echo json_encode(array_values(isset($subscribers_source_info) ? $subscribers_source_info : array())); ?>;

  // var bg_linear_gradient = ["","#C82372","#911670","#5A1A81","#340F70","#F47D6D"];
  var bg_linear_gradient = ["orange","#53a164","#5A1A81","#0dcde1","#0D8BF1","#FC427B"];

  var social_network_shared_chart_data = {
    type: 'doughnut',
    // type: 'pie',
    data: {
      datasets: [{
        data: only_values,
        backgroundColor: bg_linear_gradient,
        pointColor:bg_linear_gradient,
        
      }],
      labels: only_keys,
    },
    options: {
      cutoutPercentage : 40,
      responsive: true,
      legend: {
        display: false,
        align:'start',
        position: 'left',
        fullSize: true,
        labels: {
            fontColor: '#333',
            fontSize: 14,
            padding: 20
        },
      },
      hover: {
         mode: false
       },
      
      animation: {
        animateScale: true,
        animateRotate: true
      },
      plugins: {
        datalabels: {
            display: false,
        }
      },
    }
   };

  var social_network_info_my_chart = new Chart(social_network_shared_config, social_network_shared_chart_data);
</script>



<!-- email campaign statistics -->
<script>
  var data = [{
      data: <?php echo json_encode($email_campaign_chart_data); ?>,
      labels: ['pending','processing','Completed'],
      backgroundColor: ["#FC427B","orange","#53a164"],
      borderColor: "#fff"
  }];

  var options = {
      responsive: true,
      cutoutPercentage: 20,
      tooltips: {
          enabled: false,
      },
      
      plugins: {
          datalabels: {
              formatter: (value, ctx) => {
                if(value > 0) {
                  let sum = 0;
                  let dataArr = ctx.chart.data.datasets[0].data;
                  dataArr.map(data => {
                        sum += data;
                  });
                  let percentage = (value*100 / sum).toFixed(1)+"%";
                  return percentage;
                }
              },
              color: '#fff',
              font: {
                weight: '900',
                size: 24,
              },
              display: function(context) {
                  return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
               }
          }
      },
  };

  var ctx = document.getElementById("email_campaign").getContext('2d');
  var myChart = new Chart(ctx, {
      type: 'pie',
      data: {
          datasets: data
      },
      options: options
  });
</script>

<!-- sms campaign statistics -->
<script>
  var data2 = [{
      data: <?php echo json_encode($sms_campaign_chart_data); ?>,
      labels: <?php echo json_encode($sms_campaign_chart_labels); ?>,
      backgroundColor: ["#FC427B","orange","#53a164"],
      borderColor: "#fff"
  }];

  var options2 = {  
      responsive: true,  
      cutoutPercentage: 20,
      tooltips: {
          enabled: false,
      },
      plugins: {
          datalabels: {
              formatter: (value, ctx) => {
                if(value > 0) {
                  let sum = 0;
                  let dataArr = ctx.chart.data.datasets[0].data;
                  dataArr.map(data => {
                        sum += data;
                  });
                  let percentage = (value*100 / sum).toFixed(1)+"%";
                  return percentage;
                }
              },
              color: '#fff',
              font: {
                weight: '900',
                size: 24,
              },
              display: function(context) {
                  return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
               }
          }
      }
  };

  var ctx = document.getElementById("sms_campaign").getContext('2d');
  var myChart = new Chart(ctx, {
      type: 'pie',
      data: {
          datasets: data2
      },
      options: options2
  });
</script>


<script type="text/javascript">
	$(document).on('click', '.no_action', function(event) {
	  event.preventDefault();
	});
</script>

<script>
	$(document).ready(function() {

    $(document).on('click', '.currency_item', function(event) {
      event.preventDefault();
      var item_name = $(this).attr('store_id');
      $.ajax({
        url: '<?php echo base_url() ?>'+'dashboard/set_currency',
        type: 'POST',
        data: {item_name: item_name},
        success:function(){
          location.reload();
        }
      })
      
    });
		
		$(document).on('click', '.month_change', function(e) {
		  e.preventDefault(); 
		  $(".month_change").removeClass('active');
		  $(this).addClass('active');
		  var month_no = $(this).attr('month_no');
		  var month_name = $(this).html();
		  $("#orders-month").html(month_name);

		  $(".month_change_middle_content").hide();
		  $("#loader").removeClass('hidden');

      var url = "<?php echo base_url('dashboard/get_first_div_content')?>";
      
		  $.ajax({
		     type:'POST' ,
		     url: url,
		     data: {month_no,user_id_url},
		     dataType : 'JSON',
		     success:function(response)
		     {
		      	$("#loader").addClass('hidden');
		      	$("#fbsub").html(response.fbsub);
		      	$("#igsub").html(response.igsub);
		      	$("#esub").html(response.esub);
		      	$("#total_subscribers").html(response.total_sub);
		      	$(".month_change_middle_content").show();
		     }
		  });
		});
    
    $(document).on('click', '.ecommerce_month_change', function(e) {
      e.preventDefault(); 
      $(".ecommerce_month_change").removeClass('active');
      $(this).addClass('active');
      var month_no = $(this).attr('month_no');
      var month_name = $(this).html();
      $("#ecommerce-month").html(month_name);

      $(".ecommerce_month_change_middle_content").hide();
      $("#e_loader").removeClass('hidden');
      
      var url = "<?php echo base_url('dashboard/get_ecommerce_div_content')?>";

      $.ajax({
         type:'POST' ,
         url: url,
         data: {month_no,user_id_url},
         dataType : 'JSON',
         success:function(response)
         {
            $("#e_loader").addClass('hidden');
            $("#order_block").html(response.total_orders);
            $("#recovered_block").html(response.summary_recovered_cart);
            $("#checkout_block").html(response.summary_checkout_cart);
            $("#total_earning").html(response.summary_earning);
            $(".ecommerce_month_change_middle_content").show();
         }
      });
    });

    $("#products-carousel").owlCarousel({
      items: 6,
      rtl : is_rtl,
      margin: 10,
      autoplay: true,
      autoplayTimeout: 1000,
      autoplayHoverPause:true,
      loop: true,
      responsive: {
        0: {
          items: 2
        },
        768: {
          items: 4
        },
        1200: {
          items: 6
        }
      }
    });


	});
</script>


<?php include(APPPATH.'views/ecommerce/cart_modal.php'); ?>

<?php include(APPPATH.'views/calendar/fullcalendar_css.php'); ?>
<?php include(APPPATH.'views/calendar/fullcalendar_custom_js.php'); ?>

<link rel="stylesheet" href="<?php echo base_url('assets/css/system/dashboard.css'); ?>">