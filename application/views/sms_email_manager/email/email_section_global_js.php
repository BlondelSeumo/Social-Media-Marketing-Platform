<script>
	$(document).ready(function($) {
		var base_url = '<?php echo base_url(); ?>';
        var user_id = '<?php echo md5($this->user_id); ?>';
		var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";

		$('[data-toggle=\"tooltip\"]').tooltip();

		$(".yscroll").mCustomScrollbar({
		  autoHideScrollbar:true,
		  theme:"rounded-dark"
		});

		/* Creating Firstname text button for summernote texteditor */
		var firstName = function (context) {
		  var ui = $.summernote.ui;

		  // create button
		  var button = ui.button({
		    contents: '<i class="fas fa-user"/> '+'<?php echo $this->lang->line("First Name") ?>',
		    container: 'body',
		    tooltip: '<?php echo $this->lang->line("You can include #FIRST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>',
		    click: function () {
		      context.invoke('editor.insertText', ' #FIRST_NAME# ');
		    }
		  });

		  return button.render(); 
		}

		/* creating Lastname text button for summernote texteditor */
		var lastName = function (context) {
		  var ui = $.summernote.ui;

		  // create button
		  var button = ui.button({
		    contents: '<i class="fas fa-user"/> '+'<?php echo $this->lang->line("Last Name") ?>',
		    container: 'body',
		    tooltip: '<?php echo $this->lang->line("You can include #LAST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>',
		    click: function () {
		      context.invoke('editor.insertText', ' #LAST_NAME# ');
		    }
		  });

		  return button.render();
		}

		/* Creating Unsubscriber text button for summernote texteditor */
		var unsubscriberlink = function (context) {
		  var ui = $.summernote.ui;

		  // create button
		  var button = ui.button({
		    contents: '<i class="fas fa-bell-slash"/> '+'<?php echo $this->lang->line("Unsubscribe Link") ?>',
		    container: 'body',
		    tooltip: '<?php echo $this->lang->line("You can include #UNSUBSCRIBE_LINK# variable inside your message. The variable will be replaced by real value when we will send it.") ?>',
		    click: function () {
		      context.invoke('editor.insertText', ' #UNSUBSCRIBE_LINK# ');
		    }
		  });

		  return button.render();
		}


		/* button style extra toolbar in summernote */
		$('#message').summernote({
			height: 300,	
			toolbar: [
			    ['style', ['style']],
			    ['font', ['bold', 'underline', 'clear']],
			    ['fontname', ['fontname']],
			    ['color', ['color']],
			    ['para', ['ul', 'ol', 'paragraph']],
			    ['table', ['table']],
			    ['insert', ['link', 'picture']],
			    ['view', ['codeview']],
			    ['mybutton', ['first_name','last_name','unsubscriberLink']]
			],

			buttons: {
			  first_name: firstName,
			  last_name: lastName,
			  unsubscriberLink: unsubscriberlink,
			},
			placeholder: '<?php echo $this->lang->line('Write your message here...'); ?>'
		});

		$('div.note-group-select-from-files').remove();

		/* calendar in date input field */
		var today = new Date();
		var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
		$('.datepicker_x').datetimepicker({
		    theme:'light',
		    format:'Y-m-d H:i:s',
		    formatDate:'Y-m-d H:i:s',
		    minDate: today,
		    maxDate: next_date

		})

		/* Check Email valid or not from Email API section */
		function validateEmail(email) {
		  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
		  return regex.test(email);
		}

		/* SMTP Email Datatable */
		var perscroll1;
		var table1 = $("#mytable1").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: true,
		    order: [[ 1, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		      "url": base_url+'sms_email_manager/smtp_config_data',
		      "type": 'POST'
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
		          targets: '',
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
		// End of datatable section	

		// ===============================================================================
		// 									Test Email Sednding Section
		// ===============================================================================
		$(document).on('click', '.send_testmail', function(event) {
	  		event.preventDefault();

	  		var table_id = $(this).attr("table_id");
	  		$("#table_id").val(table_id);
	  		$("#modal_send_test_email").modal();
		});

		$("#modal_send_test_email").on('hidden.bs.modal', function(event) {
			event.preventDefault();
			$("#table_id").val("");
			$("#recipient_email").val("").removeClass('is-invalid');
			$("#test_subject").val("").removeClass('is-invalid');
			$("#test_message").val("").removeClass('is-invalid');
			$('#test_message').summernote('reset');
			$("#show_message").removeClass('alert alert-light').html(""); 
			table1.draw();
		});

		$(document).on('click', '#send_test_email', function(event) {
			event.preventDefault();

			var table_id = $("#table_id").val();
			var service_type = $("#service_type").val();
			var email=$("#recipient_email").val();
			var subject=$("#test_subject").val();
			var message=$("#test_message").val(); 

			if(email=='') {
				$("#recipient_email").addClass('is-invalid');
				return false;
			}
			else {
				$("#recipient_email").removeClass('is-invalid');
			}

			if(!validateEmail(email) && email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}

			if(subject=='') {
				$("#test_subject").addClass('is-invalid');
				return false;
			}
			else {
				$("#test_subject").removeClass('is-invalid');
			}

			if(message=='') {
				$("#test_message").addClass('is-invalid');
				return false;
			}
			else {
				$("#test_message").removeClass('is-invalid');
			}

			$(this).addClass('btn-progress');
			$("#show_message").html('');
			$.ajax({
				context: this,
				type:'POST' ,
				url: "<?php echo site_url(); ?>sms_email_manager/send_test_mail",
				data:{table_id:table_id,service_type:service_type,email:email,message:message,subject:subject},
				success:function(response){

					$(this).removeClass('btn-progress');
					$("#show_message").addClass("alert alert-light");
					$("#show_message").html(response);
				}
			});

		});

		// ===============================================================================
		// 									Test Email Sednding Section
		// ===============================================================================



		$(document).on('click', '.new_smtp', function(event) {
			event.preventDefault();
			$("#new_smtp_api_form_modal").modal();
		});

		/* SMTP Email Info saving section */
		$(document).on('click', '#save_smtp', function(event) {
			event.preventDefault();

			var smtp_email 	  = $("#smtp_email").val();
			var smtp_host     = $("#smtp_host").val();
			var smtp_port 	  = $("#smtp_port").val();
			var smtp_username = $("#smtp_username").val();
			var smtp_password = $("#smtp_password").val();
			var smtp_type 	  = $("#smtp_type").val();
			var smtp_status   = $("#smtp_status").val();

			if(smtp_email == "" || smtp_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Email is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(smtp_email) && smtp_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(smtp_host == "" || smtp_host == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Host is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_port == "" || smtp_port == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Port is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_username == "" || smtp_username == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Username is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_password == "" || smtp_password == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Password is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_type == "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Type is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#add_new_smtp")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_save_smtp_api',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#new_smtp_api_form_modal").on('hidden.bs.modal', function ()
		{
		    $("#add_new_smtp").trigger('reset');
		    $("#smtp_type").change();
		    table1.draw();
		});

		$("#update_smtp_api_form_modal").on('hidden.bs.modal', function ()
		{
		    table1.draw();
		});

		$(document).on('click', '.edit_smtp', function(event) {
			event.preventDefault();
			
			$("#update_smtp_api_form_modal").modal();
			var table_id = $(this).attr("table_id");

			var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
			$("#update_form_body").html(loading);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_get_smtp_api_info',
				type: 'POST',
				data: {table_id: table_id},
				success:function(response){
					$("#update_form_body").html(response);
				}
			})
		});

		$(document).on('click', '#update_smtp', function(event) {
			event.preventDefault();

			var smtp_email 	  = $("#updated_smtp_email").val();
			var smtp_host     = $("#updated_smtp_host").val();
			var smtp_port 	  = $("#updated_smtp_port").val();
			var smtp_username = $("#updated_smtp_username").val();
			var smtp_password = $("#updated_smtp_password").val();
			var smtp_type 	  = $("#updated_smtp_type").val();
			var smtp_status   = $("#updated_smtp_status").val();

			if(smtp_email == "" || smtp_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Email is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(smtp_email) && smtp_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(smtp_host == "" || smtp_host == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Host is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_port == "" || smtp_port == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Port is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_username == "" || smtp_username == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Username is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_password == "" || smtp_password == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Password is Required"); ?>', 'warning');
			    return;
			}
			if(smtp_type == "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("SMTP Type is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var updatedData = new FormData($("#smtp_api_update_form")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_update_smtp_api',
				type: 'POST',
				dataType: 'json',
				data: updatedData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$(document).on('click','.delete_smtp',function(e){
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
		                url:"<?php echo base_url('sms_email_manager/delete_smtp_api')?>",
		                data:{table_id:table_id},
		                success:function(response){ 

		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('API Information has been Deleted Successfully.'); ?>',position: 'bottomRight'});
		                        table1.draw();
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
		                    }
		                }
		            });
		        } 
		    });
		});


		// Mailgun section 
		var perscroll2;
		var table2 = $("#mytable2").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: true,
		    order: [[ 1, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		      "url": base_url+'sms_email_manager/mailgun_config_data',
		      "type": 'POST'
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
		          targets: [0,1,2,5,6],
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

		$(document).on('click', '.new_mailgun', function(event) {
			event.preventDefault();
			$("#new_mailgun_api_form_modal").modal();
		});

		$(document).on('click', '#save_mailgun', function(event) {
			event.preventDefault();

			var mailgun_email = $("#mailgun_email").val();
			var mailgun_domain = $("#mailgun_domain").val();
			var mailgun_api_key = $("#mailgun_api_key").val();
			

			if(mailgun_email == "" || mailgun_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(mailgun_email) && mailgun_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(mailgun_domain == "" || mailgun_domain == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Domain is Required"); ?>', 'warning');
			    return;
			}
			if(mailgun_api_key == "" || mailgun_api_key == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("API Key is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#add_new_mailgun")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_mailgun_api_save',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#new_mailgun_api_form_modal").on('hidden.bs.modal', function ()
		{
			$("#add_new_mailgun").trigger('reset');
		    table2.draw();
		});

		$(document).on('click', '.edit_mailgun_api', function(event) {
			event.preventDefault();

			$("#update_mailgun_api_form_modal").modal();
			var table_id = $(this).attr("table_id");

			var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
			$("#update_mailgun_form_body").html(loading);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_get_mailgun_api_info',
				type: 'POST',
				data: {table_id: table_id},
				success:function(response){
					$("#update_mailgun_form_body").html(response);
				}
			})
		});

		$(document).on('click', '#update_mailgun', function(event) {
			event.preventDefault();

			var mailgun_email = $("#updated_mailgun_email").val();
			var mailgun_domain = $("#updated_mailgun_domain").val();
			var mailgun_api_key = $("#updated_mailgun_api_key").val();
			

			if(mailgun_email == "" || mailgun_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(mailgun_email) && mailgun_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(mailgun_domain == "" || mailgun_domain == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Domain is Required"); ?>', 'warning');
			    return;
			}
			if(mailgun_api_key == "" || mailgun_api_key == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("API Key is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#update_mailgun_api_form")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_update_mailgun_api_info',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#update_mailgun_api_form_modal").on('hidden.bs.modal', function ()
		{
		    table2.draw();
		});


		$(document).on('click','.delete_mailgun_api',function(e){
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
		                url:"<?php echo base_url('sms_email_manager/delete_mailgun_api')?>",
		                data:{table_id:table_id},
		                success:function(response){ 

		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('API Information has been Deleted Successfully.'); ?>',position: 'bottomRight'});
		                        table2.draw();
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
		                    }
		                }
		            });
		        } 
		    });
		});


		// Mandrill API Section
		var perscroll3;
		var table3 = $("#mytable3").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: true,
		    order: [[ 1, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		      "url": base_url+'sms_email_manager/mandrill_config_data',
		      "type": 'POST'
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
		          targets: '',
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
		        if (perscroll3) perscroll3.destroy();
		        perscroll3 = new PerfectScrollbar('#mytable3_wrapper .dataTables_scrollBody');
		      }
		    },
		    scrollX: 'auto',
		    fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		      if(areWeUsingScroll)
		      { 
		        if (perscroll3) perscroll3.destroy();
		        perscroll3 = new PerfectScrollbar('#mytable3_wrapper .dataTables_scrollBody');
		      }
		    }
		});

		$(document).on('click', '.new_mandrill', function(event) {
			event.preventDefault();
			$("#new_mandrill_api_form_modal").modal();
		});

		$(document).on('click', '#save_mandrill', function(event) {
			event.preventDefault();

			var name 		   = $("#mandrill_name").val();
			var mandrill_email = $("#mandrill_email").val();
			var mandrill_api   = $("#mandrill_api_key").val();

			if(name == "" || name == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Name is Required"); ?>', 'warning');
			    return;
			}
			if(mandrill_email == "" || mandrill_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(mandrill_email) && mandrill_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(mandrill_api == "" || mandrill_api == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("API Key is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#add_new_mandrill")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_mandrill_api_save',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#new_mandrill_api_form_modal").on('hidden.bs.modal', function ()
		{
			$("#add_new_mandrill").trigger('reset');
		    table3.draw();
		});

		$(document).on('click', '.edit_mandrill_api', function(event) {
			event.preventDefault();

			$("#update_mandrill_api_form_modal").modal();
			var table_id = $(this).attr("table_id");

			var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
			$("#update_mandrill_form_body").html(loading);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_get_mandrill_api_info',
				type: 'POST',
				data: {table_id: table_id},
				success:function(response){
					$("#update_mandrill_form_body").html(response);
				}
			})
		});

		$(document).on('click', '#update_mandrill', function(event) {
			event.preventDefault();

			var name 		   = $("#updated_mandrill_name").val();
			var mandrill_email = $("#updated_mandrill_email").val();
			var mandrill_api   = $("#updated_mandrill_api_key").val();

			if(name == "" || name == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Name is Required"); ?>', 'warning');
			    return;
			}
			if(mandrill_email == "" || mandrill_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(mandrill_email) && mandrill_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(mandrill_api == "" || mandrill_api == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("API Key is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#update_mandrill_api")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_update_mandrill_api_info',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#update_mandrill_api_form_modal").on('hidden.bs.modal', function ()
		{
		    table3.draw();
		});

		$(document).on('click','.delete_mandrill_api',function(e){
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
		                url:"<?php echo base_url('sms_email_manager/delete_mandrill_api')?>",
		                data:{table_id:table_id},
		                success:function(response){ 

		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('API Information has been Deleted Successfully.'); ?>',position: 'bottomRight'});
		                        table3.draw();
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
		                    }
		                }
		            });
		        } 
		    });
		});


		// Sendgrid Section
		var perscroll4;
		var table4 = $("#mytable4").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: true,
		    order: [[ 1, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		      "url": base_url+'sms_email_manager/sendgrid_config_data',
		      "type": 'POST'
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
		          targets: '',
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
		        if (perscroll4) perscroll4.destroy();
		        perscroll4 = new PerfectScrollbar('#mytable4_wrapper .dataTables_scrollBody');
		      }
		    },
		    scrollX: 'auto',
		    fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		      if(areWeUsingScroll)
		      { 
		        if (perscroll4) perscroll4.destroy();
		        perscroll4 = new PerfectScrollbar('#mytable4_wrapper .dataTables_scrollBody');
		      }
		    }
		});

		$(document).on('click', '.new_sendgrid', function(event) {
			event.preventDefault();
			$("#new_sendgrid_api_form_modal").modal();
		});

		$(document).on('click', '#save_sendgrid', function(event) {
			event.preventDefault();

			var sendgrid_email    = $("#sendgrid_email").val();
			var sendgrid_username = $("#sendgrid_username").val();
			var sendgrid_password = $("#sendgrid_password").val();

			if(sendgrid_email == "" || sendgrid_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email is Required"); ?>', 'warning');
			    return;
			}
			if(sendgrid_username == "" || sendgrid_username == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Username is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(sendgrid_email) && sendgrid_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(sendgrid_password == "" || sendgrid_password == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Password is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#add_new_sendgrid")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_sendgrid_api_save',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#new_sendgrid_api_form_modal").on('hidden.bs.modal', function ()
		{
			$("#add_new_sendgrid").trigger('reset');
		    table4.draw();
		});

		$(document).on('click', '.edit_sendgrid_api', function(event) {
			event.preventDefault();

			$("#update_sendgrid_api_form_modal").modal();
			var table_id = $(this).attr("table_id");

			var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
			$("#update_sendgrid_form_body").html(loading);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_get_sendgrid_api_info',
				type: 'POST',
				data: {table_id: table_id},
				success:function(response){
					$("#update_sendgrid_form_body").html(response);
				}
			})
		});

		$(document).on('click', '#update_sendgrid', function(event) {
			event.preventDefault();

			var sendgrid_email    = $("#updated_sendgrid_email").val();
			var sendgrid_username = $("#updated_sendgrid_username").val();
			var sendgrid_password = $("#updated_sendgrid_password").val();

			if(sendgrid_email == "" || sendgrid_email == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Email is Required"); ?>', 'warning');
			    return;
			}
			if(sendgrid_username == "" || sendgrid_username == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Username is Required"); ?>', 'warning');
			    return;
			}
			if(!validateEmail(sendgrid_email) && sendgrid_email != "")
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide valid email address"); ?>', 'warning');
			    return;
			}
			if(sendgrid_password == "" || sendgrid_password == null)
			{
			    swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Password is Required"); ?>', 'warning');
			    return;
			}

			$(this).addClass('btn-progress');
			var that = $(this);
			var savingsData = new FormData($("#update_sendgrid_api_form")[0]);

			$.ajax({
				url: base_url+'sms_email_manager/ajax_sendgrid_api_update',
				type: 'POST',
				dataType: 'json',
				data: savingsData,
				cache: false,
				contentType: false,
				processData: false,
				success:function(response){
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

		$("#update_sendgrid_api_form_modal").on('hidden.bs.modal', function ()
		{
		    table4.draw();
		});

		$(document).on('click','.delete_sendgrid_api',function(e){
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
		                url:"<?php echo base_url('sms_email_manager/delete_sendgrid_api')?>",
		                data:{table_id:table_id},
		                success:function(response){ 

		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('API Information has been Deleted Successfully.'); ?>',position: 'bottomRight'});
		                        table4.draw();
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
		                    }
		                }
		            });
		        } 
		    });
		});


		/*===================================================================================================================== */
		/* 										Email Campaign Section															*/
		/*===================================================================================================================== */

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


		/* Email Campaign Datatable Section Has been started from here */
		var perscroll_email;
		var table_email = $("#mytable_email_campaign").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: false,
		    order: [[ 1, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		        "url": base_url+'sms_email_manager/email_campaign_lists_data',
		        "type": 'POST',
		        data: function ( d )
		        {
		            d.campaign_status = $('#campaign_status').val();
		            d.post_date_range = $('#post_date_range_val').val();
		            d.searching_campaign = $('#searching_campaign').val();
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
					targets: 'no-sort', 
					orderable: false
				},
		        {
					targets: 'centering', 
					className: 'text-center'
				},
		        {
		          targets: [0,1,3,4],
		          className: 'text-center'
		        },
		        {
		          targets: [0,1,2],
		          sortable: false
		        }
		    ],
		    fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
		      if(areWeUsingScroll)
		      {
		        if (perscroll_email) perscroll_email.destroy();
		        perscroll_email = new PerfectScrollbar('#mytable_email_campaign_wrapper .dataTables_scrollBody');
		      }
		    },
		    scrollX: 'auto',
		    fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		      if(areWeUsingScroll)
		      { 
		        if (perscroll_email) perscroll_email.destroy();
		        perscroll_email = new PerfectScrollbar('#mytable_email_campaign_wrapper .dataTables_scrollBody');
		      }
		    }
		});

		$(document).on('change', '#campaign_status', function(event) {
		  event.preventDefault(); 
		  table_email.draw();
		});

		$(document).on('change', '#post_date_range_val', function(event) {
		  event.preventDefault(); 
		  table_email.draw();
		});

		$(document).on('click', '#email_search_submit', function(event) {
		  event.preventDefault(); 
		  table_email.draw();
		});
		/* Email Campaign datatable section ended here */
		

		$(".schedule_block_item").hide();

		$(document).on('change','input[name=schedule_type]',function(){
		    var schedule_type = $("input[name=schedule_type]:checked").val();

		    if(typeof(schedule_type)=="undefined"){
		        $(".schedule_block_item").show();
		    }
		    else
		    {
		        $("#schedule_time").val("");
		        $("#time_zone").val("");
		        $(".schedule_block_item").hide();
		    }
		});

	   	$("#uploademail_attachment").uploadFile({
			url:base_url+"sms_email_manager/ajax_attachment_upload",
			fileName:"file",
			maxFileSize:20*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			acceptFiles:".png,.jpg,.jpeg,docx,.txt,.pdf,.ppt,.zip,.avi,.mp4,.mkv,.wmv,.mp3",
			maxFileCount:1,
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('sms_email_manager/delete_attachment');?>";
			    for (var i = 0; i < data.length; i++) {
			        $.post(delete_url, {op: "delete",name: data[i]},
			            function (resp,textStatus, jqXHR) {                
			            });
			    }
		  	}
		});

	   	$(document).on('change','#page,#label_ids,#excluded_label_ids,#user_gender,#user_time_zone,#user_locale',function(){
	   	    var page_id=$("#page").val();
	   	    var user_gender=$("#user_gender").val();
	   	    var user_time_zone=$("#user_time_zone").val();
	   	    var user_locale=$("#user_locale").val();
	   	    var label_ids=$("#label_ids").val();
	   	    var excluded_label_ids=$("#excluded_label_ids").val();

	   	    if(typeof(label_ids)==='undefined') label_ids = "";
	   	    if(typeof(excluded_label_ids)==='undefined') excluded_label_ids = "";

	   	    var load_label='0';
	   	    if($(this).attr('id')=='page') load_label='1';

	   	    if(load_label=='1')
	   	    {
	   	        $("#dropdown_con").removeClass('hidden');
	   	        $("#first_dropdown").html('<?php echo $this->lang->line("Loading labels..."); ?>');
	   	        $("#second_dropdown").html('<?php echo $this->lang->line("Loading labels..."); ?>');
	   	    }

	   	    $("#page_subscriber").html('<i class="fas fa-spinner fa-spin"></i>');
	   	    $("#total_targetted_subscribers").html('<i class="fas fa-spinner fa-spin"></i>');

	   	    if(page_id=="")
	   	    {
	   	        $("#page_subscriber,#targetted_subscriber").html("0");
	   	    }

	   	    $.ajax({
	   	        type:'POST' ,
	   	        url: base_url+"sms_email_manager/get_subscribers_email",
	   	        data: {page_id:page_id,label_ids:label_ids,excluded_label_ids:excluded_label_ids,user_gender:user_gender,user_time_zone:user_time_zone,user_locale:user_locale,load_label:load_label},
	   	        dataType : 'JSON',
	   	        success:function(response){

	   	            if(load_label=='1')
	   	            {
	   	                $("#dropdown_con").removeClass('hidden');
	   	                $("#first_dropdown").html(response.first_dropdown);
	   	                $("#second_dropdown").html(response.second_dropdown);
	   	            }

	   	            var totalpagesubscribers = response.pageinfo.page_total_subscribers;
	   	            var targettedReach = response.pageinfo.subscriber_count;

	   	            $("#page_subscriber").html(targettedReach + "/"+totalpagesubscribers);

	   	            /* Section is for showing total subscribers = targetted_subscribers + group_emails; */
	   	            var ext_subscribers = $("#contact_emails").html();
	   	            var findOriginal = ext_subscribers.split("/");

	   	            var totalTargettedSubscribers = parseInt(findOriginal[0]) + parseInt(targettedReach);

	   	            var totalSubscribers = parseInt(findOriginal[1]) + parseInt(totalpagesubscribers);

	   	            $("#total_targetted_subscribers").html(totalTargettedSubscribers+"/"+totalSubscribers);

	   	            if(load_label=='1')
	   	            {
	   	                if (typeof(xlabels)!=='undefined' && xlabels!="") 
	   	                {
   	                    	var xlabels_array = xlabels.split(',');

	   	                    $("#label_ids").val(xlabels_array).trigger('change');
	   	                }
	   	                if (typeof(xexcluded_label_ids)!=='undefined' && xexcluded_label_ids!="") 
	   	                {
	   	                    var xexcluded_array = xexcluded_label_ids.split(',');
	   	                    $("#excluded_label_ids").val(xexcluded_array).trigger('change');
	   	                }
	   	            }

	   	            $(".waiting").hide();
	   	        }

	   	    });
	   	});

	   	$(document).on('select2:select','#label_ids',function(e){   
	   	    var label_id = e.params.data.id;
	   	    var temp;

	   	    var excluded_label_ids = $("#excluded_label_ids").val();
	   	    for(var i=0;i<excluded_label_ids.length;i++)
	   	    {
	   	        if(parseInt(excluded_label_ids[i])==parseInt(label_id))
	   	        {
	   	            temp = "#label_ids option[value='"+label_id+"']";
	   	            $(temp).prop("selected", false);
	   	            $("#label_ids").trigger('change');
	   	            return false;
	   	        }
	   	    }
	   	});


	   	$(document).on('select2:select','#excluded_label_ids',function(e){
	   	    var label_id = e.params.data.id;
	   	    var temp;

	   	    var label_ids = $("#label_ids").val();
	   	    for(var i=0;i<label_ids.length;i++)
	   	    {
	   	        if(parseInt(label_ids[i])==parseInt(label_id))
	   	        {
	   	            temp = "#excluded_label_ids option[value='"+label_id+"']";
	   	            $(temp).prop("selected", false);
	   	            $("#excluded_label_ids").trigger('change');
	   	            return false;
	   	        }
	   	    }

	   	});


	   	$(document).on('change', '#contacts_id', function(event) {
	   	    event.preventDefault();

	   	    var contact_ids = $("#contacts_id").val();
	   	    var total_pageSubscribers = $("#page_subscriber").html();
	   	    var splitedPageSubscribers = total_pageSubscribers.split("/");
	   	    var targetted_page_subscribers = splitedPageSubscribers[0];
	   	    var totalPageSubscribersSplited = splitedPageSubscribers[1];
	   	    var prevContacts = $("#contact_emails").html();

	   	    $("#contact_emails").html('<i class="fas fa-spinner fa-spin"></i>');
	   	    $("#total_targetted_subscribers").html('<i class="fas fa-spinner fa-spin"></i>');

	   	    if(contact_ids != "") {

		   	    $.ajax({
		   	        url: base_url+'sms_email_manager/contacts_total_emails',
		   	        type: 'POST',
		   	        dataType: "JSON",
		   	        data: {contact_ids: contact_ids},
		   	        success:function(response){

		   	        	var totalTargetted_subscribers = parseInt(targetted_page_subscribers) + parseInt(response.total_contact_with_email);
		   	        	var allSubscribers = parseInt(totalPageSubscribersSplited) + parseInt(response.total_contact);

		   	        	$("#contact_emails").html(parseInt(response.total_contact_with_email) +"/"+ parseInt(response.total_contact));
		   	        	$("#total_targetted_subscribers").html(totalTargetted_subscribers+"/"+allSubscribers);

		   	        }
		   	    })

	   	    } else {
	   	    	
   	        	$("#contact_emails").html("0/0");
   	        	var x = parseInt("0") + parseInt(targetted_page_subscribers); 
   	        	var y = parseInt("0") + parseInt(totalPageSubscribersSplited); 
   	        	$("#total_targetted_subscribers").html(x+"/"+y);
   	        }

	   	});


		$(document).on('click','#create_campaign',function(){

		    var campaign_name = $("#campaign_name").val();
		    var email_subject = $("#email_subject").val();
		    var message       = $("#message").val();
            var template_id   = $("#email-template").val();
		    var contacts_id   = $("#contacts_id").val();
		    var from_email      = $("#from_email").val();
		    var schedule_type = $("input[name=schedule_type]:checked").val();
		    var schedule_time = $("#schedule_time").val();
		    var time_zone     = $("#time_zone").val();
		    var page_name     = $("#page").val();

		    /* Campaign Name can't be empty */
		    if(campaign_name =='') {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please give a campaign name"); ?>", 'warning');
		        return;
		    }

		    
		    /* Email SUbject can't be empty */
		    if(email_subject =='') {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Email Subject is Required"); ?>", 'warning');
		        return;
		    }

		    /* Email Message field can't be empty */
		    if('' === message && '' === template_id) {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please write your message or select an email template."); ?>", 'warning');
		        return;
		    }

		    /* Must select an email API to send emails */
		    if(from_email =='') {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select a Email API"); ?>", 'warning');
		        return;
		    }

		    // contact group and manual number
		    if(page_name=="" && (contacts_id == "" || contacts_id == null || typeof(contacts_id) == 'undefined')) {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please Select Contacts from Page or Contact Groups."); ?>", 'warning');
		        return;

		    }

		    // if schedule is later
		    if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone=="")) {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select schedule time/time zone."); ?>", 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress');
		    var that = $(this);

		    var report_link = base_url+"sms_email_manager/email_campaign_lists";
		    var success_message = "<?php echo $this->lang->line('Campaign have been submitted successfully.'); ?> <a href='"+report_link+"'><?php echo $this->lang->line('See report here.'); ?></a>";

		    var queryString = new FormData($("#email_campaign_form")[0]);
		    
		    $.ajax({
		        url:base_url+'sms_email_manager/create_email_campaign_action',
		        type:'POST',
		        data: queryString,
		        dataType: 'JSON',
		        cache: false,
		        contentType: false,
		        processData: false,
		        success:function(response)
		        {
		            $(that).removeClass('btn-progress');

		            if(response.status=='1')
		            {
		              var span = document.createElement("span");
		              span.innerHTML = success_message;
		              swal({ title:'<?php echo $this->lang->line("Campaign Submitted"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});
		            }
		            else 
		                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error').then((value) => {window.location.href=report_link;});
		        }
		    });
		
		});

		$(document).on('click','#update_campaign',function(){

		    var campaign_name = $("#campaign_name").val();
		    var email_subject = $("#email_subject").val();
		    var message       = $("#message").val();
		    var contacts_id   = $("#contacts_id").val();
            var template_id   = $("#email-template").val();
		    var from_email    = $("#from_email").val();
		    var schedule_type = $("input[name=schedule_type]:checked").val();
		    var schedule_time = $("#schedule_time").val();
		    var time_zone     = $("#time_zone").val();
		    var page_name     = $("#page").val();

		    /* Campaign Name can't be empty */
		    if(campaign_name =='')
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please give a campaign name"); ?>", 'warning');
		        return;
		    }

		    /* Email SUbject can't be empty */
		    if(email_subject =='')
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Email Subject is Required"); ?>", 'warning');
		        return;
		    }

		    /* Email Message field can't be empty */
		    if('' === message && '' === template_id) {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please write your message"); ?>", 'warning');
		        return;
		    }

		    /* Must select an email API to send emails */
		    if(from_email =='')
		    {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select a Email API"); ?>", 'warning');
		        return;
		    }

		    /* Check that atleast one section of subscribers are selected */
		    if(page_name=="" && (contacts_id == "" || contacts_id == null || typeof(contacts_id) == 'undefined')) {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please Select Contacts from Page or Contact Groups."); ?>", 'warning');
		        return;

		    }

		    /* If schedule type is later then timezone and schedule time is required */
		    if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone=="")) {
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select schedule time/time zone."); ?>", 'warning');
		        return;
		    }

		    $(this).addClass('btn-progress');
		    var that = $(this);

		    var report_link = base_url+"sms_email_manager/email_campaign_lists";
		    var success_message = "<?php echo $this->lang->line('Campaign have been Updated successfully.'); ?> <a href='"+report_link+"'><?php echo $this->lang->line('See report here.'); ?></a>";

		    var queryString = new FormData($("#update_email_campaign_form")[0]);
		    
		    $.ajax({
		        url:base_url+'sms_email_manager/edit_email_campaign_action',
		        type:'POST',
		        data: queryString,
		        dataType: 'JSON',
		        cache: false,
		        contentType: false,
		        processData: false,
		        success:function(response)
		        {
		            $(that).removeClass('btn-progress');

		            if(response.status=='1')
		            {
		              var span = document.createElement("span");
		              span.innerHTML = success_message;
		              swal({ title:'<?php echo $this->lang->line("Campaign Submitted"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});
		            }
		            else 
		                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error').then((value) => {window.location.href=report_link;});
		        }
		    });
		
		});

		var perscroll_email_campaign_report;
        table_email_campaign_report = $("#mytable_email_campaign_report").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: {
                url: base_url+'sms_email_manager/ajax_get_email_campaign_report_info',
                type: 'POST',
                data: function ( d )
                {
                    d.table_id = $("#put_row_id").val();
                    d.searching = $("#report_search").val();
                    d.rate_type = $("#rate_type").val();
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
					targets: [4,5,6],
					className: 'text-center'
              	},
              	{
					targets: [0,1,2,3,4],
					sortable: false
              	}
            ],
            fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll_email_campaign_report) perscroll_email_campaign_report.destroy();
                    perscroll_email_campaign_report = new PerfectScrollbar('#mytable_email_campaign_report_wrapper .dataTables_scrollBody');
            }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                if(areWeUsingScroll)
                { 
                if (perscroll_email_campaign_report) perscroll_email_campaign_report.destroy();
                perscroll_email_campaign_report = new PerfectScrollbar('#mytable_email_campaign_report_wrapper .dataTables_scrollBody');
                }
            }
        });

        $(document).on('change', '#rate_type', function(event) {
          event.preventDefault(); 
          table_email_campaign_report.draw();
        });

        $(document).on('click', '#email_report_search_submit', function(event) {
          event.preventDefault(); 
          table_email_campaign_report.draw();
        });



		// report table started
		// var table_email_campaign_report = '';
		// var perscroll_email_campaign_report;
		// $(document).on('click','.campaign_report',function(e){
		//   e.preventDefault();

		//     var table_id = $(this).attr('table_id');
		//     if(table_id !='') $("#put_row_id").val(table_id);

		//     var campaignName     = $(this).attr("campaign_name");
		//     var email_api     	 = $(this).attr("email_api");
		//     var successfullysent = $(this).attr("successfullysent");
		//     var totalThread      = $(this).attr("totalThread");
		//     var campaignSubject  = $(this).attr("campaign_subject");
		//     var campaignMessage  = $(this).attr("campaign_message");
		//     var campaignStatus   = $(this).attr("campaign_status");
		//     var attachment   	 = $(this).attr("attachment");

		//     $("#email_restart_button").hide();
		//     $("#attachment_div").hide();

		//     var posting_status = '';
		//     if(campaignStatus == '0') posting_status = 'Pending';
		//     if(campaignStatus == '1') posting_status = 'Processing';
		//     if(campaignStatus == '2') posting_status = 'Completed';
		//     if(campaignStatus == '3') {
		//         posting_status = 'Paused';
		//         $("#email_restart_button").show();
		//     }

		//     if(campaignStatus == '2') $("#options_div").hide();

		//     if(attachment != ""){
		//     	$("#attachment_div").show();
		//     	$("#attachment_btn").attr("title",attachment);
		//     }

		//     $("#email_campaign_name").html(campaignName);
		//     $("#api_name").html(email_api);
		//     $("#posting_status").html(posting_status);
		//     $("#sent_state").html(successfullysent+'/'+totalThread);

		//     $(".email_subject").html("<strong><h4 class='m-0'>"+campaignSubject+"</h4></strong>");
		//     $(".original_message").html(campaignMessage);

		//     $("#edit_content").attr("href",base_url+"sms_email_manager/edit_email_campaign_content/"+table_id);
		//     $("#email_restart_button").attr("table_id",table_id);

		//     $("#campaign_report_modal").modal();

		//     setTimeout(function(){
		// 	    if (table_email_campaign_report == '')
		// 	    {
		// 	        table_email_campaign_report = $("#mytable_email_campaign_report").DataTable({
		// 	            serverSide: true,
		// 	            processing:true,
		// 	            bFilter: false,
		// 	            order: [[ 1, "desc" ]],
		// 	            pageLength: 10,
		// 	            ajax: {
		// 	                url: base_url+'sms_email_manager/ajax_get_email_campaign_report_info',
		// 	                type: 'POST',
		// 	                data: function ( d )
		// 	                {
		// 	                    d.table_id = $("#put_row_id").val();
		// 	                    d.searching = $("#report_search").val();
		// 	                }
		// 	            },
		// 	            language: 
		// 	            {
		// 	              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
		// 	            },
		// 	            dom: '<"top"f>rt<"bottom"lip><"clear">',
		// 	            columnDefs: [
		// 	            	{
		// 	            		targets:[1],
		// 	            		visible: false
		// 	            	},
		// 	              	{
		// 						targets: [4,5,6],
		// 						className: 'text-center'
		// 	              	},
		// 	              	{
		// 						targets: [0,1,2,3,4],
		// 						sortable: false
		// 	              	}
		// 	            ],
		// 	            fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
		// 	            if(areWeUsingScroll)
		// 	            {
		// 	                if (perscroll_email_campaign_report) perscroll_email_campaign_report.destroy();
		// 	                    perscroll_email_campaign_report = new PerfectScrollbar('#mytable_email_campaign_report_wrapper .dataTables_scrollBody');
		// 	            }
		// 	            },
		// 	            scrollX: 'auto',
		// 	            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		// 	                if(areWeUsingScroll)
		// 	                { 
		// 	                if (perscroll_email_campaign_report) perscroll_email_campaign_report.destroy();
		// 	                perscroll_email_campaign_report = new PerfectScrollbar('#mytable_email_campaign_report_wrapper .dataTables_scrollBody');
		// 	                }
		// 	            }
		// 	        });
		// 	    }
		// 	    else table_email_campaign_report.draw();
		// 	},1000);
		// });

		$('#campaign_report_modal').on('hidden.bs.modal', function () {
		    $("#report_search").val("");
		    $("#email_campaign_name").html("");
		    $("#api_name").html("");
		    $("#posting_status").html("");
		    $(".email_subject").html('');
		    $(".original_message").html('');
		    $("#sent_state").html("");
		    $("#edit_content").attr("href","");

		    $(".email_accordion .accordion-header").addClass('collapsed');
		    $(".email_accordion .accordion-header").attr('aria-expanded','false');
		    $(".email_accordion .accordion-body").removeClass('show');

		    table_email_campaign_report.draw();
		    table_email.draw();
		});


		$(document).on('click', '#updateMessage', function(event) {
		    event.preventDefault();

		    var table_id = $("#table_id").val();
		    var message = $("#message").val();
		    if(message == "") {
		    	
		        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please type a message. System can not send blank message."); ?>", 'warning');
		        return;

		    }

		    $(this).addClass('btn-progress');
		    var that = $(this);

		    var queryString = new FormData($("#edit_message_form")[0]);
		    $.ajax({
		        type:'POST' ,
		        url: base_url+"sms_email_manager/edit_email_campaign_content_action",
		        data: queryString,
		        cache: false,
		        contentType: false,
		        processData: false,
		        success:function(response)
		        { 
		            $(that).removeClass('btn-progress');
		            var report_link = base_url+"sms_email_manager/email_campaign_lists";
		            if(response == "1")
		            {
		                swal({ title:'<?php echo $this->lang->line("Campaign Updated"); ?>', content:'<?php echo $this->lang->line("Campaign have been updated successfully."); ?>',icon:'success'}).then((value) => {window.location.href=report_link;});
		            }
		        }
		    });

		});

		// restart the camapaign where it is left
		$(document).on('click','.email_restart_button',function(e){
		    e.preventDefault();
		    var table_id = $(this).attr('table_id');

		    swal({
		        title: '<?php echo $this->lang->line("Force Resume"); ?>',
		        text: '<?php echo $this->lang->line('Do you want to resume this campaign?') ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
		            $.ajax({
		               context: this,
		               type:'POST' ,
		               url: "<?php echo base_url('sms_email_manager/restart_email_campaign')?>",
		               data: {table_id:table_id},
		               success:function(response)
		               {
		                    $(this).parent().prev().removeClass('btn-progress btn-outline-primary').addClass('btn-primary');
		                    if(response=='1') 
		                    {
		                        $("#campaign_report_modal").modal('hide');
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been resumed by force successfully."); ?>',position: 'bottomRight'});
		                        table_email.draw();
		                        table_email_campaign_report.draw();
		                    }       
		                    else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
		               }
		            });
		        } 
		    });

		});

		$(document).on('click','.pause_email_campaign_info',function(e){
		    e.preventDefault();
		    var table_id = $(this).attr('table_id');

		    swal({
		        title: '<?php echo $this->lang->line("Pause Campaign"); ?>',
		        text: '<?php echo $this->lang->line("Do you want to pause this campaign?"); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
		            $.ajax({
		               context: this,
		               type:'POST' ,
		               url: "<?php echo base_url('sms_email_manager/ajax_email_campaign_pause')?>",
		               data: {table_id:table_id},
		               success:function(response)
		               {
		                    $(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

		                    if(response=='1') 
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been paused successfully."); ?>',position: 'bottomRight'});
		                        table_email.draw();
		                    }       
		                    else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
		               }
		            });
		        } 
		    });

		});

		$(document).on('click','.play_email_campaign_info',function(e){
		    e.preventDefault();
		    var table_id = $(this).attr('table_id');
		    var somethingwentwrong = '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>';

		    swal({
		        title: '<?php echo $this->lang->line("Resume Campaign"); ?>',
		        text: '<?php echo $this->lang->line("Do you want to start this campaign?"); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
		            $.ajax({
		               context: this,
		               type:'POST' ,
		               url: "<?php echo base_url('sms_email_manager/ajax_email_campaign_play')?>",
		               data: {table_id:table_id},
		               success:function(response)
		               {
		                    $(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

		                    if(response=='1') 
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been resumed successfully."); ?>',position: 'bottomRight'});
		                        table_email.draw();
		                    }       
		                    else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
		               }
		            });
		        } 
		    });

		});

		$(document).on('click','.force_email',function(e){
		    e.preventDefault();
		    var id = $(this).attr('id');
		    var alreadyEnabled = "<?php echo $this->lang->line("This campaign is already enabled for processing."); ?>";
		    var doyoureallywanttoReprocessthiscampaign = "<?php echo $this->lang->line("Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and didn't send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?"); ?>";

		    swal({
		        title: '<?php echo $this->lang->line("Force Re-process Campaign"); ?>',
		        text: doyoureallywanttoReprocessthiscampaign,
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
		            $.ajax({
		               context: this,
		               type:'POST' ,
		               url: "<?php echo base_url('sms_email_manager/force_reprocess_email_campaign')?>",
		               data: {id:id},
		               success:function(response)
		               {
		                    $(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

		                    if(response=='1') 
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been re-processed by force successfully."); ?>',position: 'bottomRight'});
		                        table_email.draw();
		                    }       
		                    else iziToast.error({title: '',message: alreadyEnabled,position: 'bottomRight'});
		               }
		            });
		        } 
		    });

		});

		var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";
		$(document).on('click','.delete_email_campaign',function(e){
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
		            var campaign_id = $(this).attr('id');

		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('sms_email_manager/delete_email_campaign')?>",
		                data:{campaign_id:campaign_id},
		                success:function(response)
		                { 
		                    if(response == '1')
		                    {
		                        iziToast.success({title: '',message: '<?php echo $this->lang->line('Campaign has been deleted successfully.'); ?>',position: 'bottomRight'});
		                    } else
		                    {
		                        iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
		                    }

		                    table_email.draw();
		                }
		            });
		        } 
		    });
		});

        function prepare_select_options(data, selected = null) {

            var output = '<option value=""><?php echo $this->lang->line('Select template'); ?></option>';

            for (let key in data) {
                if (selected === key) {
                    output += '<option value="' + key + '" selected>' + data[key] + '</option>';
                } else {
                    output += '<option value="' + key + '">' + data[key] + '</option>';
                }
            }

            return output;
        }

        $(document).on('click', '#refresh-email-template', function(e) {
            e.preventDefault();

            var formData = new FormData();
            formData.append('user_id', user_id);

            $.ajax({
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                url: base_url + 'sms_email_manager/email_templates_list',
                success: function(res) {

                    var data = JSON.parse(res);

                    if (true === data.status) {

                        var select_container = $('#email-template');
                        var options = prepare_select_options(data.data);

                        select_container.html(options);

                        iziToast.info({
                            title: '<?php echo $this->lang->line('Action!'); ?>',
                            message: data.message,
                            position: 'bottomRight'
                        });
                        
                        return;

                    } else if (false === data.status) {

                        iziToast.info({
                            title: '<?php echo $this->lang->line('Action!'); ?>',
                            message: data.message,
                            position: 'bottomRight'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('status: ', status, 'error:', error);

                    iziToast.info({
                        title: '<?php echo $this->lang->line('Error!'); ?>',
                        message: error,
                        position: 'bottomRight'
                    });                  
                },
            });
            
        });

        $(document).on('click', '#rich-text-editor-tab, #drag-and-drop-tab', function(e) {
            e.preventDefault();

            var selected_tab = $(this)[0].id;

            $('#selected-tab').val(selected_tab);
        });

	});
</script>