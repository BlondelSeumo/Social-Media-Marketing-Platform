<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-edit"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('announcement/full_list'); ?>"><?php echo $this->lang->line("Announcement"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <form class="form-horizontal" action="<?php echo site_url().'announcement/edit_action';?>" method="POST">
          <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <label><?php echo $this->lang->line("Title"); ?> *</label><br/>
                <input type="hidden" id="hidden_id" name="hidden_id" value="<?php echo $xdata['id'];?>"/>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo $xdata['title'];?>" />
                <span class="red"><?php echo form_error('title'); ?></span>
              </div>

              <div class="form-group">
                <label><?php echo $this->lang->line("Description"); ?> *</label><br/>
                <textarea name="description" style="height:200px !important;" class="form-control" id="description"><?php echo $xdata['description'];?></textarea>
                <span class="red"><?php echo form_error('description'); ?></span>
              </div>

              <div class="form-group">
                <div class="form-group">
                  <label for="status" > <?php echo $this->lang->line('Status');?></label><br>
                  <label class="custom-switch mt-2">
                    <input type="checkbox" name="status" value="published" <?php if($xdata['status']=='published') echo 'checked'; ?> class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Publish');?></span>
                    <span class="red"><?php echo form_error('status'); ?></span>
                  </label>
                </div> 
              </div>
              
            </div>
            <div class="card-footer bg-whitesmoke">
              <button name="submit" type="submit" id="save_announcement" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
              <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("announcement/full_list",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
