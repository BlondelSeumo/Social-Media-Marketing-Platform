<?php 
  $this->load->view("include/upload_js"); 

  $image_upload_limit = 1; 
  if($this->config->item('messengerbot_image_upload_limit') != '')
  $image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 
?>
<style>
  .category_sidebar {
    position:sticky;
    top: 0;
  }

  .article.article-style-c { 
    border-radius: 20px !important;
  }

  .article.article-style-c .article-header {
    height: 150px !important;
    background-color: none !important;
  }

  .article.article-style-c .article-details .article-category { margin-bottom: 10px !important; }

  .article .article-header .article-image { 
    border-top-right-radius: 20px;
    border-top-left-radius: 20px; 
  }

  .article .article-details {
    padding: 15px;
    line-height: 0px !important;
    background-color: transparent;
  }

  .article.article-style-c .template_description { 
    line-height: 20px; 
    word-break: break-all;
    height: 40px;
  }

  .list-group-flush .list-group-item {
    border: 1px solid rgb(181 170 170 / 13%);
    border-right: 0 !important;
    border-left: 0 !important;
    color: #615c5c;
    font-weight: 400;
    font-size: 14px;
    letter-spacing: 0px;
    padding: 10px;
  }

  .list-group-flush .list-group-item a {
    color: #615c5c;
    text-decoration: none;
  }

  .list-group-flush .list-group-item:first-child {
    border-top-right-radius: 5px;
    border-top-left-radius: 5px;
  }

  .list-group-flush .list-group-item:last-child {
    border-bottom-right-radius: 5px;
    border-bottom-left-radius: 5px;
  }

  .list-group-flush .list-group-item:hover { 
    color:var(--blue); 
  }

  .list-group-flush .list-group-item.active  { 
    background-color: var(--blue);
  }

  .list-group-flush .list-group-item.active { 
    color: #fff;
  }

  .list-group-flush .list-group-item:hover
  {
    -webkit-animation: swing 1s ease;
    animation: swing 1s ease;
    -webkit-animation-iteration-count: 1;
    animation-iteration-count: 1;
  }

  #bot_category { width: 100% !important; }

  @keyframes swing
  {
      15%
      {
          -webkit-transform: translateX(5px);
          transform: translateX(5px);
      }
      30%
      {
          -webkit-transform: translateX(-5px);
          transform: translateX(-5px);
      }
      50%
      {
          -webkit-transform: translateX(3px);
          transform: translateX(3px);
      }
      65%
      {
          -webkit-transform: translateX(-3px);
          transform: translateX(-3px);
      }
      80%
      {
          -webkit-transform: translateX(2px);
          transform: translateX(2px);
      }
      100%
      {
          -webkit-transform: translateX(0);
          transform: translateX(0);
      }
  }

  .dotted_elipse i { font-size:18px !important; }
  .pagination {
    align-items: center;
    justify-content: center;
  }

</style>

