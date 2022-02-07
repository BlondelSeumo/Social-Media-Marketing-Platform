<?php $this->load->view('admin/theme/message'); ?>
<?php $is_demo=$this->is_demo; ?>
<?php $is_admin=($this->session->userdata('user_type') == "Admin") ? 1:0; ?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/instagram/auto_comment_reply_template.css');?>">

<div id="dynamic_field_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" id="add_name">
                <div class="modal-header">
                    <h5 class="modal-title text-center"><i class="fa fa-th-large"></i> <?php echo $this->lang->line('Please Give The Following Information For Post Auto Comment'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body pb-0">

                    <label>
                        <i class="fa fa-th-large"></i>
                         <?php echo $this->lang->line('Template Name'); ?>
                    </label>
                    <div class="form-group">
                        <input type="text" name="template_name" id="name" class="form-control" placeholder="<?php echo $this->lang->line('Your Template Name'); ?>" />
                    </div>
                    <div id="dynamic_field">

                    </div>

                    <button type="button" name="add_more" id="add_more" class="font_size_10px text-center btn btn-sm btn-outline-primary add_more_edit float-right"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more'); ?></button>
                    <button type="button" id="add_more_new" class="font_size_10px text-center btn btn-sm btn-outline-primary add_more_new float-right">
                        <i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more'); ?>
                    </button>
                    <div class="smallspace clearfix"></div>
                    <div class="col-xs-12 text-center" id="response_status"></div>
                </div>
                <div class="modal-footer mt-2">
                    <input type="hidden" name="hidden_id" id="hidden_id" />
                    <input type="hidden" name="action" id="action" value="insert" />
                                           
                    <button type="submit" name="submit" id="submit" class="btn btn-primary btn-lg"><i class='fa fa-save'></i> <?php echo $this->lang->line('Save'); ?></button>
                    <button class="btn btn-lg btn-secondary float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel"); ?></button>
                    
                </div>
            </form>
        </div>
    </div>

</div> 


<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fa fa fa-th-large"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
         <a class="btn btn-primary" name="add" id="add"  href="#">
            <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new template"); ?>
         </a> 
        </div>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
          <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body data-card">
                <div class="table-responsive">
                  <table class="table table-bordered" id="mytable">
                    <thead>
                      <tr>
                        <th>#</th>      
                        <th><?php echo $this->lang->line("Template ID"); ?></th>      
                        <th><?php echo $this->lang->line("Template Name"); ?></th>
                        <th class="min_width_150px"><?php echo $this->lang->line("Actions"); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>             
              </div>

            </div>
          </div>
        </div>
    </div>
</section>  



<script src="<?php echo base_url('assets/js/system/instagram/auto_comment_reply_template.js');?>"></script>


<div class="modal fade" id="delete_template_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center"><?php  echo $this->lang->line("Template Delete Confirmation") ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="delete_template_modal_body">                

            </div>
        </div>
    </div>
</div>
