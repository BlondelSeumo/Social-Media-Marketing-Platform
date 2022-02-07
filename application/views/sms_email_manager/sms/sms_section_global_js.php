<script>
    $(document).ready(function($) {

        var base_url = '<?php echo base_url(); ?>';

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
        var table = $("#mytable").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: 
            {
              "url": base_url+'sms_email_manager/sms_api_list_data',
              "type": 'POST',
              data: function ( d )
              {
                  d.searching = $('#searching').val();
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
                  targets: [0,1,3,4,5],
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
        // End of datatable section

        $(document).on('click', '.see_api_details', function(event) {
        	event.preventDefault();

        	var table_id = $(this).attr("table_id");
        	$("#api_info").modal();

            $("#routesmsHostname_div").hide();

        	var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';

        	$("#api_info_modal_body").hide();
        	$("#info_body").append(loading);

        	$.ajax({
        		url: base_url+'sms_email_manager/api_infos',
        		type: 'POST',
        		dataType:'json',
        		data: {table_id: table_id},
        		success:function(response)
        		{
        			$(".waiting").remove();
        			$("#api_info_modal_body").show();

        			$("#auth_id_val").html(response.username_auth_id);
        			$("#api_secret_val").html(response.password_auth_token);
                    if(response.gateway_name == "routesms.com"){
                        $("#routesmsHostname_div").show();
                        $("#routesmsHostname_val").html(response.hostname);
                    }
        			$("#api_id_val").html(response.api_id);
        			$("#remaining_credits_val").html(response.remaining_credetis);

        		}
        	})
        	
        });

        function isURL(str) {
          var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
          '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
          '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
          '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
          '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
          '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
          return pattern.test(str);
        }

        var namesForAuthId = ['plivo','twilio','nexmo','planet','msg91.com','africastalking.com','routesms.com'];
        var namesForAuthToken = ['plivo','twilio','nexmo','planet','semysms.net','clickatell','routesms.com'];
        var namesForApiId = ['semysms.net','clickatell','clickatell-platform'];

        $(document).on('click', '.add_gateway', function(event) {
        	event.preventDefault();
        	$("#add_sms_api_form_modal").modal();
        });

        $(document).on('click', '.add_custom_gateway', function(event) {
            event.preventDefault();

            $("#custom_api_name").val("");
            $("#custom_api_url").val("");
            $("#analyzed_section").html("");

            $("#form_action_type").remove();
            $("#form_table_id").remove();

            $("#update_custom_api").prop('disabled', 'true');

            $("#custom_sms_api_modal").modal();
        });

        // Custom API POST METHOD

         $(document).on('click', '.add_custom_gateway_post_method', function(event) {
            event.preventDefault();

            $("#custom_api_name_post_method").val("");
            $("#custom_api_base_url_post_method").val("");
            $('#text_response_section').html('');

            $("#parameters_body").html('<tr><td><input type="text" name="key[]" class="form-control key" placeholder="Enter Key"></td><td><select name="types[]"  class="form-control types"><option value="fixed" selected>Fixed</option><option value="destination_number">DESTINATION_NUMBER</option><option value="message_content">MESSAGE_CONTENT</option></select></td><td><input type="text" name="value[]"  class="form-control value" placeholder="Enter value"></td><td></td></tr>')

            $("#form_action_type").remove();
            $("#form_table_id").remove();
            $("#update_custom_api").prop('disabled', 'true');
            $("#custom_sms_api_modal_post_method").modal();

        });



        $("#routehostdiv").hide();
        // $("#updated_routehostdiv").hide();

        $(document).on('change', '#gateway_name', function(event) {
            event.preventDefault();
            var gateway_name = $("#gateway_name").val();
            if(gateway_name == "routesms.com")
            {
                $("#routehostdiv").show();

            } else {
                $("#routehostdiv").hide();
            }

        });

        $(document).on('change', '#updated_gateway_name', function(event) {
            event.preventDefault();
            var gateway_name = $("#updated_gateway_name").val();
            if(gateway_name == "routesms.com")
            {
                $("#updated_routehostdiv").show();

            } else {
                $("#updated_routehostdiv").hide();
            }

        });


        /* Save SMS API */
        $(document).on('click', '#save_api', function(event) {
        	event.preventDefault();
        	
        	var gateway_name = $("#gateway_name").val();
            var username_auth_id = $("#username_auth_id").val();
            var routesms_host_name = $("#routesms_host_name").val();
            var password_auth_token = $("#password_auth_token").val();
            var api_id = $("#api_id").val();
            var phone_number = $("#phone_number").val();

            /* Gateway Name checking */
        	if(gateway_name == "")
        	{
        		swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Gateway is Required"); ?>', 'warning');
        		return;
        	}

            /* Check that Routesms.com hostname is valid or not */
            if(gateway_name == "routesms.com" && !isURL(routesms_host_name)){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Route host URL is not valid."); ?>', 'warning');
                return;
            }

            /* checking api key or sender information for the corresponding API */
            if(namesForAuthId.indexOf(gateway_name) != -1 && (username_auth_id == "" || phone_number == "")){

                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You didn`t Provided all required information of the Gateway"); ?>', 'warning');
                return;
            }

            if(gateway_name == "clickatell" && username_auth_id == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("API Username is Required"); ?>', 'warning');
                return;
            }

            /* checking host name for routesms.com gateway */
            if(gateway_name == "routesms.com" && routesms_host_name == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Routesms Host name is required."); ?>', 'warning');
                return;
            }

            /* checking auth token/api secret/password section for the corresponding gateways */
            if(namesForAuthToken.indexOf(gateway_name) != -1 && password_auth_token == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You didn`t Provided all required information of the Gateway"); ?>', 'warning');
                return;
            }

            /* Checking API ID field for the corresponding Gateways */
            if(namesForApiId.indexOf(gateway_name) != -1 && api_id == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You didn`t Provided all required information of the Gateway"); ?>', 'warning');
                return;
            }
            

        	$(this).addClass('btn-progress')
        	var that = $(this);

            var report_link = base_url+"sms_email_manager/sms_api_lists";

        	var alldatas = new FormData($("#sms_api_form")[0]);

        	$.ajax({
        		url: base_url+'sms_email_manager/ajax_create_sms_api',
        		type: 'POST',
        		dataType: 'JSON',
        		data: alldatas,
        		cache: false,
        		contentType: false,
        		processData: false,
        		success:function(response)
        		{
        			$(that).removeClass('btn-progress');
                    var span = document.createElement("span");
                    span.innerHTML = response.msg;

        			if(response.status == "1")
        			{
                        swal({ title:'<?php echo $this->lang->line("Success"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});

        			} else 
        			{
                        swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {window.location.href=report_link;});
        			}

        		}
        	})
        });

        $(document).on('click', '.edit_api', function(event) {
        	event.preventDefault();
        	$("#update_sms_api_form_modal").modal();
        	var table_id = $(this).attr("table_id");
            var gateway = $(this).attr("gateway");

            var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
            $("#updated_form_modal_body").html(loading);

        	$.ajax({
        		url: base_url+'sms_email_manager/ajax_get_api_info_for_update',
        		type: 'POST',
        		data: {table_id: table_id},
        		success:function(response)
        		{
                    if(response){

                        $("#updated_form_modal_body").html(response);
                        $("#updated_routehostdiv").hide();
                        if(gateway == "routesms.com")
                            $("#updated_routehostdiv").show();

                    }
                    else
                        $("#updated_form_modal_body").html(loading);
        		}     
        	})
        });

        $(document).on('click', '#update_api', function(event) {
            event.preventDefault();

            var gateway_name = $("#updated_gateway_name").val();

            var username_auth_id = $("#updated_username_auth_id").val();
            var password_auth_token = $("#updated_password_auth_token").val();
            var routesms_host_name = $("#update_routesms_host_name").val();
            var api_id = $("#updated_api_id").val();
            var phone_number = $("#updated_phone_number").val();

            /* Gateway Name checking */
            if(gateway_name == "")
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Gateway is Required"); ?>', 'warning');
                return;
            }

            /* Check that Routesms.com hostname is valid or not */
            if(gateway_name == "routesms.com" && !isURL(routesms_host_name)){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Route host URL is not valid."); ?>', 'warning');
                return;
            }

            /* checking api key or sender information for the corresponding API */
            if(namesForAuthId.indexOf(gateway_name) != -1 && (username_auth_id == "" || phone_number == "")){

                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You didn`t Provided all required information of the Gateway"); ?>', 'warning');
                return;
            }

            if(gateway_name == "clickatell" && username_auth_id == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("API Username is Required"); ?>', 'warning');
                return;
            }

            /* checking host name for routesms.com gateway */
            if(gateway_name == "routesms.com" && routesms_host_name == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Routesms Host name is required."); ?>', 'warning');
                return;
            }

            /* checking auth token/api secret/password section for the corresponding gateways */
            if(namesForAuthToken.indexOf(gateway_name) != -1 && password_auth_token == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You didn`t Provided all required information of the Gateway"); ?>', 'warning');
                return;
            }

            /* Checking API ID field for the corresponding Gateways */
            if(namesForApiId.indexOf(gateway_name) != -1 && api_id == ""){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You didn`t Provided all required information of the Gateway"); ?>', 'warning');
                return;
            }

            $(this).addClass('btn-progress')
            var that = $(this);
            var report_link = base_url+"sms_email_manager/sms_api_lists";

            var updated_data = new FormData($("#update_sms_api_form")[0]);

            $.ajax({
                url: base_url+'sms_email_manager/ajax_update_sms_api',
                type: 'POST',
                dataType: 'JSON',
                data: updated_data,
                cache: false,
                contentType: false,
                processData: false,
                success:function(response)
                {
                    $(that).removeClass('btn-progress');

                    var span = document.createElement("span");
                    span.innerHTML = response.msg;

                    if(response.status == "1")
                    {
                        swal({ title:'<?php echo $this->lang->line("Success"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});

                    } else 
                    {
                        swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {window.location.href=report_link;});
                    }

                }
            })
        });

        var Doyouwanttodeletethisrecordfromdatabase2 = "<?php echo $this->lang->line('If you delete this API, then all SMS Campaigns which were created with this API will be deleted. So do you really want to detete this API?'); ?>";
        $(document).on('click','.delete_api',function(e){
            e.preventDefault();
            swal({
                title: '<?php echo $this->lang->line("Are you sure?"); ?>',
                text: Doyouwanttodeletethisrecordfromdatabase2,
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
                        url:"<?php echo base_url('sms_email_manager/delete_sms_api')?>",
                        data:{table_id:table_id},
                        success:function(response){ 

                            if(response == '1')
                            {
                                iziToast.success({title: '',message: '<?php echo $this->lang->line('API has been Deleted Successfully.'); ?>',position: 'bottomRight'});
                                table.draw();
                            } else
                            {
                                iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
                            }
                        }
                    });
                } 
            });
        });

        $(document).on('click', '#instruction_guide', function(event) {
        	event.preventDefault();
        	$("#instruction_guide_modal").modal();
        });

        $('#instruction_guide_modal').on("hidden.bs.modal", function (e) { 
            if ($('.modal:visible').length) { $('body').addClass('modal-open'); }
        });
   

        $("#add_sms_api_form_modal").on('hidden.bs.modal', function ()
        {
            $("#sms_api_form").trigger('reset');
            $("#gateway_name").change();
            table.draw();
        });

        $("#api_info").on('hidden.bs.modal', function ()
        {
            $("#auth_id_val").html("");
            $("#api_secret_val").html("");
            $("#api_id_val").html("");
            $("#remaining_credits_val").html("");
            table.draw();
        }); 

        $("#update_sms_api_form_modal").on('hidden.bs.modal', function ()
        {
            table.draw();
        });


        $(document).on('click', '.test_sms', function(event) {
            event.preventDefault();
            var api_table_id = $(this).attr('table_id');
            var test_gateway_name = $(this).attr('gateway_name');
            $("#test_sms_modal").modal();
            $("#test_gateway_name").val(test_gateway_name);
            $("#sms_api_table_id").val(api_table_id);
            $("#response").css('display','none');
        });

        $("#test_sms_modal").on('hidden.bs.modal', function ()
        {
            table.draw();
            $("#response-div").html("");
            $("#recipient_number").val('');
            $("#test_message").val('');
        });

        $(document).on('click', '#send_test_sms', function(event) {
            event.preventDefault();

            var table_id = $("#sms_api_table_id").val();
            var test_gateway_name = $("#test_gateway_name").val();
            var recipient_number = $("#recipient_number").val();
            var test_message = $("#test_message").val();

            if(table_id == "" || table_id == 0) {

                var report_link = base_url+"sms_email_manager/sms_api_lists";

                var span = document.createElement("span");
                span.innerHTML = '<?php echo $this->lang->line("Something went wrong, please try once again."); ?>';

                swal({ title:'<?php echo $this->lang->line("error"); ?>', content:span,icon:'error'}).then((value) => {window.location.href=report_link;});
                return;
            }

            if(recipient_number == "") {
                $("#recipient_number").addClass('is-invalid');
                return false;
            } else {
                $("#recipient_number").removeClass('is-invalid');
            }

            if(test_message == "") {
                $("#test_message").addClass('is-invalid');
                return false;
            } else {
                $("#test_message").removeClass('is-invalid');
            }

            $(this).addClass('btn-progress');

            $.ajax({
                context: this,
                url: base_url+'sms_email_manager/send_test_sms',
                type: 'POST',
                dataType:'JSON',
                data: {table_id: table_id,number: recipient_number, message: test_message,test_gateway_name:test_gateway_name},
                success:function(response){

                    $("#response").css('display','block');
                    $(this).removeClass('btn-progress');
                    
                    if(test_gateway_name == 'custom') {
                        var result = response.id;
                    } else {
                        var result = JSON.stringify(response,undefined, 4);
                    }

                    $("#response-div").text(result);

                }
            })
            
        });

        // SMS API section ended here

        // ======================================================= SMS Campaign JS SEction ========================================

        var base_url = '<?php echo base_url(); ?>';
        var somethingwentwrong = '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>';

        $(".schedule_block_item").hide();

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

        var perscroll_sms;
        var table_sms = $("#mytable_sms_campaign").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: 
            {
                "url": base_url+'sms_email_manager/sms_campaign_lists_data',
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
                  targets: [0,1,4,5,6,7,8],
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
                if (perscroll_sms) perscroll_sms.destroy();
                perscroll_sms = new PerfectScrollbar('#mytable_sms_campaign_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (perscroll_sms) perscroll_sms.destroy();
                perscroll_sms = new PerfectScrollbar('#mytable_sms_campaign_wrapper .dataTables_scrollBody');
              }
            }
        });


        $(document).on('change', '#campaign_status', function(event) {
          event.preventDefault(); 
          table_sms.draw();
        });

        $(document).on('change', '#post_date_range_val', function(event) {
          event.preventDefault(); 
          table_sms.draw();
        });

        $(document).on('click', '#sms_search_submit', function(event) {
          event.preventDefault(); 
          table_sms.draw();
        });
        // // End of datatable section

        var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $this->lang->line('Do you want to detete this record?'); ?>";
        $(document).on('click','.delete_sms_campaign',function(e){
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
                        url:"<?php echo base_url('sms_email_manager/delete_sms_campaign')?>",
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

                            table_sms.draw();
                        }
                    });
                } 
            });
        });



        /** Including variables on click **/
        $(document).on('click','#contact_first_name',function(){ 
            var $txt = $("#message");
            var caretPos = $txt[0].selectionStart;

            var textAreaTxt = $txt.val();
            var txtToAdd = " #FIRST_NAME# ";
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        });

        $(document).on('click','#contact_last_name',function(){ 
            var $txt = $("#message");
            var caretPos = $txt[0].selectionStart;
            var textAreaTxt = $txt.val();
            var txtToAdd = " #LAST_NAME# ";
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        });

        $(document).on('click','#contact_mobile_number',function(){  
            var $txt = $("#message");
            var caretPos = $txt[0].selectionStart;
            var textAreaTxt = $txt.val();
            var txtToAdd = " #MOBILE# ";
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        });

        $(document).on('click','#contact_email_address',function(){  
            var $txt = $("#message");
            var caretPos = $txt[0].selectionStart;
            var textAreaTxt = $txt.val();
            var txtToAdd = " #EMAIL_ADDRESS# ";
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        });
        /** End of Including variables by click **/

        $(document).on('change', '#country_code_action', function(event) {
            event.preventDefault();
            /* Act on the event */
            var action = $(this).val();
            var country_code = $("#country_code").val();

            if(action == "1")
            {
                $("#country_code_add").val(country_code);
                if($("#country_code_remove").val() !='')
                {
                    $("#country_code_remove").val("");
                }
            }
            if(action=='0')
            {
                $("#country_code_remove").val(country_code);
                if($("#country_code_add").val() !='')
                {
                    $("#country_code_add").val("");
                }
            }

        });

        Dropzone.autoDiscover = false;
        $("#dropzone").dropzone({ 
            url: "<?php echo site_url();?>sms_email_manager/ajax_campaign_import_csv_files",
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

        // import csv section
        $("#import_submit").click(function(){      
            var fileval = $("#csv_file").val();
            if(fileval=="")
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("No file selected, Please upload a file.");?>', 'warning');
            else
            {  
                $(this).addClass('btn-progress')
                var that = $(this);

                $.ajax({
                    url: base_url+'sms_email_manager/generating_numbers',
                    type: 'POST',
                    data: {fileval:fileval},
                    dataType:'json',
                    success: function (response)                
                    {
                        $(that).removeClass('btn-progress');   
                        if(response.status=='1')
                        {               
                            var file_content = response.file;
                            var to_numbers = $("#to_numbers").val().trim();
                            if(to_numbers != "") file_content = ','+file_content;   
                            file_content = to_numbers + file_content;
                            var totalNumbers = file_content.split(",").length;
                            $("#manual_numbers").html('<i class="fas fa-spinner fa-spin"></i>');
                            $("#manual_numbers").html(totalNumbers);
                            $("#to_numbers").val(file_content);
                            $("#csv_import_modal").modal('hide');
                            iziToast.success({title: '',message: '<?php echo $this->lang->line("import from csv was successful")?>',position: 'bottomRight'});
                        }
                        else
                        {
                            var error=response.status.replace(/<\/?[^>]+(>|$)/g, "");
                            iziToast.error({title: '',message: error,position: 'bottomRight'});
                        }
                    }
                });
            }         
                 
        });

        $("#csv_import_modal").on("hidden.bs.modal",function(){
            $("#csv_file").val("");
            $(".dz-remove").click();
        });

        
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

        $(document).on('click','#create_campaign',function(){

            var campaign_name = $("#campaign_name").val();
            var message       = $("#message").val();
            var contacts_id   = $("#contacts_id").val();
            var to_numbers    = $("#to_numbers").val().trim();
            var from_sms      = $("#from_sms").val();
            var schedule_type = $("input[name=schedule_type]:checked").val();
            var schedule_time = $("#schedule_time").val();
            var time_zone     = $("#time_zone").val();
            var page_name     = $("#page").val();

            // campaign name
            if(campaign_name =='')
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please give a campaign name"); ?>", 'warning');
                return;
            }

            // sms api select
            if(from_sms =='')
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select a SMS API"); ?>", 'warning');
                return;
            }

            // write message
            if(message =='')
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please write your message"); ?>", 'warning');
                return;
            }

            if(page_name == "" && contacts_id == "" && to_numbers == "")
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please Select Page name or Contact Group."); ?>", 'warning');
                return;
            }


            // if schedule is later
            if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone==""))
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select schedule time/time zone."); ?>", 'warning');
                return;
            }

            $(this).addClass('btn-progress');
            var that = $(this);

            var report_link = base_url+"sms_email_manager/sms_campaign_lists";
            var success_message = "<?php echo $this->lang->line('Campaign have been submitted successfully.'); ?> <a href='"+report_link+"'><?php echo $this->lang->line('See report here.'); ?></a>";

            var queryString = new FormData($("#sms_campaign_form")[0]);
            
            $.ajax({
                url:base_url+'sms_email_manager/create_sms_campaign_action',
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


        $("#update_sms_campaign_btn").click(function()
        {
            var campaign_name = $("#campaign_name").val();
            var message       = $("#message").val();
            var contacts_id   = $("#contacts_id").val();
            var to_numbers    = $("#to_numbers").val().trim();
            var from_sms      = $("#from_sms").val();
            var schedule_type = $("input[name=schedule_type]:checked").val();
            var schedule_time = $("#schedule_time").val();
            var time_zone     = $("#time_zone").val();
            var page_name     = $("#page").val();

            // campaign name
            if(campaign_name =='')
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please give a campaign name"); ?>", 'warning');
                return;
            }

            // sms api select
            if(from_sms =='')
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select a SMS API"); ?>", 'warning');
                return;
            }

            // write message
            if(message =='')
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please write your message"); ?>", 'warning');
                return;
            }

            if(page_name == "" && contacts_id == "" && to_numbers == "")
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please Select Page name or Contact Group."); ?>", 'warning');
                return;
            }

            // if schedule is later
            if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone==""))
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please select schedule time/time zone."); ?>", 'warning');
                return;
            }

            $(this).addClass('btn-progress');
            var that = $(this);

            var report_link = base_url+"sms_email_manager/sms_campaign_lists";
            var success_message = "<?php echo $this->lang->line('Campaign have been updated successfully.'); ?> <a href='"+report_link+"'><?php echo $this->lang->line('See report here.'); ?></a>";

            var queryString = new FormData($("#updated_sms_campaign_form")[0]);
            
            $.ajax({
                url:base_url+'sms_email_manager/edit_sms_campaign_action',
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


        // report table started
        var table_campaign_report = '';
        var perscroll_campaign_report;
        $(document).on('click','.campaign_report',function(e){
          e.preventDefault();

            var table_id = $(this).attr('table_id');
            if(table_id !='') $("#put_row_id").val(table_id);

            var campaignName     = $(this).attr("campaign_name");
            var sms_api_name     = $(this).attr("send_as");
            var successfullysent = $(this).attr("successfullysent");
            var totalThread      = $(this).attr("totalThread");
            var campaignMessage  = $(this).attr("campaign_message");
                campaignMessage  = campaignMessage.replace(/(\r\n|\n\r|\r|\n)/g, "<br>")
            var campaignStatus   = $(this).attr("campaign_status");
            
            $("#restart_button").hide();

            var posting_status = '';
            if(campaignStatus == '0') posting_status = 'Pending';
            if(campaignStatus == '1') posting_status = 'Processing';
            if(campaignStatus == '2') posting_status = 'Completed';
            if(campaignStatus == '3') {
                posting_status = 'Paused';
                $("#restart_button").show();
            }

            if(campaignStatus == '2') $("#options_div").hide();


            $("#sms_campaign_name").html(campaignName);
            $("#api_name").html(sms_api_name);
            $("#posting_status").html(posting_status);
            $("#sent_state").html(successfullysent+'/'+totalThread);
            $("#original_message").html(campaignMessage);

            $("#edit_content").attr("href",base_url+"sms_email_manager/edit_campaign_content/"+table_id);
            $("#restart_button").attr("table_id",table_id);

            $("#campaign_report_modal").modal();

            setTimeout(function(){
                if (table_campaign_report == '')
                {
                    table_campaign_report = $("#mytable_campaign_report").DataTable({
                        serverSide: true,
                        processing:true,
                        bFilter: false,
                        order: [[ 1, "desc" ]],
                        pageLength: 10,
                        ajax: {
                            url: base_url+'sms_email_manager/ajax_get_sms_campaign_report_info',
                            type: 'POST',
                            data: function ( d )
                            {
                                d.table_id = $("#put_row_id").val();
                                d.searching = $("#report_search").val();
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
                              targets: [0,1,5],
                              className: 'text-center'
                            },
                            {
                              targets: [0,1,2,3,4,6],
                              sortable: false
                            }
                        ],
                        fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
                        if(areWeUsingScroll)
                        {
                            if (perscroll_campaign_report) perscroll_campaign_report.destroy();
                                perscroll_campaign_report = new PerfectScrollbar('#mytable_campaign_report_wrapper .dataTables_scrollBody');
                        }
                        },
                        scrollX: 'auto',
                        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                            if(areWeUsingScroll)
                            { 
                            if (perscroll_campaign_report) perscroll_campaign_report.destroy();
                            perscroll_campaign_report = new PerfectScrollbar('#mytable_campaign_report_wrapper .dataTables_scrollBody');
                            }
                        }
                    });
                }
                else table_campaign_report.draw();
            },1000);
        });

        $(document).on('keyup', '#report_search', function(event) {
          event.preventDefault(); 
          table_campaign_report.draw();
        });

        $('#campaign_report_modal').on('hidden.bs.modal', function () {
            $("#report_search").val("");
            $("#sms_campaign_name").html("");
            $("#api_name").html("");
            $("#posting_status").html("");
            $("#original_message").html('');
            $("#sent_state").html("");
            $("#edit_content").attr("href","");
            table_campaign_report.draw();
            table_sms.draw();
        });


        $(document).on('click', '#updateMessage', function(event) {
            event.preventDefault();

            var table_id = $("#table_id").val();
            var message = $("#message").val();
            if(message == "")
            {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line("Please type a message. System can not send blank message."); ?>", 'warning');
                return;

            }

            $(this).addClass('btn-progress');
            var that = $(this);

            var queryString = new FormData($("#edit_message_form")[0]);
            $.ajax({
                type:'POST' ,
                url: base_url+"sms_email_manager/edit_campaign_content_action",
                data: queryString,
                cache: false,
                contentType: false,
                processData: false,
                success:function(response)
                { 
                    $(that).removeClass('btn-progress');
                    var report_link = base_url+"sms_email_manager/sms_campaign_lists";
                    if(response == "1")
                    {
                        swal({ title:'<?php echo $this->lang->line("Campaign Updated"); ?>', content:'<?php echo $this->lang->line("Campaign have been updated successfully."); ?>',icon:'success'}).then((value) => {window.location.href=report_link;});
                    }
                }
            });

        });

        // restart the camapaign where it is left
        $(document).on('click','.restart_button',function(e){
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
                       url: "<?php echo base_url('sms_email_manager/restart_campaign')?>",
                       data: {table_id:table_id},
                       success:function(response)
                       {
                            $(this).parent().prev().removeClass('btn-progress btn-outline-primary').addClass('btn-primary');
                            if(response=='1') 
                            {
                                $("#campaign_report_modal").modal('hide');
                                iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been resumed by force successfully."); ?>',position: 'bottomRight'});
                                table_sms.draw();
                                table_campaign_report.draw();
                            }       
                            else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
                       }
                    });
                } 
            });

        });

        $(document).on('click','.pause_campaign_info',function(e){
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
                       url: "<?php echo base_url('sms_email_manager/ajax_sms_campaign_pause')?>",
                       data: {table_id:table_id},
                       success:function(response)
                       {
                            $(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

                            if(response=='1') 
                            {
                                iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been paused successfully."); ?>',position: 'bottomRight'});
                                table_sms.draw();
                            }       
                            else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
                       }
                    });
                } 
            });

        });

        $(document).on('click','.play_campaign_info',function(e){
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
                       url: "<?php echo base_url('sms_email_manager/ajax_sms_campaign_play')?>",
                       data: {table_id:table_id},
                       success:function(response)
                       {
                            $(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

                            if(response=='1') 
                            {
                                iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been resumed successfully."); ?>',position: 'bottomRight'});
                                table_sms.draw();
                            }       
                            else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
                       }
                    });
                } 
            });

        });

        $(document).on('click','.force',function(e){
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
                       url: "<?php echo base_url('sms_email_manager/force_reprocess_sms_campaign')?>",
                       data: {id:id},
                       success:function(response)
                       {
                            $(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

                            if(response=='1') 
                            {
                                iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been re-processed by force successfully."); ?>',position: 'bottomRight'});
                                table_sms.draw();
                            }       
                            else iziToast.error({title: '',message: alreadyEnabled,position: 'bottomRight'});
                       }
                    });
                } 
            });

        });

    // =================================================== SMS Report Section ===============================================

        setTimeout(function(){ 
          $('#sms_date_range').daterangepicker({
            ranges: {
              '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
              '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
              '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment()
          }, function (start, end) {
            $('#sms_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
          });
        }, 2000);

        // sms logs
        setTimeout(function(){ 
          $('#sms_log_date_range').daterangepicker({
            ranges: {
              '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
              '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
              '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment()
          }, function (start, end) {
            $('#sms_log_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
          });
        }, 2000);


        // for sms whole report count(depricated now)
        // section deprecated
        var perscroll_sms_report;
        var table_sms_report = $("#mytable_sms_report").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 5, "desc" ]],
            pageLength: 10,
            ajax: 
            {
              "url": base_url+'sms_email_manager/sms_reports_data',
              "type": 'POST',
              data: function ( d )
              {
                  d.sms_date_range = $('#sms_date_range_val').val();
              }
            },
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
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
                if (perscroll_sms_report) perscroll_sms_report.destroy();
                perscroll_sms_report = new PerfectScrollbar('#mytable_sms_report_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (perscroll_sms_report) perscroll_sms_report.destroy();
                perscroll_sms_report = new PerfectScrollbar('#mytable_sms_report_wrapper .dataTables_scrollBody');
              }
            }
        });

        $(document).on('change', '#sms_date_range_val', function(event) {
          event.preventDefault(); 
          table_sms_report.draw();
        });

        $(document).on('click', '#download_sms_reports', function(event) {
            event.preventDefault();

            var sms_date_range = $("#sms_date_range_val").val();

            $(this).addClass('btn-progress');
            var that = $(this);

            $.ajax({
                url: base_url+"sms_email_manager/download_sms_report",
                type: 'POST',
                dataType: 'JSON',
                data: {sms_date_range: sms_date_range},
                success:function(response){
                    $(that).removeClass('btn-progress');
                    if(response.file_name != ""){
                        var download_link = base_url+response.file_name;
                        window.location.href= download_link;
                        table_sms_report.draw();

                    }
                }

            })
            
        });
        // end section deprecated


        var perscroll_sms_logs;
        table_sms_logs = $("#mytable_sms_logs").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: 
            {
                "url": base_url+'sms_email_manager/sms_logs_data',
                "type": 'POST',
                data: function ( d )
                {
                    d.sms_logs_date_range = $('#sms_log_date_range_val').val();
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
                if (perscroll_sms_logs) perscroll_sms_logs.destroy();
                perscroll_sms_logs = new PerfectScrollbar('#mytable_sms_logs_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (perscroll_sms_logs) perscroll_sms_logs.destroy();
                perscroll_sms_logs = new PerfectScrollbar('#mytable_sms_logs_wrapper .dataTables_scrollBody');
              }
            }
        });

        $(document).on('change', '#sms_log_date_range_val', function(event) {
          event.preventDefault(); 
          table_sms_logs.draw();
        });


        $(document).on('click', '.see_message', function(event) {
            event.preventDefault();
            
            $("#see_contact_message").modal();
            $("#message_body").html("");
            var msg = $(this).attr("sendMessage");
            var final_msg = msg.replace(/(\r\n|\n\r|\r|\n)/g, "<br>");

            if(final_msg != "") $("#message_body").append('<div class="alert alert-light">'+final_msg+'</div>');
        });

        $("#see_contact_message").on('hidden.bs.modal', function(event) {
            table_sms_logs.draw();
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
            $("#targetted_subscriber").html('<i class="fas fa-spinner fa-spin"></i>');

            if(page_id=="")
            {
                $("#page_subscriber,#targetted_subscriber").html("0");
            }

            // $("#submit_post").addClass('btn-progress');

            $.ajax({
                type:'POST' ,
                url: base_url+"sms_email_manager/get_subscribers_phone",
                data: {page_id:page_id,label_ids:label_ids,excluded_label_ids:excluded_label_ids,user_gender:user_gender,user_time_zone:user_time_zone,user_locale:user_locale,load_label:load_label},
                dataType : 'JSON',
                success:function(response){

                    if(load_label=='1')
                    {
                        $("#dropdown_con").removeClass('hidden');
                        $("#first_dropdown").html(response.first_dropdown);
                        $("#second_dropdown").html(response.second_dropdown);
                    }

                    // $("#submit_post").removeClass("btn-progress");

                    $("#page_subscriber").html(response.pageinfo.page_total_subscribers);
                    $("#targetted_subscriber").html(response.pageinfo.subscriber_count);

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

            $("#contact_numbers").html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: base_url+'sms_email_manager/contacts_total_numbers',
                type: 'POST',
                data: {contact_ids: contact_ids},
                success:function(response){
                    if(response!="")
                        $("#contact_numbers").html(response);
                    else
                        $("#contact_numbers").html("0");


                }
            })

        });

        $(document).on('keyup', '#to_numbers', function(event) {
            event.preventDefault();

            var numbers = $("#to_numbers").val();
            if(numbers != ""){
                numbers = numbers.split(",").length;
                $("#manual_numbers").html('<i class="fas fa-spinner fa-spin"></i>');
                $("#manual_numbers").html(numbers);
            } else
            {
                $("#manual_numbers").html("0");
            }
        });


        /* analyze custom api */
        var custom_api_analyzed_result;
        $(document).on('click', '#analyze_button', function(event) {
            event.preventDefault();
            
            let custom_url = $("#custom_api_url").val();
            let action_type = $("#form_action_type").val();
            let table_id = $("#form_table_id").val();
            let is_first_time_edit_request = $("#is_first_time_edit_request").val();

            $("#analyzed_section").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>');

            if (custom_url != '') {

                $.ajax({
                    url: '<?php echo base_url("sms_email_manager/analize_custom_api_url") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {custom_url: custom_url, action_type: action_type, table_id: table_id, is_first_time_edit_request: is_first_time_edit_request},
                    success: function (response) {

                        custom_api_analyzed_result = response;
                        // console.log(response);
                        if (response.message == 'error') {

                            swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please provide a valid url"); ?>', 'error');
                            $("#analyzed_section").html('');
                        } else {

                            $("#analyzed_section").html(response.message);
                            $("#update_custom_api").prop('disabled', false);
                            update_custom_generated_url();
                        }


                        $("#is_first_time_edit_request").remove();
                    }
                });
            } else {
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please put the input first."); ?>', 'warning');
                $("#analyzed_section").html('');
            }

            
        });
        
    


        /* on modal close remove the is open window value change */
        $('#myModal').on('hidden.bs.modal', function () {
            $("#is_first_time_edit_request").remove();
        })

        var url_const = [];
            url_const['destination_number'] = '#DESTINATION_NUMBER#';
            url_const['sender_id'] = '#SENDER_ID#';
            url_const['message_content'] = '#MESSAGE_CONTENT#';
        


        function update_custom_generated_url () {
            
            let query_pieces = custom_api_analyzed_result.query_pieces;
            let final_url = custom_api_analyzed_result.base_url + '?';

            let current_val, current_key, siblings;

            $('.parsed_single_value').each(function(index, el) {
                
                siblings = $(el).parent().siblings();
                current_key = $($(siblings)[0]).text();

                if (query_pieces[current_key]['has_changed']) {
                    current_val = custom_api_analyzed_result.query_pieces[current_key]['changed_value'];
                } else {
                    current_val = query_pieces[current_key]['initial_value'];
                }

                final_url += current_key + '=' + current_val + '&';
            });

            final_url = final_url.substring(0, final_url.length - 1);
            $("#updated_url").html(final_url);
        }

        $(document).on('change', '.select_option', function(event) {
            event.preventDefault();
            
            let current_option = $(this).val();
            let key_name;

            let current_siblings = $(this).parent().siblings();
            current_siblings.each(function(index, el) {

                if (index == 0) {
                    key_name = $(el).text();
                } else if (index == 1) {

                    let temp = $(el).children();

                    if (current_option != 'fixed') {

                        $(temp).prop("disabled", true);
                        $(temp).val(url_const[current_option]);

                        $(temp).keyup();
                    } else {

                        $(temp).prop("disabled", false);
                        $(temp).val(custom_api_analyzed_result.query_pieces[key_name]['initial_value']);
                        $(temp).keyup();
                    }
                }
            });
            
            update_custom_generated_url();
        });


        $(document).on('keyup', '.parsed_single_value', function(event) {
            
            let siblings = $(this).parent().siblings();
            let key_name = $($(siblings)[0]).text();
            
            /* we are asuming that only fixed type values are editable */
            custom_api_analyzed_result.query_pieces[key_name]['has_changed'] = true;
            custom_api_analyzed_result.query_pieces[key_name]['changed_value'] = $(this).val();

            update_custom_generated_url();
        });


        $(document).on('click', '#update_custom_api', function(event) {
            event.preventDefault();
            
            let custom_api_name = $("#custom_api_name").val();
            let custom_api_url = $("#custom_api_url").val();
            let updated_url = $("#updated_url").text();

            let action_type = $("#form_action_type").val();
            let table_id = $("#form_table_id").val();

            if (custom_api_name == '' || custom_api_url == '' || updated_url == '') {
                swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Either api name or api url is empty."); ?>', 'error');
            } else {

                $(this).addClass('btn-progress');

                $.ajax({
                    context: this,
                    url: '<?php echo base_url('sms_email_manager/create_custom_api'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {custom_api_name: custom_api_name,custom_api_url: custom_api_url,updated_url: updated_url,action_type: action_type, table_id: table_id},
                    success: function (response) {

                        $(this).removeClass('btn-progress');

                        if (response.status == 'success') {
                         
                            swal({ title: '<?php echo $this->lang->line("Success"); ?>', text: response.message, icon: 'success', buttons: true, dangerMode: true, })
                            .then((willDelete) => {
                                if (willDelete) 
                                    $("#custom_sms_api_modal").modal('hide');
                            });
                        } else {

                            swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'error');
                        }



                        table.draw();
                    }
                });
                
            }
        });

        //Ronok Post method code
        $(document).on('click', '#update_custom_api_post_method', function(event) {
            event.preventDefault();
            let custom_api_name = $("#custom_api_name_post_method").val();
            let custom_api_url = $("#custom_api_base_url_post_method").val();
            let action_type = $("#form_action_type").val();
            let table_id = $("#form_table_id").val();
            var elem = document.getElementsByClassName("key");
            var names = [];
            for(var i=0;i<elem.length;++i)
            {
                if(typeof elem[i].value !=="undefined"){
                    names.push(elem[i].value);
                }
            }
            var key = names;
            var elem = document.getElementsByClassName("types");
            var names = [];
            for(var i=0;i<elem.length;++i)
            {
                if(typeof elem[i].value !=="undefined"){
                    names.push(elem[i].value);
                }
            }
            var types = names;
            var elem = document.getElementsByClassName("value");
            var names = [];
            for(var i=0;i<elem.length;++i)
            {
                if(typeof elem[i].value !=="undefined"){
                    names.push(elem[i].value);
                }
            }
            var value = names;
            if (custom_api_name == '' || custom_api_url == '') {
                swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Either api name or api url is empty."); ?>', 'error');
            }

            else{

               $(this).addClass('btn-progress');
               $.ajax({
                context: this,
                url: '<?php echo base_url('sms_email_manager/create_custom_api_post_method'); ?>',
                type: 'POST',
                dataType: 'json',
                data:{custom_api_name_post_method: custom_api_name,custom_api_base_url_post_method:custom_api_url ,action_type: action_type, table_id: table_id,key:key,types:types,value:value},
                success: function (response) {

                    $(this).removeClass('btn-progress');

                    if (response.status == 'success') {

                        swal({ title: '<?php echo $this->lang->line("Success"); ?>', text: response.message, icon: 'success', buttons: true, dangerMode: true, })
                        .then((willDelete) => {
                            if (willDelete) 
                                $("#custom_sms_api_modal_post_method").modal('hide');
                        });
                    } else {

                        swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'error');
                    }



                    table.draw();
                }
            });
               
           }

       });

        $(document).on('click', '.edit_custom_api', function(event) {
            event.preventDefault();
            
            $(this).addClass('btn-progress');

            let table_id = $(this).attr('table_id');

            $.ajax({
                context: this,
                url: '<?php echo base_url('sms_email_manager/edit_custom_api_info') ?>',
                type: 'POST',
                dataType: 'json',
                data: {table_id: table_id},
                success: function (response) {

                    $(this).removeClass('btn-progress');
                    if (response.status == 'success') {

                        $("#custom_api_name").val(response.name);
                        $("#custom_api_url").val(response.input_http_url);

                        $("#form_action_type").remove();
                        $("#form_table_id").remove();

                        $("#custom_api_name").after('<input id="form_action_type" type="hidden" name="action_type" value="edit">');
                        $("#custom_api_name").after('<input id="form_table_id" type="hidden" name="table_id" value="' + table_id + '">');
                        $("#custom_api_name").after('<input id="is_first_time_edit_request" type="hidden"  value="yes">');

                        $("#analyze_button").click();

                        $("#custom_sms_api_modal").modal();
                    } else if(response.status == 'error') {
                        swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                    }
                }
            });
            
        });



        //Code For Custom Post Api [Ronok]


        $("#add_new_parameter").click(function(){
            $(".parameter").append(' <tr class="remove_project_file"> <td> <input type="text" name="key[]"  class="form-control key" placeholder="Enter Key"> </td><td> <select name="types[]"  class="form-control types"> <option value="fixed" selected>Fixed</option> <option value="destination_number">DESTINATION_NUMBER</option> <option value="message_content">MESSAGE_CONTENT</option> </select> </td><td> <input type="text" name="value[]" class="form-control value" placeholder="Enter value"> </td><td> <button class="btn btn-danger close_data"><i class="fa fa-close"></i></button> </td></tr>');
        });




      //text Response data
      var custom_api_analyzed_result;

      $(document).on('click', '#text_response', function(event) {
        event.preventDefault();
        let custom_api_name_post_method = $("#custom_api_name_post_method").val();
        let custom_api_base_url_post_method=$("#custom_api_base_url_post_method").val();
        let action_type = $("#form_action_type").val();
        let table_id = $("#form_table_id").val();
        let is_first_time_edit_request = $("#is_first_time_edit_request").val();
        var elem = document.getElementsByClassName("key");
        var names = [];
        for(var i=0;i<elem.length;++i)
        {
            if(typeof elem[i].value !=="undefined"){
                names.push(elem[i].value);
            }
        }
        var key = names;
        var elem = document.getElementsByClassName("types");
        var names = [];
        for(var i=0;i<elem.length;++i)
        {
            if(typeof elem[i].value !=="undefined"){
                names.push(elem[i].value);
            }
        }
        var types = names;
        var elem = document.getElementsByClassName("value");
        var names = [];
        for(var i=0;i<elem.length;++i)
        {
            if(typeof elem[i].value !=="undefined"){
                names.push(elem[i].value);
            }
        }
        var value = names;

        $("#text_response_section").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>');

        if (custom_api_base_url_post_method != '') {

            $.ajax({
                url: '<?php echo base_url("sms_email_manager/analize_custom_api_url_post_method") ?>',
                type: 'POST',
                dataType: 'json',
                data: {custom_api_name_post_method: custom_api_name_post_method,custom_api_base_url_post_method:custom_api_base_url_post_method ,action_type: action_type, table_id: table_id, is_first_time_edit_request: is_first_time_edit_request,key:key,types:types,value:value},
                success: function (response) {

                    custom_api_analyzed_result = response;
                        // console.log(response);
                        if (response.status != '1') {

                            swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                            $("#text_response_section").html('');
                        } else {

                            $("#text_response_section").html(response.message);
                            $("#update_custom_api_post_method").prop('disabled', false);
                        }


                        $("#is_first_time_edit_request").remove();
                    }
                });
        } else {
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please put the input first."); ?>', 'warning');
            $("#text_response_section").html('');
        }


    });

      $(document).on('click', '.edit_custom_post_api', function(event) {
        event.preventDefault();
        
        $(this).addClass('btn-progress');

        let table_id = $(this).attr('table_id');

        $.ajax({
            context: this,
            url: '<?php echo base_url('sms_email_manager/edit_custom_post_api_info') ?>',
            type: 'POST',
            dataType: 'json',
            data: {table_id: table_id},
            success: function (response) {

                $(this).removeClass('btn-progress');
                if (response.status == 'success') {

                    $("#custom_api_name_post_method").val(response.name);
                    $("#custom_api_base_url_post_method").val(response.base_url);
                    // $('.key').val(response.key);
                    // $('.types').val(response.type);
                    // $('.value').val(response.value);

                    let str = '';
                    response.postData.forEach((item,index) => {

                        let show = (index === 0) ? '':'<button class="btn btn-danger close_data"><i class="fa fa-close"></i></button>';
                        
                        let parameter_type = item.type;
                        let parameter_value = item.value;
                        
                        let fixed_selected, destination_selected, message_selected = "";
                        if(parameter_type === "fixed") fixed_selected = "selected";
                        if(parameter_type === "destination_number") destination_selected = "selected";
                        if(parameter_type === "message_content") message_selected = "selected";

                        str += '<tr><td><input type="text" name="key[]" class="form-control key" placeholder="Enter Key" value="'+item.key+'"></td><td><select name="types[]"  class="form-control types"><option value="fixed" '+fixed_selected+'>Fixed</option><option value="destination_number" '+destination_selected+'>DESTINATION_NUMBER</option><option value="message_content" '+message_selected+'>MESSAGE_CONTENT</option></select></td><td><input type="text" name="value[]"  class="form-control value" value="'+parameter_value+'" placeholder="Enter value"></td><td>'+show+'</td></tr>';
                    });

                    $("#parameters_body").html(str);


                    $("#form_action_type").remove();
                    $("#form_table_id").remove();

                    $("#custom_api_name_post_method").after('<input id="form_action_type" type="hidden" name="action_type" value="edit">');
                    $("#custom_api_name_post_method").after('<input id="form_table_id" type="hidden" name="table_id" value="' + table_id + '">');
                    $("#custom_api_name_post_method").after('<input id="is_first_time_edit_request" type="hidden"  value="yes">');

                    $("#custom_sms_api_modal_post_method").modal();
                } else if(response.status == 'error') {
                    swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                }
            }
        });
        
    });

      $(document).on('click', '.close_data', function(event) {
       event.preventDefault(); 

       $(this).parent().parent().remove();
   });

    });
</script>