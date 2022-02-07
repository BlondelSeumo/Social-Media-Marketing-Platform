"use strict";
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
        "url": base_url+'instagram_reply/autoreply_template_manager_data',
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
          targets: [0,1,2,3,4],
          className:'text-center',
          sortable: false
        },
        {
            targets: [4],
            "render": function ( data, type, row, meta ) 
            {
                var id = row[1];
                var edit_str=global_lang_edit;
                var delete_str=global_lang_delete;
                var str="";   
                str="&nbsp;<a class='text-center edit_reply_info btn btn-circle btn-outline-warning' href='#' title='"+edit_str+"' table_id='"+id+"'>"+'<i class="fa fa-edit"></i>'+"</a>";
                str=str+"&nbsp;<a href='#' class='text-center delete_reply_info btn btn-circle btn-outline-danger' title='"+delete_str+"' table_id="+id+" '>"+'<i class="fa fa-trash"></i>'+"</a>";
              
                return str;
            }
        }
    ]
});
// End of datatable section

$('[data-toggle="popover"]').popover(); 
$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});


var content_counter = 1;
var edit_content_counter = 1;


$(document).on('click', '.enable_auto_commnet', function () {

    $("#auto_reply_message_modal").addClass("modal");
    $("#auto_reply_message_modal").modal();

    $(".message").val('').trigger("change");
    $(".filter_word").val('').trigger("change");
    $("#delete_offensive_comment_hide").prop("checked", true);
    $("#multiple_reply").prop("checked", false);
    $("#auto_campaign_name").val('');
    $("#hide_comment_after_comment_reply").prop("checked", false);

    $("#generic").prop("checked", false);
    $("#filter").prop("checked", false);
    $("#generic_message_div").hide();
    $("#filter_message_div").hide();

    for (var i = 2; i <= 20; i++) {
        $("#filter_div_" + i).hide();
        $("#filter_message_" + i).val('').change();
    }

    $("#private_message_offensive_words").val('').change();
    $("#generic_message_private").val('').change();
    content_counter = 1;
    $("#content_counter").val(content_counter);
    $("#add_more_button").show();

    $("#manual_reply_by_post").removeClass('modal');
});

