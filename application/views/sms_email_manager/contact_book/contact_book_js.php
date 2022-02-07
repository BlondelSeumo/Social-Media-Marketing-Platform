<script>
	$(document).ready(function($) 
	{
		var base_url = '<?php echo base_url(); ?>';

		$('[data-toggle=\"tooltip\"]').tooltip();

		$(document).on('change', '#rows_number', function(event) {
		    event.preventDefault();
		    $("#group_search_submit").click();
		});

		$(document).on('keypress', '.group_search', function(event) {
		    if(event.which == 13) event.preventDefault();
		}); 

		$(document).on('click', '.add_group', function(event) {
		    event.preventDefault();
		    $("#add_contact_group_modal").modal();
		});

		$(document).on('click', '#save_group', function(event) {
		    event.preventDefault();
		    
		    var group_name = $("#group_name").val();
		    if(group_name == '')
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Group Name is Required"); ?>', 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress')
		    var that = $(this);

		    $.ajax({
		        url: base_url+'sms_email_manager/add_contact_group_action',
		        type: 'POST',
		        data: {group_name: group_name},
		        success:function(response)
		        {
		            $(that).removeClass('btn-progress');

		            if(response == "1")
		            {
		                iziToast.success({title: '',message: '<?php echo $this->lang->line('Contact Group has been created successfully.'); ?>',position: 'bottomRight'});
		            } else if(response == "2")
		            {
		            	iziToast.error({title: '',message: '<?php echo $this->lang->line('Group Name Already Exists, please try with different one.'); ?>',position: 'bottomRight'});
		            } 
		            else 
		            {
		                iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
		            }

		        }
		    })
		});

		$(document).on('click', '.edit_group', function(event) {
		    event.preventDefault();

		    $("#update_contact_group_modal").modal();
		    var group_id = $(this).attr("group_id");
		    var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';

		    $("#group_body").html(loading);

		    $.ajax({
		        url: base_url+'sms_email_manager/ajax_get_group_info',
		        type: 'POST',
		        data: {group_id: group_id},
		        success:function(response)
		        {
		            if(response)
		            {
		                $("#group_body").html(response);
		            }
		        }     
		    })

		});

		$(document).on('click', '#update_group', function(event) {
		    event.preventDefault();

		    var table_id = $("#table_id").val();
		    var group_name = $("#update_group_name").val();
		    if(group_name == '')
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Group Name is Required"); ?>', 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress')
		    var that = $(this);

		    $.ajax({
		        url: base_url+'sms_email_manager/ajax_update_group_info',
		        type: 'POST',
		        data: {table_id: table_id,group_name:group_name},
		        success:function(response)
		        {
		            $(that).removeClass('btn-progress');

	                if(response == "1")
	                {
	                    iziToast.success({title: '',message: '<?php echo $this->lang->line('Contact Group has been Updated successfully.'); ?>',position: 'bottomRight'});
	                } else if(response == "2")
	            	{
	            		iziToast.error({title: '',message: '<?php echo $this->lang->line('Group Name Already Exists, please try with different one.'); ?>',position: 'bottomRight'});
	            	}  
	                else 
	                {
	                    iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
	                }
		        }     
		    })
		});

		var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";
		var Doyouwanttodeletealltheserecordsfromdatabase = "<?php echo $this->lang->line('Do you want to detete all the records from the database?'); ?>";

		$(document).on('click','.delete_group',function(e){
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
		            var table_id = $(this).attr('group_id');
		            var that = $(this);
		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('sms_email_manager/delete_contact_group')?>",
		                data:{table_id:table_id},
		                success:function(response)
		                { 
		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('Contact Group has been deleted successfully.'); ?>',position: 'bottomRight',timeout: 3000});
		                        $(that).parent().parent().parent().parent().parent().remove();
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight',timeout: 3000});
		                    }

		                    // setTimeout(function(){ location.reload(); }, 3000);
		                }
		            });
		        } 
		    });
		});


		// this is for contact list table
		var perscroll1;
		var table1 = $("#mytable1").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: false,
		    order: [[ 2, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		      "url": base_url+'sms_email_manager/contact_lists_data',
		      "type": 'POST',
		      data: function ( d )
		      {
		        d.group_id = $('#group_id').val();
		        d.contact_list_searching = $('#contact_list_searching').val();
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
		          targets: [0,1,2,5,6,8],
		          className: 'text-center'
		        },
		        {
		          targets: '',
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

		$(document).on('change', '#group_id', function(event) {
		  event.preventDefault(); 
		  table1.draw();
		});

		$(document).on('click', '#contact_list_search_submit', function(event) {
		  event.preventDefault(); 
		  table1.draw();
		});
		// end of contact list table


		$(document).on('click', '.add_new_contact', function(event) {
		    event.preventDefault();
		    $("#add_contact_form_modal").modal();
		});


		function validateEmail(email) {
		  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
		  return regex.test(email);
		}

		$(document).on('click', '#save_contact', function(event) {
		    event.preventDefault();

		    var first_name = $("#first_name").val();
		    var last_name = $("#last_name").val();
		    var contact_email = $("#contact_email").val();
		    var phone_number = $("#phone_number").val();
		    var contact_group_name = $("#contact_group_name").val();

		    if(first_name == "" && last_name == "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Name is Required"); ?>', 'warning');
		        return;
		    }

		    if(contact_email == "" && phone_number == "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email/Phone number is Required"); ?>', 'warning');
		        return;
		    }

		    if(!validateEmail(contact_email) && contact_email != "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
		        return;
		    }

		    if(contact_group_name == "" || contact_group_name == null || typeof(contact_group_name) == "undefined")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Contact Group is Required"); ?>', 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress')
		    var that = $(this);


		    var alldatas = new FormData($("#contact_add_form")[0]);

		    $.ajax({
		        url: base_url+'sms_email_manager/ajax_create_new_contact',
		        type: 'POST',
		        dataType: 'JSON',
		        data: alldatas,
		        cache: false,
		        contentType: false,
		        processData: false,
		        success:function(response)
		        {
		            $(that).removeClass('btn-progress');

		            if(response.status == "1")
		            {
		                iziToast.success({title: '',message: response.msg,position: 'bottomRight'});

		            } else 
		            {
		                iziToast.error({title: '',message: response.msg,position: 'bottomRight'});
		            }

		        }
		    })

		});

		$(document).on('click', '.edit_contact', function(event) {
		    event.preventDefault();
		    $("#update_contact_form_modal").modal();

		    var table_id = $(this).attr("table_id");
		    var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
		    $("#update_contact_modal_body").html(loading);

		    $.ajax({
		        url: base_url+'sms_email_manager/ajax_get_contact_update_info',
		        type: 'POST',
		        data: {table_id:table_id},
		        success:function(response)
		        {
		            $("#update_contact_modal_body").html(response);
		        }
		    })
		});


		$(document).on('click', '#update_contact', function(event) {
		    event.preventDefault();

		    var first_name = $("#updated_first_name").val();
		    var last_name = $("#updated_last_name").val();
		    var contact_email = $("#updated_contact_email").val();
		    var phone_number = $("#updated_phone_number").val();
		    var contact_group_name = $("#updated_contact_group_name").val();

		    if(first_name == "" && last_name == "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Name is Required"); ?>', 'warning');
		        return;
		    }

		    if(contact_email == "" && phone_number == "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email/Phone number is Required"); ?>', 'warning');
		        return;
		    }

		    if(!validateEmail(contact_email) && contact_email != "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
		        return;
		    }

		    if(contact_group_name == "" || contact_group_name == null || typeof(contact_group_name) == "undefined")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Contact Group is Required"); ?>', 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress')
		    var that = $(this);


		    var alldatas = new FormData($("#contact_update_form")[0]);

		    $.ajax({
		        url: base_url+'sms_email_manager/ajax_update_contact',
		        type: 'POST',
		        dataType: 'JSON',
		        data: alldatas,
		        cache: false,
		        contentType: false,
		        processData: false,
		        success:function(response)
		        {
		            $(that).removeClass('btn-progress');

		            if(response.status == "1")
		            {
		                iziToast.success({title: '',message: response.msg,position: 'bottomRight'});

		            } else 
		            {
		                iziToast.error({title: '',message: response.msg,position: 'bottomRight'});
		            }

		        }
		    })

		});

		var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";
		$(document).on('click','.delete_contact',function(e){
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
		                url:"<?php echo base_url('sms_email_manager/delete_contact')?>",
		                data:{table_id:table_id},
		                success:function(response)
		                { 
		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('Contact Group has been deleted successfully.'); ?>',position: 'bottomRight',timeout: 3000});
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight',timeout: 3000});
		                    }
		                    table1.draw();
		                }
		            });
		        } 
		    });
		});


		$(document).on('click', '#export_contact', function(event) {
		    event.preventDefault();

		    var contact_ids = [];
		    $(".datatableCheckboxRow:checked").each(function ()
		    {
		        contact_ids.push(parseInt($(this).val()));
		    });
		    
		    if(contact_ids.length==0) 
		    {
		        swal('<?php echo $this->lang->line("Warning")?>', '<?php echo $this->lang->line("You have to select Contacts to export.") ?>', 'warning');
		        return false;
		    }
		    else  
		    {
		        $(this).addClass('btn-progress');
		        $.ajax({
		            context: this,
		            type:'POST',
		            url: base_url+"sms_email_manager/ajax_export_contacts",
		            data:{info:contact_ids},
		            success:function(response){
		                $(this).removeClass('btn-progress');
		                if(response != '')
		                {
		                    var download_url = base_url+response;
		                    window.location.assign(download_url);
		                }
		            }
		        });
		    }
		});

		$(document).on('click', '#assign_sms_email_sequence', function(event) {
		  event.preventDefault();
		  var ids = [];

		  $(".datatableCheckboxRow:checked").each(function ()
		  {
		      ids.push(parseInt($(this).val()));
		  });

		  if(ids=="") 
		  {
		    swal('<?php echo $this->lang->line("Warning") ?>', '<?php echo $this->lang->line("You have to select Contacts to assign sequence.") ?>', 'warning');
		    return;
		  } 
		  
		  $.ajax({
		    type:'POST' ,
		    url: "<?php echo site_url(); ?>sms_email_manager/get_sequence_campaigns",
		    data:{ids:ids},
		    success:function(response)
		    {
		       $("#sequence_campaigns").html(response);
		    }
		  });  

		  $("#assign_sqeuence_campaign_modal").modal(); 

		});



		$(document).on('click','#assign_sequence_submit',function(e){
		  e.preventDefault();

		  var ids = [];
		  $(".datatableCheckboxRow:checked").each(function ()
		  {
		      ids.push(parseInt($(this).val()));
		  });
		  
		  if(ids=="") 
		  {
		    swal('<?php echo $this->lang->line("Warning") ?>', '<?php echo $this->lang->line("You have to select Contacts to assign sequence.") ?>', 'warning');
		    return;
		  } 

		  var sequence_id = $("#sequence_ids").val();
		  var page_id = $("#page_id").val();
		  var count = sequence_id.length;
		  
		  if(count==0) 
		  {
		    swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("You have to select campaign to assign.") ?>', 'error');
		    return;
		  } 

		  $("#assign_sequence_submit").addClass("btn-progress");

		  $.ajax({
		    type:'POST' ,
		    url: "<?php echo site_url(); ?>sms_email_manager/bulk_sequence_campaign_assign",
		    data:{ids:ids,sequence_id:sequence_id,page_id:'0'},
		    success:function(response)
		    {
		      $("#assign_sequence_submit").removeClass("btn-progress");
		      swal('<?php echo $this->lang->line("Sequence Campaign Assign") ?>', '<?php echo $this->lang->line("Sequence campaign have been assigned successfully"); ?>', 'success')
		      .then((value) => {
		        $("#assign_sqeuence_campaign_modal").modal('hide');  
		        table1.draw(false);
		      });

		    }
		  });  

		});


		$(document).on('click', '#delete_all_contacts', function(event) {
			event.preventDefault();

			var contact_ids = [];
			$(".datatableCheckboxRow:checked").each(function ()
			{
			    contact_ids.push(parseInt($(this).val()));
			});
			
			if(contact_ids.length==0) {

			    swal('<?php echo $this->lang->line("Warning")?>', '<?php echo $this->lang->line("You didn`t select any Contact to delete.") ?>', 'warning');
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
				    	    url: base_url+"sms_email_manager/ajax_delete_all_selected_contacts",
				    	    data:{info:contact_ids},
				    	    success:function(response){
				    	        $(this).removeClass('btn-progress');

				    	        if(response == '1') {

				    	        	iziToast.success({title: '',message: '<?php echo $this->lang->line('Selected Contacts has been deleted Successfully.'); ?>',position: 'bottomRight'});

				    	        } else {

				    	        	iziToast.success({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});

				    	        }

				    	        table1.draw();
				    	    }
				    	});

				    } 
				});
			}

		});

		$("#assign_sequence").select2();

		$(document).on('click', '.contact_details', function(event) {
			event.preventDefault();
			$("#contact_details_modal").modal();
			var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
			$("#details_body").html(loading);

			var id = $(this).attr('table_id');
			var groups = $(this).attr('groups');

			$.ajax({
				url: base_url+'sms_email_manager/get_contact_details',
				type: 'POST',
				data: {id: id,groups:groups},
				success:function(response) {
					$("#details_body").html(response);
				}
			})
			
		});

		$(document).on('click', '#assign_manual_sequence_submit', function(event) {
			event.preventDefault();

			var contact_id = $("#contact_id").val();
			var campaign_ids = $("#assign_campaign_id").val();
			var notes = $("#notes").val();
			$(this).addClass('btn-progress');

			$.ajax({
				context:this,
				url: base_url+'sms_email_manager/manual_assign_sequence',
				type: 'POST',
				data: {contact_id: contact_id,campaign_ids:campaign_ids,notes:notes},
				success:function(response) {
					$(this).removeClass('btn-progress');
					iziToast.success({title: '',message: '<?php echo $this->lang->line("Contact information has been saved successfully."); ?>',position: 'bottomRight'});
				}
			})

		});

		$(document).on('click', '.subscribe_unsubscribe_contact', function(event) {
			event.preventDefault();

			$(this).addClass('btn-progress');
			var contact_details_id = $(this).attr("id");
			$("#assign_manual_sequence_submit").addClass('btn-progress');

			$.ajax({
			  context: this,
			  type:'POST',
			  url: base_url+'sms_email_manager/subscribe_unsubscribe_contact_action',
			  data:{contact_details_id:contact_details_id},
			  success:function(response){
			     $(this).removeClass('btn-progress');
			     $("#assign_manual_sequence_submit").removeClass('btn-progress');
			     
			     var splitId = contact_details_id.split("-");

			     if(response == "unsubscribed") {

			     	var id = splitId[0];
			     	$(this).attr("id", id+"-1");
			     	$(this).text("(Subscribe)");
			     	$("#status").text("Unsubscribed");
			     	iziToast.success({title: '',message: '<?php echo $this->lang->line("Contact has been successfully unsubscribed.") ?>',position: 'bottomRight'});

			     } else if(response == 'subscribed') {

			     	var id = splitId[0];
			     	$(this).attr("id", id+"-0");
			     	$(this).text("(Unsubscribe)");
			     	$("#status").text("Subscribed")
			     	iziToast.success({title: '',message: '<?php echo $this->lang->line("Contact has been successfully subscribed.") ?>',position: 'bottomRight'});

			     } else {

			     	iziToast.error({title: '',message: '<?php echo $this->lang->line("Something went wrong, try once again.") ?>',position: 'bottomRight'});
			     }
			  }
			});
		});

		$("#contact_details_modal").on("hidden.bs.modal",function() {
			table1.draw();
		});


		$(document).on('click', '#import_contact', function(event) {
		    event.preventDefault();
		    $("#import_contacts_modal").modal();
		    $("#success_message_div").hide();
		});

		Dropzone.autoDiscover = false;
		$("#dropzone").dropzone({ 
		    url: "<?php echo site_url();?>sms_email_manager/ajax_import_csv_files",
		    maxFilesize:25,
		    uploadMultiple:false,
		    paramName:"file",
		    createImageThumbnails:true,
		    acceptedFiles: ".csv",
		    maxFiles:1,
		    addRemoveLinks:true,
		    success:function(file, response){
		        $("#csv_file").val(eval(response));
		    },
		    removedfile: function(file) {
		        var name = $("#csv_file").val();
		        if(name !="")
		        {
		            $(".dz-preview").remove();
		            $.ajax({
		                type: 'POST',
		                url: '<?php echo site_url();?>sms_email_manager/delete_uploaded_csv_file',
		                data: {op: "delete",name: name},
		                success: function(data){
		                    $("#csv_file").val('');
		                }
		            });
		        }
		        else
		        {
		            $(".dz-preview").remove();
		        }

		    },
		});

		$(document).on('click', '#upload_imported_csv', function(event) {
		    event.preventDefault();

		    var contact_group = $("#csv_group_id").val();
		    var csvFile = $("#csv_file").val();

		    if(contact_group == "" || contact_group == null || typeof(contact_group) == "undefined")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Contact Group is Required."); ?>', 'warning');
		        return;
		    }

		    if(csvFile == "")
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please Upload Contact CSV file."); ?>', 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress');
		    var that = $(this);

		    var imported_files_data = new FormData($("#import_contact_csv")[0]);

		    $.ajax({
		        url: base_url+'sms_email_manager/import_contact_action_ajax',
		        type: 'POST',
		        data: imported_files_data,
		        dataType:'json',
		        async: false,
		        cache: false,
		        contentType: false,
		        processData: false,
		        success:function(response){
		            $(that).removeClass('btn-progress');
		            var report_link = base_url+"sms_email_manager/contact_list";
		            $("#success_message_div").show();
		            if(response.status=='ok')
		            {   
	                	var total_inserted = response.total_inserted;
	                	var total_updated = response.total_updated;

	                	$("#success_message").html('<b>'+total_inserted+'</b> <?php echo $this->lang->line('Contacts Has been Inserted and'); ?> <b>'+total_updated+'</b> <?php echo $this->lang->line('Contacts Has been Updated.'); ?>');

	                	if(response.rejected == "1") {
	                		var totalRejected = response.total_rejected;
	                		var seethecontacts = '<b>'+totalRejected+'</b> <?php echo $this->lang->line('Contacts Has been rejected from insertion because of either email or phone number has already exists in the database.'); ?>'
	                		$("#contact_upload_error_file").html(seethecontacts);
	                	}
		            }
		            else
		            {
		                var error = response.status.replace(/<\/?[^>]+(>|$)/g, "");
		                $("#success_message").html(error);

		            }

		            $("#import_contact_modal_footer").addClass('hidden');

		        }
		    });
		    
		});

		
		$("#add_contact_form_modal").on('hidden.bs.modal', function ()
		{
		    $("#contact_add_form").trigger('reset');
		    $("#contact_group_name").change();
		    table1.draw();
		});

		$("#update_contact_form_modal").on('hidden.bs.modal', function ()
		{
		    table1.draw();
		});

		$("#import_contacts_modal").on('hidden.bs.modal', function ()
		{
			$("#import_contact_modal_footer").removeClass('hidden');
		    $("#csv_group_id").val("").change();
		    $(".dz-remove").click();
		    $("#csv_file").val("");
		    table1.draw();
		});


		$("#add_contact_group_modal,#update_contact_group_modal").on('hidden.bs.modal', function ()
		{
		    location.reload();
		});
		
	});
</script>