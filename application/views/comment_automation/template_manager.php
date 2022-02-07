<?php $this->load->view('admin/theme/message'); ?>
<?php 
  $this->load->view("include/upload_js"); 
  if(ultraresponse_addon_module_exist())  $commnet_hide_delete_addon = 1;
  else $commnet_hide_delete_addon = 0;

  if(addon_exist(201,"comment_reply_enhancers")) $comment_tag_machine_addon = 1;
  else $comment_tag_machine_addon = 0;    
?>

<?php 
  
  $Youdidntprovideallinformation = $this->lang->line("you didn't provide all information.");
  $Pleaseprovidepostid = $this->lang->line("please provide post id.");
  $Youdidntselectanyoption = $this->lang->line("you didn\'t select any option.");
  
  $AlreadyEnabled = $this->lang->line("already enabled");
  $ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking = $this->lang->line("This post ID is not found in database or this post ID is not associated with the page you are working.");
  $EnableAutoReply = $this->lang->line("enable auto reply");

 ?>


<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fa fa-list-alt"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary enable_page_response" href="#">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new template"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="table-responsive">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th><?php echo $this->lang->line("Template ID"); ?></th>      
                    <th><?php echo $this->lang->line("Template Name"); ?></th>
                    <th><?php echo $this->lang->line("Page Name"); ?></th>
                    <th style="min-width: 150px"><?php echo $this->lang->line("Actions"); ?></th>
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
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
     
  });

  $(document).ready(function(){

    var base_url = "<?php echo base_url(); ?>";

    // datatable section started
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax: 
        {
            "url": base_url+'comment_automation/autoreply_template_manager_data',
            "type": 'POST',
            "dataSrc": function ( json ) 
            {
              $(".table-responsive").niceScroll();
              return json.data;
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
              className:'text-center',
              sortable: false
            },
            {
                targets: [4],
                "render": function ( data, type, row, meta ) 
                {
                    var id = row[1];
                    var edit_str="<?php echo $this->lang->line('Edit');?>";
                    var delete_str="<?php echo $this->lang->line('Delete');?>";
                    var str="";   
                    str="&nbsp;<a class='text-center edit_reply_info btn btn-circle btn-outline-warning' href='#' title='"+edit_str+"' table_id='"+id+"'>"+'<i class="fa fa-edit"></i>'+"</a>";
                    str=str+"&nbsp;<a href='#' class='text-center delete_reply_info btn btn-circle btn-outline-danger' title='"+delete_str+"' table_id="+id+" '>"+'<i class="fa fa-trash"></i>'+"</a>";
                  
                    return str;
                }
            }
        ]
    });
    // End of datatable section


    var content_counter = 1;
    var edit_content_counter = 1;

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});



    $(document).on('click','.delete_reply_info',function(e){
        e.preventDefault();
        var doDelete = "<?php echo $this->lang->line('are you sure that you want to delete this record?'); ?>";
        swal({
            title: '<?php echo $this->lang->line("Are you sure?"); ?>',
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
                    url:"<?php echo base_url('comment_automation/delete_template')?>",
                    data:{table_id:table_id},
                    success:function(response){ 
                      if(response == "successfull")
                      {                        
                        iziToast.success({title: '',message: '<?php echo $this->lang->line("Template has been deleted successfully."); ?>',position: 'bottomRight'});
                        table.draw();
                      }
                      else
                      {
                        iziToast.error({title: '',message: '<?php echo $this->lang->line("Something went wrong, please try again."); ?>',position: 'bottomRight'});
                      }
                    }
                });
            } 
        });

    });


    $(document).on('click','.enable_page_response',function(event){
      event.preventDefault();
     
        var page_table_id = $(this).attr('page_table_id');
        var post_id = $(this).attr('page_id');

        $("#auto_reply_page_id").val(page_table_id);
        $("#auto_reply_post_id").val(post_id);
        $("#manual_enable").val('no');

        $(".message").val('').click();
        $(".filter_word").val('');
        $("#auto_campaign_name").val('');
        $("#comment_reply_enabled").prop("checked", true);
        $("#delete_offensive_comment_hide").prop("checked", true);
        $("#multiple_reply").prop("checked", false);
        $("#auto_like_comment").prop("checked", false);
        $("#hide_comment_after_comment_reply").prop("checked", false);

        $("#generic").prop("checked", false);
        $("#filter").prop("checked", false);
        $("#generic_message_div").hide();
        $("#filter_message_div").hide();


        for(var i=2;i<=20;i++)
        {
        $("#filter_div_"+i).hide();
        }
        content_counter = 1;
        $("#content_counter").val(content_counter);
        $("#add_more_button").show();

        $("#response_status").html('');

        $("#auto_reply_message_modal").addClass("modal");
        $("#auto_reply_message_modal").modal();

    });


    $(document).on('click','.edit_enable_page_response',function(event){
        event.preventDefault();

        var page_table_id = $(this).attr('page_table_id');
        var post_id = $(this).attr('page_id');

        $("#auto_reply_page_id").val(page_table_id);
        $("#auto_reply_post_id").val(post_id);
        $("#manual_enable").val('no');
        $(".message").val('');
        $(".filter_word").val('');
        for(var i=2;i<=20;i++)
        {
        $("#filter_div_"+i).hide();
        }
        content_counter = 1;
        $("#content_counter").val(content_counter);
        $("#add_more_button").show();

        $("#response_status").html('');

        $("#edit_auto_reply_message_modal").addClass("modal");
        $("#edit_auto_reply_message_modal").modal();

    });
    


    $("#content_counter").val(content_counter);
  
    $(document).on('click','#add_more_button',function(){
      content_counter++;
      if(content_counter == 20)
        $("#add_more_button").hide();
      $("#content_counter").val(content_counter);

      $("#filter_div_"+content_counter).show();
    
      /** Load Emoji For Filter Word when click on add more button **/

      
      $("#comment_reply_msg_"+content_counter).emojioneArea({
          autocomplete: false,
        pickerPosition: "bottom"
      });

    });


    $(document).on('change','input[name=message_type]',function(){    
      if($("input[name=message_type]:checked").val()=="generic")
      {
        $("#generic_message_div").show();
        $("#filter_message_div").hide();

        $("#generic_message").emojioneArea({
          autocomplete: false,
          pickerPosition: "bottom"
        });



      }
      else 
      {
        $("#generic_message_div").hide();
        $("#filter_message_div").show();

        /*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
        
        $("#comment_reply_msg_1, #nofilter_word_found_text").emojioneArea({
          autocomplete: false,
          pickerPosition: "bottom"
        });
        
        

      }
    });

        
    $(document).on('click','.lead_first_name',function(){

      var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
      
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
      
      

      var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
      var new_text = textAreaTxt + txtToAdd;
      $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
      $(this).parent().next().next().next().children('.emojionearea-editor').click();
      
    });

    $(document).on('click','.lead_last_name',function(){
        
      var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
      
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
      
      
        
        var txtToAdd = " #LEAD_USER_LAST_NAME# ";
      var new_text = textAreaTxt + txtToAdd;
       $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
       $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
       
       
    });

    $(document).on('click','.lead_tag_name',function(){
      
      var textAreaTxt = $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html();
      
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
      
      
        
        
        var txtToAdd = " #TAG_USER# ";
      var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().next().next().children('.emojionearea-editor').click();
      
      
    });



    $(document).on('click','#save_button',function(){
      var post_id = $("#auto_reply_post_id").val();
      var reply_type = $("input[name=message_type]:checked").val();
      var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
      var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
      if (typeof(reply_type)==='undefined')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
        return false;
      }
      var auto_campaign_name = $("#auto_campaign_name").val().trim();
      if(reply_type == 'generic')
      {
        if(auto_campaign_name == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
          return false;
        }
      }
      else
      {
        if(auto_campaign_name == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
          return false;
        }
      }

      $(this).addClass('btn-progress');

      var queryString = new FormData($("#auto_reply_info_form")[0]);
        $.ajax({
          type:'POST' ,
          url: base_url+"comment_automation/autoreply_template_submit",
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          context: this,
          success:function(response){
              $(this).removeClass('btn-progress');
              if(response.status=="1")
              {
                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                      table.draw();
                      $("#auto_reply_message_modal").modal('hide');
                    });
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
          }

        });

    });

    

    $(document).on('click','#modal_close',function(){
      $("#auto_reply_message_modal").modal("hide");
      table.draw();
    });

    $(document).on('click','#edit_modal_close',function(){
      $("#edit_auto_reply_message_modal").modal("hide");     
      table.draw();
    });



    $(document).on('click','.edit_reply_info',function(event){

      event.preventDefault();
  
      var emoji_load_div_list="";


      $("#manual_edit_reply_by_post").removeClass('modal');
      $("#edit_auto_reply_message_modal").addClass("modal");
      $("#edit_response_status").html("");
      

      var table_id = $(this).attr('table_id');
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>comment_automation/templatemanager_reply_info",
        data:{table_id:table_id},
        dataType:'JSON',
        success:function(response){
          $("#table_id").val(response.table_id);
          $("#edit_auto_reply_post_id").val(response.edit_auto_reply_post_id);
          $("#edit_auto_campaign_name").val(response.edit_auto_campaign_name);

          $("#edit_page_ids").html(response.page_list);
          var selected_page_id = $("#edit_page_ids").val();
          $("#dynamic_page_id").val(selected_page_id);

          $("#edit_private_message_offensive_words").html(response.postbacks);
          $("#edit_generic_message_private").html(response.postbacks);
          $("#edit_nofilter_word_found_text_private").html(response.postbacks);
          for(var j=1;j<=20;j++)
          {
            $("#edit_filter_div_"+j).hide();
            $("#edit_filter_message_"+j).html(response.postbacks);
          }

          if(response.trigger_matching_type == 'exact')
            $("#edit_trigger_keyword_exact").attr('checked','checked');
          else
            $("#edit_trigger_keyword_string").attr('checked','checked');

          // comment hide and delete section
          if(response.is_delete_offensive == 'hide')
          {
              $("#edit_delete_offensive_comment_hide").attr('checked','checked');
          }
          else
          {
            $("#edit_delete_offensive_comment_delete").attr('checked','checked');
          }
          $("#edit_delete_offensive_comment_keyword").val(response.offensive_words);


          $("#edit_private_message_offensive_words").val(response.private_message_offensive_words).click();
      
          /** make the emoji loads div id in a string for selection . This is the first add. **/
         // emoji_load_div_list=emoji_load_div_list;

          if(response.hide_comment_after_comment_reply == 'no')
            $("#edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
          else
            $("#edit_hide_comment_after_comment_reply").attr('checked','checked');
          // comment hide and delete section

          $("#edit_"+response.reply_type).prop('checked', true);
          // added by mostofa on 27-04-2017
          if(response.comment_reply_enabled == 'no')
            $("#edit_comment_reply_enabled").removeAttr('checked','checked');
          else
            $("#edit_comment_reply_enabled").attr('checked','checked');

          if(response.multiple_reply == 'no')
            $("#edit_multiple_reply").removeAttr('checked','checked');
          else
            $("#edit_multiple_reply").attr('checked','checked');

          if(response.auto_like_comment == 'no')
            $("#edit_auto_like_comment").removeAttr('checked','checked');
          else
            $("#edit_auto_like_comment").attr('checked','checked');

          var inner_content = '<i class="fas fa-times"></i> Remove';
          
          if(response.reply_type == 'generic')
          {
            $("#edit_generic_message_div").show();
            $("#edit_filter_message_div").hide();
            var i=1;
            edit_content_counter = i;
            var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
            auto_reply_text_array = JSON.parse(auto_reply_text_array_json,'true');
            $("#edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click(); 
            $("#edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).click();
      
             /** Add generic reply textarea id into the emoji load div list***/
            if(emoji_load_div_list == '')
            emoji_load_div_list=emoji_load_div_list+"#edit_generic_message";
            else
            emoji_load_div_list=emoji_load_div_list+", #edit_generic_message";
          
          
            // comment hide and delete section
            
            $("#edit_generic_image_for_comment_reply_display").attr('src',auto_reply_text_array[0]['image_link']).show();
            if(auto_reply_text_array[0]['image_link']=="")
            {
              $("#edit_generic_image_for_comment_reply_display").prev('span').removeClass('remove_media').html('');
              $("#edit_generic_image_for_comment_reply_display").hide();
            }
            else
              $("#edit_generic_image_for_comment_reply_display").prev('span').addClass('remove_media').html(inner_content);


            var vidreplace='<source src="'+auto_reply_text_array[0]['video_link']+'" id="edit_generic_video_comment_reply_display" type="video/mp4">';
            $("#edit_generic_video_comment_reply_display").parent().html(vidreplace).show();
            
            if(auto_reply_text_array[0]['video_link']=='')
            {
              $("#edit_generic_video_comment_reply_display").parent().prev('span').removeClass('remove_media').html('');
              $("#edit_generic_video_comment_reply_display").parent().hide();
            }
            else
              $("#edit_generic_video_comment_reply_display").parent().prev('span').addClass('remove_media').html(inner_content);


            $("#edit_generic_image_for_comment_reply").val(auto_reply_text_array[0]['image_link']);
            $("#edit_generic_video_comment_reply").val(auto_reply_text_array[0]['video_link']);
            // comment hide and delete section
          }
          else
          {
            var edit_nofilter_word_found_text = JSON.stringify(response.edit_nofilter_word_found_text);
            edit_nofilter_word_found_text = JSON.parse(edit_nofilter_word_found_text,'true');
            $("#edit_nofilter_word_found_text").val(edit_nofilter_word_found_text[0]['comment_reply']).click();
            $("#edit_nofilter_word_found_text_private").val(edit_nofilter_word_found_text[0]['private_reply']).click();
      
            /**Add no match found textarea into emoji load div list***/
            if(emoji_load_div_list == '')
            emoji_load_div_list=emoji_load_div_list+"#edit_nofilter_word_found_text";
            else
            emoji_load_div_list=emoji_load_div_list+", #edit_nofilter_word_found_text";


          
          
            // comment hide and delete section

            $("#edit_nofilter_image_upload_reply_display").attr('src',edit_nofilter_word_found_text[0]['image_link']).show();
            if(edit_nofilter_word_found_text[0]['image_link']=="")
            {
              $("#edit_nofilter_image_upload_reply_display").prev('span').removeClass('remove_media').html('');
              $("#edit_nofilter_image_upload_reply_display").hide();              
            }
            else
              $("#edit_nofilter_image_upload_reply_display").prev('span').addClass('remove_media').html(inner_content);


            var vidreplace='<source src="'+edit_nofilter_word_found_text[0]['video_link']+'" id="edit_nofilter_video_upload_reply_display" type="video/mp4">';
            $("#edit_nofilter_video_upload_reply_display").parent().html(vidreplace).show();
            
            if(edit_nofilter_word_found_text[0]['video_link']=='')
            {
              $("#edit_nofilter_video_upload_reply_display").parent().prev('span').removeClass('remove_media').html('');
              $("#edit_nofilter_video_upload_reply_display").parent().hide();
            }
            else
              $("#edit_nofilter_video_upload_reply_display").parent().prev('span').addClass('remove_media').html(inner_content);


            $("#edit_nofilter_image_upload_reply").val(edit_nofilter_word_found_text[0]['image_link']);
            $("#edit_nofilter_video_upload_reply").val(edit_nofilter_word_found_text[0]['video_link']);
            // comment hide and delete section

            $("#edit_filter_message_div").show();
            $("#edit_generic_message_div").hide();
            var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
            auto_reply_text_array = JSON.parse(auto_reply_text_array,'true');

            for(var i = 0; i < auto_reply_text_array.length; i++) {
                var j = i+1;
                $("#edit_filter_div_"+j).show();
              $("#edit_filter_word_"+j).val(auto_reply_text_array[i]['filter_word']);
              var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
              $("#edit_filter_message_"+j).val(unscape_reply_text).click();
              // added by mostofa 25-04-2017
              var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
              $("#edit_comment_reply_msg_"+j).val(unscape_comment_reply_text).click();
              
              if(emoji_load_div_list == '')
              emoji_load_div_list=emoji_load_div_list+"#edit_comment_reply_msg_"+j;
              else
              emoji_load_div_list=emoji_load_div_list+", #edit_comment_reply_msg_"+j;

        
              // comment hide and delete section
              
              $("#edit_filter_image_upload_reply_display_"+j).attr('src',auto_reply_text_array[i]['image_link']).show();
              if(auto_reply_text_array[i]['image_link']=="")
              {
                $("#edit_filter_image_upload_reply_display_"+j).prev('span').removeClass('remove_media').html('');
                $("#edit_filter_image_upload_reply_display_"+j).hide();
              }
              else
                $("#edit_filter_image_upload_reply_display_"+j).prev('span').addClass('remove_media').html(inner_content);


              var vidreplace='<source src="'+auto_reply_text_array[i]['video_link']+'" id="edit_filter_video_upload_reply_display'+j+'" type="video/mp4">';
              $("#edit_filter_video_upload_reply_display"+j).parent().html(vidreplace).show();
              if(auto_reply_text_array[i]['video_link']=='')
              {
                $("#edit_filter_video_upload_reply_display"+j).parent().prev('span').removeClass('remove_media').html('');
                $("#edit_filter_video_upload_reply_display"+j).parent().hide();
              }
              else
                $("#edit_filter_video_upload_reply_display"+j).parent().prev('span').addClass('remove_media').html(inner_content);

              $("#edit_filter_image_upload_reply_"+j).val(auto_reply_text_array[i]['image_link']);
              $("#edit_filter_video_upload_reply_"+j).val(auto_reply_text_array[i]['video_link']);
              // comment hide and delete section
            }

            edit_content_counter = i+1;
            $("#edit_content_counter").val(edit_content_counter);
          }
          $("#edit_auto_reply_message_modal").modal();
        },
        
      });

      setTimeout(function(){
      
        $(emoji_load_div_list).emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom"
        });
      },2000);
      
      setTimeout(function(){
      
        $(".previewLoader").hide();
        
      },2000);


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
    

    $(document).on('click','#edit_save_button',function(){
      var post_id = $("#edit_auto_reply_post_id").val();
      var edit_auto_campaign_name = $("#edit_auto_campaign_name").val();
      var reply_type = $("input[name=edit_message_type]:checked").val();
      var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
      var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
      if (typeof(reply_type)==='undefined')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
        return false;
      }
      if(reply_type == 'generic')
      {
        if(edit_auto_campaign_name == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
          return false;
        }
      }
      else
      {
        if(edit_auto_campaign_name == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
          return false;
        }
      }

      $(this).addClass('btn-progress');

      var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
        $.ajax({
          type:'POST' ,
          url: base_url+"comment_automation/update_templatemanager_info",
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          context: this,
          success:function(response){
              $(this).removeClass('btn-progress');
              if(response.status=="1")
              {
                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                      table.draw();
                      $("#edit_auto_reply_message_modal").modal('hide');
                    });
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
          }

        });

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


    $(document).on('click','.cancel_button',function(){
      $("#auto_reply_message_modal").modal('hide');
      $("#edit_auto_reply_message_modal").modal('hide');
    });

    $(document).on('click','.remove_media',function(){
      $(this).parent().prev('input').val('');
      $(this).parent().hide();
    });

    
  });