<section class="section">
  <div class="section-header">
    <h1><i class="fa fa-th-large"></i> <?php echo $this->lang->line('Saved Templates'); ?> </h1>
    <div class="section-header-button">
      <a class="btn btn-primary export_bot" href="#">
        <i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line("Upload Template"); ?>
      </a>
  </div>
    
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot/bot_menu_section'); ?>"><?php echo $this->lang->line("Messenger Bot Features"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $category_id = isset($_GET['category']) ? $_GET['category']:""; ?>
  <div class="section-body">

    <div class="row">
      <div class="col-12 col-md-2">
        <div class="category_sidebar">
            <ul class="list-group  list-group-flush">
              <a class="list-group-item pointer <?php if($category_id == '') echo 'active'; ?>" href="<?php echo base_url("messenger_bot/saved_templates2"); ?>"><i class="fas fa-book-open"></i> <?php echo $this->lang->line('All Categories'); ?></a>
              <?php foreach ($category_list as $category) { ?>
                <a class="list-group-item pointer <?php if($category_id == $category['id']) echo 'active'; ?>" href="<?php echo base_url("messenger_bot/saved_templates2/?category={$category['id']}"); ?>" title="<?php echo $category['category_name']; ?>">
                  <i class="fas fa-book-open"></i> <?php echo (strlen($category['category_name']) > 10) ? substr($category['category_name'], 0, 12).'...' : $category['category_name']; ?></a>
                </a>
              <?php } ?>
            </ul>
        </div>
      </div>

      <div class="col-12 col-md-10">
        <div class="row">
          <?php foreach ($template_lists as $template) : 
            $preview_img = isset($template["preview_image"]) ? $template["preview_image"] : "";
            if($preview_img != '' && file_exists('upload/image/'.$template['user_id'].'/'.$preview_img)) {
                $preview_img = base_url('upload/image/'.$template['user_id'].'/'.$preview_img);
            }
            else {
                $preview_img = base_url().'assets/img/news/img01.jpg';
            }
            ?>

          <div class="col-12 col-md-4">
            <article class="article article-style-c">
              <div class="article-header template_header">
                <div class="article-image" data-background="<?php echo $preview_img; ?>">
                </div>
              </div>
              <div class="article-details template_details">
                <div class="article-category">
                  <a><?php echo (strlen($template['category_name']) > 12) ? substr($template['category_name'], 0, 10).'...' : $template['category_name']; ?></a>
                  <div class="bullet"></div> 
                  <a><?php echo date_time_calculator($template['saved_at'],true) ?></a>
                </div>
                <div class="article-title template_title">
                  <h2>
                    <a href="#"><?php echo (strlen($template['template_name']) > 24) ? substr($template['template_name'], 0, 24).'...' : $template['template_name']; ?></a>
                    <div class="dropdown d-inline dropright float-right">
                      <a class="pointer dropdown-toggle no_caret dotted_elipse" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                      </a>
                      <?php $action_width = (4*47)+20; ?>
                      <div class="dropdown-menu mini_dropdown text-center" style="width:<?php echo $action_width.'px !important'; ?>">
                        <a target="_BLANK" data-toggle='tooltip' title='<?php echo $this->lang->line('View Template'); ?>' href='<?php echo base_url('messenger_bot/saved_template_view/'.$template['id']); ?>' class='btn btn-circle btn-outline-primary'><i class='fas fa-eye'></i></a>
                        <?php if($template['user_id'] == $this->user_id) { ?>
                        <a data-toggle='tooltip' title='<?php echo $this->lang->line('Edit Template'); ?>' href='' class='btn btn-circle btn-outline-warning export_bot_edit' table_id="<?php echo $template['id']?>"><i class='fas fa-edit'></i></a>
                        <?php } ?>
                        <a data-toggle='tooltip' title='<?php echo $this->lang->line('Download Template'); ?>' href='<?php echo base_url("messenger_bot/export_bot_download/".$template['id'])?>' class='btn btn-circle btn-outline-success' table_id="<?php echo $template['id']?>"><i class='fas fa-cloud-download-alt'></i></a>
                        <a data-toggle='tooltip' title='<?php echo $this->lang->line('Delete Template'); ?>' href='' class='btn btn-circle btn-outline-danger delete_template' table_id="<?php echo $template['id']?>"><i class='fas fa-trash-alt'></i></a>
                      </div>
                    </div>
                  </h2>
                </div>
                <div class="template_description text-muted"><?php echo (strlen($template['description']) > 70) ? substr($template['description'], 0, 60).'...' : $template['description']; ?></div>
                <hr>
                <div class="text-center">
                  <button class="btn btn-primary btn-block install_template" current_template_id="<?php echo $template['id']; ?>"><?php echo $this->lang->line('Install Template'); ?></button>
                </div>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
        </div>
        <div class="row">
          <div class="col-12">
            <?php echo $page_links; ?>
          </div>
        </div>
      </div>
    </div>

  </div>

</section>


