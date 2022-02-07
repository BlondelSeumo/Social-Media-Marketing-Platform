<section class="section section_custom pt-1">
  <div class="section-header d-none">
    <h1><i class="fas fa-palette"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
        <a class="btn btn-primary" href="#" id="add_new_row"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Add Pickup Point'); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("Pickup Point"); ?></div>
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
                    <th><?php echo $this->lang->line("Title"); ?></th>      
                    <th><?php echo $this->lang->line("Details"); ?></th>
                    <th><?php echo $this->lang->line("Status"); ?></th>
                    <th><?php echo $this->lang->line("Actions"); ?></th>
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


<?php $drop_menu = '<a class="btn btn-primary float-right btn-lg" href="#" id="add_new_row"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Add').'</a>';?>
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
            url: base_url+'ecommerce/pickup_point_list_data',
            type: 'POST'
        },          
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
          {
              targets: [1],
              visible: false
          },
          {
              targets: [2,4,5],
              className: 'text-center'
          },
          {
              targets: [0,1,4,5],
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
        $("#add_row_form_modal").modal();
    });

    $(document).on('click', '#save_row', function(event) {
        event.preventDefault();

        var point_name = $("#point_name").val();
        var point_details = $("#point_details").val();

        if(point_name == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Name name is required"); ?>', 'warning');
            return;
        }

        if(point_details == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Details value is required"); ?>', 'warning');
            return;
        }        

        $(this).addClass('btn-progress')
        var that = $(this);

        var alldatas = new FormData($("#row_add_form")[0]);

        $.ajax({
            url: base_url+'ecommerce/ajax_create_new_pickup_point',
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
            url: base_url+'ecommerce/ajax_get_pickup_point_update_info',
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

        var point_name = $("#point_name2").val();
        var point_details = $("#point_details2").val();

        if(point_name == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Name name is required"); ?>', 'warning');
            return;
        }

        if(point_details == "")
        {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Details value is required"); ?>', 'warning');
            return;
        }  

        $(this).addClass('btn-progress')
        var that = $(this);

        var alldatas = new FormData($("#row_update_form")[0]);

        $.ajax({
            url: base_url+'ecommerce/ajax_update_pickup_point',
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
                    url:"<?php echo base_url('ecommerce/delete_pickup_point')?>",
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
    
    
    $("#add_row_form_modal").on('hidden.bs.modal', function ()
    {
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
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Point")." : ".$this->session->userdata("ecommerce_selected_store_title"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">                    
                        <form action="#" enctype="multipart/form-data" id="row_add_form" method="post">
                            <input type="hidden" id="store_id" name="store_id" value="<?php echo $this->session->userdata("ecommerce_selected_store"); ?>">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Point Name'); ?> *</label>
                                        <input type="text" class="form-control" name="point_name" id="point_name">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Point Details'); ?> *</label>
                                        <textarea id="point_details" name="point_details" class="form-control"></textarea>
                                    </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
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
                    <a class="btn btn-light btn-lg float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_row_form_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit Point")." : ".$this->session->userdata("ecommerce_selected_store_title"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
              <div id="update_row_modal_body"></div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-primary btn-lg" id="update_row" name="update_row" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
                    <a class="btn btn-light btn-lg float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line('Cancel'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>


<style type="text/css">
  .select2-search__field{width: 100% !important;}
</style>