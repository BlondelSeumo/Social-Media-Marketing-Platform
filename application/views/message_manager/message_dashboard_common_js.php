<script>

  var base_url="<?php echo base_url(); ?>";
  var loading = '<br><div class="col-12 text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 60px; margin-top: 100px; margin-bottom: 100px;"></i></div>';
  var refresh_interval = 10000;
  var auto_refresh_con = '';

  function openTab(url) {
     var win = window.open(url, '_blank');
     win.focus();
  }

  $("#postback_reply_button").tooltip();

  function get_subscriber_action_content2(id,subscribe_id,page_id)
  {
    $("#subscriber_action").html(loading);
    $.ajax({
      type:'POST' ,
      url: "<?php echo site_url(); ?>subscriber_manager/subscriber_actions_modal",
      data:{id:id,page_id:page_id,subscribe_id:subscribe_id,call_from_conversation:'1'},
      success:function(response)
      {
        $("#subscriber_action").html(response);
      }
    }); 
  }

  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
  });
  
  $("document").ready(function(){

    setTimeout(function(){
        auto_refresh_con = setInterval(function(){$('.open_conversation.bg-light').click();},refresh_interval);
        $(document).on('change','#refresh_interval',function(e){
          refresh_interval = $("#refresh_interval").val();
          clearInterval(auto_refresh_con);
          auto_refresh_con = setInterval(function(){$('.open_conversation.bg-light').click();},refresh_interval);
        });
        // setInterval(function(){$('#refresh_data').click();},180015);
        ajax_call(".open_conversation:first");
     }, 500);
    
    $(document).on('change', '#page_id', function(event) {
      event.preventDefault();
      var pageid = $(this).val();
      if(pageid =='') {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select a Facebook page / Instagram account"); ?>', 'warning');
        return false;
      }
      $(".search_list").val('');
      $('#refresh_data').attr('page_table_id',pageid);
      $.ajax({
        url: base_url+'home/switch_to_page',
        type: 'POST',
        data: {page_id: pageid},
        success:function(){
        }
      })
      ajax_call(".open_conversation:first");

      $.ajax({
        url: base_url+'message_manager/get_dropdown_postback/'+pageid+'/'+social_media+'/0',
        type: 'POST',
        success:function(response){
          $("#postbackModalBody").html(response);
        }
      })
      

    });


    $(document).on('click','.open_conversation',function(){
      var already_loaded = false;
      if($(this).hasClass('bg-light')) already_loaded = true;
      $('.media').removeClass('bg-light');
      $(this).addClass('bg-light');
      var from_user_id = $(this).attr('from_user_id'); 
      var from_user = $(this).attr('from_user');
      var subscribe_id = $(this).attr('from_user_id');
      var thread_id = $(this).attr('thread_id');
      var page_table_id = $(this).attr('page_table_id');
      var last_message_id  =  $(".card-body .chat-item:last .chat-details").attr('message_id');
      if(!already_loaded) last_message_id = '';
      $("#chat_with").html("<?php echo $this->lang->line('Loading...')?>");
      $("#final_reply_button").attr('thread_id',thread_id);
      $("#final_reply_button").attr('page_table_id',page_table_id);
      $("#final_reply_button").attr('from_user_id',from_user_id);
      // $("#conversation_modal_body").html(loading);   
      var page_table_id_social_media = $(this).attr('page_table_id')+"-"+social_media;
      if(!already_loaded) get_subscriber_action_content2(0,from_user_id,page_table_id_social_media); 

      $.ajax({
          context:this,
          url:base_url+'message_manager/'+get_post_conversation_url,
          type:'POST',
          data:{thread_id:thread_id,page_table_id:page_table_id,from_user_id:from_user_id,last_message_id:last_message_id},
          success:function(response){
            $("#chat_with").html(from_user);
            if(already_loaded) $("#conversation_modal_body").append(response);
            else $("#conversation_modal_body").html(response);

            $("#conversation_modal_body").getNiceScroll().remove();
            $("#conversation_modal_body").css('overflow','hidden');

            if(already_loaded){
              var count = (response.match(/chat-item/g) || []).length;
              if(count>0) $('.open_conversation[from_user_id='+from_user_id+'] .badge-pill').html(count).removeClass('d-none');        
              else $('.open_conversation[from_user_id='+from_user_id+'] .badge-pill').addClass('d-none');        
            }
            setTimeout(function(){ 
               $("#conversation_modal_body").css('overflow-y','auto');
               $("#conversation_modal_body").niceScroll();
               var element = document.getElementById("conversation_modal_body");
               element.scrollTop = element.scrollHeight; 
              },1000);       
          }
          
        });
    });

    $(document).on('click','#final_reply_button',function(e){
      e.preventDefault();
      var thread_id = $(this).attr('thread_id');
      var page_table_id = $(this).attr('page_table_id');
      var from_user_id = $(this).attr('from_user_id');
      var reply_message = $("#reply_message").val().trim();
      var message_tag = $("#message_tag").val().trim();
      
      if(reply_message == '')
      {
        alertify.alert("<?php $this->lang->line('Alert'); ?>","<?php echo $this->lang->line('You did not provide any reply message'); ?>",function(){});
        return false;
      }
      $("#reply_message").val('');
      $("#final_reply_button").addClass('disabled');
      $.ajax({
          url:base_url+'message_manager/'+reply_to_conversation_url,
          type:'POST',
          data:{page_table_id:page_table_id,reply_message:reply_message,from_user_id:from_user_id,message_tag:message_tag},
          success:function(response){
            $("#conversation_modal_body").append(response);
            $("#final_reply_button").removeClass('disabled');
            setTimeout(function(){ var element = document.getElementById("conversation_modal_body");
            element.scrollTop = element.scrollHeight; }, 100);     
          }
          
        });
    });

    $("#put_content").html(loading);

    function ajax_call(selected)
    {   
      var page_table_id = $("#refresh_data").attr('page_table_id');
      $("#chat_with").html("<?php echo $this->lang->line('Loading...')?>");
      $.ajax({
          url:base_url+'message_manager/'+get_pages_conversation_url,
          type:'POST',
          data:{page_table_id:page_table_id},
          success:function(response){
            $("#put_content").html(response); 
            $("#put_content").getNiceScroll().remove();            
            $("#put_content").css('overflow','hidden');
            
            setTimeout(function(){ 
              $(selected).click();
              $("#put_content").niceScroll();
              var put_content_position = $(selected).position();
              $('#put_content').getNiceScroll().doScrollPos(0,put_content_position.top);
            }, 200);
          }
        });       
    }
    
    $(document).on('click','#refresh_data',function(e){
      e.preventDefault();
      var from_user_id = $('.open_conversation.bg-light').attr('from_user_id');
      $("#put_content").html(loading);   
      var selected = ".open_conversation[from_user_id="+from_user_id+"]";
      ajax_call(selected);
    });   

    $(document).on('click','.postback-item',function(e){
      e.preventDefault();
      var page_table_id = $("#refresh_data").attr('page_table_id');
      var subscriber_id = $('.open_conversation.bg-light').attr('from_user_id');
      var postback_id = $(this).attr('data-id');

      $(this).addClass('disabled');

      $.ajax({
          context:this,
          url:base_url+'message_manager/send_postback_reply',
          type:'POST',
          data:{page_table_id,subscriber_id,postback_id,social_media},
          dataType:'JSON',
          success:function(response){
            $(this).removeClass('disabled');
            $('#postbackModal').modal('hide');
            if(response.status=='1'){
              iziToast.success({title: '<?php echo $this->lang->line("Template Sent")?>',message:response.message,position: 'bottomRight'});
              $('.open_conversation.bg-light').click();
            }
            else{
              iziToast.error({title: '<?php echo $this->lang->line("Error")?>',message:response.message,position: 'bottomRight'});            
            }
          }
        });
    });   

  });

  function search_in_subscriber_ul(obj,ul_id){  // obj = 'this' of jquery, ul_id = id of the ul 
    var filter=$(obj).val().toUpperCase().trim();
    var count_li = 0;
    $('#'+ul_id+' li').each(function(){
      var content=$(this).text().trim();

      if (content.toUpperCase().indexOf(filter) > -1) {
        $(this).css('display','');
        count_li++;
      }
      else $(this).css('display','none');
    });

    if(filter.length>=3 && count_li==0){
      var page_table_id = $("#refresh_data").attr('page_table_id');
      $.ajax({
          url:base_url+'message_manager/search_subscriber_database',
          type:'POST',
          data:{page_table_id,social_media,filter},
          success:function(response){
            $("#put_content").append(response);
          }
        });
    }

  }

