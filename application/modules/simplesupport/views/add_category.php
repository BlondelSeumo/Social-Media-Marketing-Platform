<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('simplesupport/tickets'); ?>"><?php echo $this->lang->line("Support Desk"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">

      <form class="form-horizontal" action="<?php echo site_url().'simplesupport/add_category_action';?>" method="POST">
        <div class="card">
          <div class="card-body">

            <div class="form-group">
              <label for="category_name"> <?php echo $this->lang->line("Category Name")?> *</label>
              <input name="category_name" value="<?php echo set_value('category_name');?>"  class="form-control" type="text">
              <span class="red"><?php echo form_error('category_name'); ?></span>
            </div>            
            
          </div>

          <div class="card-footer bg-whitesmoke">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("simplesupport/support_category_manager",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>  
    </div>
  </div>
</section>
