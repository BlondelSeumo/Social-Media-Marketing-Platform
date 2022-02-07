
<style>
    .dropdown-toggle::after{content:none !important;}
    #searching_page{max-width: 50% !important;}
    #campaign_status{width: 110px !important;}
    @media (max-width: 575.98px) { #searching_page{max-width: 77% !important;} }
</style>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-pager"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary" href="<?php echo base_url('menu_manager/create_page'); ?>">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Page"); ?>
            </a> 
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("menu_manager/index"); ?>"><?php echo $this->lang->line("Menu Manager"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="input-group float-left" id="searchbox">
                                    <input type="text" class="form-control" id="searching_page" name="searching_page" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                              <a href="javascript:;" id="page_date_range" class="btn btn-primary btn-lg icon-left float-right btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="page_date_range_val">
                              <a href="#" class="btn btn-danger btn-lg float-right mr-2 delete_selected_page" data-toggle="tooltip" title="<?php echo $this->lang->line("Delete Selected"); ?>"><i class="fas fa-trash-alt"></i> <?php echo $this->lang->line("Delete"); ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive2">
                                    <table class="table table-bordered" id="mytable_custom_page_lists">
                                        <thead>
                                            <tr>
                                                <th>#</th> 
                                                <th style="vertical-align:middle;width:20px">
                                                    <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/>
                                                    <label for="datatableSelectAllRows"></label>        
                                                </th>      
                                                <th><?php echo $this->lang->line("ID"); ?></th>      
                                                <th><?php echo $this->lang->line("Page Name"); ?></th>
                                                <th><?php echo $this->lang->line("Slug"); ?></th>
                                                <th><?php echo $this->lang->line("URL"); ?></th>
                                                <th><?php echo $this->lang->line("Created"); ?></th>
                                                <th><?php echo $this->lang->line('Actions'); ?></th>
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
        </div>
    </div>
</section>


<script>
    $(document).ready(function($) {

        var base_url = '<?php echo base_url(); ?>';
        var Doyouwanttodeletealltheserecordsfromdatabase = "<?php echo $this->lang->line('Do you want to detete all the records from the database?'); ?>";
        var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";

        setTimeout(function(){ 
          $('#page_date_range').daterangepicker({
            ranges: {
              '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
              '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
              '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment()
          }, function (start, end) {
            $('#page_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
          });
        }, 2000);

        var today = new Date();
        var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
        $('.datepicker_x').datetimepicker({
            theme:'light',
            format:'Y-m-d H:i:s',
            formatDate:'Y-m-d H:i:s',
            minDate: today,
            maxDate: next_date
        })

        $('[data-toggle=\"tooltip\"]').tooltip();

        // =========================== SMS API Section started and datatable section started ========================
        var perscroll;
        var table = $("#mytable_custom_page_lists").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 2, "desc" ]],
            pageLength: 10,
            ajax: 
            {
              "url": base_url+'menu_manager/page_lists_data',
              "type": 'POST',
              data: function ( d )
              {
                  d.searching = $('#searching_page').val();
                  d.page_date_range = $('#page_date_range_val').val();
              }
            },
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                  targets: [2],
                  visible: false
                },
                {
                  targets: [0,1,3,4,6,7],
                  className: 'text-center'
                },
                {
                  targets: [0,1,3,4,6],
                  sortable: false
                }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_custom_page_lists_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_custom_page_lists_wrapper .dataTables_scrollBody');
              }
            }
        });


        $(document).on('keyup', '#searching_page', function(event) {
          event.preventDefault(); 
          table.draw();
        });

        $(document).on('change', '#page_date_range_val', function(event) {
          event.preventDefault(); 
          table.draw();
        });

        $(document).on('click','.delete_page',function(e){
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
                        url:base_url+"menu_manager/delete_single_page",
                        data:{table_id:table_id},
                        success:function(response)
                        { 
                            if(response == '1')
                            {
                                iziToast.success({title: '',message: '<?php echo $this->lang->line('Page has been deleted successfully.'); ?>',position: 'bottomRight',timeout: 3000});
                            } else
                            {
                                iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight',timeout: 3000});
                            }
                            table.draw();
                        }
                    });
                } 
            });
        });


        $(document).on('click', '.delete_selected_page', function(event) {
          event.preventDefault();

          var page_ids = [];
          $(".datatableCheckboxRow:checked").each(function ()
          {
              page_ids.push(parseInt($(this).val()));
          });
          
          if(page_ids.length==0) {

              swal('<?php echo $this->lang->line("Warning")?>', '<?php echo $this->lang->line("You didn`t select any Page to delete.") ?>', 'warning');
              return false;

          }
          else {

            swal({title: '<?php echo $this->lang->line("Are you sure?"); ?>',text: Doyouwanttodeletealltheserecordsfromdatabase,icon: 'warning',buttons: true,dangerMode: true,})
            .then((willDelete) => {

                if (willDelete) {

                  $(this).addClass('btn-progress');
                  $.ajax({
                      context: this,
                      type:'POST',
                      url: base_url+"menu_manager/ajax_delete_all_selected_pages",
                      data:{info:page_ids},
                      success:function(response){
                          $(this).removeClass('btn-progress');

                          if(response == '1') {

                            iziToast.success({title: '',message: '<?php echo $this->lang->line('Selected Contacts has been deleted Successfully.'); ?>',position: 'bottomRight'});

                          } else {

                            iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});

                          }

                          table.draw();
                      }
                  });

                } 
            });
          }

        });
    });
</script>