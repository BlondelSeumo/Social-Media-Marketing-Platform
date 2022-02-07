<section class="section">
  <div class="section-header mb-3 pr-0">
    <h1><i class="fab fa-instagram"></i> <?php  echo $this->lang->line('Live Chat'); ?> </h1>
    <?php if(!empty($page_info)) : ?>
    <div class="section-header-breadcrumb">
      <div class="section-header-breadcrumb">
          <select name="page_id" id="page_id" class="form-control select2">
            <option value=""><?php echo $this->lang->line('Select'); ?></option>
            <?php foreach($page_info as $page) : ?>
              <option value="<?php echo $page['id'] ?>" <?php if($page['id']==$page_table_id) echo 'selected'; ?>><?php echo $page['insta_username']; ?></option>

            <?php endforeach; ?>
          </select>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="section-body">

    <?php if(empty($page_info)) : ?>
      <div class="card" id="nodata">
        <div class="card-body">
          <div class="empty-state">
            <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
             <h2 class="mt-0"><?php echo $this->lang->line("We could not find any page.");?></h2>
            <p class="lead"><?php echo $this->lang->line("Please import account if you have not imported yet.")."<br>".$this->lang->line("If you have already imported account then enable bot connection for one or more page to continue.") ?></p>
            <a href="<?php echo base_url('social_accounts'); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Continue");?></a>
          </div>
        </div>
      </div>
    <?php else : ?>
    
    <div class="row main_row">
      <div class="col-12 col-sm-6 col-lg-3 no_padding_col">
        <div class="card card-success no_radius">
          <div class="card-header">
            <h4 class="w-100 pr-0">
             <span class="float-left pr-2"> <?php echo $this->lang->line('Subscribers'); ?></span>
              <input type="text" class="form-control float-left search_list" autofocus onkeyup="search_in_subscriber_ul(this,'put_content')" placeholder="<?php echo $this->lang->line("Search...") ?>">
              <a class="btn btn-outline-primary btn-sm float-right px-2 py-0" data-toggle="tooltip" title="<?php echo $this->lang->line("Reload") ?>" name="refresh_data" id="refresh_data" href="#" page_table_id="<?php echo $page_table_id; ?>"> <i class="fas fa-sync"></i>
              </a>
            </h4>
          </div>
          <div class="card-body p-0">
            <ul class="list-unstyled list-unstyled-border nicescroll" id="put_content">              
            </ul>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-6 no_padding_col">
        <div class="card chat-box card-success no_radius" style="min-height: 536px" id="mychatbox2">
           <div class="card-header">
              <h4 class="w-100 pr-0">                
                <span class="float-left pr-2"><i class="fas fa-circle text-success mr-2" title="" data-toggle="tooltip" data-original-title="Online"></i><span id="chat_with"></span></span>
                <input type="text" class="form-control float-left search_list" onkeyup="search_in_div(this,'conversation_modal_body')" placeholder="<?php echo $this->lang->line("Search...") ?>">
                <select name="refresh_seconds" id="refresh_interval" class="form-control float-right py-0 d-none d-sm-inline">
                  <option value="10000"> <?php echo $this->lang->line('Reload');?></option>
                  <option value="5000"> 5 <?php echo $this->lang->line('Sec');?></option>
                  <option value="10000"> 10 <?php echo $this->lang->line('Sec');?></option>
                  <option value="15000"> 15 <?php echo $this->lang->line('Sec');?></option>
                  <option value="20000"> 20 <?php echo $this->lang->line('Sec');?></option>
                  <option value="30000"> 30 <?php echo $this->lang->line('Sec');?></option>
                  <option value="60000"> 60 <?php echo $this->lang->line('Sec');?></option>
                </select>
              </h4>
              
           </div>
           <div class="card-body chat-content2  bg-info-light-alt gradient nicescroll" style="overflow-y: auto;" id="conversation_modal_body">              
           </div>
           <div class="card-footer chat-form">
              <form id="chat-form2">

                <div class="row">
                  <div class="col-2 col-md-1 no_padding_col_right mt-2">
                    <div class="input-group-append">
                      <button type="button" title="<?php echo $this->lang->line('Send Postback Template');?>" class="btn btn-danger" id="postback_reply_button" data-toggle="modal" data-target="#postbackModal">
                        <i class="fas fa-robot"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-10 col-md-3 no_padding_col_right mt-2">
                    <?php echo form_dropdown('message_tag', $tag_list, 'HUMAN_AGENT','class="form-control select2" id="message_tag" style="width: 100% !important;height:50px !important;"'); ?>
                  </div>
                  <div class="col-12 col-md-8 no_padding_col_left mt-2">
                    <div class="input-group">                  
                       <input type="text" id="reply_message" class="form-control border no_radius" placeholder="<?php echo $this->lang->line('Type a message..');?>">
                       
                      <div class="input-group-append">
                        <button class="btn btn-primary" id="final_reply_button">
                          <i class="far fa-paper-plane"></i>
                        </button>
                      </div>
                    </div>  
                  </div>
                </div>                          
                 
                 
              </form>
           </div>
        </div>
      </div>
      <div class="col-12 col-sm-12 col-lg-3">
        <div class="card card-primary" style="min-height: 520px">
          <!-- <div class="card-header">
            <h4 class="w-100">
              <?php echo $this->lang->line('Actions'); ?>
            </h4>
          </div> -->
          <!-- <div class="card-body p-0"> -->
            <div id="subscriber_action">
            </div>
          <!-- </div> -->
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
<script>
  var social_media = 'ig';
  var get_post_conversation_url = 'get_post_conversation_instagram';
  var get_pages_conversation_url = 'get_pages_conversation_instagram';
  var reply_to_conversation_url = 'reply_to_conversation_instagram';
</script>


<?php include(FCPATH.'application/views/message_manager/message_dashboard_common_js.php');?>