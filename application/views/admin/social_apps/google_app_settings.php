<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fab fa-google"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
        <a class="btn btn-primary" href="<?php echo base_url('social_apps/add_google_settings') ?>"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Add New APP'); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('social_apps/settings'); ?>"><?php echo $this->lang->line("Social Apps"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">

            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th style="vertical-align:middle;width:20px">
                        <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                    </th>
                    <th><?php echo $this->lang->line("ID"); ?></th>      
                    <th><?php echo $this->lang->line("APP Name"); ?></th>      
                    <th><?php echo $this->lang->line("APP Key"); ?></th>      
                    <th><?php echo $this->lang->line("Client ID"); ?></th>      
                    <th><?php echo $this->lang->line("Client Secret"); ?></th>
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





<script>       
  var base_url="<?php echo site_url(); ?>";  
 
  $(document).ready(function() {

    var perscroll;
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 2, "desc" ]],
        pageLength: 10,
        ajax: {
            url: base_url+'social_apps/google_settings_data',
            type: 'POST'
        },          
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
          {
              targets: [1,2],
              visible: false
          },
          {
              targets: '',
              className: 'text-center'
          },
          {
              targets: [0,1,6,7,8],
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


    $(document).on('click','.delete_app',function(e){
      e.preventDefault();
      var ifyoudeletethisaccount = "<?php echo $this->lang->line('If you delete this APP then, all the imported Google accounts and Campaigns will be deleted too corresponding to this APP.'); ?>";
      swal({
        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
        text: ifyoudeletethisaccount,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          var app_table_id = $(this).attr('table_id');
          $(this).removeClass('btn-outline-danger');
          $(this).addClass('btn-danger');
          $(this).addClass('btn-progress');

          $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo site_url();?>social_apps/delete_app_google",
            dataType: 'json',
            data:{app_table_id : app_table_id},
            success:function(response){ 
              
              $(this).removeClass('btn-progress');
              $(this).removeClass('btn-danger');
              $(this).addClass('btn-outline-danger');

              if(response.status == 1)
              {
                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                    location.reload();
                  });
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
            }
          });
        } 
      });
    });

    $(document).on('click','.change_state',function(e){
      e.preventDefault();
      var ifyoudeletethisaccount = "<?php echo $this->lang->line('If you change this APP status to inactive then, all the imported Google accounts and Campaigns will not work corresponding to this APP.'); ?>";
      swal({
        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
        text: ifyoudeletethisaccount,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          var app_table_id = $(this).attr('table_id');
          $(this).removeClass('btn-outline-danger');
          $(this).addClass('btn-danger');
          $(this).addClass('btn-progress');

          $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo site_url();?>social_apps/change_app_status_google",
            dataType: 'json',
            data:{app_table_id : app_table_id},
            success:function(response){ 
              
              $(this).removeClass('btn-progress');
              $(this).removeClass('btn-danger');
              $(this).addClass('btn-outline-danger');

              if(response.status == 1)
              {
                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                    location.reload();
                  });
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
            }
          });
        } 
      });
    });


  });
</script>