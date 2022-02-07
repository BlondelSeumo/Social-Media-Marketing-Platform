<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-history"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <a href="<?php echo base_url('payment/transaction_log_manual'); ?>" class="btn btn-primary"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('Manual Transaction Log'); ?></a> 
    </div>
    <div class="section-header-breadcrumb">
      <?php 
      if($this->session->userdata("user_type")=="Admin") 
      echo '<div class="breadcrumb-item">'.$this->lang->line("Subscription").'</div>';
      else echo '<div class="breadcrumb-item">'.$this->lang->line("Payment").'</div>';
      ?>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php 
    $this->load->view('admin/theme/message'); 
    if($this->session->flashdata('xendit_currency_error') != '')
    echo "<div class='alert alert-danger text-center'><i class='fas fa-check-circle'></i> ".$this->session->flashdata('xendit_currency_error')."</div>";
  ?>

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
                    <th><?php echo $this->lang->line("Email"); ?></th>      
                    <th><?php echo $this->lang->line("First Name"); ?></th>      
                    <th><?php echo $this->lang->line("Last Name"); ?></th>      
                    <th><?php echo $this->lang->line("Method"); ?></th>
                    <th><?php echo $this->lang->line("Cycle Start"); ?></th>
                    <th><?php echo $this->lang->line("cycle End"); ?></th>
                    <th><?php echo $this->lang->line("Paid at"); ?></th>
                    <th><?php echo $this->lang->line("Amount")." ".$curency_icon; ?></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <th><?php echo $this->lang->line("Total"); ?></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                  </tr>
                </tfoot>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>


<?php
$drop_menu ='<a href="javascript:;" id="payment_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="payment_date_range_val">';
?>


<script>       
    var base_url="<?php echo site_url(); ?>";

    var drop_menu = '<?php echo $drop_menu;?>';
    setTimeout(function(){ 
      $("#mytable_filter").append(drop_menu); 
      $('#payment_date_range').daterangepicker({
        ranges: {
          '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
          '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
          '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      }, function (start, end) {
        $('#payment_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
      });
    }, 2000);
    
   
    $(document).ready(function() {

      var perscroll;
      var table = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: true,
          order: [[ 2, "desc" ]],
          pageLength: 10,
          ajax: {
              url: base_url+'payment/transaction_log_data',
              type: 'POST',
              data: function ( d )
              {
                  d.payment_date_range = $('#payment_date_range_val').val();
              }
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
                targets: [6,7,8,9],
                className: 'text-center'
            },
            {
                targets: [10],
                className: 'text-right'
            },
            {
                targets: [0,1,2],
                sortable: false
            }
          ],
          footerCallback: function ( row, data, start, end, display ) {
              var api = this.api(), data;
              var payment_total = api
              .column( 10 )
              .data()
              .reduce( function (a, b) {
                return parseInt(a) + parseInt(b);
              }, 0 );
              $( api.column( 10 ).footer() ).html('<?php echo $curency_icon;?>'+payment_total);
          },
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

      $(document).on('change', '#payment_date_range_val', function(event) {
        event.preventDefault(); 
        table.draw();
      });
  });
  
 
</script>