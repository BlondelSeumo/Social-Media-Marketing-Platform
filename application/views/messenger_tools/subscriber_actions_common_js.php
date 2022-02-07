<script type="text/javascript">
	//  loads subscriber action modal content
	function get_subscriber_action_content(id,subscribe_id,page_id)
	{
		$(".multi_layout").html(' <div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');

		$.ajax({
		  type:'POST' ,
		  url: "<?php echo site_url(); ?>subscriber_manager/subscriber_actions_modal",
		  data:{id:id,page_id:page_id,subscribe_id:subscribe_id},
		  success:function(response)
		  {
		    $(".multi_layout").html(response);
		  }
		}); 
	}
	//  refresh label and broadcast block 
	function subscriber_actions_refresh(id)
	{
	  $("#save_changes").addClass("btn-progress");
	  $.ajax({
	    context: this,
	    type:'POST',
	    dataType:'JSON',
	    url:"<?php echo site_url();?>subscriber_manager/subscriber_actions_refresh",
	    data:{id:id},
	    success:function(response){
	       $("#save_changes").removeClass("btn-progress");
	       $("#subscriber_labels_container").html(response.label_dropdown);
	       $("#broadcast_block").html(response.broadcast_block);
	    }
	  });
	}


	$("document").ready(function(){
		$(document).on('click','.client_thread_subscribe_unsubscribe',function(e){
        e.preventDefault();
        $(this).addClass('btn-progress');
        $("#save_changes").addClass("btn-progress");
        var client_subscribe_unsubscribe_status = $(this).attr('id');
        var social_media = $(this).attr('social_media');

        var exp = [];
        var exp = client_subscribe_unsubscribe_status.split("-");
        var id = exp[0];

        $.ajax({
          context: this,
          type:'POST',
          dataType:'JSON',
          url:"<?php echo site_url();?>subscriber_manager/client_subscribe_unsubscribe_status_change",
          data:{client_subscribe_unsubscribe_status:client_subscribe_unsubscribe_status,subscriber_details_page:'1',social_media:social_media},
          success:function(response){
             $(this).removeClass('btn-progress');
             $("#save_changes").removeClass('btn-progress');
             $(this).parent().html(response.button2);
             subscriber_actions_refresh(id);
             if(response.status=='1') iziToast.success({title: '',message: response.message,position: 'bottomRight'});
             else iziToast.error({title: '',message: response.message,position: 'bottomRight'});
          }
        });

      });


      $(document).on('click','.client_thread_start_stop',function(e){
        e.preventDefault();
        $(this).addClass('btn-progress');
        $("#save_changes").addClass("btn-progress");
        var call_from_conversation = $(this).attr('call-from-conversation');
        if(call_from_conversation!='1')  call_from_conversation = '0';
        var client_thread_start_stop = $(this).attr('button_id');
        $.ajax({
          context: this,
          type:'POST',
          dataType:'JSON',
          url:"<?php echo site_url();?>subscriber_manager/start_stop_bot_reply",
          data:{client_thread_start_stop:client_thread_start_stop,call_from_conversation:call_from_conversation},
          success:function(response){
             $(this).removeClass('btn-progress');
             $("#save_changes").removeClass('btn-progress');
             $(this).parent().html(response.button);
             iziToast.success({title: '',message: response.message,position: 'bottomRight'});       
          }
        });

      });


      // create an new label and put inside label list
      $(document).on('click','#create_label',function(e){
        e.preventDefault();
        var id=$(this).attr('data-id');
        var page_id=$(this).attr('data-page-id');
        var social_media=$(this).attr('data-social-media');

        swal("<?php echo $this->lang->line('Label Name'); ?>", {
          content: "input",
          button: {text: "<?php echo $this->lang->line('New Label'); ?>"},
        })
        .then((value) => {
          var label_name = `${value}`;
          if(label_name!="" && label_name!='null')
          {
            $("#save_changes").addClass("btn-progress");
            $.ajax({
              context: this,
              type:'POST',
              dataType:'JSON',
              url:"<?php echo site_url();?>subscriber_manager/create_label_and_assign",
              data:{id:id,page_id:page_id,label_name:label_name,social_media:social_media},
              success:function(response){
                 $("#save_changes").removeClass("btn-progress");
                 var newOption = new Option(response.text, response.id, true, true);
                 $('#subscriber_labels').append(newOption).trigger('change');
              }
            });
          }
        });

      });


      // saves label list and sequence campaign
      $(document).on('click','#save_changes',function(e){
        e.preventDefault();
        var id=$(this).attr('data-id');
        var page_id=$(this).attr('data-page-id');
        var subscribe_id=$(this).attr('data-subscribe-id');
        var group_id=$("#subscriber_labels").val();
        var campaign_id=$("#assign_campaign_id").val();

        var social_media = $(this).attr('data-social-media');
        var page_id_media = page_id+"-"+social_media;

        $("#save_changes").addClass("btn-progress");
        $.ajax({
          context: this,
          type:'POST',
          dataType:'JSON',
          url:"<?php echo site_url();?>/subscriber_manager/save_subscriber_changes",
          data:{id:id,page_id:page_id,group_id:group_id,campaign_id:campaign_id,social_media:social_media},
          success:function(response){
             $("#save_changes").removeClass("btn-progress");
             get_subscriber_action_content(id,subscribe_id,page_id_media);
             iziToast.success({title: '',message: '<?php echo $this->lang->line("Subscriber information has been saved successfully."); ?>',position: 'bottomRight'});
          }
        });

      });      

    $(document).on('click','.update_user_details',function(e){
      e.preventDefault();
      $(this).addClass('btn-progress');
      $("#save_changes").addClass("btn-progress");
      var post_value = $(this).attr('button_id');
      var social_media = $(this).attr('social_media');

      var exp=[];
      exp=post_value.split("-");
      var id=exp[0];
      var subscribe_id=exp[1];
      var page_id=exp[2];

      var page_id_media = page_id+"-"+social_media;

      $.ajax({
        context: this,
        type:'POST',
        dataType:'JSON',
        url:"<?php echo site_url();?>subscriber_manager/sync_subscriber_data",
        data:{post_value:post_value,social_media:social_media},
        success:function(response){
           $(this).removeClass('btn-progress');
           $("#save_changes").removeClass('btn-progress');
           if(response.status=='1')   iziToast.success({title: '',message: response.message,position: 'bottomRight'});       
           else  iziToast.error({title: '',message: response.message,position: 'bottomRight'});
           get_subscriber_action_content(id,subscribe_id,page_id_media);    
        }
      });

    });

    $(document).on('click','.reset_user_input_flow',function(e){
      e.preventDefault();
      $(this).addClass('btn-progress');
      $("#save_changes").addClass("btn-progress");
      var post_value = $(this).attr('button_id');
      var social_media = $(this).attr('social_media');

      var exp=[];
      exp=post_value.split("-");
      var id=exp[0];
      var subscribe_id=exp[1];
      var page_id=exp[2];

      var page_id_media = page_id+"-"+social_media;

      $.ajax({
        context: this,
        type:'POST',
        dataType:'JSON',
        url:"<?php echo site_url();?>subscriber_manager/reset_user_input_flow",
        data:{post_value:post_value,social_media:social_media},
        success:function(response){
           $(this).removeClass('btn-progress');
           $("#save_changes").removeClass('btn-progress');
           if(response.status=='1')   iziToast.success({title: '',message: response.message,position: 'bottomRight'});       
           else  iziToast.error({title: '',message: response.message,position: 'bottomRight'});
           get_subscriber_action_content(id,subscribe_id,page_id_media);    
        }
      });

    });

    $(document).on('click','.delete_user_details',function(e){
      e.preventDefault();

      swal({
         title: '<?php echo $this->lang->line("Delete Subscriber"); ?>',
         text: '<?php echo $this->lang->line("Do you really want to delete this subscriber?"); ?>',
         icon: 'warning',
         buttons: true,
         dangerMode: true,
       })
       .then((willDelete) => {
         if (willDelete) 
         {
             $(this).addClass('btn-progress');
             $("#save_changes").addClass("btn-progress");
             var post_value = $(this).attr('button_id');
             var social_media = $(this).attr('social_media');
             $.ajax({
               context: this,
               type:'POST',
               dataType:'JSON',
               url:"<?php echo site_url();?>subscriber_manager/delete_subsriber",
               data:{post_value:post_value,social_media:social_media},
               success:function(response){
                  $(this).removeClass('btn-progress');
                  $("#save_changes").removeClass('btn-progress');
                  $("#subscriber_actions_modal").modal('hide');
                  iziToast.success({title: '',message: '<?php echo $this->lang->line("Subscriber has been deleted successfully."); ?>',position: 'bottomRight'});    
               }
             });
         } 
       });       

    });
	});
</script>