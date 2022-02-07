<script>
$(document).ready(function($) {


    /* Creating Firstname text button for summernote texteditor */
    var firstName = function (context) {
      var ui = $.summernote.ui;

      // create button
      var button = ui.button({
        contents: '<i class="fas fa-user"/> ',
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
        contents: '<i class="fas fa-user-circle"></i>',
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
        contents: '<i class="fas fa-bell-slash"/>',
        container: 'body',
        tooltip: '<?php echo $this->lang->line("You can include #UNSUBSCRIBE_LINK# variable inside your message. The variable will be replaced by real value when we will send it.") ?>',
        click: function () {
          context.invoke('editor.insertText', ' #UNSUBSCRIBE_LINK# ');
        }
      });

      return button.render();
    }

    var base_url = '<?php echo base_url(); ?>';
    var perscroll_sms_email_template;
    var table_sms_email_template = $("#mytable_sms_email_templates").DataTable({
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax: 
        {
            "url": base_url+'sms_email_manager/template_lists_data',
            "type": 'POST',
            data: function ( d )
            {
                d.template_type = $('#template_type').val();
                d.template_text = $('#template_text').val();
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
            if (perscroll_sms_email_template) perscroll_sms_email_template.destroy();
            perscroll_sms_email_template = new PerfectScrollbar('#mytable_sms_email_templates_wrapper .dataTables_scrollBody');
          }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
          if(areWeUsingScroll)
          { 
            if (perscroll_sms_email_template) perscroll_sms_email_template.destroy();
            perscroll_sms_email_template = new PerfectScrollbar('#mytable_sms_email_templates_wrapper .dataTables_scrollBody');
          }
        }
    });

    $(document).on('keyup', '#template_text', function(event) {
      event.preventDefault(); 
      table_sms_email_template.draw();
    });

    $(document).on('click','.lead_first_name',function(){
      
      var textAreaTxt = $(this).parent().next("textarea").val();
      
      var lastIndex = textAreaTxt.lastIndexOf("<br>");   
      var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
      lastTag = lastTag.trim(lastTag);

      if(lastTag=="<br>")
        textAreaTxt = textAreaTxt.substring(0, lastIndex); 
        
      var txtToAdd = " #FIRST_NAME# ";
      var new_text = textAreaTxt + txtToAdd;
      $(this).parent().next("textarea").val(new_text);
          
    });

    $(document).on('click','.lead_last_name',function(){

      var textAreaTxt = $(this).parent().next().next("textarea").val();
      
      var lastIndex = textAreaTxt.lastIndexOf("<br>");   
      var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
      lastTag=lastTag.trim(lastTag);

      if(lastTag=="<br>")
        textAreaTxt = textAreaTxt.substring(0, lastIndex); 
        
      var txtToAdd = " #LAST_NAME# ";
      var new_text = textAreaTxt + txtToAdd;
      $(this).parent().next().next("textarea").val(new_text);
         
    });

    $(document).on('click', '.create_new_template', function(event) {
      event.preventDefault();

      $("#create_template_modal").modal();
      var tempalteType = $(this).attr("template_type");
      $("#save_template").attr("button-type",tempalteType);

      if(tempalteType == 'email') {
        $("#name-div").addClass('col-md-6')
        $("#subject-div").css("display","block");
        // $("#template_contents").summernote();
        
        /* button style extra toolbar in summernote */
        $('#template_contents').summernote({
          height: 300,  
          toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear']],
              ['fontname', ['fontname']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['table', ['table']],
              ['insert', ['link', 'picture','video']],
              ['view', ['codeview']],
              ['mybutton', ['first_name','last_name','unsubscriberLink']]
          ],

          buttons: {
            first_name: firstName,
            last_name: lastName,
            unsubscriberLink: unsubscriberlink,
          }
        });

        $(".button-outline").hide();

        $('div.note-group-select-from-files').remove();
      } else {
        $(".button-outline").show();
        $("#subject-div").css("display","none");
      }

    });

    $("#create_template_modal").on('hidden.bs.modal',function(){
      table_sms_email_template.draw();
      $("#template_name").val("");
      $("#template_subject").val("");
      $("#template_contents").val("");
      $("#template_contents").summernote('destroy');
    });

    $(document).on('click', '#save_template', function(event) {
      event.preventDefault();

      var type = $(this).attr("button-type");
      var temp_name = $("#template_name").val();
      var csrf_token = $("#sms_email_sequence_csrf_token").val();
      var temp_subject = "";
      var temp_contents = $("#template_contents").val();
      
      if(type == 'email') {
        temp_subject = $("#template_subject").val();
      }

      $(this).addClass('btn-progress');

      $.ajax({
        context:this,
        url: base_url+'sms_email_manager/create_template_action',
        type: 'POST',
        dataType: 'JSON',
        data: {template_type:type,temp_name:temp_name,temp_subject:temp_subject,temp_contents:temp_contents,csrf_token:csrf_token},
        success:function(response) {

          $(this).removeClass('btn-progress');
          if (true === response.error) {
            swal({title: 'Error!',text: response.message,icon: 'error'});
          } else if(response.status == "1") {
            $("#create_template_modal").modal('hide');
            iziToast.success({title: '',message: response.message,position: 'bottomRight'});
            table_sms_email_template.draw();
          } else {
            iziToast.error({title: '',message: response.message,position: 'bottomRight'});

          }
        }
      })
      
    });


    $(document).on('click', '.edit_template', function(event) {
      event.preventDefault();

      $("#update_template_modal").modal();
      var table_id = $(this).attr("table_id");
      var type = $(this).attr("type");


      var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:60px;padding:20px;"></i></div>';
      $("#update_template_content").html(loading);

      $.ajax({
        url: base_url+'sms_email_manager/get_template_info',
        type: 'POST',
        data: {table_id: table_id,type:type},
        success:function(response){

          $("#update_template_content").html(response);

          if(type == 'email') {
            // $("#updated_template_contents").summernote();
            
            $(".button-outline").hide();
            
            /* button style extra toolbar in summernote */
            $('#updated_template_contents').summernote({
              height: 300,  
              toolbar: [
                  ['style', ['style']],
                  ['font', ['bold', 'underline', 'clear']],
                  ['fontname', ['fontname']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['table', ['table']],
                  ['insert', ['link', 'picture','video']],
                  ['view', ['codeview']],
                  ['mybutton', ['first_name','last_name','unsubscriberLink']]
              ],

              buttons: {
                first_name: firstName,
                last_name: lastName,
                unsubscriberLink: unsubscriberlink,
              }
            });

            $('div.note-group-select-from-files').remove();
          } 

          if(type == 'sms') {
            $(".button-outline").show();
          }

        }
      })
      
    });

    $(document).on('click', '#update_template', function(event) {
      event.preventDefault();

      var tableid = $("#table_id").val();
      var tem_type = $("#tem_type").val();
      var updated_template_name = $("#updated_template_name").val();
      var updated_template_subject = '';
      if(tem_type == 'email') {
        updated_template_subject = $("#updated_template_subject").val();
      }
      var updated_template_contents = $("#updated_template_contents").val();
      var csrf_token = $("#sms_email_sequence_csrf_token").val();

      $(this).addClass('btn-progress');

      $.ajax({
        context:this,
        url: base_url+'sms_email_manager/update_template_action',
        type: 'POST',
        dataType: 'JSON',
        data: {tableid:tableid,tem_type:tem_type,updated_template_name:updated_template_name,updated_template_subject:updated_template_subject,updated_template_contents:updated_template_contents,csrf_token:csrf_token},
        success:function(response) {

          $(this).removeClass('btn-progress');
          if (true === response.error) {
            swal({title: 'Error!',text: response.message,icon: 'error'});
          } else if(response.status == "1") {
            $("#update_template_modal").modal('hide');
            iziToast.success({title: '',message: response.message,position: 'bottomRight'});
            table_sms_email_template.draw();
          } else {
            iziToast.error({title: '',message: response.message,position: 'bottomRight'});

          }
        }
      })
    });

    $("#update_template_modal").on('hidden.bs.modal',function(){
      table_sms_email_template.draw();
      $("#updated_template_name").val("");
      $("#updated_template_contents").val("");
      $("#updated_template_subject").val("");
      $("#updated_template_contents").summernote('destroy');
    });


    $(document).on('click','.delete_template',function(e){
        e.preventDefault();
        swal({
            title: '<?php echo $this->lang->line("Are you sure?"); ?>',
            text: '<?php echo $this->lang->line("Do you want to delete this template?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) 
            {
                var table_id = $(this).attr('table_id');
                var type = $(this).attr('type');
                var csrf_token = $("#sms_email_sequence_csrf_token").val();

                $.ajax({
                    context: this,
                    type:'POST' ,
                    url:"<?php echo base_url('sms_email_manager/delete_template')?>",
                    data:{table_id:table_id,type:type,csrf_token:csrf_token},
                    success:function(response){ 

                        if(response == '1')
                        {
                            iziToast.success({title: '',message: '<?php echo $this->lang->line('Template has been Deleted Successfully.'); ?>',position: 'bottomRight'});
                            table_sms_email_template.draw();
                        } else
                        {
                            iziToast.error({title: '',message: '<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>',position: 'bottomRight'});
                        }
                    }
                });
            } 
        });
    });

    $(document).on('click', '.view-emial-template', function(e) {
        e.preventDefault();

        var email_template_modal = $('#email-template-modal'),
            email_template_content = $('#email-template-content'),

            email_template_data = $(this).next().html();

            // email_template_data = $(this).data('email-template-data');
            // email_template_data = JSON.parse(email_template_data);

        // Opens up modal
        $('.xit-spinner').show();
        email_template_modal.modal();
        email_template_content.html(email_template_data).promise().done(function(data){
            $('.xit-spinner').hide();
        });
    });

});
</script>