<div class="modal fade" id="export_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <h5 class="modal-title"><i class="fa fa-file-export"></i> <?php echo $this->lang->line("Upload Template");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="export_bot_modal_body">             

                <form id="export_bot_form" method="POST">
                  <div class="col-12">
                    <div class="well text-justify" style="border:1px solid var(--blue);padding:15px;color:var(--blue);">
                      <?php echo $this->lang->line("Webview form will not be exported/imported. If bot settings have webview form created, then after importing that bot settings for a page, you will need to create new form & change the form URL by the new URL for that page."); ?>
                    </div>
                  </div><br>
                  <!-- <input type="hidden" name="export_id" id="export_id"> -->
                  <div class="col-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Name');?> *</label>
                      <input type="text" name="template_name" class="form-control" id="template_name">                    
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Description');?> </label>
                      <textarea type="text" rows="4" name="template_description" class="form-control" id="template_description"></textarea>                    
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label> <?php echo $this->lang->line('Template Category'); ?></label>
                      <small class="blue float-right pointer" id="create_category"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Create category'); ?></small>
                      <select name="bot_category" id="bot_category" class="form-control select2 bot_category" style="width:100% !important">
                        <option value=""><?php echo $this->lang->line('Select Category'); ?></option>
                        <?php foreach ($category_list as $key => $value) {
                          echo '<option value="'.$value['id'].'">'.$value['category_name'].'</option>';  
                        } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="row">
                      <div class="col-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line('Template Preview Image');?>
                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("Template Preview Image"); ?>" data-content="<?php echo $this->lang->line("Upload a preview image for this template and the image will be showed as preview image of the template.").'Square image like (400x400) is recommended.'; ?>"><i class='fa fa-info-circle'></i> </a>&nbsp;
                            <span style="cursor:pointer;" title="<?php echo $this->lang->line('Preview'); ?>" class="badge badge-status blue load_preview_modal float-right" item_type="image" file_path=""><i class="fa fa-eye"></i></span>
                          </label>
                          

                          <input type="hidden" name="template_preview_image" class="form-control" id="template_preview_image">                   
                          <div id="template_preview_image_div"><?php echo $this->lang->line("upload") ?></div>
                        </div>
                      </div>

                      <div class="col-6 type3">
                        <div class="form-group">
                          <label><?php echo $this->lang->line('Upload Template JSON');?></label>
                          <div class="form-group">    
                            <div id="json_upload"><?php echo $this->lang->line('Upload');?></div>
                            <input type="hidden" id="json_upload_input" name="json_upload_input">
                          </div>                
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php if($this->session->userdata("user_type")=='Admin'){ ?>
                    <div class="col-12">

                      <div class="form-group">
                        <div class="control-label"><?php echo $this->lang->line('Template Access'); ?> *</div>
                        <div class="custom-switches-stacked mt-2">
                          <label class="custom-switch">
                            <input type="radio" name="template_access" value="private" id="only_me_input" class="custom-switch-input" checked>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line("Only me"); ?></span>
                          </label>
                          <label class="custom-switch">
                            <input type="radio" name="template_access" value="public" id="other_user_input" class="custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line("Me as well as other users"); ?></span>
                          </label>
                        </div>                
                      </div>

                    </div>

                    <div class="col-12 hidden" id="allowed_package_ids_con">
                      <div class="form-group">
                        <label><?php echo $this->lang->line('Choose User Packages');?> *</label><br/>
                        <?php echo form_dropdown('allowed_package_ids[]', $package_list, '','class="form-control select2" id="allowed_package_ids" multiple'); ?>
                      </div>
                    </div>
                  <?php } ?>
                  
                  <div class="row">
                    <div class="col-6">
                      <a href="#" id="export_bot_submit" class="btn btn-primary btn-lg"><i class="fa fa-file-export"></i> <?php echo $this->lang->line("Upload Template");?></a>
                    </div>                
                    <div class="col-6">
                      <a href="#" id="cancel_bot_submit" class="btn btn-secondary btn-lg float-right"><i class="fa fa-close"></i> <?php echo $this->lang->line("Cancel");?></a>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_export_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <h5 class="modal-title"><i class="fa fa-edit"></i> <?php echo $this->lang->line("Edit Saved Template");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="edit_export_bot_modal_body">
              <br><div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div></br>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modal_for_preview" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('item preview'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div id="image_preview_div_modal" style="display: none;">
          <img id="modal_preview_image" width="100%" src="">
        </div>
        <div id="video_preview_div_modal" style="display: none;">
          <video width="100%" id="modal_preview_video" controls>
            
          </video>
        </div>
        <div id="audio_preview_div_modal" style="display: none;">
          <audio width="100%" id="modal_preview_audio" controls>
            
          </audio>
        </div>
        <div>
          <input class="form-control" type="text" id="preview_text_field">
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="install_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line('Install Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <form action="#" id="install_template_form" method="POST">
              <input type="hidden" name="template_id" id="template_id" value="">
              <div class="form-group">
                <label for=""> <?php echo $this->lang->line('Intall to Page'); ?> </label>
                <select name="page_id" id="page_id" class="form-control select2" style="width:100% !important;">
                  <option value=""><?php echo $this->lang->line('Select Page'); ?></option>
                  <?php foreach ($page_lists as $page) {
                    echo "<option value={$page['id']}>{$page['page_name']} [{$page['account_name']}]</option>";
                  } ?>
                </select>
              </div>

              <div>
                <button class="btn btn-primary btn-block btn-lg install_template_action"><i class="fas fa-cloud-download"></i><?php echo $this->lang->line('Install'); ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript">
  $(document).ready(function() { 
      // setTimeout(function(){ $("#collapse_me_plz").click();}, 100);
      $("#select2").select2();
      $(document).on('click', '#cancel_bot_submit', function(e){
        e.preventDefault();
        $("#export_bot_modal").modal('hide');
      });
      $(document).on('click', '#cancel_bot_submit2', function(e){
        e.preventDefault();
        $("#edit_export_bot_modal").modal('hide');
      });
      $('[data-toggle="tooltip"]').tooltip()
      $("#allowed_package_ids").select2({ width: "100%" });

      $(document).on('click','.export_bot',function(e){
        e.preventDefault();
        var table_id = $(this).attr('table_id');
        // $("#export_id").val(table_id);

        $('#allowed_package_ids').val(null).trigger('change');
        $("#template_name").val('');
        $("#template_description").val('');
        $("#template_preview_image").val('');
        $("#only_me_input").prop("checked", true);
        $("#other_user_input").prop("checked", false); 
        $("#allowed_package_ids_con").addClass('hidden')

        $("#export_bot_modal").modal();
      });

      $(document).on('change','input[name=template_access]',function(){
        var template_access = $(this).val();
        if(template_access=='private') $("#allowed_package_ids_con").addClass('hidden');
        else $("#allowed_package_ids_con").removeClass('hidden');
      });


      $(document).on('change','input[name=template_access2]',function(){
        var template_access = $(this).val();
        if(template_access=='private') $("#allowed_package_ids_con2").addClass('hidden');
        else $("#allowed_package_ids_con2").removeClass('hidden');
      });

      $("#json_upload").uploadFile({
          url:base_url+"messenger_bot/upload_json_template",
          fileName:"myfile",
          showPreview:false,
          returnType: "json",
          dragDrop: true,
          showDelete: true,
          multiple:false,
          maxFileCount:1, 
          acceptFiles:".json",
          deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('messenger_bot/upload_json_template_delete');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) { 
                      $("#json_upload_input").val(''); 
                      $(".type1,.type2").show();                      
                    });
             
           },
           onSuccess:function(files,data,xhr,pd)
             {
                 var data_modified = data;
                 $("#json_upload_input").val(data_modified);
                 $(".type1,.type2").hide();
             }
      });



      var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
      var image_upload_limit = "<?php echo $image_upload_limit; ?>";
      $("#template_preview_image_div").uploadFile({
        url:base_url+"messenger_bot/upload_image_only",
        fileName:"myfile",
        maxFileSize:image_upload_limit*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
        deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_file');?>";
            $.post(delete_url, {op: "delete",name: data},
                function (resp,textStatus, jqXHR) {
                  $("#template_preview_image").val('');                    
                });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
               $("#template_preview_image").val(data_modified);
           }
      });

      $(document).on('click','.load_preview_modal',function(e){
        e.preventDefault();
        var item_type = $(this).attr('item_type');
        var file_path = $(this).parent().next().val();
        var user_id = "<?php echo $this->user_id; ?>";

        var res = file_path.match(/http/g);
        if(file_path != '' && res === null)
          file_path = base_url+"upload/image/"+user_id+"/"+file_path;

        $("#preview_text_field").val(file_path);
        if(item_type == 'image')
        {
          $("#modal_preview_image").attr('src',file_path);
          $("#image_preview_div_modal").show();
          $("#video_preview_div_modal").hide();
          $("#audio_preview_div_modal").hide();
          
        }
        $("#modal_for_preview").modal();
      });

      $(document).on('click','.load_preview_modal_edit',function(e){
        e.preventDefault();
        var item_type = $(this).attr('item_type');
        var file_path = $(this).parent().next().val();
        var user_id = "<?php echo $this->user_id; ?>";

        var res = file_path.match(/http/g);
        if(file_path != '' && res === null)
          file_path = base_url+"upload/image/"+user_id+"/"+file_path;

        $("#preview_text_field").val(file_path);
        if(item_type == 'image')
        {
          $("#modal_preview_image").attr('src',file_path);
          $("#image_preview_div_modal").show();
          $("#video_preview_div_modal").hide();
          $("#audio_preview_div_modal").hide();
          
        }
        $("#modal_for_preview").modal();
      });

      $(document).on('click','.export_bot_edit',function(e){
        e.preventDefault();
        var table_id = $(this).attr('table_id');
        $("#edit_export_bot_modal").modal();

        $.ajax({
          type:'POST' ,
          url:"<?php echo site_url();?>messenger_bot/get_bot_template_form",
          data:{table_id:table_id},
          success:function(response){ 
             $('#edit_export_bot_modal_body').html(response);  
          }
        });
      });


      $(document).on('click','#export_bot_submit',function(e){
        e.preventDefault();
        var template_name = $("#template_name").val();
        var template_access = $('input[name=template_access]:checked').val();
        var allowed_package_ids = $("#allowed_package_ids").val();
        var bot_category = $("#bot_category").val();
        var filename = $("#json_upload_input").val();

        if(template_name=="")
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide template name.');?>", 'warning');
          return;
        }

        if(bot_category=="")
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select template category.');?>", 'warning');
          return;
        }

        if(filename=="")
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Upload your template json file.');?>", 'warning');
          return;
        }

        if(template_access=="public" && allowed_package_ids==null)
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('You must choose user packages to give them template access.');?>", 'warning');
          return;
        }

        $(this).addClass('btn-progress');
        var queryString = new FormData($("#export_bot_form")[0]);
        $.ajax({
              type:'POST' ,
              url: base_url+"messenger_bot/save_messenger_template_info",
              dataType: 'JSON',
              data: queryString,
              cache: false,
              contentType: false,
              processData: false,
              context: this,
              success:function(response)
              { 
                $(this).removeClass('btn-progress');
                var report_link = base_url+'messenger_bot/saved_templates2';
                var success_message=response.message;
                var span = document.createElement("span");
                span.innerHTML = success_message;
                swal({ title:'<?php echo $this->lang->line("Template Upload Status"); ?>', content:span,icon:'success'}).then((value) => {location.reload();});
              }
        });

      });


      $(document).on('click','#update_bot_submit',function(e){
        e.preventDefault();
        var template_name = $("#template_name2").val();
        var template_access = $('input[name=template_access2]:checked').val();
        var allowed_package_ids = $("#allowed_package_ids2").val();
        var bot_category = $("#bot_category2").val();
        var filename = $("#json_upload_input_edit").val();

        if(template_name=="")
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide template name.');?>", 'warning');
          return;
        }

        if(bot_category=="")
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select template category.');?>", 'warning');
          return;
        }

        if(template_access=="public" && allowed_package_ids==null)
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('You must choose user packages to give them template access.');?>", 'warning');
          return;
        }

        $(this).addClass('btn-progress');
        var queryString = new FormData($("#export_bot_form_edit")[0]);
        $.ajax({
              type:'POST' ,
              url: base_url+"messenger_bot/update_messenger_template_info",
              dataType: 'JSON',
              data: queryString,
              cache: false,
              contentType: false,
              processData: false,
              context: this,
              success:function(response)
              { 
                $(this).removeClass('btn-progress');
                var report_link = base_url+'messenger_bot/saved_templates2';
                var success_message=response.message;
                var span = document.createElement("span");
                span.innerHTML = success_message;
                swal({ title:'<?php echo $this->lang->line("Template Upload Status"); ?>', content:span,icon:'success'}).then((value) => {location.reload();});
              }
        });

      });

      $(document).on('click', '.install_template', function(event) {
        event.preventDefault();
        var template_id = $(this).attr("current_template_id");
        $("#template_id").val(template_id)
        $("#install_template_modal").modal();

      });



      $(document).on('click','.install_template_action',function(e){
        e.preventDefault();
        var template_id = $("#template_id").val();
        var page_id = $("#page_id").val();

        if(template_id=="")
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('You must select a template or upload one.');?>", 'warning');
          return;
        }

        $(this).addClass('btn-progress');

        var queryString = new FormData($("#install_template_form")[0]);
        $.ajax({
              type:'POST' ,
              url: base_url+"messenger_bot/import_bot_check",
              dataType: 'JSON',
              data: {import_id:page_id, template_id:template_id,json_upload_input:""},
              context: this,
              success:function(response)
              { 
                $(this).removeClass('btn-progress');
                if(response.status=='1')
                {
                  // var json_upload_input=response.json_upload_input;
                  swal({
                    title: '<?php echo $this->lang->line("Warning!"); ?>',
                    text: response.message,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                  })
                  .then((willDelete) => {
                    if (willDelete) 
                    {
                      $(this).addClass('btn-progress');
                      $.ajax({
                        context: this,
                        type:'POST' ,
                        url:"<?php echo site_url();?>messenger_bot/import_bot",
                        // dataType: 'json',
                        data:{json_upload_input:'',page_id:response.page_id,template_id:response.template_id},
                        success:function(response2){ 
                          $(this).removeClass('btn-progress');
                          var success_message=response2;
                          var span = document.createElement("span");
                          span.innerHTML = success_message;
                          swal({ title:'<?php echo $this->lang->line("Import Status"); ?>', content:span,icon:'success'}).then((value) => {location.reload();});
                        }
                      });
                    } 
                  });
                }
                else
                {
                  swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                }
              }
        });
      });

      $(document).on('click', '.install_template', function(event) {
        event.preventDefault();
        var page_id = $("#page_id").val();
        var template_id = $("#installed_template_id").val();

        $.ajax({
          url: base_url+'messenger_bot/import_bot',
          type: 'POST',
          data: {page_id: page_id,template_id:template_id},
          success:function(response){

          }
        })
        
      });


      // create an new category and put inside category list
      $(document).on('click','#create_category',function(e){
        e.preventDefault();

        swal("<?php echo $this->lang->line('Category Name'); ?>", {
          content: "input",
          button: {text: "<?php echo $this->lang->line('New Category'); ?>"},
        })
        .then((value) => {
          var category_name = `${value}`;
          if(category_name!="" && category_name!='null')
          {
            $("#save_changes").addClass("btn-progress");
            $.ajax({
              context: this,
              type:'POST',
              dataType:'JSON',
              url:"<?php echo site_url();?>messenger_bot/add_template_Category",
              data:{category_name:category_name},
              success:function(response){

                 $("#save_changes").removeClass("btn-progress");

                 if(response.error) {
                    var span = document.createElement("span");
                    span.innerHTML = response.error;

                    swal({
                      icon: 'error',
                      title: '<?php echo $this->lang->line('Error'); ?>',
                      content:span,
                    });

                 } else {
                    var newOption = new Option(response.text, response.id, true, true);
                    $('#bot_category').append(newOption).trigger('change');
                  }
              }
            });
          }
        });

      });


      // create an new group and put inside group list
      $(document).on('click','#create_category2',function(e){
        e.preventDefault();

        swal("<?php echo $this->lang->line('Category Name'); ?>", {
          content: "input",
          button: {text: "<?php echo $this->lang->line('New Category'); ?>"},
        })
        .then((value) => {
          var category_name = `${value}`;
          if(category_name!="" && category_name!='null')
          {
            $("#save_changes").addClass("btn-progress");
            $.ajax({
              context: this,
              type:'POST',
              dataType:'JSON',
              url:"<?php echo site_url();?>messenger_bot/add_template_Category",
              data:{category_name:category_name},
              success:function(response){

                 $("#save_changes").removeClass("btn-progress");

                 if(response.error) {
                    var span = document.createElement("span");
                    span.innerHTML = response.error;

                    swal({
                      icon: 'error',
                      title: '<?php echo $this->lang->line('Error'); ?>',
                      content:span,
                    });

                 } else {
                    var newOption = new Option(response.text, response.id, true, true);
                    $('#bot_category2').append(newOption).trigger('change');
                  }
              }
            });
          }
        });

      });

      $(document).on('click','.delete_template',function(e){
        e.preventDefault();
        swal({
          title: '<?php echo $this->lang->line("Warning!"); ?>',
          text: '<?php echo $this->lang->line("Do you really want to delete this template?"); ?>',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) 
          {
            var base_url = '<?php echo site_url();?>';
            $(this).addClass('btn-progress');
            $(this).removeClass('btn-circle');

            var id = $(this).attr('table_id');

            $.ajax({
              context: this,
              type:'POST' ,
              url:"<?php echo site_url();?>messenger_bot/delete_template",
              dataType: 'json',
              data: {id:id},
              context: this,
              success:function(response){ 
                $(this).removeClass('btn-progress');
                $(this).addClass('btn-circle');
                var report_link = base_url+'messenger_bot/saved_templates2';
                if(response == '1')
                {
                  swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Template has been deleted successfully."); ?>', 'success').then((value) => {window.location.href=report_link;});
                  
                }
                else
                {
                  iziToast.error({title: '',message: '<?php echo $this->lang->line("Something went wrong."); ?>',position: 'bottomRight'});
                }
              }
            });
          } 
        });


      });






  });
</script>