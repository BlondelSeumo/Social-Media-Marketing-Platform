<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-bell"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('announcement/full_list'); ?>"><?php echo $this->lang->line("Announcement"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>
  <?php $description =  preg_replace("/(https?:\/\/[a-zA-Z0-9\-._~\:\/\?#\[\]@!$&'\(\)*+,;=]+)/", '<a target="_BLANK" href="$1">$1</a>', $xdata['description']); // find and replace links with ancor tag ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="section-title mt-0"><?php echo $this->lang->line("Title"); ?> :<small> <?php echo $xdata['title'];?></small> </div>
            <div class="section-title"><?php echo $this->lang->line("Description"); ?></div>
            <div class="p-3 mb-2 bg-light text-dark" style="margin-left: 45px;"><?php echo nl2br($description);?></div>
            <div class="section-title"><?php echo $this->lang->line("Published"); ?> <small><?php echo date_time_calculator($xdata['created_at'],true); ?></small></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>