$(document).on('change', 'input[name=message_type]', function () {
    if ($("input[name=message_type]:checked").val() == "generic") {
        $("#generic_message_div").show();
        $("#filter_message_div").hide();

        /*** Load Emoji for generic message when clicked ***/
        
        $("#generic_message").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom"
         });

    }
    else {
        $("#generic_message_div").hide();
        $("#filter_message_div").show();

        /*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
        
        $("#comment_reply_msg_1, #nofilter_word_found_text").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom"
        });

    }
});

$(document).on('change','input[name=edit_message_type]',function(){    
  if($("input[name=edit_message_type]:checked").val()=="generic")
  {
    $("#edit_generic_message_div").show();
    $("#edit_filter_message_div").hide();
    
  }
  else 
  {
    $("#edit_generic_message_div").hide();
    $("#edit_filter_message_div").show();
    
    
    /*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
    
    $("#edit_comment_reply_msg_1, #edit_nofilter_word_found_text").emojioneArea({
      autocomplete: false,
      pickerPosition: "bottom"
    });
  }
});


function get_page_id()
{
    var page_id = $("#dynamic_page_id").val();
    return page_id;
}


$(document).on('change', '#ig_account_page_id,#ig_account_edit_page_id', function(event) {
    event.preventDefault();
    var page_id = $(this).val();
    $("#dynamic_page_id").val(page_id);
    var current_val = $(".private_reply_postback").val();
    $.ajax({
      type:'POST' ,
      url: base_url+"instagram_reply/get_ig_postback",
      data: {page_id:page_id},
      success:function(response){
        $(".private_reply_postback").html(response);
      }
    });
});


$(document).on('click', '#save_button', function () {
    var reply_type = $("input[name=message_type]:checked").val();
    var Youdidntselectanyoption = instagram_auto_comment_template_youdidntselectanyoption;
    var Youdidntprovideallinformation = instagram_all_auto_comment_report_youdidntprovideallinformation;
    var instagram_select_an_account = instagram_selectanaccount;

    var page_table_id = $("#ig_account_page_id").val();

    if(page_table_id == '') {
        swal(global_lang_warning, instagram_select_an_account, 'warning');
        return false;
    }

    if (typeof(reply_type) === 'undefined') {
        swal(global_lang_warning, Youdidntselectanyoption, 'warning');
        return false;
    }
    var auto_campaign_name = $("#auto_campaign_name").val().trim();
    if (reply_type == 'generic') {
        if (auto_campaign_name == '') {
            swal(global_lang_warning, Youdidntprovideallinformation, 'warning');
            return false;
        }
    }
    else {
        if (auto_campaign_name == '') {
            swal(global_lang_warning, Youdidntprovideallinformation, 'warning');
            return false;
        }
    }



    $(this).addClass('btn-progress');

    var queryString = new FormData($("#auto_reply_info_form")[0]);
    var AlreadyEnabled = instagram_template_manager_alreadyenabled;
    $.ajax({
        type: 'POST',
        url: base_url + "instagram_reply/create_template_action",
        data: queryString,
        dataType: 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        context:this,
        success: function (response) {
          $(this).removeClass('btn-progress');
            if (response.status == "1") {
              swal(global_lang_success, response.message, 'success').then((value) => {
                  $("#auto_reply_message_modal").modal('hide');
                  table.draw();
                });
            }
            else {
                swal(global_lang_error, response.message, 'error');
            }
        }

    });

});

$(document).on('click', '.edit_reply_info', function () {

    var emoji_load_div_list="";

    $("#edit_auto_reply_message_modal").addClass("modal");
    $("#edit_response_status").html("");
    for (var j = 1; j <= 20; j++) {
        $("#edit_filter_div_" + j).hide();
    }

    var table_id = $(this).attr('table_id');
    $(".previewLoader").show();
    $.ajax({
        type: 'POST',
        url: base_url+"instagram_reply/edit_template",
        data: {table_id: table_id},
        dataType: 'JSON',
        success: function (response) {
            $("#table_id").val(response.table_id);
            $("#edit_auto_campaign_name").val(response.edit_auto_campaign_name);
            // $("#edit_account_lists").html(response.ig_page_list);

            $("#edit_account_lists").html(response.ig_page_list);
            var selected_page_id = $("#ig_account_edit_page_id").val();
            $("#dynamic_page_id").val(selected_page_id);

            $("#edit_private_message_offensive_words").html(response.postbacks);
            $("#edit_generic_message_private").html(response.postbacks);
            $("#edit_nofilter_word_found_text_private").html(response.postbacks);

            if(response.trigger_matching_type == 'exact')
              $("#edit_trigger_keyword_exact").attr('checked','checked');
            else
              $("#edit_trigger_keyword_string").attr('checked','checked');

            // comment hide and delete section
            if (response.is_delete_offensive == 'hide') {
                $("#edit_delete_offensive_comment_hide").attr('checked', 'checked');
            }
            else {
                $("#edit_delete_offensive_comment_delete").attr('checked', 'checked');
            }
            $("#edit_delete_offensive_comment_keyword").val(response.offensive_words);
            $("#edit_private_message_offensive_words").val(response.private_message_offensive_words).change();

            /** make the emoji loads div id in a string for selection . This is the first add. **/
            emoji_load_div_list=emoji_load_div_list;

            if(response.hide_comment_after_comment_reply == 'no')
              $("#edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
            else
              $("#edit_hide_comment_after_comment_reply").attr('checked','checked');
            // comment hide and delete section

            $("#edit_" + response.reply_type).prop('checked', true);
            // added by mostofa on 27-04-2017
            if(response.multiple_reply == 'no')
              $("#edit_multiple_reply").removeAttr('checked','checked');
            else
              $("#edit_multiple_reply").attr('checked','checked');

            if (response.reply_type == 'generic') {
                $("#edit_generic_message_div").show();
                $("#edit_filter_message_div").hide();
                var i = 1;
                edit_content_counter = i;
                var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
                auto_reply_text_array = JSON.parse(auto_reply_text_array_json, 'true');
                $("#edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click();
                $("#edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).change();

                /** Add generic reply textarea id into the emoji load div list***/
                if(emoji_load_div_list == '')
                  emoji_load_div_list=emoji_load_div_list+"#edit_generic_message";
                else
                  emoji_load_div_list=emoji_load_div_list+", #edit_generic_message";
            }
            else {
                var edit_nofilter_word_found_text = JSON.stringify(response.edit_nofilter_word_found_text);
                edit_nofilter_word_found_text = JSON.parse(edit_nofilter_word_found_text, 'true');
                $("#edit_nofilter_word_found_text").val(edit_nofilter_word_found_text[0]['comment_reply']).click();
                $("#edit_nofilter_word_found_text_private").val(edit_nofilter_word_found_text[0]['private_reply']).change();

                /**Add no match found textarea into emoji load div list***/
                if(emoji_load_div_list == '')
                  emoji_load_div_list=emoji_load_div_list+"#edit_nofilter_word_found_text";
                else
                  emoji_load_div_list=emoji_load_div_list+", #edit_nofilter_word_found_text";


                $("#edit_filter_message_div").show();
                $("#edit_generic_message_div").hide();
                var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
                auto_reply_text_array = JSON.parse(auto_reply_text_array, 'true');

                for (var i = 0; i < auto_reply_text_array.length; i++) {
                    var j = i + 1;
                    $("#edit_filter_div_" + j).show();
                    $("#edit_filter_word_" + j).val(auto_reply_text_array[i]['filter_word']);
                    var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
                    $("#edit_filter_message_" + j).html(response.postbacks);
                    $("#edit_filter_message_" + j).val(unscape_reply_text).change();
                    // added by mostofa 25-04-2017
                    var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
                    $("#edit_comment_reply_msg_" + j).val(unscape_comment_reply_text).click();

                    if(emoji_load_div_list == '')
                      emoji_load_div_list=emoji_load_div_list+"#edit_comment_reply_msg_"+j;
                    else
                      emoji_load_div_list=emoji_load_div_list+", #edit_comment_reply_msg_"+j;
                }

                edit_content_counter = i + 1;
                $("#edit_content_counter").val(edit_content_counter);
            }
            $("#edit_auto_reply_message_modal").modal();
        }
    });

    setTimeout(function(){
    
        $(emoji_load_div_list).emojioneArea({
                autocomplete: false,
                pickerPosition: "bottom"
        });
    },2000);
    
    setTimeout(function(){
    
        $(".previewLoader").hide();
        
    },5000);

});


