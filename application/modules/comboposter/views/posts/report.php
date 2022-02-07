<section class="section">
  <div class="section-header">
    <h1><i class="fa fa-file-text"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('ultrapost') ?>"><?php echo $this->lang->line("Comboposter"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('comboposter/'.$campaign_type.'_post/campaigns') ?>"><?php echo $this->lang->line(ucfirst($campaign_type)." post"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4><?php echo $this->lang->line("Campaign Info"); ?></h4>
            </div>
            <div class="card-body data-card">
              
              <!-- campaign name section -->
              <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Campaign Name"); ?></h2>
              <p class="section-lead"><?php echo $campaigns_info['campaign_name']; ?></p>

              <?php if ($campaigns_info['title'] != ''): ?>
                <!-- title section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Title"); ?></h2>
                <p class="section-lead"><?php echo $campaigns_info['title']; ?></p>
              <?php endif ?>

              <?php if (($campaign_type == 'link' || $campaign_type == 'image') && $campaigns_info['link'] != ''): ?>              
                <!-- link section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Link"); ?></h2>
                <p class="section-lead"><?php echo $campaigns_info['link']; ?></p>
              <?php endif ?>
              
              <?php if ($campaign_type == 'video'): ?>              
                <!-- privacy_type section -->
                <h2 class="section-title hidden" style="margin-top: 10px;"><?php echo $this->lang->line("Privacy Type"); ?></h2>
                <p class="section-lead hidden"><?php echo ucfirst($campaigns_info['privacy_type']); ?></p>
              <?php endif ?>
              
              
              <?php if ($campaign_type == 'image' && $campaigns_info['image_url'] != ''): ?>              
                <!-- image section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Image"); ?></h2>
                <p class="section-lead"><img src="<?php echo $campaigns_info['image_url']; ?>" alt="image" height="250" width="250"></p>
              <?php endif ?>

              <?php if (($campaign_type == 'video' || $campaign_type == 'link') && $campaigns_info['thumbnail_url'] != ''): ?>
                <!-- thumbnail section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Thumbnail"); ?></h2>
                <p class="section-lead"><img src="<?php echo $campaigns_info['thumbnail_url']; ?>" alt="image" height="250" width="250"></p>
              <?php endif ?>

              <?php if ($campaign_type == 'video' && $campaigns_info['video_url'] != ''): ?>              
                <!-- video section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Video"); ?></h2>
                <p class="section-lead">
                  <video width="320" height="240" controls>
                    <source src="<?php echo $campaigns_info['video_url']; ?>" type="video/mp4">
                    <source src="<?php echo $campaigns_info['video_url']; ?>" type="video/ogg">
                    Your browser does not support the video tag.
                  </video>
                </p>
              <?php endif ?>

              <?php if ($campaign_type != 'html' && $campaigns_info['message'] != ''): ?>              
                <!-- message section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Message"); ?></h2>
                <p class="section-lead"><?php echo $campaigns_info['message']; ?></p>
              <?php endif ?>

              <?php if (($campaign_type == 'image' || $campaign_type == 'html') && $campaigns_info['rich_content'] != ''): ?>
                <!-- rich content section -->
                <h2 class="section-title" style="margin-top: 10px;"><?php echo $this->lang->line("Rich Content"); ?></h2>
                <div class="section-lead"><?php echo $campaigns_info['rich_content']; ?></div>
              <?php endif ?>

            </div>
          </div>
      </div>
      <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4><?php echo $this->lang->line("Posting Report"); ?></h4>
            </div>
            <div class="card-body data-card">

              <?php if (count($post_report) == 0): ?>

                  <div class="empty-state" data-height="400" style="height: 400px;">
                    <img class="img-fluid" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
                    <h2 class="mt-0"><?php echo $this->lang->line("Looks like there is no data"); ?></h2>
                    <p class="lead">
                      <?php echo $this->lang->line("We didn't find any data. May be your campaign has not posted in any social media."); ?>
                    </p>
                  </div>

              <?php else: ?>

                  <ul class="nav nav-tabs" id="myTab2" role="tablist">

                      <?php $i = 0; $first_selected_tab = ''; ?>
                      <?php foreach ($post_report as $key => $single_report): ?>
                        
                        <?php 
                            if ($i == 0) {
                              $first_selected_tab = $key;
                            }
                        ?>
                        <li class="nav-item">
                          <a class="nav-link <?php if ($i == 0) echo "active show"; ?>" id="<?php echo lcfirst($key); ?>-tab" data-toggle="tab" href="#<?php echo lcfirst($key); ?>" role="tab" aria-controls="home" aria-selected="true"><?php echo $key; ?></a>
                        </li>

                        <?php $i++; ?>
                      <?php endforeach ?>

                  </ul>
                  <div class="tab-content table-bordered" style="border-top: none;">
                    
                      
                      <?php foreach ($post_report as $key => $single_report): ?>
                          
                          <div class="tab-pane fade <?php if ($key == $first_selected_tab) echo "active show"; ?>" id="<?php echo lcfirst($key); ?>" role="tabpanel" aria-labelledby="<?php echo lcfirst($key); ?>-tab">

                              <div class="list-group pl-3 pr-3">
                                <!-- <a href="#" class="list-group-item list-group-item-action">    
                                </a> -->
                                <div class="row">
                                    <?php foreach ($single_report as $report): ?>
                                        
                                        <div class="col-12 col-md-6 mb-2">
                                          
                                            <li class="list-group-item list-group-item-action">
                                                <div class="row">
                                                  <div class="col-12 col-md-9">
                                                    <img class="mr-3 rounded-circle mCS_img_loaded" width="35" src="<?php echo isset($report["display_account_image"]) ? $report["display_account_image"] : ''; ?>" alt="avatar">
                                                    <h6 style="font-size: 14px;font-weight: 400;" class="page_list_media_title media-title mt-2 d-inline"><?php echo isset($report["display_name"]) ? $report["display_name"] : ''; ?></h6>
                                                  </div>

                                                  <div class="col-10 col-md-3">
                                                    <?php // if (isset($report['report']) && strpos($report['report'], 'http') !== false): ?>

                                                      <?php 

                                                      $first_4_http_check=substr($report['report'], 0,4); ; 
                                                        if(isset($report['report']) && $first_4_http_check=='http'): ?>
                                                         <a class="btn btn-outline-primary btn-rounded float-right" href="<?php echo $report['report']; ?>"><?php echo $this->lang->line("Visit Post"); ?></a>   
                                                    <?php elseif(isset($report['report'])): ?>    
                                                        <?php echo $report['report']; ?>
                                                    <?php else: ?>
                                                        <?php $this->lang->line("Report not found."); ?>
                                                    <?php endif ?> 
                                                  </div>
                                                </div>
                                                
                                            </li>

                                        </div>

                                    <?php endforeach ?>
                                </div>

                              </div>
                          </div>

                          
                      <?php endforeach ?>

                  </div>

              <?php endif ?>

            </div>
      </div>
    </div>
    
  </div>
</section> 