</script>

<?php include(FCPATH.'application/views/messenger_tools/subscriber_actions_common_js.php');?>


<!-- Modal -->
<div class="modal fade" id="postbackModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title full_width" id="postbackModalLabel">
          <i class="fas fa-paper-plane"></i> <?php echo $this->lang->line('Send Template')?>
          <input type="text" class="form-control d-inline" autofocus style="width: 120px;" autofocus="" onkeyup="search_in_class(this,'postback-item')" placeholder="<?php echo $this->lang->line('Search') ?>...">
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="height:500px;overflow:auto" id="postbackModalBody">
       <?php echo $postback_list; ?>
      </div>
    </div>
  </div>
</div>


<style>
@media (min-width: 992px) {
    .no_padding_col{padding: 0 !important;}
    <?php echo !$is_rtl ? '.main_row{margin-left: 0;}' : '.main_row{margin-right: 0;}'; ?>
    <?php echo !$is_rtl ? '.no_padding_col_left{padding-left: 0 !important;}.no_padding_col_right{padding-right: 0 !important;}' : '.no_padding_col_left{padding-right: 0 !important;}.no_padding_col_right{padding-left: 0 !important;}'; ?>
  }
}
.card.chat-box{border-radius: 0;}
.search_list{width: 130px;}
#refresh_interval{width: 115px;height: 30px;padding: 0 10px !important;}
.chat-box .chat-form .btn{position: relative;transform: none; border-radius: 0;height: 42px;width: 50px;top: 0;right: 0;}
.chat-box .chat-form .form-control{height: 42px;}
.select2-container--default .select2-selection--single{border-radius: 0 !important;}
.no_radius{border-radius: 0 !important;}