$(document).on('click', '#edit_save_button', function () {
    var edit_auto_campaign_name = $("#edit_auto_campaign_name").val();
    var reply_type = $("input[name=edit_message_type]:checked").val();
    var Youdidntselectanyoption = instagram_all_auto_comment_report_youdidntselectanyoption;
    var Youdidntprovideallinformation = instagram_template_manager_youdidntprovideallinformation;
    if (typeof(reply_type) === 'undefined') {
        swal(global_lang_warning, Youdidntselectanyoption, 'warning');
        return false;
    }

    if (reply_type == 'generic') {
        if (edit_auto_campaign_name == '') {
            swal(global_lang_warning, Youdidntprovideallinformation, 'warning');
            return false;
        }
    }
    else {
        if (edit_auto_campaign_name == '') {
            swal(global_lang_warning, Youdidntprovideallinformation, 'warning');
            return false;
        }
    }

    $(this).addClass('btn-progress');

    var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
    $.ajax({
        type: 'POST',
        url: base_url + "instagram_reply/edit_template_action",
        data: queryString,
        dataType: 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        context:this,
        success: function (response) {
          $(this).removeClass('btn-progress');
            if (response.status == "1") {
                swal(global_lang_success, response.message, 'success').then((value) => {
                  $("#edit_auto_reply_message_modal").modal('hide');
                  table.draw();
                });
            }
            else {
                swal(global_lang_error, response.message, 'error');
            }
        }

    });

});


/// add & refresh template section

$('.modal').on("hidden.bs.modal", function (e) { 
    if ($('.modal:visible').length) { 
        $('body').addClass('modal-open');
    }
});

$(document).on('click','.add_template',function(e){
    e.preventDefault();
    var selectAnAccountFirst = instagram_selectanaccountfirst

    var current_id=$(this).prev().prev().attr('id');
    var current_val=$(this).prev().prev().val();
    var page_id = get_page_id();
    if(page_id=="")
    {
      swal(global_lang_warning, selectAnAccountFirst, 'warning');
      return false;
    }
    $("#add_template_modal").attr("current_id",current_id);
    $("#add_template_modal").attr("current_val",current_val);
    $("#add_template_modal").modal();
});