</script>


<div class="modal fade" id="auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg"  style="min-width: 70%;">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" style="padding: 10px 20px 10px 20px;"><?php echo $this->lang->line("Please give the following information for post auto reply") ?></h5>
            <button type="button" class="close" id='modal_close'  aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>

            <form action="#" id="auto_reply_info_form" method="post">
              <input type="hidden" name="auto_reply_page_id" id="auto_reply_page_id" value="">
              <input type="hidden" name="auto_reply_post_id" id="auto_reply_post_id" value="">
              <input type="hidden" name="manual_enable" id="manual_enable" value="">

              <div class="modal-body" id="auto_reply_message_modal_body">
          
          <div id="new_template_section">

            <div class="row" style="padding: 10px 20px 10px 20px;">                
              <div class="col-12 col-md-4">
                <label><i class="fas fa-file-alt"></i> <?php echo $this->lang->line("Please select a page for auto-reply") ?></label>
              </div>
              <div class="col-12 col-md-8">
                <select class="form-control select2" id="page_ids" name="page_ids[]">
                  <option><?php echo $this->lang->line('Select a page'); ?></option>
                <?php
                  foreach ($page_list as $key => $value) 
                  {
                    echo '<optgroup label="'.addslashes($value['fb_user_name']).'">';
                    foreach ($value['data'] as $key2 => $value2) 
                    {
                      echo "<option value='".$value2['table_id']."'>".$value2['page_name']."</option>";
                    }
                    echo '</optgroup>';
                  }
                ?>                      
                </select>
              </div>
            </div>


            <!-- comment hide and delete section -->
            <div class="row" style="padding: 10px 20px 10px 20px; <?php if(!$commnet_hide_delete_addon) echo "display: none;"; ?> ">
              <div class="col-12">
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
                  </div>
                  <div class="row">
                    <div class="col-12 col-md-6">
                      <label class="custom-switch">
                        <input type="radio" name="delete_offensive_comment" value="hide" id="delete_offensive_comment_hide" class="custom-switch-input" checked>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
                      </label>
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="custom-switch">
                        <input type="radio" name="delete_offensive_comment" value="delete" id="delete_offensive_comment_delete" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <br/><br/>
              
              <div class="col-12">                
                <div class="row">               
                  <div class="col-12 col-md-6" id="delete_offensive_comment_keyword_div">
                    <div class="form-group" style="border: 1px dashed #ccc; padding: 10px;">
                      <label><i class="fa fa-tags"></i> <small><?php echo $this->lang->line("write down the offensive keywords in comma separated") ?></small>
                      </label>
                      <textarea class="form-control message" name="delete_offensive_comment_keyword" id="delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions") ?>" style="height:59px !important;"></textarea>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                  	<div class="form-group clearfix" style="border: 1px dashed #ccc; padding: 10px;">
	                    <label><small>
	                      <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply after deleting offensive comment") ?></small>
	                    </label>
	                    <div>   
	                      <select class="form-group private_reply_postback" id="private_message_offensive_words" name="private_message_offensive_words">
	                        <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
	                      </select>

	                      <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
	                      <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                    
	                    </div>
	                </div>
                  </div>
                </div>
              </div>
            </div>  
            <!-- end of comment hide and delete section -->

            <div class="row" style="padding: 10px 20px 10px 20px;">
              <!-- added by mostofa on 26-04-2017 -->
              <div class="col-12">
                <div class="row">                 
                  <div class="col-12 col-md-6"><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("do you want to send reply message to a user multiple times?") ?></label></div>
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label class="custom-switch">
                        <input type="checkbox" name="multiple_reply" value="yes" id="multiple_reply" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="smallspace clearfix"></div>
              <div class="col-12">
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line("do you want to enable comment reply?") ?></label>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label class="custom-switch">
                        <input type="checkbox" name="comment_reply_enabled" value="yes" id="comment_reply_enabled" class="custom-switch-input" checked>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="smallspace clearfix"></div>
              <div class="col-12">
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label><i class="fa fa-comment"></i> <?php echo $this->lang->line("do you want to like on comment by page?") ?></label>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label class="custom-switch">
                        <input type="checkbox" name="auto_like_comment" value="yes" id="auto_like_comment" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                      </label>
                    </div> 
                  </div>
                </div>
              </div>
              <div class="smallspace clearfix"></div>
              <!-- comment hide and delete section -->
              <div class="col-12" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label><i class="fa fa-eye-slash"></i>  <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
                  </div>
                  <div class="col-12 col-md-6">
                      <div class="form-group">
                        <label class="custom-switch">
                          <input type="checkbox" name="hide_comment_after_comment_reply" value="yes" id="hide_comment_after_comment_reply" class="custom-switch-input">
                          <span class="custom-switch-indicator"></span>
                          <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                        </label>
                      </div>
                  </div>
                </div>
              </div>
              <!-- comment hide and delete section -->

              <div class="smallspace clearfix"></div>


              <div class="col-12">
                <div class="custom-control custom-radio">
                  <input type="radio" name="message_type" value="generic" id="generic" class="custom-control-input radio_button">
                  <label class="custom-control-label" for="generic"><?php echo $this->lang->line("generic message for all") ?></label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" name="message_type" value="filter" id="filter" class="custom-control-input radio_button">
                  <label class="custom-control-label" for="filter"><?php echo $this->lang->line("send message by filtering word/sentence") ?></label>
                </div>
              </div>
              <div class="col-12" style="margin-top: 15px;">
                <div class="form-group">
                  <label>
                    <i class="fas fa-monument"></i> <?php echo $this->lang->line("auto reply campaign name") ?> <span class="red">*</span> 
                    <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your auto reply campaign name here") ?>"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <input class="form-control" type="text" name="auto_campaign_name" id="auto_campaign_name" placeholder="<?php echo $this->lang->line("write your auto reply campaign name here") ?>">
                </div>
              </div>


              <div class="col-12" id="generic_message_div" style="display: none;">
                <div class="form-group clearfix" style="border: 1px dashed #ccc; padding: 10px;">
                  <label>
                    <i class="fa fa-envelope"></i> <?php echo $this->lang->line("Message for comment reply") ?> <span class="red">*</span>
                    <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <?php if($comment_tag_machine_addon) {?>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
                  </span>
                  <?php } ?>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                  </span>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                  </span> 
                  
                  <div class="clearfix"></div>            
                  <textarea class="form-control message" name="generic_message" id="generic_message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>

                  <!-- comment hide and delete section -->
                  <br/>
                  <div class="row clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                    <div class="col-12 col-md-6">
                      <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?></label>                  
                      <div class="form-group">      
                                    <div id="generic_comment_image"><?php echo $this->lang->line("upload") ?></div>      
                      </div>
                      <div id="generic_image_preview_id"></div>
                      <span class="red" id="generic_image_for_comment_reply_error"></span>
                      <input type="text" name="generic_image_for_comment_reply" class="form-control" id="generic_image_for_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="control-label" ><i class="fa fa-youtube"></i>  <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
                        <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                      </label>
                      <div class="form-group">      
                                    <div id="generic_video_upload"><?php echo $this->lang->line("upload") ?></div>       
                      </div>
                      <div id="generic_video_preview_id"></div>
                      <span class="red" id="generic_video_comment_reply_error"></span>
                      <input type="hidden" name="generic_video_comment_reply" class="form-control" id="generic_video_comment_reply" placeholder="<?php echo $this->lang->line("Put your image url here or click upload") ?>"  />
                    </div>
                  </div>
                  <br/><br/>
                  <!-- comment hide and delete section -->


                  <label>
                    <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
                  </label>
                  <div>     
                    <select class="form-group private_reply_postback" id="generic_message_private" name="generic_message_private">
                      <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                    </select>

                    <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                    <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                  
                  </div>
                  
                </div>
              </div>


              <div class="col-12" id="filter_message_div" style="display: none;">
                <div class="row">
                  <div class="col-12 col-md-6">
                  <label class="custom-switch">
                    <input type="radio" name="trigger_matching_type" value="exact" id="trigger_keyword_exact" class="custom-switch-input" checked>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Reply if the filter word exactly matches.'); ?></span>
                  </label>
                  </div>
                  <div class="col-12 col-md-6">
                  <label class="custom-switch">
                    <input type="radio" name="trigger_matching_type" value="string" id="trigger_keyword_string" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Reply if any matches occurs with filter word.'); ?>
                  </label>
                  </div>
                </div><br/>
                <?php for ($i=1; $i <= 20 ; $i++) : ?>
                    <div class="form-group clearfix" id="filter_div_<?php echo $i; ?>" style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 50px;">
                      <label>
                        <i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span class="red">*</span>
                        <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, wanto to know, when") ?>"><i class='fa fa-info-circle'></i> </a>
                      </label>
                      <input class="form-control filter_word" type="text" name="filter_word_<?php echo $i; ?>" id="filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">

                      
                      <!-- new feature comment reply section -->
                      <br/>
                      <label>
                        <i class="fa fa-envelope"></i> <?php echo $this->lang->line("msg for comment reply") ?><span class="red">*</span>
                        <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                      </label>
                      <?php if($comment_tag_machine_addon) {?>
                      <span class='float-right'> 
                        <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
                      </span>
                      <?php } ?>
                      <span class='float-right'> 
                        <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                      </span>
                      <span class='float-right'> 
                        <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                      </span> 
                      <div class="clearfix"></div>
                      <textarea class="form-control message" name="comment_reply_msg_<?php echo $i; ?>" id="comment_reply_msg_<?php echo $i; ?>"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>

                      <!-- comment hide and delete section -->
                      <br/>
                      <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                        <div class="row">                         
                          <div class="col-12 col-md-6">
                            <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                            </label>                  
                            <div class="form-group">      
                                          <div id="filter_image_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>      
                            </div>
                            <div id="generic_image_preview_id_<?php echo $i; ?>"></div>
                            <span class="red" id="generic_image_for_comment_reply_error_<?php echo $i; ?>"></span>
                            <input type="text" name="filter_image_upload_reply_<?php echo $i; ?>" class="form-control" id="filter_image_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("Put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                          </div>

                          <div class="col-12 col-md-6">
                            <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
                              <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                            </label>
                            <div class="form-group">      
                                          <div id="filter_video_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>      
                            </div>
                            <div id="generic_video_preview_id_<?php echo $i; ?>"></div>
                            <span class="red" id="edit_generic_video_comment_reply_error_<?php echo $i; ?>"></span>
                            <input type="hidden" name="filter_video_upload_reply_<?php echo $i; ?>" class="form-control" id="filter_video_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("Put your image url here or click upload") ?>"  />
                          </div>
                        </div>
                      </div>
                      <!-- comment hide and delete section -->
                      <br/>
                      <label>
                        <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
                      </label>
                      <div>    
                        <select class="form-group private_reply_postback" id="filter_message_<?php echo $i; ?>" name="filter_message_<?php echo $i; ?>">
                          <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                        </select>

                        <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                        <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                   
                      </div>


                    </div>
                <?php endfor; ?>
                
                <div class="clearfix">
                  <input type="hidden" name="content_counter" id="content_counter" />
                  <button type="button" class="btn btn-sm btn-outline-primary float-right" id="add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?></button>
                </div>

                <div class="form-group clearfix" id="nofilter_word_found_div" style="margin-top: 10px; border: 1px dashed #ccc; padding: 10px;">
                  <label>
                    <i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
                    <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <?php if($comment_tag_machine_addon) {?>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
                  </span>
                  <?php } ?>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                  </span>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                  </span> 
                  <div class="clearfix"></div>
                  <textarea class="form-control message" name="nofilter_word_found_text" id="nofilter_word_found_text"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>

                  <!-- comment hide and delete section -->
                  <br/>
                  <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                    <div class="row">                     
                      <div class="col-12 col-md-6">
                        <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                        </label>                  
                        <div class="form-group">      
                                      <div id="nofilter_image_upload"><?php echo $this->lang->line("upload") ?></div>      
                        </div>
                        <div id="nofilter_generic_image_preview_id"></div>
                        <span class="red" id="nofilter_image_upload_reply_error"></span>
                        <input type="text" name="nofilter_image_upload_reply" class="form-control" id="nofilter_image_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
                          <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                        </label>
                        <div class="form-group">      
                                      <div id="nofilter_video_upload"><?php echo $this->lang->line("upload") ?></div>      
                        </div>
                        <div id="nofilter_video_preview_id"></div>
                        <span class="red" id="nofilter_video_upload_reply_error"></span>
                        <input type="hidden" name="nofilter_video_upload_reply" class="form-control" id="nofilter_video_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>"  />
                      </div>
                    </div>
                  </div>
                  <br/><br/>
                  <!-- comment hide and delete section -->

                  <label>
                    <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply if no matching found") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
                  </label>
                  <div>   
                    <select class="form-group private_reply_postback" id="nofilter_word_found_text_private" name="nofilter_word_found_text_private">
                      <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                    </select>

                    <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                    <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                    
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 text-center" id="response_status"></div>
              </div>
        
        <!-- This hidden field is for detecting the clicked button type -->
        <input type="hidden" name="btn_type" value="" id="submit_btn_values">
            </form>
            <div class="clearfix"></div>
            <div class="modal-footer" style="padding-left: 45px; padding-right: 45px; ">
              <div class="row">
                <div class="col-6">
                  <button class="btn btn-lg btn-primary float-left" id="save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
                </div>  
                <div class="col-6">
                  <button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" style="padding: 10px 20px 10px 20px;"><?php echo $this->lang->line("Please give the following information for post auto reply") ?></h5>
            <button type="button" class="close" id='edit_modal_close'  aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          
        <form action="#" id="edit_auto_reply_info_form" method="post">
          <input type="hidden" name="table_id" id="table_id" value="">
          <input type="hidden" name="edit_auto_reply_post_id" id="edit_auto_reply_post_id" value="">
          <div class="modal-body" id="edit_auto_reply_message_modal_body">   

          <div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
            
            <div class="row" style="padding: 10px 20px 10px 20px;">                
              <div class="col-12 col-md-4">
                <label><i class="fas fa-file-alt"></i> <?php echo $this->lang->line("Please select a page for auto-reply") ?></label>
              </div>
              <div class="col-12 col-md-8">
                <select class="form-control select2" id="edit_page_ids" name="edit_page_ids[]">                     
                </select>
              </div>
            </div>

            <!-- comment hide and delete section -->
            <div class="row" style="padding: 10px 20px 10px 20px;<?php if(!$commnet_hide_delete_addon) echo "display: none;"; ?> ">
              <div class="col-12">
                <div class="row">             
                  <div class="col-12 col-md-6" >
                    <label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
                  </div>
                  <div class="row">
                    <div class="col-12 col-md-6">
                      <label class="custom-switch">
                        <input type="radio" name="edit_delete_offensive_comment" value="hide" id="edit_delete_offensive_comment_hide" class="custom-switch-input" checked>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
                      </label>
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="custom-switch">
                        <input type="radio" name="edit_delete_offensive_comment" value="delete"  id="edit_delete_offensive_comment_delete" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <br/><br/>
              <div class="col-12 col-md-6" id="edit_delete_offensive_comment_keyword_div">
                <div class="form-group" style="border: 1px dashed #ccc; padding: 10px;">
                  <label><i class="fa fa-tags"></i> <small><?php echo $this->lang->line("write down the offensive keywords in comma separated") ?></small>
                  </label>
                  <textarea class="form-control message" name="edit_delete_offensive_comment_keyword" id="edit_delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions") ?>" style="height:59px !important;"></textarea>
                </div>
              </div>

              <div class="col-12 col-md-6">
              	<div class="form-group clearfix" style="border: 1px dashed #ccc; padding: 10px;">
	                <label><small>
	                  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply after deleting offensive comment") ?></small>
	                </label>
	                <div>        
	                  <select class="form-group private_reply_postback" id="edit_private_message_offensive_words" name="edit_private_message_offensive_words">
	                    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
	                  </select>

	                  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
	                  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>        
	                </div>
	            </div>
              </div>

        </div> 
              <!-- end of comment hide and delete section -->
        <div class="row" style="padding: 10px 20px 10px 20px;">
          <!-- added by mostofa on 26-04-2017 -->
          <div class="col-12">
            <div class="row">             
              <div class="col-12 col-md-6" ><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("do you want to send reply message to a user multiple times?") ?></label></div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="edit_multiple_reply" value="yes" id="edit_multiple_reply" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <div class="col-12">
            <div class="row">             
              <div class="col-12 col-md-6" >
                <label><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line("do you want to enable comment reply?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="edit_comment_reply_enabled" value="yes" id="edit_comment_reply_enabled" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>          
            </div>          
          </div>
          <div class="smallspace clearfix"></div>
          <div class="col-12">
            <div class="row">             
              <div class="col-12 col-md-6" >
                <label><i class="fa fa-comment"></i> <?php echo $this->lang->line("do you want to like on comment by page?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="edit_auto_like_comment" value="yes" id="edit_auto_like_comment" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <!-- comment hide and delete section -->
          <div class="col-12" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
            <div class="row">             
              <div class="col-12 col-md-6" >
                <label><i class="fa fa-eye-slash"></i> <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="edit_hide_comment_after_comment_reply" value="yes" id="edit_hide_comment_after_comment_reply" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <!-- comment hide and delete section -->

          <div class="smallspace clearfix"></div>

          <div class="col-12">
            <div class="custom-control custom-radio">
              <input type="radio" name="edit_message_type" value="generic" id="edit_generic" class="custom-control-input radio_button">
              <label class="custom-control-label" for="edit_generic"><?php echo $this->lang->line("generic message for all") ?></label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" name="edit_message_type" value="filter" id="edit_filter" class="custom-control-input radio_button">
              <label class="custom-control-label" for="edit_filter"><?php echo $this->lang->line("send message by filtering word/sentence") ?></label>
            </div>

          </div>
          <div class="col-12" style="margin-top: 15px;">
            <div class="form-group">
              <label>
                <i class="fa fa-monument"></i> <?php echo $this->lang->line("auto reply campaign name") ?> <span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your auto reply campaign name here") ?>"><i class='fa fa-info-circle'></i> </a>
              </label>
              <input class="form-control" type="text" name="edit_auto_campaign_name" id="edit_auto_campaign_name" placeholder="<?php echo $this->lang->line("write your auto reply campaign name here") ?>">
            </div>
          </div>

          <div class="col-12" id="edit_generic_message_div" style="display: none;">
            <div class="form-group clearfix" style="border: 1px dashed #ccc; padding: 10px;">
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("message for comment reply") ?> <span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
              <div class="clearfix"></div>
              <textarea class="form-control message" name="edit_generic_message" id="edit_generic_message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>
              

              <!-- comment hide and delete scetion -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                      <div id="edit_generic_comment_image"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="edit_generic_image_preview_id"></div>
                    <span class="red" id="generic_image_for_comment_reply_error"></span>
                    <input type="text" name="edit_generic_image_for_comment_reply" class="form-control" id="edit_generic_image_for_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <img src="" alt="image" id="edit_generic_image_for_comment_reply_display" height="240" width="100%" style="display:none;" />
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                      <div id="edit_generic_video_upload"><?php echo $this->lang->line("upload") ?></div>      
                    </div>
                    <div id="edit_generic_video_preview_id"></div>
                    <span class="red" id="edit_generic_video_comment_reply_error"></span>
                    <input type="hidden" name="edit_generic_video_comment_reply" class="form-control" id="edit_generic_video_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>" />
                    
                    <div class="overlay_wrapper">
                      <span></span>
                      <video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
                        <source src="" id="edit_generic_video_comment_reply_display" type="video/mp4">
                      <?php echo $this->lang->line("your browser does not support the video tag.") ?>
                      </video>
                    </div>
                  </div>
                </div>
              </div>
              <br/><br/>
              <!-- comment hide and delete scetion -->


              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>    
                <select class="form-group private_reply_postback" id="edit_generic_message_private" name="edit_generic_message_private">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

              	<a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
              	<a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                   
              </div>
              
            </div>
          </div>

          <div class="col-12" id="edit_filter_message_div" style="display: none;">
            <div class="row">
              <div class="col-12 col-md-6">
              <label class="custom-switch">
                <input type="radio" name="edit_trigger_matching_type" value="exact" id="edit_trigger_keyword_exact" class="custom-switch-input" checked>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"><?php echo $this->lang->line('Reply if the filter word exactly matches.'); ?></span>
              </label>
              </div>
              <div class="col-12 col-md-6">
              <label class="custom-switch">
                <input type="radio" name="edit_trigger_matching_type" value="string" id="edit_trigger_keyword_string" class="custom-switch-input">
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"><?php echo $this->lang->line('Reply if any matches occurs with filter word.'); ?>
              </label>
              </div>
            </div><br/>
          <?php for($i=1;$i<=20;$i++) :?>
            <div class="form-group clearfix" id="edit_filter_div_<?php echo $i; ?>" style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 50px;">
              <label>
                <i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, want to know, when") ?>"><i class='fa fa-info-circle'></i> </a>
              </label>
              <input class="form-control filter_word" type="text" name="edit_filter_word_<?php echo $i; ?>" id="edit_filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">
              

              <br/>
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("msg for comment reply") ?><span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
              <div class="clearfix"></div>
              <textarea class="form-control message" name="edit_comment_reply_msg_<?php echo $i; ?>" id="edit_comment_reply_msg_<?php echo $i; ?>"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>
              

              <!-- comment hide and delete section -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                                  <div id="edit_filter_image_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="edit_generic_image_preview_id_<?php echo $i; ?>"></div>
                    <span class="red" id="edit_generic_image_for_comment_reply_error_<?php echo $i; ?>"></span>
                    <input type="text" name="edit_filter_image_upload_reply_<?php echo $i; ?>" class="form-control" id="edit_filter_image_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <img src="" alt="image" id="edit_filter_image_upload_reply_display_<?php echo $i; ?>" height="240" width="100%" style="display:none"/>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                      <div id="edit_filter_video_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="edit_generic_video_preview_id_<?php echo $i; ?>"></div>
                    <span class="red" id="edit_generic_video_comment_reply_error_<?php echo $i; ?>"></span>
                    <input type="hidden" name="edit_filter_video_upload_reply_<?php echo $i; ?>" class="form-control" id="edit_filter_video_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>"  />
                    
                    <div class="overlay_wrapper">
                      <span></span>
                      <video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
                        <source src="" id="edit_filter_video_upload_reply_display<?php echo $i; ?>" type="video/mp4">
                      <?php echo $this->lang->line("your browser does not support the video tag.") ?>
                      </video>
                    </div>
                  </div>
                </div>
              </div>
              <!-- comment hide and delete section -->

              <br/>

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>   
                <select class="form-group private_reply_postback" id="edit_filter_message_<?php echo $i; ?>" name="edit_filter_message_<?php echo $i; ?>">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

                <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                    
              </div>

            </div>
          <?php endfor; ?>
            

            <div class="clearfix">
              <input type="hidden" name="edit_content_counter" id="edit_content_counter" />
              <button type="button" class="btn btn-sm btn-outline-primary float-right" id="edit_add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?></button>
            </div>

            <div class="form-group clearfix" id="edit_nofilter_word_found_div" style="margin-top: 10px; border: 1px dashed #ccc; padding: 10px;">
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
              <div class="clearfix"></div>
              <textarea class="form-control message" name="edit_nofilter_word_found_text" id="edit_nofilter_word_found_text"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>
              
              
              <!-- comment hide and delete section -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">                 
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                                  <div id="edit_nofilter_image_upload"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="edit_nofilter_generic_image_preview_id"></div>
                    <span class="red" id="edit_nofilter_image_upload_reply_error"></span>
                    <input type="text" name="edit_nofilter_image_upload_reply" class="form-control" id="edit_nofilter_image_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    
                    <div class="overlay_wrapper">
                      <span></span>
                      <img src="" alt="image" id="edit_nofilter_image_upload_reply_display" height="240" width="100%" style="display:none;" />
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                      <div id="edit_nofilter_video_upload"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="edit_nofilter_video_preview_id"></div>
                    <span class="red" id="edit_nofilter_video_upload_reply_error"></span>
                    <input type="hidden" name="edit_nofilter_video_upload_reply" class="form-control" id="edit_nofilter_video_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>" />
                    
                    <div class="overlay_wrapper">
                      <span></span>
                      <video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
                        <source src="" id="edit_nofilter_video_upload_reply_display" type="video/mp4">
                      <?php echo $this->lang->line("your browser does not support the video tag.") ?>
                      </video>
                    </div>
                  </div>
                </div>
              </div>
              <br/><br/>
              <!-- comment hide and delete section -->

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply if no matching found") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>      
                <select class="form-group private_reply_postback" id="edit_nofilter_word_found_text_private" name="edit_nofilter_word_found_text_private">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

              	<a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
              	<a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>                 
              </div>
              
            </div>
          </div>
          </div>
          <div class="col-12 text-center" id="edit_response_status"></div>
        </div>
      </form>
      <div class="clearfix"></div>
      <div class="modal-footer" style="padding-left: 45px; padding-right: 45px; ">
        <div class="row">
          <div class="col-6">
            <button class="btn btn-lg btn-primary float-left" id="edit_save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
          </div>  
          <div class="col-6">
            <button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  $("document").ready(function(){

    $("#page_ids,.private_reply_postback,#edit_page_ids").select2({ width: "100%" }); 

    $(document).on('change','#page_ids,#edit_page_ids',function(e){
      var page_table_ids = $(this).val();
      $("#dynamic_page_id").val(page_table_ids);
      $.ajax({
        type:'POST' ,
        url: base_url+"comment_automation/get_private_reply_postbacks",
        data: {page_table_ids:page_table_ids},
        dataType : 'JSON',
        success:function(response){
          $(".private_reply_postback").html(response.options);
        }

      });
    });

    
    $("#filemanager_close").click(function(){
      $("#modal-live-video-library").removeClass('modal');
    });

    var base_url="<?php echo site_url(); ?>";
    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    <?php for($k=1;$k<=20;$k++) : ?>
      $("#edit_filter_video_upload_<?php echo $k; ?>").uploadFile({
            url:base_url+"comment_automation/upload_live_video",
            fileName:"myfile",
            // maxFileSize:500*1024*1024,
            showPreview:false,
            returnType: "json",
            dragDrop: true,
            showDelete: true,
            multiple:false,
            maxFileCount:1, 
            acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
            deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
              $.post(delete_url, {op: "delete",name: data},
                function (resp,textStatus, jqXHR) {  
                    $("#edit_filter_video_upload_reply_<?php echo $k; ?>").val('');              
                });

            },
            onSuccess:function(files,data,xhr,pd)
            {
              var file_path = base_url+"upload/video/"+data;
              $("#edit_filter_video_upload_reply_<?php echo $k; ?>").val(file_path);  
            }
          });


          $("#edit_filter_image_upload_<?php echo $k; ?>").uploadFile({
                url:base_url+"comment_automation/upload_image_only",
                fileName:"myfile",
                // maxFileSize:1*1024*1024,
                showPreview:false,
                returnType: "json",
                dragDrop: true,
                showDelete: true,
                multiple:false,
                maxFileCount:1, 
                acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
                deleteCallback: function (data, pd) {
                    var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
                    $.post(delete_url, {op: "delete",name: data},
                        function (resp,textStatus, jqXHR) {
                          $("#edit_filter_image_upload_reply_<?php echo $k; ?>").val('');                      
                        });
                   
                 },
                 onSuccess:function(files,data,xhr,pd)
                   {
                       var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                       $("#edit_filter_image_upload_reply_<?php echo $k; ?>").val(data_modified); 
                   }
            });
    <?php endfor; ?>

    <?php for($k=1;$k<=20;$k++) : ?>
      $("#filter_video_upload_<?php echo $k; ?>").uploadFile({
            url:base_url+"comment_automation/upload_live_video",
            fileName:"myfile",
            // maxFileSize:500*1024*1024,
            showPreview:false,
            returnType: "json",
            dragDrop: true,
            showDelete: true,
            multiple:false,
            maxFileCount:1, 
            acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
            deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
              $.post(delete_url, {op: "delete",name: data},
                function (resp,textStatus, jqXHR) {  
                    $("#filter_video_upload_reply_<?php echo $k; ?>").val('');              
                });

            },
            onSuccess:function(files,data,xhr,pd)
            {
              var file_path = base_url+"upload/video/"+data;
              $("#filter_video_upload_reply_<?php echo $k; ?>").val(file_path); 
            }
          });


          $("#filter_image_upload_<?php echo $k; ?>").uploadFile({
                url:base_url+"comment_automation/upload_image_only",
                fileName:"myfile",
                // maxFileSize:1*1024*1024,
                showPreview:false,
                returnType: "json",
                dragDrop: true,
                showDelete: true,
                multiple:false,
                maxFileCount:1, 
                acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
                deleteCallback: function (data, pd) {
                    var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
                    $.post(delete_url, {op: "delete",name: data},
                        function (resp,textStatus, jqXHR) {
                          $("#filter_image_upload_reply_<?php echo $k; ?>").val('');                      
                        });
                   
                 },
                 onSuccess:function(files,data,xhr,pd)
                   {
                       var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                       $("#filter_image_upload_reply_<?php echo $k; ?>").val(data_modified);  
                   }
            });
    <?php endfor; ?>

    $("#generic_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      // maxFileSize:500*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#generic_video_comment_reply").val('');              
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#generic_video_comment_reply").val(file_path); 
      }
    });


    $("#generic_comment_image").uploadFile({
          url:base_url+"comment_automation/upload_image_only",
          fileName:"myfile",
          // maxFileSize:1*1024*1024,
          showPreview:false,
          returnType: "json",
          dragDrop: true,
          showDelete: true,
          multiple:false,
          maxFileCount:1, 
          acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
          deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) {
                    $("#generic_image_for_comment_reply").val('');                      
                  });
             
           },
           onSuccess:function(files,data,xhr,pd)
             {
                 var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                 $("#generic_image_for_comment_reply").val(data_modified);    
             }
      });


    $("#nofilter_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      // maxFileSize:500*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#nofilter_video_upload_reply").val('');              
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#nofilter_video_upload_reply").val(file_path); 
      }
    });


    $("#nofilter_image_upload").uploadFile({
          url:base_url+"comment_automation/upload_image_only",
          fileName:"myfile",
          // maxFileSize:1*1024*1024,
          showPreview:false,
          returnType: "json",
          dragDrop: true,
          showDelete: true,
          multiple:false,
          maxFileCount:1, 
          acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
          deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) {
                    $("#nofilter_image_upload_reply").val('');                      
                  });
             
           },
           onSuccess:function(files,data,xhr,pd)
             {
                 var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                 $("#nofilter_image_upload_reply").val(data_modified);    
             }
      });

    $("#edit_generic_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      // maxFileSize:500*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#edit_generic_video_comment_reply").val('');              
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#edit_generic_video_comment_reply").val(file_path);  
      }
    });


    $("#edit_generic_comment_image").uploadFile({
          url:base_url+"comment_automation/upload_image_only",
          fileName:"myfile",
          // maxFileSize:1*1024*1024,
          showPreview:false,
          returnType: "json",
          dragDrop: true,
          showDelete: true,
          multiple:false,
          maxFileCount:1, 
          acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
          deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) {
                    $("#edit_generic_image_for_comment_reply").val('');                      
                  });
             
           },
           onSuccess:function(files,data,xhr,pd)
             {
                 var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                 $("#edit_generic_image_for_comment_reply").val(data_modified);   
             }
      });


    $("#edit_nofilter_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      // maxFileSize:500*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#edit_nofilter_video_upload_reply").val('');              
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#edit_nofilter_video_upload_reply").val(file_path);  
      }
    });


    $("#edit_nofilter_image_upload").uploadFile({
          url:base_url+"comment_automation/upload_image_only",
          fileName:"myfile",
          // maxFileSize:1*1024*1024,
          showPreview:false,
          returnType: "json",
          dragDrop: true,
          showDelete: true,
          multiple:false,
          maxFileCount:1, 
          acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
          deleteCallback: function (data, pd) {
              var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) {
                    $("#edit_nofilter_image_upload_reply").val('');                      
                  });
             
           },
           onSuccess:function(files,data,xhr,pd)
             {
                 var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                 $("#edit_nofilter_image_upload_reply").val(data_modified);   
             }
      });

    });
