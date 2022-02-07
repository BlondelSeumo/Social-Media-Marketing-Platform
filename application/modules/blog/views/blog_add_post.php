<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo site_url().'blog/posts';?>"><?php echo $this->lang->line("Blog Manager"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4><?php echo $this->lang->line("Write Your Post")?></h4>
          </div>
          <form class="form-horizontal" name="post-store" action="<?php echo site_url().'blog/add_post_action';?>" method="POST">
            <div class="card-body">
              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line("Title")?></label>
                <div class="col-sm-12 col-md-7">
                  <input type="text" id="title" name="title" value="<?php echo set_value('title');?>"  class="form-control" placeholder="<?php echo $this->lang->line("Title")?>">
                  <div class="red title_error"></div>
                </div>
              </div>

              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line("Keywords")?></label>
                <div class="col-sm-12 col-md-7">
                  <input type="text" id="keywords" name="keywords" value="<?php echo set_value('keywords');?>"  class="form-control inputtags" placeholder="<?php echo $this->lang->line("Keywords")?>">
                  <div class="red title_error"></div>
                </div>
              </div>

              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line("Category")?></label>
                <div class="col-sm-12 col-md-7">
                  <?php echo form_dropdown('category_id', $category_lists, set_value('category_id'), array('class'=>'form-control select2', 'id'=>'category_id')); ?>
                  <div class="red category_id_error"></div>
                </div>
              </div>
              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line("Content")?></label>
                <div class="col-sm-12 col-md-7">
                  <textarea class="summernote" name="body" id="body"></textarea>
                  <div class="red body_error"></div>
                </div>
              </div>
              
              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line("Thumbnail")?></label>
                <div class="col-sm-12 col-md-4">
                  <div id="dropzone" class="dropzone dz-clickable">
                    <div class="dz-default dz-message" style="">
                      <input class="form-control" name="thumbnail" id="thumbnail" placeholder="" type="hidden">
                        <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" style="font-size: 35px;color: var(--blue);"></i> <?php echo $this->lang->line('Upload'); ?></span>
                    </div>
                 </div>
                  <div class="red thumbnail_error"></div>
                  <span class="text-muted"><?php echo $this->lang->line("Maximum Size 1MB, Recommended dimension 750x400");?></span>
                </div>
              </div>
              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line("Tags")?></label>
                <div class="col-sm-12 col-md-7">
                  <?php echo form_dropdown('tags[]', $tag_lists, set_value('tags'), array('class'=>'form-control select2', 'id'=>'tags', 'multiple'=>true)); ?>
                  <span class="red tags_error"></span>
                </div>
              </div>

              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><?php echo $this->lang->line('Status'); ?></label>
                <div class="col-sm-12 col-md-7">
                  <select class="form-control selectric" name="status" id="status">
                    <option value="1"><?php echo $this->lang->line('Publish'); ?></option>
                    <option value="0"><?php echo $this->lang->line('Draft'); ?></option>
                    <option value="2"><?php echo $this->lang->line('Pending'); ?></option>
                  </select>
                  <span class="red status_error"></span>
                </div>
              </div>
              <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                <div class="col-sm-12 col-md-7">
                  <button name="submit" type="submit" id="save_post" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
                  <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("blog/posts",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
                </div>
              </div>
            </div><!--/.card-body-->
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  $(function(){
    $(".inputtags").tagsinput('items');
  });
  var base_url="<?php echo site_url(); ?>";

  $(document.body).on('submit', 'form[name="post-store"]', function(e) {
    e.preventDefault();

    $("#save_post").addClass("btn-progress");
    $('.form-control').removeClass('is-invalid');
    var action = $(this).attr('action');
    var formData = $(this).serialize();

    $.ajax({
      type:'POST' ,
      url:action,
      data: formData,
      dataType : 'JSON',
      success:function(response)
      {
        $("#save_post").removeClass("btn-progress");

        if (response.status == '0'){
          $.each(response.errors, function(key, value){
            $('.'+key.replace('[]', '')+'_error').html(value);
          });
        }

        if(response.status == "1") {
          swal('<?php echo $this->lang->line("Success")?>',response.message, 'success')
          .then((value) => {
            window.location.replace(base_url+"blog/posts");
          });
        } 
          
        if(response.status == "2"){
          swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
        }
      }
    });
  });
</script>

<script type="text/javascript">
  $(function(){
    Dropzone.autoDiscover = false;
    $("#dropzone").dropzone({ 
      url: "<?php echo site_url();?>blog/upload_post_thumbnail",
      maxFilesize:1,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".jpeg,.jpg,.png,.gif",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response){
          $("#thumbnail").val(eval(response));
      },
      removedfile: function(file) {
          var name = $("#thumbnail").val();
          if(name !="")
          {
            $(".dz-preview").remove();
            $.ajax({
              type: 'POST',
              url: '<?php echo site_url();?>blog/delete_post_thumbnail',
              data: {op: "delete",name: name},
              success: function(data){
                $("#thumbnail").val('');
              }
            });
          }
          else
          {
            $(".dz-preview").remove();
          }
      },
    });
  });
</script>