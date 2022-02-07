<?php $this->load->view('admin/theme/message'); ?>

<style type="text/css">
    .button{
        margin-top: 10px;
    }
    .datagrid-body
    {
      overflow: hidden !important; 
    }

    .emojionearea, .emojionearea.form-control
    {
        height: 150px !important;
    }


</style>

<div id="dynamic_field_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="page_name">
                <div class="modal-body" style="padding-bottom:0">
                    <div class="row">
                          <div class="col-12"> 
                            <?php if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")) : ?>
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please select a page"); ?></label>
                                <select class="form-control select2" id="page_table_id" name="page_table_id" style="width:100%;">
                                  <?php 
                                    echo "<option value=''>".$this->lang->line('Choose a Page')."</option>";
                                    foreach ($group_page_list as $key => $value) 
                                    {
                                      echo '<optgroup label="'.$value['media_name'].'">';
                                      foreach ($value['page_list'] as $key2 => $value2) 
                                      {
                                        echo "<option value='".$key2."' >".$value2."</option>";
                                      }
                                      echo '</optgroup>';
                                    } 
                                  ?>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                              <label class="d-block"><?php echo $this->lang->line('Media'); ?></label>
                              <div class="row">
                                <div class="col-12 col-md-6">
                                  <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="media_type_fb" name="media_type" value="fb" class="custom-control-input">
                                    <label class="custom-control-label" for="media_type_fb"><?php echo $this->lang->line('Facebook'); ?></label>
                                  </div>
                                </div>
                                <div class="col-12 col-md-6">
                                  <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="media_type_ig" name="media_type" value="ig" class="custom-control-input">
                                    <label class="custom-control-label" for="media_type_ig"><?php echo $this->lang->line('Instagram'); ?></label>
                                  </div>
                                </div>
                              </div>
                            </div> -->
                            <?php else : ?>
                              <div class="form-group">
                                <label><?php echo $this->lang->line("Please select a page"); ?></label>
                                <?php 
                                  $page_list[''] = $this->lang->line("Choose a Page");
                                  echo form_dropdown('page_table_id',$page_list,'','id="page_table_id" class="form-control select2" style="width:100%;"'); 
                                ?>
                              </div>       
                            <?php endif; ?>
                          </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 10px;">
                    <button class="btn btn-lg btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel"); ?></button>
                    <button id="submit" class="btn btn-primary btn-lg"><i class="fas fa-check"></i> <?php echo $this->lang->line('Ok'); ?></button>
                    
                </div>
            </form>
        </div>
    </div>

</div> 

<div class="table-responsive data-card">
  <table class="table table-bordered table-sm table-striped" id="mytable">
    <thead>
      <tr>
        <th>#</th>      
        <th><?php echo $this->lang->line("Template ID"); ?></th>      
        <th><?php echo $this->lang->line("Reference Name"); ?></th>
        <th><?php echo $this->lang->line("Page Name"); ?></th>
        <th><?php echo $this->lang->line("Media Type"); ?></th>
        <th style="min-width: 150px"><?php echo $this->lang->line("Actions"); ?></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>  

<?php 
$areyousure=$this->lang->line("are you sure");
$builder_load_url = base_url("visual_flow_builder/load_builder/{$page_auto_id}/1/{$media_type}");
$drop_menu = '<a href="'.$builder_load_url.'" class="float-right btn btn-primary" type="button" target="_BLANK"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create New Flow").'</a>';
?> 

<script>
$(document).ready(function(){

    var drop_menu = '<?php echo $drop_menu;?>';
    setTimeout(function(){ 
      $("#mytable_filter").append(drop_menu);
    }, 1000);

    var base_url="<?php echo base_url(); ?>";
    var page_auto_id = "<?php echo $page_auto_id; ?>";
    if(page_auto_id != 0)
      var data_url = base_url+'visual_flow_builder/visual_flow_builder_data/'+page_auto_id;
    else
      var data_url = base_url+'visual_flow_builder/visual_flow_builder_data';

    // datatable section started
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax: 
        {
            "url": data_url,
            "type": 'POST',
            "dataSrc": function ( json ) 
            {
              $(".table-responsive").niceScroll();
              return json.data;
            } 
        },
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
              targets: [1,3,4],
              visible: false
            },
            {
              targets: '',
              className: 'text-center',
              sortable: false
            },
            {
                targets: [4],
                "render": function ( data, type, row, meta ) 
                {
                    var media_type = row[4];
                    var str = '';
                    if(media_type == 'ig')
                        str = 'Instagram';
                    else
                        str = 'Facebook';
                    return str;
                }
            },
            {
                targets: [5],
                "render": function ( data, type, row, meta ) 
                {
                    var id = row[1];
                    var media_type = row[4];
                    var edit_str="<?php echo $this->lang->line('Edit');?>";
                    var delete_str="<?php echo $this->lang->line('Delete');?>";
                    var str="";   
                    var edit_url = base_url + "visual_flow_builder/edit_builder_data/" + id + "/1/" + media_type;
                    str="&nbsp;<a target='_blank' class='text-center btn btn-circle btn-outline-warning' href='"+edit_url+"' title='"+edit_str+"'>"+'<i class="fa fa-edit"></i>'+"</a>";

                    str=str+"&nbsp;<a name='delete' href='#' class='text-center delete_data btn btn-circle btn-outline-danger ' title='"+delete_str+"' table_id="+id+" '>"+'<i class="fa fa-trash"></i>'+"</a>";
                  
                    return str;
                }
            }
        ]
    });
    // End of datatable section

    $('#add').click(function(e){
        e.preventDefault();
        $('#dynamic_field_modal').modal('show');
    });

    $('#submit').click(function(e) {
       e.preventDefault();
       var page_id_media = $('#page_table_id').val();
       var page_id_media_array = page_id_media.split("-");

       var page_table_id = page_id_media_array[0];
       var media_type = 'fb';
       if (typeof page_id_media_array[1] !== 'undefined') {
         media_type = page_id_media_array[1];
       }

       if(page_table_id == '')
       {
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You have to select a page"); ?>', 'warning');
          return false;
       }
       else
       {
          var link = base_url + "visual_flow_builder/load_builder/" + page_table_id + "/1/" + media_type;
          window.location.replace(link);
       }

    });

    $(document).on('click', '.edit_reply_info', function(event) {
      event.preventDefault();
      var table_id = $(this).attr('table_id');
      var media_type = $(this).attr('media_type');
      var link = base_url + "visual_flow_builder/edit_builder_data/" + table_id + "/1/" + media_type;
      window.location.replace(link);
    });


    $(document).on('click', '.delete_data', function(event) {
        event.preventDefault();
        swal({
            title: '<?php echo $this->lang->line("Warning"); ?>',
            text: '<?php echo $this->lang->line("Are you sure you want to delete this campaign"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        })
        .then((willreset) => {
            if (willreset) 
            {
                $(this).addClass('btn-progress');
                var table_id = $(this).attr('table_id');

                $.ajax({
                    context: this,
                    type:'POST',
                    url: base_url + "visual_flow_builder/delete_flowbuilder_data",
                    dataType: 'json',
                    data: {table_id},
                    success:function(response){ 
                    if(response.status == 1)
                    {
                        $(this).removeClass('btn-progress');
                        
                        swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                          table.draw();
                      });
                    }
                    else
                    {
                        swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                    }
                },
                error:function(response){
                    var span = document.createElement("span");
                    span.innerHTML = response.responseText;
                    swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
                }
            });
            } 
        });
    });
     

});
</script>