$('#add_template_modal').on('shown.bs.modal',function(){ 

  var randTiime = rand_time;
  var page_id = get_page_id();
  var iframe_link = base_url+'messenger_bot/create_new_template/1/'+page_id+"/0/ig?lev="+randTiime;
  $(this).find('iframe').attr('src',iframe_link); 

});  

$('#add_template_modal').on('hidden.bs.modal', function (e) { 
    var current_id=$("#add_template_modal").attr("current_id");
    var current_val=$("#add_template_modal").attr("current_val");
    var page_id = get_page_id();
    var selectAnAccountFirst = instagram_selectanaccountfirst;
    if(page_id=="")
    {
        swal(global_lang_warning, selectAnAccountFirst, 'warning');
        return false;
    }

   $.ajax({
     type:'POST' ,
     url: base_url+"instagram_reply/get_ig_postback",
     data: {page_id:page_id},
     success:function(response){
       $("#"+current_id).html(response);
     }
   });
});

$(document).on('click','.ref_template',function(e){
  e.preventDefault();
  var current_val=$(this).prev().prev().prev().val();
  var current_id=$(this).prev().prev().prev().attr('id');
  var page_id = get_page_id();
  var selectAnAccountFirst = instagram_selectanaccountfirst;
   if(page_id=="")
   {
     swal(global_lang_warning, selectAnAccountFirst, 'warning');
     return false;
   }
   $.ajax({
     type:'POST',
     url: base_url+"instagram_reply/get_ig_postback",
     data: {page_id:page_id},
     success:function(response){
       $("#"+current_id).html(response).val(current_val);
     }
   });
});


/// add & refresh template section



$(document).on('click','.cancel_button',function(){
  $("#edit_auto_reply_message_modal").modal('hide');
});

$(document).on('click','#modal_close',function(){
    $("#auto_reply_message_modal").modal('hide');
    table.draw();
});

$("#content_counter").val(content_counter);

$(document).on('click', '#add_more_button', function () {
    content_counter++;
    if (content_counter == 20)
        $("#add_more_button").hide();
    $("#content_counter").val(content_counter);

    $("#filter_div_" + content_counter).show();

    /** Load Emoji For Filter Word when click on add more button **/
    
   
    $("#comment_reply_msg_"+content_counter).emojioneArea({
        autocomplete: false,
        pickerPosition: "bottom"
    });
    
});

$(document).on('click','#edit_add_more_button',function(){
  if(edit_content_counter == 21)
    $("#edit_add_more_button").hide();
  $("#edit_content_counter").val(edit_content_counter);

  $("#edit_filter_div_"+edit_content_counter).show();
  
  /** Load Emoji For Filter Word when click on add more button during Edit**/
  

  $("#edit_comment_reply_msg_"+edit_content_counter).emojioneArea({
    autocomplete: false,
    pickerPosition: "bottom"
  });
  
  edit_content_counter++;
  
});

$(document).on('click','.lead_first_name',function(){ 
    
    var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
    
    var lastIndex = textAreaTxt.lastIndexOf("<br>");
    
    if(lastIndex!='-1')
        textAreaTxt = textAreaTxt.substring(0, lastIndex);
        
    var txtToAdd = " #LEAD_USER_NAME# ";
    var new_text = textAreaTxt + txtToAdd;
    $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
    $(this).parent().next().next().next().children('.emojionearea-editor').click();
    
    
});

$(document).on('click','.lead_tag_name',function(){

    var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
    
    var lastIndex = textAreaTxt.lastIndexOf("<br>");
    
    if(lastIndex!='-1')
        textAreaTxt = textAreaTxt.substring(0, lastIndex);
        
    var txtToAdd = " #TAG_USER# ";
    var new_text = textAreaTxt + txtToAdd;
   $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
   $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
   
});

$(document).on('click','.delete_reply_info',function(e){
    e.preventDefault();
    var doDelete = instagram_all_auto_comment_report_doyouwanttodeletethisrecordfromdatabase;
    swal({
        title: global_lang_are_you_sure,
        text: doDelete,
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
                url:base_url+"instagram_reply/delete_template",
                data:{table_id:table_id},
                success:function(response){ 
                  if(response == "successfull")
                  {                        
                    iziToast.success({title: '',message: instagram_auto_comment_template_deleted_successfully,position: 'bottomRight'});
                    table.draw();
                  }
                  else
                  {
                    iziToast.error({title: '',message: global_lang_something_went_wrong,position: 'bottomRight'});
                  }
                }
            });
        } 
    });

});


});