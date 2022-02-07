 "use strict";
 var Youdidntprovideallinformation =instagram_all_auto_comment_report_youdidntprovideallinformation;
 var Youdidntselectanyoption = instagram_auto_comment_template_youdidntselectanyoption;
 var Youdidntprovideallcomment = instagram_auto_comment_template_youdidntprovideallcomment;
 var AutoComment =instagram_auto_comment_template_autocomment;
 var remove = global_lang_remove;
 var AddComments = instagram_auto_comment_template_addcomments;
 $(document).ready(function(){
    // datatable section started
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax: 
        {
            "url": base_url+'comment_automation/template_manager_data',
            "type": 'POST',
            "dataSrc": function ( json ) 
            {
              $(".table-responsive").niceScroll();
              return json.data;
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
              targets: '',
              className: 'text-center',
              sortable: false
            },
            {
                targets: [3],
                "render": function ( data, type, row, meta ) 
                {
                    var id = row[1];
                    var edit_str=global_lang_edit;
                    var delete_str=global_lang_delete;
                    var str="";   
                    str="&nbsp;<a class='text-center edit_reply_info btn btn-circle btn-outline-warning edit' href='#' name='edit' title='"+edit_str+"' id='"+id+"'>"+'<i class="fa fa-edit"></i>'+"</a>";
                    str=str+"&nbsp;<a name='delete' href='#' class='text-center delete_reply_info btn btn-circle btn-outline-danger delete' title='"+delete_str+"' id="+id+" '>"+'<i class="fa fa-trash"></i>'+"</a>";
                  
                    return str;
                }
            }
        ]
    });
    // End of datatable section



    var count = 10;
    var wrapper= $('#dynamic_field');
    var add_button_edit      = $(".add_more_edit");
    var add_button = $("#add_more_new");
    var x=1;

    function add_dynamic_input_field(x)
    {     
        var output = '<div class="card card-primary single_item mt-2 pb-0 mb-0">';
        output += '<div class="card-header"><h4 class="modal-title text-center"><i class="fa fa-comments"></i> '+AutoComment+'</h4></div> <div class="card-body"><textarea type="text" name="auto_reply_comment_text[]" id="auto_reply_comment_text_'+x+'" class="form-control name_list w-100 height_70px" placeholder="'+AddComments+'"></textarea><span class="clearfix"><a href="#" class="font_size_10px text-center  btn btn-sm btn-outline-danger remove_field float-right clearfix"><i class="fas fa-times"></i> '+remove+'</a></span></div>';
        output += '</div>';
        $(wrapper).append(output);
    }

    $(document).on('click', '#add', function(e) {
        e.preventDefault();
        add_dynamic_input_field(x);
        $(".add_more_edit").hide();
        $('#action').val("insert");
        $('#submit').val(global_lang_submit);
        $('#dynamic_field_modal').modal('show');

         $("#auto_reply_comment_text_"+x).emojioneArea({
                autocomplete: false,
                pickerPosition: "bottom"
        });
    });

    $(add_button).on('click', function(e){

        e.preventDefault();
        if(x<count){
            x++;
            add_dynamic_input_field(x);

             $("#auto_reply_comment_text_"+x).emojioneArea({
                autocomplete: false,
                pickerPosition: "bottom"
            });
            
        }

    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); 
        
        $(this).parent().parent().parent().remove(); x--;
        

    });


    $(document).on('submit', '#add_name', function(event) {
        event.preventDefault();

        $("#response_status").html('');

        var name = $("#name").val().trim();

        const comment_block_num = $(".single_item").length;
        let comment_block_content;


        if (comment_block_num === 1) {

            comment_block_content = $(".single_item").find('textarea').val();
            console.log(comment_block_content);
        }

        if(name === '' || comment_block_num === 0 || (comment_block_num === 1 && comment_block_content == '')){
            swal(global_lang_warning, Youdidntprovideallinformation, 'warning');
            return false;
        }
        var form_data = $(this).serialize();

        var auto_reply_comment_text = $('#auto_reply_comment_text').val();
        if(auto_reply_comment_text == ''){
            swal(global_lang_warning, Youdidntprovideallcomment, 'warning');
            return false;
        }
        var action = $('#action').val();

        $(this).addClass('btn-progress');
        $.ajax({
            url:base_url+"comment_automation/create_template_action",
            method:"POST",
            data:form_data,
            context:this,
            success:function(data)
            {
                $(this).removeClass('btn-progress');
                

                if(action == 'insert')
                {
                    swal(global_lang_success, global_lang_saved_successfully, 'success').then((value) => {
                                      $("#dynamic_field_modal").modal('hide');
                                      $('#add_name')[0].reset();
                                      location.reload();
                                    });
                }

                if(action == 'edit')
                {
                    swal(global_lang_success, global_lang_saved_successfully, 'success').then((value) => {
                                      $("#dynamic_field_modal").modal('hide');
                                      $('#add_name')[0].reset();
                                      location.reload();
                                    });
                }
                
      
            }
        });
      
    });

    $(document).on('click', '.edit', function(e){
        e.preventDefault();
        var id = $(this).attr("id");
        $("#add_more_new").hide();

        $.ajax({
            url:base_url+"comment_automation/ajaxselect",
            method:"POST",
            data:{id:id},
            dataType:"JSON",
            success:function(data)
            {
                $('#name').val(data.template_name);
                $('#dynamic_field').html(data.auto_reply_comment_text);
                $('#action').val('edit');
                $('.modal-title').html("<i class='fa fa-comments'></i> "+instagram_auto_comment_template_autocomment);
                $('.modal-header .modal-title').html("<i class='fa fa-comments'></i> "+instagram_auto_comment_template_please_give_the_following_information_for_post_auto_comment);
                $('#submit').val(global_lang_update);
                $('#hidden_id').val(id);
                $('#dynamic_field_modal').modal('show');

                x=data.x;

                for(var k=1; k<=x;k++){

                      $("#auto_reply_comment_text_"+k).emojioneArea({
                            autocomplete: false,
                            pickerPosition: "bottom"
                        });
                }
                
               
                $(add_button_edit).on('click', function(e){
                    e.preventDefault();
                    if(x<count){
                        x++;
                        add_dynamic_input_field(x);

                        $("#auto_reply_comment_text_"+x).emojioneArea({
                            autocomplete: false,
                            pickerPosition: "bottom"
                        });
                        
                    }
                });
            }
        });
    });


  
    $(document).on('click','.delete',function(e){
        if(is_demo=='1' && is_admin=='1')
        {
            swal(global_lang_warning, instagram_auto_comment_template_can_not_delete_from_admin, 'warning');
            return;
        }
        e.preventDefault();
        swal({
            title: global_lang_are_you_sure,
            text: instagram_all_auto_comment_report_doyouwanttodeletethisrecordfromdatabase,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) 
            {
                var id = $(this).attr("id");

                $.ajax({
                    context: this,
                    type:'POST' ,
                    url:base_url+"comment_automation/delete_comment",
                    data: {id:id},
                    success:function(response){ 
                        iziToast.success({title: '',message: instagram_auto_comment_template_deleted_successfully,position: 'bottomRight'});
                        table.draw();
                    }
                });
            } 
        });

    });


    $('#dynamic_field_modal').on('hidden.bs.modal', function () { 
        location.reload();
    })


});