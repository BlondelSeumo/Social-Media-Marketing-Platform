<div class="modal fade" id="pageresponse_auto_likeshare_modal" data-backdrop="static" data-keyboard="false">>
  <div class="modal-dialog" style="min-width: 60%;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title text-center"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("Enable Auto Like/Share");?></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body" id="pageresponse_auto_likeshare_modal_body">
        <br/>
        <div class="text-center" id="autolikeshare_response_status"></div>
        <div class="ajax_content"></div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer text-center">
        <button class="btn btn-lg btn-primary" id="autolikeshare_save_button"><i class="fa fa-save"></i> <?php echo $this->lang->line('Save');?></button> 
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="pageresponse_auto_likeshare_edit_modal" data-backdrop="static" data-keyboard="false">>
  <div class="modal-dialog" style="min-width: 60%;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title text-center"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit Auto Like/Share");?></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body" id="pageresponse_auto_likeshare_edit_modal_body">
        <br/>
        <div class="text-center" id="autolikeshare_edit_response_status"></div>
        <div class="ajax_content"></div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer text-center">
        <button class="btn btn-lg btn-primary" id="autolikeshare_edit_button"><i class="fa fa-save"></i> <?php echo $this->lang->line('Save');?></button> 
      </div>

    </div>
  </div>
</div>



<script>

  var base_url = "<?php echo base_url(); ?>";
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";

  $(document).ready(function(){

    $('#pageresponse_auto_likeshare_modal').on('hidden.bs.modal', function () {
      $(".page_list_item.active").click();
    });

    $('#pageresponse_auto_likeshare_edit_modal').on('hidden.bs.modal', function () {
      $(".page_list_item.active").click();
    });

    $(document).on('click','.enable_auto_share',function(e){
      e.preventDefault();
      var page_response_user_info_id = $(this).attr('page_response_user_info_id');
      var page_table_id = $(this).attr('page_table_id');
      var page_id = $(this).attr('page_id');
      $("#pageresponse_auto_likeshare_modal .modal-footer").hide();
      var loading = '<div class="text-center modal_waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
      $("#pageresponse_auto_likeshare_modal_body .ajax_content").html(loading);
      $("#pageresponse_auto_likeshare_modal").modal();
      $.ajax({
          type:'POST' ,
          url: base_url+"comment_automation/add_auto_like_share",
          data: {page_response_user_info_id:page_response_user_info_id,page_table_id:page_table_id,page_id:page_id},
          // dataType : 'JSON',
          // async: false,
          success:function(response){
             
             $("#pageresponse_auto_likeshare_modal_body .ajax_content").html(response);
              $("#pageresponse_auto_likeshare_modal .modal-footer").show();
          }

        });
    });

    $(document).on('click','#autolikeshare_save_button',function(){      
      var post_id = $("#auto_reply_post_id").val();pageresponse_auto_likeshare_modal_body

      var auto_share_post = $("input[name=auto_share_post]:checked").val();
      var auto_like_post = $("input[name=auto_like_post]:checked").val();
      var auto_share_this_post_by_pages = $("#auto_share_this_post_by_pages").val();
      var auto_like_this_post_by_pages = $("#auto_like_this_post_by_pages").val();

      if(typeof(auto_share_post)=='undefined' && typeof(auto_like_post)=='undefined')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please enable auto share or auto like."); ?>', 'warning');
        return;
      }

      

      if(auto_share_post=='1' && auto_share_this_post_by_pages.length==0)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select page for auto sharing."); ?>', 'warning');
        return;
      }

      if(auto_like_post=='1' && auto_like_this_post_by_pages.length==0)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select page for auto liking."); ?>', 'warning');
        return;
      }

      $(this).addClass('btn-progress');

      var queryString = new FormData($("#autolikeshare_reply_info_form")[0]);
      $.ajax({
        type:'POST' ,
        url: base_url+"comment_automation/ajax_auto_share_like_submit",
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
                $(".page_list_item.active").click();
                $("#pageresponse_auto_likeshare_modal").modal('hide');
              }); 
            }
            else
            {
              swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
            }   
        }

      });

    });

    $(document).on('click','.edit_auto_share',function(e){
      e.preventDefault();
      var table_id = $(this).attr('table_id');
      var page_response_user_info_id = $(this).attr('page_response_user_info_id');
      var loading = '<div class="text-center modal_waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
      $("#pageresponse_auto_likeshare_edit_modal_body .ajax_content").html(loading);
      $("#pageresponse_auto_likeshare_edit_modal").modal();
      $.ajax({
          type:'POST' ,
          url: base_url+"comment_automation/edit_auto_like_share",
          data: {table_id:table_id,page_response_user_info_id:page_response_user_info_id},
          // dataType : 'JSON',
          // async: false,
          success:function(response){
             
             $("#pageresponse_auto_likeshare_edit_modal_body .ajax_content").html(response);
          }

        });
    });


    $(document).on('click','#autolikeshare_edit_button',function(){    

      var auto_share_post = $("input[name=edit_auto_share_post]:checked").val();
      var auto_like_post = $("input[name=edit_auto_like_post]:checked").val();
      var auto_share_this_post_by_pages = $("#edit_auto_share_this_post_by_pages").val();
      var auto_like_this_post_by_pages = $("#edit_auto_like_this_post_by_pages").val();


      if(auto_share_post=='1' && auto_share_this_post_by_pages.length==0)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select page for auto sharing."); ?>', 'warning');
        return;
      }

      if(auto_like_post=='1' && auto_like_this_post_by_pages.length==0)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select page for auto liking."); ?>', 'warning');
        return;
      }

      // var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
      // $("#autolikeshare_edit_response_status").html(loading);

      $(this).addClass('btn-progress');

      var queryString = new FormData($("#autolikeshare_edit_auto_reply_info_form")[0]);
      $.ajax({
        type:'POST' ,
        url: base_url+"comment_automation/edit_auto_like_share_submit",
        data: queryString,
        // dataType : 'JSON',
        // async: false,
        contentType:false,
        cache: false,
        processData:false,
        context: this,
        success:function(response){
            $(this).removeClass('btn-progress');
            if(response=="success")
            {
              swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("campaign information has been saved successfully.");?>', 'success').then((value) => {
                $(".page_list_item.active").click();
                $("#pageresponse_auto_likeshare_edit_modal").modal('hide');
              });
  
            }
            else
            {
              swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('something went wrong, please try again.');?>", 'error');
            }
        }

      });
    });
    
  });
</script>