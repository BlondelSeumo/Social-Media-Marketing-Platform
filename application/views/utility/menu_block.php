<section class="section">
  <div class="section-header">
      <h1><i class="fas fa-search"></i> <?php echo $page_title; ?></h1>
      <div class="section-header-breadcrumb">
          <div class="breadcrumb-item"><?php echo $page_title; ?></div>
      </div>
  </div>

  <div class="section-body">
      <div class="row">  

          <div class="col-12 col-lg-6">
              <div class="card card-large-icons">
                  <div class="card-icon text-primary"><i class="fas fa-adjust"></i></div>
                  <div class="card-body">
                      <h4><?php echo $this->lang->line("Website Comparison"); ?></h4>
                      <p><?php echo $this->lang->line("Social existency (share, like, comment...)"); ?></p>
                      <div class="dropdown">
                          <a href="<?php echo base_url('search_tools/comparision'); ?>" class="no_hover"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                      </div>
                  </div>
              </div>
          </div>

          <!-- <div class="col-lg-6">
              <div class="card card-large-icons">
                  <div class="card-icon text-primary"><i class="fas fa-map-marked"></i></div>
                  <div class="card-body">
                      <h4><?php echo $this->lang->line("Place Search"); ?></h4>
                      <p><?php echo $this->lang->line("Page, website, mobile, address..."); ?></p>
                      <div class="dropdown">
                          <a href="<?php echo base_url('search_tools/place_search'); ?>" class="no_hover"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                      </div>
                  </div>
              </div>
          </div> -->
          <?php if($this->config->item('instagram_reply_enable_disable')) : ?>
          <div class="col-12 col-lg-6">
            <div class="card card-large-icons">
              <div class="card-icon text-primary">
                <i class="fas fa-tags"></i>
              </div>
              <div class="card-body">
                <h4><?php echo $this->lang->line("Hashtag Search"); ?></h4>
                <p><?php echo $this->lang->line("Search Top & Recent media with hashtag in Instagram"); ?></p>
                <a href="<?php echo base_url("instagram_reply/hashTag_search"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
          <?php endif; ?>

      </div>
  </div>
</section>



<style type="text/css">
  .popover{min-width: 330px !important;}
  .no_hover:hover{text-decoration: none;}
</style>