.chat-box .chat-content2 {
/*background-color: #F7FAFF !important;*/
height: 320px;
/*overflow: hidden;*/
width: 100%;
padding-top: 25px !important; }
.chat-box .chat-content2 .chat-item {
display: inline-block;
width: 100%;
margin-bottom: 25px; }
.chat-box .chat-content2 .chat-item.chat-right img {
float: right; }

<?php if(!$is_rtl) { ?>
  .chat-box .chat-content2 .chat-item.chat-right .chat-details {
    margin-left: 0;
    margin-right: 70px;
    text-align: right; 
  }
  .chat-box .chat-content2 .chat-item.chat-right .chat-details .chat-text {
    text-align: left;
    background-color: var(--blue);
    color: #fff; 
  }
  <?php 
} 
else { ?>
  .chat-box .chat-content2 .chat-item.chat-right .chat-details {
    margin-left: 0;
    margin-right: 70px;
    text-align: left; 
  }
  .chat-box .chat-content2 .chat-item.chat-right .chat-details .chat-text {
    text-align: right;
    background-color: var(--blue);
    color: #fff; 
  }
<?php } ?>

.chat-box .chat-content2 .chat-item > img {
float: left;
width: 50px;
border-radius: 50%; }
.chat-box .chat-content2 .chat-item .chat-details {
margin-left: 70px; }
.chat-box .chat-content2 .chat-item .chat-details .chat-text {
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.03);
background-color: #fff;
padding: 10px 15px;
border-radius: 3px;
width: auto;
display: inline-block;
font-size: 12px; }
.chat-box .chat-content2 .chat-item .chat-details .chat-text img {
max-width: 100%;
margin-bottom: 10px; }
#put_content{height: 448px;overflow:auto;}
.chat-box .chat-content2 .chat-item.chat-typing .chat-details .chat-text {
background-image: url("data:image/svg+xml;base64,PCEtLSBCeSBTYW0gSGVyYmVydCAoQHNoZXJiKSwgZm9yIGV2ZXJ5b25lLiBNb3JlIEAgaHR0cDovL2dvby5nbC83QUp6YkwgLS0+DQo8c3ZnIHdpZHRoPSIxMjAiIGhlaWdodD0iMzAiIHZpZXdCb3g9IjAgMCAxMjAgMzAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbD0iIzk5OSI+DQogICAgPGNpcmNsZSBjeD0iMTUiIGN5PSIxNSIgcj0iMTUiPg0KICAgICAgICA8YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJyIiBmcm9tPSIxNSIgdG89IjE1Ig0KICAgICAgICAgICAgICAgICBiZWdpbj0iMHMiIGR1cj0iMC44cyINCiAgICAgICAgICAgICAgICAgdmFsdWVzPSIxNTs5OzE1IiBjYWxjTW9kZT0ibGluZWFyIg0KICAgICAgICAgICAgICAgICByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgLz4NCiAgICAgICAgPGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iZmlsbC1vcGFjaXR5IiBmcm9tPSIxIiB0bz0iMSINCiAgICAgICAgICAgICAgICAgYmVnaW49IjBzIiBkdXI9IjAuOHMiDQogICAgICAgICAgICAgICAgIHZhbHVlcz0iMTsuNTsxIiBjYWxjTW9kZT0ibGluZWFyIg0KICAgICAgICAgICAgICAgICByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgLz4NCiAgICA8L2NpcmNsZT4NCiAgICA8Y2lyY2xlIGN4PSI2MCIgY3k9IjE1IiByPSI5IiBmaWxsLW9wYWNpdHk9IjAuMyI+DQogICAgICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9InIiIGZyb209IjkiIHRvPSI5Ig0KICAgICAgICAgICAgICAgICBiZWdpbj0iMHMiIGR1cj0iMC44cyINCiAgICAgICAgICAgICAgICAgdmFsdWVzPSI5OzE1OzkiIGNhbGNNb2RlPSJsaW5lYXIiDQogICAgICAgICAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPg0KICAgICAgICA8YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJmaWxsLW9wYWNpdHkiIGZyb209IjAuNSIgdG89IjAuNSINCiAgICAgICAgICAgICAgICAgYmVnaW49IjBzIiBkdXI9IjAuOHMiDQogICAgICAgICAgICAgICAgIHZhbHVlcz0iLjU7MTsuNSIgY2FsY01vZGU9ImxpbmVhciINCiAgICAgICAgICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+DQogICAgPC9jaXJjbGU+DQogICAgPGNpcmNsZSBjeD0iMTA1IiBjeT0iMTUiIHI9IjE1Ij4NCiAgICAgICAgPGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iciIgZnJvbT0iMTUiIHRvPSIxNSINCiAgICAgICAgICAgICAgICAgYmVnaW49IjBzIiBkdXI9IjAuOHMiDQogICAgICAgICAgICAgICAgIHZhbHVlcz0iMTU7OTsxNSIgY2FsY01vZGU9ImxpbmVhciINCiAgICAgICAgICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+DQogICAgICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9ImZpbGwtb3BhY2l0eSIgZnJvbT0iMSIgdG89IjEiDQogICAgICAgICAgICAgICAgIGJlZ2luPSIwcyIgZHVyPSIwLjhzIg0KICAgICAgICAgICAgICAgICB2YWx1ZXM9IjE7LjU7MSIgY2FsY01vZGU9ImxpbmVhciINCiAgICAgICAgICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+DQogICAgPC9jaXJjbGU+DQo8L3N2Zz4NCg==");
height: 40px;
width: 60px;
background-position: center;
background-size: 60%;
background-repeat: no-repeat; }
.chat-box .chat-content2 .chat-item .chat-details .chat-time {
margin-top: 5px;
font-size: 9px;
font-weight: 400;
opacity: .6; }
#middle_column .card,  .collef .card{box-shadow: none !important;}
.chat-box .chat-form{padding: 2px;}
.card .card-header, .card .card-body, .card .card-footer {
  padding: 10px 15px; 
}
.list-unstyled-border li{border-bottom: 0 !important;}
#postback_reply_button{border-radius: 50px;width: 50px;}
</style>