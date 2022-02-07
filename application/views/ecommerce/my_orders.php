<?php 
$pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
$store_unique_id =  isset($store_data['store_unique_id']) ? $store_data['store_unique_id'] : "";
$currency = isset($ecommerce_config['currency']) ? $ecommerce_config['currency'] : "USD";
$currency_icon = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";

$form_action = base_url('ecommerce/store/'.$store_data['store_unique_id']);
$subscriber_id=$this->session->userdata($store_id."ecom_session_subscriber_id");
if($subscriber_id=="")$subscriber_id = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
$form_action = mec_add_get_param($form_action,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
?>
<?php $this->load->view('admin/theme/message'); ?>

<style type="text/css">
  @media (max-width: 575.98px) {
    #search_store_id{width: 75px;}
    #search_status{width: 80px;}
    #select2-search_store_id-container,#select2-search_status-container,#search_value{padding-left: 8px;padding-right: 5px;}
  }  
  .activities .activity .activity-detail:before{content: '' !important;}
</style>


<div class="row pt-3 pl-3 pr-3 pb-0">
  <div class="col-12 p-0">
    <div class="card bg-light no_shadow mb-0">
      <div class="card-body data-card p-0">
        <div class="row">
          <div class="col-6 col-md-4">
            <?php
            $status_list[''] = $this->lang->line("Status");                
            echo 
            '<div class="input-group mb-3" id="searchbox">
              <div class="input-group-prepend d-none">
                <input type="text" value="'.$store_id.'" name="search_store_id" id="search_store_id">
                <input type="text" value="'.$subscriber_id.'" name="search_subscriber_id" id="search_subscriber_id">
                <input type="text" value="'.$pickup.'" name="search_pickup" id="search_pickup">
              </div>
              <div class="input-group-prepend d-none">
                '.form_dropdown('search_status',$status_list,'','class="form-control select2" id="search_status"').'
              </div>
              <input type="text" class="form-control rounded-left" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
              </div>
            </div>'; ?>                                          
          </div>

          <div class="col-6 col-md-8 text-right">

        	<?php
	          echo $drop_menu ='<a href="javascript:;" id="search_date_range" class="btn btn-outline-primary btn-lg  icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="search_date_range_val">';
	        ?>

                                     
          </div>
        </div>

        <div class="table-responsive2">
            <input type="hidden" id="put_page_id">
            <table class="table table-bordered" id="mytable">
              <thead class="d-none">
                <tr>
                  <th>#</th>      
                  <th style="vertical-align:middle;width:20px">
                      <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                  </th>
                  <th><?php echo $this->lang->line("Order ID")?></th>
                  <th><?php echo $this->lang->line("Coupon")?></th>                
                  <th><?php echo $this->lang->line("Transaction ID")?></th>               
                  <th><?php echo $this->lang->line("My Data")?></th>               
              	</tr>
              </thead>
            </table>
        </div>
      </div>
    </div>
  </div>       
    
</div>
    


<script>

	var base_url="<?php echo site_url(); ?>";
	
	$('#search_date_range').daterangepicker({
	  ranges: {
	    '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
	    '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
	    '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	  },
	  startDate: moment().subtract(29, 'days'),
	  endDate  : moment()
	}, function (start, end) {
	  $('#search_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
	});

	var perscroll;
	var table1 = '';
	table1 = $("#mytable").DataTable({
	  serverSide: true,
	  processing:true,
	  bFilter: false,
	  order: [[ 2, "desc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'ecommerce/my_orders_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_store_id = $('#search_store_id').val();
            d.search_pickup = $('#search_pickup').val();
            d.search_subscriber_id = $('#search_subscriber_id').val();
            d.search_status = $('#search_status').val();
	          d.search_value = $('#search_value').val();
	          d.search_date_range = $('#search_date_range_val').val();
	      }
	  },
	  language: 
	  {
	    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
	  },
	  dom: '<"top"f>rt<"bottom"lip><"clear">',
	  columnDefs: [
	    {
	        targets: [0,1,2,3,4],
	        visible: false
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


	$("document").ready(function(){

      $(document).on('change', '#search_status', function(e) {
          table1.draw();
      });

	    $(document).on('change', '#search_date_range_val', function(e) {
        	e.preventDefault();
        	table1.draw();
      });

    	$(document).on('keypress', '#search_value', function(e) {
      	if(e.which == 13) $("#search_action").click();
    	});

    	$(document).on('click', '#search_action', function(event) {
      	event.preventDefault(); 
      	table1.draw();
    	});

      $(document).on('click', '#mp-download-file', function(e) {
        e.preventDefault();

        // Makes reference 
        var that = this;

        // Starts spinner
        $(that).removeClass('btn-outline-info');
        $(that).addClass('btn-info disabled btn-progress');

        // Grabs ID
        var file = $(this).data('id');

        // Requests for file
        $.ajax({
          type: 'POST',
          data: { file },
          dataType: 'JSON',
          url: '<?php echo base_url('ecommerce/manual_payment_download_file') ?>',
          success: function(res) {
            // Stops spinner
            $(that).removeClass('btn-info disabled btn-progress');
            $(that).addClass('btn-outline-info');

            // Shows error if something goes wrong
            if (res.error) {
              swal({
                icon: 'error',
                text: res.error,
                title: '<?php echo $this->lang->line('Error!'); ?>',
              });
              return;
            }

            // If everything goes well, requests for downloading the file
            if (res.status && 'ok' === res.status) {
              window.location = '<?php echo base_url('ecommerce/manual_payment_download_file'); ?>';
            }
          },
          error: function(xhr, status, error) {
            // Stops spinner
            $(that).removeClass('btn-info disabled btn-progress');
            $(that).addClass('btn-outline-info');

            // Shows internal errors
            swal({
              icon: 'error',
              text: error,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });
          }
        });
      });

      $(document).on('click', '.additional_info', function() { 
        $(this).addClass('btn-progress');       
        var cart_id = $(this).attr('data-id');
        $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo base_url('ecommerce/addtional_info_modal_content')?>",
            data:{cart_id:cart_id},
            success:function(response)
            { 
              $('.additional_info').removeClass('btn-progress'); 
              $('#manual-payment-modal .modal-body').html(response);
              $('#manual-payment-modal').modal();
            }
        });
      });


	});

</script>


<style type="text/css">
  a[aria-expanded="false"]::before, a[aria-expanded="true"]::before{content:'' !important;}
</style>



<div class="modal fade" tabindex="-1" role="dialog" id="manual-payment-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo $this->lang->line("Manual Payment Information");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
<?php include(APPPATH."views/ecommerce/common_style.php"); ?>