<!-- new datatable section -->

  <?php $this->load->view('admin/theme/message'); ?>
  <div class="table-responsive data-card">
    <input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>">
    <table class="table table-bordered table-sm table-striped" id="mytable">
      <thead>
        <tr>
          <th>#</th>      
          <th style="vertical-align:middle;width:20px">
              <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
          </th>
          <th><?php echo $this->lang->line("id")?></th>
          <th><?php echo $this->lang->line("FB Account")?></th>
          <th><?php echo $this->lang->line("Page Name")?></th>
          <th><?php echo $this->lang->line("Domain Count")?></th>
          <th><?php echo $this->lang->line("Action")?></th>
        </tr>
      </thead>
    </table>
  </div> 

<?php 
$areyousure=$this->lang->line("are you sure");
$drop_menu = '<a href="#" class="float-right btn btn-primary" type="button" id="add_new_domain"><i class="fas fa-plus-circle"></i> '.$this->lang->line("New Domain").'</a>';
?> 

<script>       
    var base_url="<?php echo site_url(); ?>";
    var drop_menu = '<?php echo $drop_menu;?>';
    setTimeout(function(){ 
      $("#mytable_filter").append(drop_menu);
    }, 1000);
   
    $(document).ready(function() {
      var perscroll;
      var table = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: true,
          order: [[ 2, "desc" ]],
          pageLength: 10,
          ajax: {
              url: base_url+'messenger_bot/domain_whitelist_data',
              type: 'POST',
              data: function ( d )
              {
                  d.page_id = $('#page_id').val();
              }
          },          
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
                targets: [1,2,4],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,2,6],
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


      $(document).on('click', '#search_submit', function(event) {
        event.preventDefault(); 
        table.draw();
      });

      $(document).on('click', '#add_new_domain', function(event) {
        event.preventDefault(); 
        $("#add_new_domain_modal").modal();
      });

      $(document).on('click','#add_new_domain_submit',function(){
          var page_id = '<?php echo $page_id; ?>';
          var domain_name = $("#add_new_domain_name").val();

          if(page_id=='' || domain_name=='')
          {
             swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select page and provide domain name.'); ?>", 'warning');
             return false;
          }

          $(this).addClass('btn-progress');
          $.ajax({
            type:'POST' ,
            url:"<?php echo site_url();?>messenger_bot/add_domain",
            data:{page_id:page_id,domain_name:domain_name},
            dataType:'JSON',
            context: this,
            success:function(response)
            { 
              $(this).removeClass('btn-progress');
              $("#add_new_domain_name").val('');
              if(response.status=='1')
                swal('<?php echo $this->lang->line("Success"); ?>', response.result, 'success').then((value) => {
                  $("#add_new_domain_modal").modal('hide');
                  table.draw();
                });
              else
                swal('<?php echo $this->lang->line("Error"); ?>', response.result, 'error');
            }
          });

      });

      
      var table1 = '';
      $(document).on('click','.domain_list',function(e){
        e.preventDefault();
        var page_id = $(this).attr('data-page');
        var page_name = $(this).attr('data-page-name');
        var account_name = $(this).attr('data-account-name');


        $("#put_page_id").val(page_id);
        $("#put_page_name").html(page_name);
        $("#put_account_name").html(account_name);
        $("#domain_list_modal").modal(); 

        if (table1 == '')
        {
          var perscroll1;
          var base_url = "<?php echo base_url(); ?>";
          table1 = $("#mytable1").DataTable({
              serverSide: true,
              processing:true,
              bFilter: false,
              order: [[ 1, "asc" ]],
              pageLength: 10,
              ajax: {
                  url: base_url+'messenger_bot/domain_details',
                  type: 'POST',
                  data: function ( d )
                  {
                      d.page_id = $("#put_page_id").val();
                      d.searching = $("#searching").val();
                  }
              },
              language: 
              {
                url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
              },
              dom: '<"top"f>rt<"bottom"lip><"clear">',
              columnDefs: [
                {
                    targets: '',
                    className: 'text-center'
                },
                {
                    targets: [0,3],
                    sortable: false
                }
              ],
              fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (perscroll1) perscroll1.destroy();
                  perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
              }
              },
              scrollX: 'auto',
              fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                if(areWeUsingScroll)
                { 
                if (perscroll1) perscroll1.destroy();
                perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
                }
              }
          });
        }
        else table1.draw();

      }); 

      $(document).on('keyup', '#searching', function(event) {
        event.preventDefault(); 
        table1.draw();
      });



      $(document).on('click','.delete_domain',function(e){
        e.preventDefault();

        swal({
          title: '<?php echo $this->lang->line("Delete!"); ?>',
          text: '<?php echo $this->lang->line("Do you want to detete this domain?"); ?>',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) 
          {
            var base_url = '<?php echo site_url();?>';
            $(this).addClass('btn-progress');

            var domain_id = $(this).attr('data-id');
            var col_id = $(this).attr('id');

            $.ajax({
              context: this,
              type:'POST' ,
              url:"<?php echo site_url();?>messenger_bot/delete_domain",
              // dataType: 'json',
              data:{domain_id:domain_id},
              success:function(response){ 
                $(this).removeClass('btn-progress');
                if(response=='1')
                {
                  var deleted = "<?php echo $this->lang->line("Deleted"); ?>";
                  var deleted_html="<span class='badge badge-light red'><i class='fa fa-check'></i> "+deleted+"</span>";
                  iziToast.success({title: '',message: '<?php echo $this->lang->line("Domain has been deleted successfully."); ?>',position: 'bottomRight'});
                  $("#"+col_id).parent().html(deleted_html);
                  table1.draw();
                }
                else
                {
                  swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Something went wrong, please try again later.'); ?>", 'error');
                }
              }
            });
          } 
        });


      });


      $('#add_new_domain_modal, #domain_list_modal').on('hidden.bs.modal', function () { 
        table.draw();
      });



    });
  
 
