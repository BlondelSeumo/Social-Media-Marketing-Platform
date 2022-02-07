<section class="section section_custom pt-1">
  <div class="section-header d-none">
    <h1><i class="fas fa-columns"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
        <a class="btn btn-primary" href="#" id="add_new_row"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Add Category'); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("Category"); ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card no_shadow">
          <div class="card-body data-card p-0 pt-1 pr-3">

            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th style="vertical-align:middle;width:20px">
                        <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                    </th>
                    <th><?php echo $this->lang->line("Serial"); ?></th>
                    <th><?php echo $this->lang->line("Thumbnail"); ?></th>
                    <th><?php echo $this->lang->line("Category"); ?></th>
                    <th><?php echo $this->lang->line("Status"); ?></th>
                    <th><?php echo $this->lang->line("Actions"); ?></th>
                    <th><?php echo $this->lang->line("Store"); ?></th>
                    <th><?php echo $this->lang->line("Updated at"); ?></th>
                  </tr>
                </thead>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>



<?php $drop_menu = '<a class="btn btn-primary float-right btn-lg ml-1" href="#" id="add_new_row"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add').'</a> <a class="btn btn-outline-primary float-right btn-lg" href=""  data-toggle="modal" data-target="#sort_modal"><i class="fas fa-sort"></i> '.$this->lang->line('Sort').'</a> ';?>
<script>       
  var base_url="<?php echo site_url(); ?>";
 
  $(document).ready(function() {

    $('[data-toggle=\"tooltip\"]').tooltip();

    var drop_menu = '<?php echo $drop_menu;?>';
    setTimeout(function(){ 
      $("#mytable_filter").append(drop_menu);
    }, 1000);


    var perscroll;
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 2, "asc" ]],
        pageLength: 10,
        ajax: {
            url: base_url+'ecommerce/category_list_data',
            type: 'POST'
        },          
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
          {
              targets: [1,2,7],
              visible: false
          },
          {
              targets: [3,4,5,6,7],
              className: 'text-center'
          },
          {
              targets: [0,1,3,6],
              sortable: false
          }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
              if (perscroll) perscroll.destroy();
              perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
            if(areWeUsingScroll)
            {
              if (perscroll) perscroll.destroy();
              perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }

    });

    $(document).on('click', '#add_new_row', function(event) {
        event.preventDefault();
        $('#thumb-dropzone .dz-preview').remove();
        $('#thumb-dropzone').removeClass('dz-started dz-max-files-reached');
        // Clears added file
        Dropzone.forElement('#thumb-dropzone').removeAllFiles(true);
        $("#add_row_form_modal").modal();
    });

    $(document).on('click', '#save_row', function(event) {
        event.preventDefault();

        var store_id = $("#store_id").val();
        var category_name = $("#category_name").val();

        if(store_id == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Store is required"); ?>', 'warning');
            return;
        }  

        if(category_name == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Category name is required"); ?>', 'warning');
            return;
        }      

        $(this).addClass('btn-progress')
        var that = $(this);

        var alldatas = new FormData($("#row_add_form")[0]);

        $.ajax({
            url: base_url+'ecommerce/ajax_create_new_category',
            type: 'POST',
            dataType: 'JSON',
            data: alldatas,
            cache: false,
            contentType: false,
            processData: false,
            success:function(response)
            {
                $(that).removeClass('btn-progress');

                if(response.status == "1")
                {
                    iziToast.success({title: '',message: response.message,position: 'bottomRight'});

                } else 
                {
                    iziToast.error({title: '',message: response.message,position: 'bottomRight'});
                }
                $("#add_row_form_modal").modal('hide');

            }
        })

    });

    $(document).on('click', '.edit_row', function(event) {
        event.preventDefault();
        $("#update_row_form_modal").modal();

        var table_id = $(this).attr("table_id");
        var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
        $("#update_contact_modal_body").html(loading);

        $.ajax({
            url: base_url+'ecommerce/ajax_get_category_update_info',
            type: 'POST',
            data: {table_id:table_id},
            success:function(response)
            {
                $("#update_row_modal_body").html(response);
            }
        })
    });


    $(document).on('click', '#update_row', function(event) {
        event.preventDefault();

        var category_name = $("#category_name2").val();

        if(category_name == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Category name is required"); ?>', 'warning');
            return;
        }

        $(this).addClass('btn-progress')
        var that = $(this);


        var alldatas = new FormData($("#row_update_form")[0]);

        $.ajax({
            url: base_url+'ecommerce/ajax_update_category',
            type: 'POST',
            dataType: 'JSON',
            data: alldatas,
            cache: false,
            contentType: false,
            processData: false,
            success:function(response)
            {
                $(that).removeClass('btn-progress');

                if(response.status == "1")
                {
                    iziToast.success({title: '',message: response.message,position: 'bottomRight'});

                } 
                else 
                {
                    iziToast.error({title: '',message: response.message,position: 'bottomRight'});
                }
                $("#update_row_form_modal").modal('hide');               

            }
        })

    });

    var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";
    $(document).on('click','.delete_row',function(e){
        e.preventDefault();
        swal({
            title: '<?php echo $this->lang->line("Are you sure?"); ?>',
            text: Doyouwanttodeletethisrecordfromdatabase,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) 
            {
                var table_id = $(this).attr('table_id');

                $.ajax({
                    context: this,
                    type:'POST' ,
                    dataType:'JSON',
                    url:"<?php echo base_url('ecommerce/delete_category')?>",
                    data:{table_id:table_id},
                    success:function(response)
                    { 
                        if(response.status == '1')
                        {
                            iziToast.success({title: '',message: response.message,position: 'bottomRight',timeout: 3000});
                        } else
                        {
                            iziToast.error({title: '',message: response.message,position: 'bottomRight',timeout: 3000});
                        }
                        table.draw(false);
                    }
                });
            } 
        });
    }); 
    
    
    // $("#add_row_form_modal").on('hidden.bs.modal', function ()
    // {
    //     $("#row_add_form").trigger('reset');
    //     table.draw();
    // });

    $("#add_row_form_modal").on('hidden.bs.modal', function(event) {
        event.preventDefault();
        $("#row_add_form").trigger('reset');
        table.draw();
    });

    $("#update_row_form_modal").on('hidden.bs.modal', function ()
    {
        table.draw(false);
    });
  });
