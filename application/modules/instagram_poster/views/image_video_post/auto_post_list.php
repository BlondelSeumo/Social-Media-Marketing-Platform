<?php $this->load->view('admin/theme/message'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/instagram/posting_style.css');?>">
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-paper-plane"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary" href="<?php echo base_url("instagram_poster/image_video_poster");?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new Post"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url("ultrapost");?>"><?php echo $this->lang->line("Facebook Poster"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
          	<div class="row">
          		<div class="col-md-9 col-12">
              	<div class="input-group mb-3 float-left" id="searchbox">
    	          		<!-- search by post type -->
  	          	  	<div class="input-group-prepend">
            	      	<select class="select2 form-control" id="post_type" name="post_type">
          	        	  <option value=""><?php echo $this->lang->line("Any Type"); ?></option>
  		          	      <option value="text_submit"><?php echo $this->lang->line("Text Post"); ?></option>
                        <option value="link_submit"><?php echo $this->lang->line("Link Post"); ?></option>
                        <option value="image_submit"><?php echo $this->lang->line("Image Post"); ?></option>
                        <option value="video_submit"><?php echo $this->lang->line("Video Post"); ?></option>
        	      		  </select>
  	          	    </div>

  					        <!-- search by page name -->
  	          	    <div class="input-group-prepend">
            	      	<?php echo $account_list; ?>
  	          	    </div>
                    <input type="text" class="form-control" id="searching" name="searching" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
  	          	  	<div class="input-group-append">
  	          	    	<button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
  	      	 	 	    </div>
            		</div>
          		</div>
          		<div class="col-md-3 col-12">
          			<a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
          		</div>
          	</div>
            <div class="table-responsive2">
            	<table class="table table-bordered" id="mytable">
                <thead>
                	<tr>
      							<th>#</th>      
      							<th><?php echo $this->lang->line("Campaign ID"); ?></th>      
      							<th><?php echo $this->lang->line("Name"); ?></th>
      							<th><?php echo $this->lang->line("Campaign type"); ?></th>
      							<th><?php echo $this->lang->line("Publisher"); ?></th>
      							<th><?php echo $this->lang->line("Post Type"); ?></th>
      							<th><?php echo $this->lang->line("Actions"); ?></th>
      							<th><?php echo $this->lang->line("Status"); ?></th>
      							<th><?php echo $this->lang->line("Scheduled at"); ?></th>
      							<th><?php echo $this->lang->line('Error Message'); ?></th>
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

<script src="<?php echo base_url('assets/js/system/instagram/posting_list.js');?>"></script>

<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="far fa-list-alt"></i> <?php echo $this->lang->line("Multimedia Post Report");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body data-card">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <input type="text" id="searching1" name="searching1" class="form-control width_200" placeholder="<?php echo $this->lang->line("Search..."); ?>">                                          
                  </div>
                  <div class="col-12">
                    <div class="table-responsive2">
                      <input type="hidden" id="put_row_id">
                      <table class="table table-bordered" id="mytable1">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("id"); ?></th>
                              <th><?php echo $this->lang->line("Publisher"); ?></th>
                              <th><?php echo $this->lang->line("Post Type"); ?></th>
                              <th><?php echo $this->lang->line("Post ID"); ?></th>
                              <th><?php echo $this->lang->line("Posting Status"); ?></th>
                              <th><?php echo $this->lang->line("Scheduled at"); ?></th>
                              <th><?php echo $this->lang->line("Error"); ?></th>
                            </tr>
                          </thead>
                      </table>
                    </div>
                  </div> 
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="embed_code_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-code"></i> <?php echo $this->lang->line("Get Embed Code");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="embed_code_content">
      
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="view_report" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg width_70_percent">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line("report of Text/Image/Link/Video Poster") ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body text-center" id="view_report_modal_body">                

            </div>
        </div>
    </div>
</div>