</script>



<div class="modal fade" id="domain_list_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-list-ol"></i> <?php echo $this->lang->line("Domain List");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                      <h4 class='text-left'><?php echo $this->lang->line('Page')." : "; ?><span id="put_page_name"></span> (<span id="put_account_name"></span>)</h4><br/>
                    </div>

                    <div class="col-12 margin-top">
                      <input type="text" id="searching" name="searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
                    </div>
                    <div class="col-12">
                      <div class="data-card">   
                        <input type="hidden" name="put_page_id" id="put_page_id">                  
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm table-striped" id="mytable1">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line("Domain"); ?></th> 
                                <th><?php echo $this->lang->line("Whitelisted At"); ?></th> 
                                <th><?php echo $this->lang->line("Actions"); ?></th>  
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                    </div>
                    
                </div>               
            </div>
            <!-- <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('close'); ?></button>
            </div> -->
        </div>
    </div>
</div>

<div class="modal fade" id="add_new_domain_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Domain");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div id="add_new_domain_response" class="text-center"></div>
                <div class="form-group col-12 hidden" style="padding:0 5px 0 0">
                  <label><?php echo $this->lang->line("Page") ?> *</label>
                  <select class="form-control select2" id="add_new_domain_page_old" name="add_new_domain_page_old">
                    <?php 
                    echo "<option value=''>".$this->lang->line('Choose Page')."</option>";
                    foreach ($pagelist as $key => $value) 
                    {
                      echo '<optgroup label="'.addslashes($value['account_name']).'">';
                      foreach ($value['page_data'] as $key2 => $value2) {
                        echo "<option value='".$value2['page_id']."'>".$value2['page_name']."</option>";
                      }
                      echo '</optgroup>';
                    } ?>
                  </select>
                </div> 
                <div class="form-group col-12" style="padding:0 5px 0 0">
                  <label><?php echo $this->lang->line("Domain") ?> *</label>
                  <input placeholder="http://xyz.com"  name="add_new_domain_name" id="add_new_domain_name" class="form-control" type="text"/>
                </div>        
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-lg btn-secondary float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('close'); ?></button>
              <button class="btn btn-primary btn-lg float-left" name="add_new_domain_submit" id="add_new_domain_submit" type="button"><i class="fa fa-save"></i> <?php echo $this->lang->line('save');?></button>
            </div>
        </div>
    </div>
</div>