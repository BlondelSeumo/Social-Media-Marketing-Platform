<section class="section section_custom pt-1">
    
  <div class="section-header d-none">
    <h1><i class="fas fa-box-open"></i> <?php echo $page_title;?></h1>
    <div class="section-header-button">
      <a href="<?php echo base_url('ecommerce/add_product'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Product"); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("Product"); ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

<!--   <style type="text/css">
    #search_store_id{width: 145px;}
    @media (max-width: 575.98px) {
      #search_store_id{width: 90px;}
    }
  </style> -->

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card no_shadow">
          <div class="card-body data-card p-0 pt-1 pr-3">
            <div class="row">
              <div class="col-7 col-md-9">
                <?php echo 
                '<div class="input-group mb-3" id="searchbox">
                  <div class="input-group-prepend d-none">
                    '.form_dropdown('search_store_id',$store_list,$this->session->userdata("ecommerce_selected_store"),'class="form-control select2" id="search_store_id"').'
                  </div>
                  <input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:400px;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>          

              <div class="col-5 col-md-3">             
                <a href="<?php echo base_url('ecommerce/add_product'); ?>" class="btn btn-lg btn-primary float-right"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?></a>                                 
              </div>

              <div class="col-12 d-none">
                <?php
                echo $drop_menu ='<a href="javascript:;" id="search_date_range" class="btn btn-lg btn-outline-primary float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="search_date_range_val">';
                ?>                                      
              </div>
            </div>

            <div class="table-responsive2">
                <input type="hidden" id="put_page_id">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th style="vertical-align:middle;width:20px">
                          <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                      </th>
                      <th><?php echo $this->lang->line("Thumb")?></th>                   
                      <th><?php echo $this->lang->line("Product")?></th>                   
                      <th><?php echo $this->lang->line("Price")?></th>                   
                      <th><?php echo $this->lang->line("Store")?></th>                   
                      <th><?php echo $this->lang->line("Status")?></th>                   
                      <th><?php echo $this->lang->line("Actions")?></th>                 
                      <th><?php echo $this->lang->line("Stock")?></th>                   
                      <th><?php echo $this->lang->line("Category")?></th>                   
                      <th><?php echo $this->lang->line("Updated at")?></th>                   
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
	  order: [[ 10, "desc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'ecommerce/product_list_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_store_id = $('#search_store_id').val();
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
	        targets: [1,5,9,10],
	        visible: false
	    },
	    {
	        targets: [2,4,6,7,8,10],
	        className: 'text-center'
	    },
	    {
	        targets: [2,7],
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


	$("document").ready(function(){	   

	    $(document).on('change', '#search_store_id', function(e) {
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



      	var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this products? Deleting product does not affect existing orders.'); ?>";
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
      	                url:"<?php echo base_url('ecommerce/delete_product')?>",
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
      	                    table1.draw(false);
      	                }
      	            });
      	        } 
      	    });
      	});


	});

</script>