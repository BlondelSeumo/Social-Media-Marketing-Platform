
  <?php $this->load->view('admin/theme/message'); ?>

  <input type="hidden" value="<?php echo $page_id; ?>" id="page_id" name="page_id">
  <div class="table-responsive data-card">
    <table class="table table-bordered table-sm table-striped" id="mytable">
      <thead>
        <tr>
          <th>#</th>      
          <th style="vertical-align:middle;width:20px">
              <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
          </th>
          <th><?php echo $this->lang->line("id")?></th>
          <th><?php echo $this->lang->line("Page name")?></th>
          <th><?php echo $this->lang->line("Template name")?></th>
          <th><?php echo $this->lang->line("OTN postback ID")?></th>
          <th><?php echo $this->lang->line("Total OPTin subscribers")?></th>
          <th><?php echo $this->lang->line("Message sent")?></th>
          <th><?php echo $this->lang->line("Message not sent")?></th>
          <th><?php echo $this->lang->line("Type")?></th>
          <th><?php echo $this->lang->line("Actions")?></th>
        </tr>
      </thead>
    </table>
  </div>

  <div class="modal fade" id="otn_subscribers_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width: 95%;">
      <div class="modal-content">
        <div class="modal-header bbw">
          <h5 class="modal-title blue"><i class="fas fa-users"></i> <?php echo $this->lang->line("OTN Subscribers");?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>

          <div id="modalBody" class="modal-body">
            <input type="text" class="form-control" id="postback_id" autofocus placeholder="<?php echo $this->lang->line('OTN PostBack ID'); ?>" aria-label="" aria-describedby="basic-addon2" style="max-width: 40%">
            <div class="table-responsive2 data-card">
              <input type="hidden" value="" id="get_subscribers_page_id" name="get_subscribers_page_id">
              <table class="table table-bordered table-sm table-striped" id="mytable2">
                <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line("Page Name"); ?></th> 
                    <th><?php echo $this->lang->line("First Name"); ?></th> 
                    <th><?php echo $this->lang->line("Last Name"); ?></th> 
                    <th><?php echo $this->lang->line("OTN PostBack"); ?></th> 
                    <th><?php echo $this->lang->line("Subscriber ID"); ?></th>      
                    <th><?php echo $this->lang->line("OPT-in Token"); ?></th>
                    <th><?php echo $this->lang->line("OPT-in Time"); ?></th>
                  </tr>
                </thead>
              </table>
            </div>          
          </div>

          <div class="modal-footer bg-whitesmoke">
              <button type="button" class="btn-lg btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
          </div>
      </div>
    </div>
  </div>

<?php 

  $new_otn_url_classic = base_url("messenger_bot/otn_create_new_template/1/{$page_id}");
  $new_otn_url_builder = base_url("visual_flow_builder/load_builder/{$page_id}/1/{$media_type}");

  $drop_menu = '<a href="#" class="float-right btn btn-danger ml-2 get_otn_subscribers" page_table_id="'.$page_id.'" is_iframe="1"><i class="fa fa-users"></i> '.$this->lang->line("OTN Subscribers").'</a>&nbsp;<a href="'.$new_otn_url_builder.'" target="_BLANK" class="float-right btn btn-primary" title="'.$this->lang->line('Use Flow Builder').'"><i class="fab fa-stack-overflow"></i> '.$this->lang->line("New Template").'</a>&nbsp;<a href="'.$new_otn_url_classic.'" class="btn btn-info float-right mr-2 iframed" title="'.$this->lang->line('Use Classic Editor').'"><i class="fas fa-folder-plus"></i></a>';

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
              url: base_url+'messenger_bot/otn_template_manager_data',
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
                targets: [1,2,3],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,2,4,5,10],
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

      var table2 = '';
      var perscroll2;

      $(document).on('click', '.get_otn_subscribers', function(event) {
        event.preventDefault();
        var get_subscriber_page_id = $(this).attr('page_table_id');
        $("#get_subscribers_page_id").val(get_subscriber_page_id);
        $("#otn_subscribers_modal").modal();

        setTimeout(function(){
          if(table2 == '') {

            table2 = $("#mytable2").DataTable({
                serverSide: true,
                processing:true,
                bFilter: false,
                order: [[ 7, "desc" ]],
                pageLength: 10,
                ajax: {
                    url: base_url+'messenger_bot/otn_subscribers_data',
                    type: 'POST',
                    data: function ( d )
                    {
                      d.page_table_id = $('#get_subscribers_page_id').val();
                      d.postback_id = $('#postback_id').val();
                    }
                },
                language: 
                {
                  url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                columnDefs: [
                  {
                    targets: [0,1],
                    visible: false
                  },
                  {
                      targets: '',
                      className: 'text-center'
                  },
                  {
                      targets: [0,4,6],
                      sortable: false
                  }
                ],
                fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
                if(areWeUsingScroll)
                {
                  if (perscroll2) perscroll2.destroy();
                    perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                }
                },
                scrollX: 'auto',
                fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                  if(areWeUsingScroll)
                  { 
                  if (perscroll2) perscroll2.destroy();
                  perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                  }
                }
            });

          } else {
            table2.draw();
          }
        },1000);


      });

      $('#otn_subscribers_modal').on('hidden.bs.modal', function () {
        event.preventDefault();

        $("#postback_id").val('');
        table.draw();
      });


      

      $(document).on('click','.delete_template',function(e){
        e.preventDefault();

        swal({
          title: '<?php echo $this->lang->line("Delete!"); ?>',
          text: '<?php echo $this->lang->line("If you delete this template, all the token corresponding this template will also be deleted. Do you want to detete this template?"); ?>',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) 
          {
            var base_url = '<?php echo site_url();?>';
            $(this).addClass('btn-progress');
            var table_id = $(this).attr('table_id');

            $.ajax({
              context: this,
              type:'POST' ,
              url:"<?php echo site_url();?>messenger_bot/otn_ajax_delete_template_info",
              // dataType: 'json',
              data:{table_id:table_id},
              success:function(response){ 
                $(this).removeClass('btn-progress');
                if(response=='success')
                {
                  iziToast.success({title: '',message: '<?php echo $this->lang->line("Template and all the corresponding token has been deleted successfully."); ?>',position: 'bottomRight'});
                  table.draw();
                }
                else if(response=='no_match')
                {
                  iziToast.error({title: '',message: '<?php echo $this->lang->line("No Template is found for this user with this ID."); ?>',position: 'bottomRight'});
                }
                else
                {
                  $("#delete_template_modal_body").html(response);
                  $("#delete_template_modal").modal();
                }
              }
            });
          } 
        });


      });


    });
  
 
</script>


<div class="modal fade" id="delete_template_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center"><i class="fa fa-trash"></i> <?php echo $this->lang->line("Template Delete Confirmation"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="delete_template_modal_body">                

            </div>
        </div>
    </div>
</div>