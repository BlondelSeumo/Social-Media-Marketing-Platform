<?php $this->load->view('admin/theme/message'); ?>
<style>
	/*.dropdown-toggle::after{content:none !important;}*/
	.dropdown-toggle::before{content:none !important;}
	#page_id{width: 150px;}
	#campaign_name{max-width: 30%;}
	@media (max-width: 575.98px) 
	{
		#page_id{width: 90px;}
		#campaign_name{max-width: 50%;}
	}
</style>

<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fas fa-hand-point-up"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-button">
			<a class="btn btn-primary" href="<?php echo base_url("ultrapost/cta_poster");?>">
				<i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new Post"); ?>
			</a> 
		</div>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url("ultrapost");?>"><?php echo $this->lang->line("Facebook Poster"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

  	<div class="section-body">
	    <div class="row">
	      	<div class="col-12">
	        	<div class="card">
	          		<div class="card-body data-card">
		  	        	<div class="row">
		  	        		<div class="col-12 col-md-9">
		  		            	<div class="input-group mb-3 float-left" id="searchbox">
	          	  					<!-- search by page name -->
	          	  	          	    <div class="input-group-prepend">
	          	  	          	      	<select class="select2 form-control" id="page_id" name="page_id">
	          	  	          	        	<!-- <option value=""><?php echo $this->lang->line("Page Name"); ?></option> -->
	          	  			          	    <?php foreach ($fb_page_info as $key => $value): ?>
	          	  			          	    	<option value="<?php echo $value['id'];?>" <?php if($value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'selected'; ?>><?php echo $value['page_name'];?></option>
	          	  			          	    <?php endforeach ?>
	          	  	      	      		</select>
	          	  	          	    </div>
	          	  	          	    <input type="text" class="form-control" id="campaign_name" autofocus placeholder="<?php echo $this->lang->line('campaign name'); ?>" aria-label="" aria-describedby="basic-addon2">
		  			          	  	<div class="input-group-append">
		  			          	    	<button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
		  			      	 	 	</div>
		  		          		</div>
		  	        		</div>
		  	        		<div class="col-12 col-md-3">
		  	        			<a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
		  	        		</div>
		  	        	</div>
			            <div class="table-responsive2">
			              	<table class="table table-bordered" id="mytable">
				                <thead>
			                  	<tr>
        										<th>#</th>      
        										<th><?php echo $this->lang->line("Campaign ID"); ?></th>      
        										<th><?php echo $this->lang->line('Name'); ?></th>
        										<th><?php echo $this->lang->line('Campaign type'); ?></th>
        										<th><?php echo $this->lang->line('Publisher'); ?></th>
        										<th><?php echo $this->lang->line('CTA Button'); ?></th>
        										<th><?php echo $this->lang->line('Actions'); ?></th>
        										<th><?php echo $this->lang->line('Status'); ?></th>
        										<th><?php echo $this->lang->line('Scheduled at'); ?></th>
        										<th><?php echo $this->lang->line('Error'); ?></th>
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
</section> 


<script>
$(document).ready(function($) {
	// $('.datepicker').datetimepicker({
 //    theme:'light',
 //    format:'Y-m-d',
 //    formatDate:'Y-m-d',
 //    timepicker:false
 //    });

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    var base_url = '<?php echo base_url(); ?>';

    setTimeout(function(){ 
      $('#post_date_range').daterangepicker({
        ranges: {
          '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
          '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
          '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      }, function (start, end) {
        $('#post_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
      });
    }, 2000);

    // datatable section started
    var perscroll;
    var table = $("#mytable").DataTable({
      serverSide: true,
      processing:true,
      bFilter: false,
      order: [[ 1, "desc" ]],
      pageLength: 10,
      ajax: 
      {
        "url": base_url+'ultrapost/cta_post_list_data',
        "type": 'POST',
  	    data: function ( d )
  	    {
	        d.page_id = $('#page_id').val();
	        d.campaign_name = $('#campaign_name').val();
	        d.post_date_range = $('#post_date_range_val').val();
  	    }
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
          	targets: [3,6,7,8],
          	className: 'text-center'
          },
          {
          	targets:[0,1,3,5,6,7,9],
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

    $(document.body).on('change', '#page_id', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    $(document.body).on('change', '#post_date_range_val', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    $(document.body).on('click', '#search_submit', function(event) {
      event.preventDefault(); 
      table.draw();
    });
    // End of datatable section

    // report table started
    var table1 = '';
    $(document).on('click','.view_report',function(e){
      e.preventDefault();
      var perscroll1;
      var table_id = $(this).attr('table_id');

      $("#put_row_id").val(table_id);

      $("#view_report_modal").modal();

      if (table1 == '')
      {
        table1 = $("#mytable1").DataTable({
          serverSide: true,
          processing:true,
          bFilter: false,
          order: [[ 2, "desc" ]],
          pageLength: 10,
          ajax: {
            url: base_url+'ultrapost/ajax_cta_report',
            type: 'POST',
            data: function ( d )
            {
                d.table_id = $("#put_row_id").val();
                d.searching1 = $("#searching1").val();
            }
          },
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
              targets:[1],
              visible: false
            },
            {
                targets: [3,4,5,6],
                className: 'text-center'
            },
            {
                targets: [0,1,2,3,4,7],
                sortable: false
            }
          ],
          fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
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
          },
        });
      }
      else table1.draw();
    });

    $(document).on('keyup', '#searching1', function(event) {
      event.preventDefault(); 
      table1.draw();
    });

    $('#view_report_modal').on('hidden.bs.modal', function () {
      $("#put_row_id").val('');
      $("#searching1").val("");
      table.draw();
    });
    // End of reply table


	$(document).on('click','.delete',function(e){
	  e.preventDefault();
	  swal({
	    title: '<?php echo $this->lang->line("Are you sure?"); ?>',
	    text: "<?php echo $this->lang->line('Do you really want to delete this post from the database?'); ?>",
	    icon: 'warning',
	    buttons: true,
	    dangerMode: true,
	  })
	  .then((willDelete) => {
	    if (willDelete) 
	    {
	      var id = $(this).attr('id');

	      $.ajax({
	        context: this,
	        type:'POST' ,
	        url:"<?php echo base_url('ultrapost/delete_post')?>",
	        data: {id:id},
	        success:function(response){ 
	          iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
	          table.draw();
	        }
	      });
	    } 
	  });

	});

	$(document).on('click','.delete_p',function(e){
	  e.preventDefault();
	  swal({
	    title: '<?php echo $this->lang->line("Are you sure?"); ?>',
	    text: "<?php echo $this->lang->line('This is main campaign, if you want to delete it, rest of the sub campaign will be deleted.Do you really want to delete this post from the database?'); ?>",
	    icon: 'warning',
	    buttons: true,
	    dangerMode: true,
	  })
	  .then((willDelete) => {
	    if (willDelete) 
	    {
	      var id = $(this).attr('id');

	      $.ajax({
	        context: this,
	        type:'POST' ,
	        url:"<?php echo base_url('ultrapost/delete_post')?>",
	        data: {id:id},
	        success:function(response){ 
	          iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
	          table.draw();
	        }
	      });
	    } 
	  });

	});


	// $(document.body).on('click','.view_report',function(){
	// 	var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
	// 	$("#view_report_modal_body").html(loading);
	// 	$("#view_report").modal();
	// 	var table_id = $(this).attr('table_id');
	// 	$.ajax({
	//     	type:'POST' ,
	//     	url: base_url+"ultrapost/ajax_cta_report",
	//     	data: {table_id:table_id},
	//     	// async: false,
	//     	success:function(response){
	//          	$("#view_report_modal_body").html(response);
	//     	}

	//     });
	// });


	// only pending campaign
	$(document).on('click', '.not_see_report', function(event) {
	  event.preventDefault();
	  swal("","<?php echo $this->lang->line('Sorry, Only parent campaign has shown report.'); ?>","error");
	});

	$(document).on('click', '.not_published', function(event) {
	  event.preventDefault();
	  swal("","<?php echo $this->lang->line('Sorry, this post is not published yet.'); ?>",'error');
	});

	$(document).on('click', '.not_editable', function(event) {
	  event.preventDefault();
	  swal("","<?php echo $this->lang->line('Sorry, Only Pending Campaigns Are Editable.'); ?>",'error');
	});

	$(document).on('click', '.not_delete_campaign', function(event) {
	  event.preventDefault();
	  swal("","<?php echo $this->lang->line('Sorry, Processing Campaign Can not be deleted.'); ?>",'error');
	}); 

	$(document).on('click', '.not_embed_code', function(event) {
	  event.preventDefault();
	  swal("","<?php echo $this->lang->line('Sorry, Embed code is only available for published video posts.'); ?>",'error');
	});

});
	
</script>


<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-bullhorn"></i> <?php echo $this->lang->line("Report of CTA Poster");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body data-card">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <input type="text" id="searching1" name="searching1" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width: 200px;'>                                          
                  </div>
                  <div class="col-12">
                    <div class="table-responsive2">
                      <input type="hidden" id="put_row_id">
                      <table class="table table-bordered" id="mytable1">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("id"); ?></th>
                              <th><?php echo $this->lang->line("posting page or group"); ?></th>
                              <th><?php echo $this->lang->line("Post Type"); ?></th>
                              <th><?php echo $this->lang->line("Post ID"); ?></th>
                              <th><?php echo $this->lang->line("Posting Status"); ?></th>
                              <th><?php echo $this->lang->line("Schedule Time"); ?></th>
                              <th><?php echo $this->lang->line("Error"); ?></th>
                            </tr>
                          </thead>
                      </table>
                    </div>
                  </div> 
                </div>               
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade" id="view_report" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line("report of CTA Poster") ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body text-center" id="view_report_modal_body">                

            </div>
        </div>
    </div>
</div> -->