</script>


<div class="modal fade" id="add_row_form_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Category")." : ".$this->session->userdata("ecommerce_selected_store_title"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">                    
                        <form action="#" enctype="multipart/form-data" id="row_add_form" method="post">
                            <input type="hidden" id="store_id" name="store_id" value="<?php echo $this->session->userdata("ecommerce_selected_store"); ?>">
                            <div class="row">
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                      <label for="name"> <?php echo $this->lang->line("Store")?> *</label>
                                      <?php echo form_dropdown('', $store_list,$this->session->userdata("ecommerce_selected_store"),'style="width:100%" disabled class="form-control select2"'); ?>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Category Name'); ?> *</label>
                                        <input type="text" class="form-control" name="category_name" id="category_name">
                                    </div>
                                </div>

                                <div class="col-12">
                                  <div class="form-group">
                                    <label><?php echo $this->lang->line('Thumbnail'); ?> 
                                     <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Thumbnail"); ?>" data-content="<?php echo $this->lang->line("Maximum: 500KB, Format: JPG/PNG, Preference: Square image, Recommended dimension : 100x100"); ?>"><i class='fa fa-info-circle'></i> </a>
                                    </label>
                                    <div id="thumb-dropzone" class="dropzone mb-1">
                                      <div class="dz-default dz-message">
                                        <input class="form-control" name="thumbnail" id="uploaded-file" type="hidden">
                                        <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" style="font-size: 35px;color: var(--blue);"></i> <?php echo $this->lang->line('Upload'); ?></span>
                                      </div>
                                    </div>
                                    <span class="red"><?php echo form_error('thumbnail'); ?></span>
                                  </div>
                                </div>

                                <div class="col-12">
                                  <div class="form-group">
                                    <label class="custom-switch mt-2">
                                      <input type="checkbox" name="status" id="status" value="1" class="custom-switch-input"  <?php echo 'checked'; ?>>
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
                                    </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-primary btn-lg" id="save_row" name="save_row" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save") ?> </button>
                    <a class="btn btn-light float-right btn-lg" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_row_form_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit Category")." : ".$this->session->userdata("ecommerce_selected_store_title"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
              <div id="update_row_modal_body"></div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-primary btn-lg" id="update_row" name="update_row" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
                    <a class="btn btn-light float-right btn-lg" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line('Cancel'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sort_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-sort"></i> <?php echo $this->lang->line("Sort Category"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="sortable_main_div">
                 <?php 
                 foreach ($all_categories as $key => $value)
                 {
                     echo '
                     <li data-id='.$value["id"].' class="list-group-item d-flex justify-content-between align-items-center mt-1 mb-1 no_radius text-dark" style="border:1px dashed #777">
                        '.$value['category_name'].'
                     </li>';
                 }
                 ?>
                </ul>
              
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-primary btn-lg" id="sort_cat" name="sort_cat" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
                    <a class="btn btn-light float-right btn-lg" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line('Cancel'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>



<style type="text/css">
  .select2-search__field{width: 100% !important;}
  .dropzone{width: 100% !important;}
</style>

<script>
  $(document).ready(function() {

    $("#sortable_main_div" ).sortable();

    $(document).on('click','#sort_cat',function(e){
        var serial = [];
        var count = 0;
        $('#sortable_main_div li').each(function(i, obj)
        {
            serial[count] = $(this).attr('data-id');
            count++;
        });
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: {serial},
          url: '<?php echo base_url('ecommerce/sort_category'); ?>',
          success: function(response)
          {
             if(response.status=='0') 
             swal("<?php echo $this->lang->line('Error'); ?>", response.message, 'error');          
             else swal("<?php echo $this->lang->line('Success'); ?>", response.message, 'success').then((value) => {location.reload();});
          }
        });
    });

    // Uploads files
    var uploaded_file = $('#uploaded-file');
    Dropzone.autoDiscover = false;
    $("#thumb-dropzone").dropzone({ 
      url: '<?php echo base_url('ecommerce/upload_category_thumb'); ?>',
      maxFilesize:.5,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".png,.jpg,.jpeg",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        // Shows error message
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(uploaded_file).val(data.filename);
          $("#tmb_preview").hide();
        }
      },
      removedfile: function(file) {
        var filename = $(uploaded_file).val();
        delete_uploaded_file(filename);
        $("#tmb_preview").show();
      },
    });

    function delete_uploaded_file(filename) {
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('ecommerce/delete_category_thumb'); ?>',
          success: function(data) {
            $('#uploaded-file').val('');
          }
        });
      }
      // Empties form values
      empty_form_values();     
    }

    // Empties form values
    function empty_form_values() {
      $('#thumb-dropzone .dz-preview').remove();
      $('#thumb-dropzone').removeClass('dz-started dz-max-files-reached');
      // Clears added file
      Dropzone.forElement('#thumb-dropzone').removeAllFiles(true);
    }   

});
</script>


<script>
  $(document).ready(function() {
    var uploaded_file2 = $('#uploaded-file2');
    Dropzone.autoDiscover = false;
    $("#thumb-dropzone2").dropzone({ 
      url: '<?php echo base_url('ecommerce/upload_category_thumb'); ?>',
      maxFilesize:.5,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".png,.jpg,.jpeg",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(uploaded_file2).val(data.filename);
          $("#tmb_preview2").hide();
        }
      },
      removedfile: function(file) {
        var filename = $(uploaded_file2).val();
        delete_uploaded_file2(filename);
        $("#tmb_preview2").show();
      },
    });

    function delete_uploaded_file2(filename) {
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('ecommerce/delete_category_thumb'); ?>',
          success: function(data) {
            $('#uploaded-file2').val('');
          }
        });
      }
      empty_form_values2();     
    }

    function empty_form_values2() {
      $('#thumb-dropzone2 .dz-preview').remove();
      $('#thumb-dropzone2').removeClass('dz-started dz-max-files-reached');
      Dropzone.forElement('#thumb-dropzone2').removeAllFiles(true);
    }   
});
</script>


