"use strict";
$(document).ready(function($) {

  setTimeout(function(){ 
    $('#post_date_range').daterangepicker({
      ranges: {
        global_lang_last_30_days: [moment().subtract(29, 'days'), moment()],
        global_lang_this_month  : [moment().startOf('month'), moment().endOf('month')],
        global_lang_last_month  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
        "url": base_url+'instagram_poster/image_video_auto_post_list_data',
        "type": 'POST',
  	    data: function ( d )
  	    {
  	        d.page_id = $('#page_id').val();
  	        d.post_type = $('#post_type').val();
  	        d.searching = $('#searching').val();
  	        d.post_date_range = $('#post_date_range_val').val();
  	    }
      },
      language: 
      {
        url: base_url+"assets/modules/datatables/language/"+selected_language+".json"
      },
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      columnDefs: [
          {
            targets: [1],
            visible: false
          },
          {
          	targets: [0,1,3,5,6,7,8],
          	className: 'text-center'
          },
          {
          	targets:[0,1,3,4,6,7,9],
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

  $(document).on('change', '#page_id', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('change', '#post_type', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('change', '#post_date_range_val', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('click', '#search_submit', function(event) {
    event.preventDefault(); 
    table.draw();
  });
  // End of datatable section


  // report table started
  var table1 = '';
  var perscroll1;
  $(document).on('click','.view_report',function(e){
    e.preventDefault();
    var table_id = $(this).attr('table_id');

    $("#put_row_id").val(table_id);
    
    $("#view_report_modal").modal();

    setTimeout(function(){
      if (table1 == '')
      {
        table1 = $("#mytable1").DataTable({
          serverSide: true,
          processing:true,
          bFilter: false,
          order: [[ 2, "desc" ]],
          pageLength: 10,
          ajax: {
            url: base_url+'instagram_poster/ajax_get_text_report',
            type: 'POST',
            data: function ( d )
            {
                d.table_id = $("#put_row_id").val();
                d.searching1 = $("#searching1").val();
            }
          },
          language: 
          {
            url: base_url+"assets/modules/datatables/language/"+selected_language+".json"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
              targets:[1,3],
              visible: false
            },
            {
                targets: [3,4,5,6],
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
    }, 1000);
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
  $('#embed_code_modal').on('hidden.bs.modal', function () {
    table.draw();
  });
  // End of reply table


  $(document).on('click','.delete',function(e){
    e.preventDefault();
    swal({
      title: global_lang_are_you_sure,
      text: global_lang_delete_confirmation,
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
          url:base_url+"instagram_poster/image_video_delete_post",
          data:{id:id},
          success:function(response){ 
            iziToast.success({title: '',message: global_lang_campaign_deleted_successfully,position: 'bottomRight'});
            table.draw();
          }
        });
      } 
    });

  });

  $(document).on('click','.delete_p',function(e){
    e.preventDefault();
    swal({
      title: global_lang_are_you_sure,
      text: instragram_post_delete_main_confirm,
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
          url:base_url+"instagram_poster/image_video_delete_post",
          data:{id:id},
          success:function(response){ 
            iziToast.success({title: '',message: global_lang_campaign_deleted_successfully,position: 'bottomRight'});
            table.draw();
          }
        });
      } 
    });
  });


	$(document).on('click','.embed_code',function(){

		var id = $(this).attr("id");
		var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
    $("#embed_code_content").html(loading);
		$("#embed_code_modal").modal();

		$.ajax({
	       type:'POST' ,
	       url: base_url+"instagram_poster/image_video_get_embed_code",
	       data: {id:id},
	       success:function(response)
	       {
	       		$("#embed_code_content").html(response);
	       }
		});
	});


  // only pending campaign
  $(document).on('click', '.not_see_report', function(event) {
    event.preventDefault();
    swal("",instragram_post_message_sorry1,"error");
  });

  $(document).on('click', '.not_published', function(event) {
    event.preventDefault();
    swal("",instragram_post_message_sorry2,'error');
  });

  $(document).on('click', '.not_editable', function(event) {
    event.preventDefault();
    swal("",instragram_post_message_sorry3,'error');
  });

  $(document).on('click', '.not_delete_campaign', function(event) {
    event.preventDefault();
    swal("",instragram_post_message_sorry4,'error');
  }); 

  $(document).on('click', '.not_embed_code', function(event) {
    event.preventDefault();
    swal("",instragram_post_message_sorry5,'error');
  }); 
		
});