</script>

<style type="text/css">
  .smallspace{padding: 10px 0;}
  .lead_first_name,.lead_last_name,.lead_tag_name{background: #fff !important;}
  .ajax-file-upload-statusbar{width: 100% !important;}
  .emojionearea, .emojionearea.form-control
  {
    height: 170px !important;
  }


  .emojionearea.small-height
  {
    height: 140px !important;
  }

  .overlay_wrapper {
    position: relative;
    max-height: 240px;
    width: 100%;
    overflow: hidden;
  }

  .overlay_wrapper span.remove_media {
    position: absolute;
    right: 5px;
    top: 5px;
    background-color: black;
    color: white;
    padding: 4px 15px;
    font-size: 12px;
    border-radius: 15px;
    transition: 0.4s;
    -o-transition: 0.4s;
    -webkit-transition: 0.4s;
    -moz-transition: 0.4s;
    -ms-transition: 0.4s;
    opacity: 0;
    cursor: pointer;
    visibility: hidden;
  }

  .overlay_wrapper:hover span.remove_media{
    display: block;
    opacity: 1;
    visibility: visible;
    z-index: 1000;
  }
  .add_template,.ref_template{font-size: 10px;}
  .button-outline
  {
    background: #fff;
    border: .5px dashed #ccc;
  }
  .button-outline:hover
  {
    border: 1px dashed var(--blue) !important;
    cursor: pointer;
  }
  /*#edit_add_more_button, #add_more_button
  {
    z-index: 9999 !important;
  }*/
</style>


<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body"> 
        <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>


<input type="hidden" name="dynamic_page_id" id="dynamic_page_id">
<script type="text/javascript">
	$("document").ready(function(){		
		var base_url = "<?php echo base_url(); ?>";

		$('.modal').on("hidden.bs.modal", function (e) { 
		    if ($('.modal:visible').length) { 
		        $('body').addClass('modal-open');
		    }
		});

		$(document).on('click','.add_template',function(e){
		  e.preventDefault();
		  var current_id=$(this).prev().prev().attr('id');
		  var current_val=$(this).prev().prev().val();
		  var page_id = get_page_id();
		  if(page_id=="")
		  {
		    swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		    return false;
		  }
		  $("#add_template_modal").attr("current_id",current_id);
		  $("#add_template_modal").attr("current_val",current_val);
		  $("#add_template_modal").modal();
		});

		$(document).on('click','.ref_template',function(e){
		  e.preventDefault();
		  var current_val=$(this).prev().prev().prev().val();
		  var current_id=$(this).prev().prev().prev().attr('id');
		  var page_id = get_page_id();
		   if(page_id=="")
		   {
		     swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		     return false;
		   }
		   $.ajax({
		     type:'POST',
		     url: base_url+"comment_automation/get_private_reply_postbacks",
		     data: {page_table_ids:page_id},
		     dataType: 'JSON',
		     success:function(response){
		       $("#"+current_id).html(response.options).val(current_val);
		     }
		   });
		});

		$('#add_template_modal').on('hidden.bs.modal', function (e) { 
		  var current_id=$("#add_template_modal").attr("current_id");
		  var current_val=$("#add_template_modal").attr("current_val");
		  var page_id = get_page_id();
		   if(page_id=="")
		   {
		     swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		     return false;
		   }
		   $.ajax({
		     type:'POST' ,
		     url: base_url+"comment_automation/get_private_reply_postbacks",
		     data: {page_table_ids:page_id,is_from_add_button:'1'},
		     dataType: 'JSON',
		     success:function(response){
		       $("#"+current_id).html(response.options);
		     }
		   });
		});

		// getting postback list and making iframe
		$('#add_template_modal').on('shown.bs.modal',function(){ 
			var page_id = get_page_id();
			var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
		  	$(this).find('iframe').attr('src',iframe_link); 
		});   
		// getting postback list and making iframe

	});

	function get_page_id()
	{
		var page_id = $("#dynamic_page_id").val();
		return page_id;
	}
</script>