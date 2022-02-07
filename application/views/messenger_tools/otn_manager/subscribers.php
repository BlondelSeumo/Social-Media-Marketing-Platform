<!-- new datatable section -->
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fa fa-users"></i> <?php echo $this->lang->line('OTN Subscribers'); ?> </h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("OTN Subscriber Report"); ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">

            <div class="input-group mb-3" id="searchbox">
                <div class="input-group-prepend">
                    <select class="select2 form-control" id="page_id">
                      <option value=""><?php echo $this->lang->line("Page"); ?></option>
                        <?php foreach ($page_info as $key => $value): ?>
                          <option value="<?php echo $value['id']; ?>"><?php echo $value['page_name']; ?></option>
                        <?php endforeach ?>
                  </select>
                </div>
                <input type="text" class="form-control" id="postback_id" autofocus placeholder="<?php echo $this->lang->line('OTN PostBack ID'); ?>" aria-label="" aria-describedby="basic-addon2" style="max-width: 30%">
                <div class="input-group-append">
                      <button class="btn btn-primary" id="search_submit" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
                </div>
            </div>
            
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
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

        </div>
      </div>
    </div>
    
  </div>
</section>


<script>       
    var base_url="<?php echo site_url(); ?>";
    
    $(document).ready(function() {
      var perscroll;
      table = $("#mytable").DataTable({
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
                d.page_id = $('#page_id').val();
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
              targets: [0],
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


      $(document).on('change', '#page_id', function(event) {
        event.preventDefault(); 
        table.draw();
      });


    });
</script>