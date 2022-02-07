<form id="test_message_form">	

  	<?php if($this->uri->segment(2)!='edit_conversation_message_content') 
  	{?>
	  	<div class="form-group">
	        <label class="full_width">&nbsp;</label>
		  	<ul class="list-group">
		      <li class="list-group-item d-flex justify-content-between align-items-center">
		        <?php echo $this->lang->line("Page Subscribers"); ?> 
		        <span class="badge badge-status" id="page_subscriber">0</span>
		      </li>
		      <li class="list-group-item d-flex justify-content-between align-items-center active">
		        <?php echo $this->lang->line("Targeted Reach"); ?>
		        <span class="badge badge-status" id="targetted_subscriber">0</span>
		      </li>
			</ul>
		</div>
	<?php 
	} ?>

    <div id="test_send_modal_content">
		<div id="test_message_response" class="table-responsive"></div>
		<div class="form-group">
            <label class="full_width" style="margin-bottom: 0">
           		<?php echo $this->lang->line("Test Subscribers"); ?>
           		<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line('Subscriber Name');?>" data-content="<?php echo $this->lang->line('Choose up to 3 subscriber to test how it will look. Start typing, it is auto-complete.') ?>"><i class='fa fa-info-circle'></i> </a>              	
           		<a class="btn btn-sm btn-light float-right" id="submit_test_post" name="submit_test_post"><i class="fa fa-envelope"></i> <?php echo $this->lang->line("Send Test Message") ?> </a>
            </label>
            <select style="width:100px;"  name="test_send[]" id="test_send" multiple="multiple" class="tokenize-sample form-control test_send_autocomplete">                                     
            </select>
        </div> 
  	</div>

</form>


<style type="text/css">.select2{width:100% !important;}</style>



<script type="text/javascript">
	var base_url="<?php echo base_url();?>";
	$("document").ready(function(){	

		$('.test_send_autocomplete').tokenize({
		    datas: base_url+"messenger_bot_broadcast/lead_autocomplete",
		    maxElements : 3
		});

		var today = new Date();
		$('.datepicker_x').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today
		});


		var emoji_message_div =	$("#message").emojioneArea({
		    autocomplete: false,
			pickerPosition: "bottom"
		});


	    $(document).on('click','#submit_test_post',function(){ 
	    	var thread_ids = $('.test_send_autocomplete').tokenize().toArray();
	    	if(thread_ids.length==0) 
	    	{
	        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please choose a subscriber to send test message.");?>', 'warning');
	    	 	return;
	    	}
	    	var message = $("#message").val();

	    	if(message=="")
	  		{
	        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Message is empty. Please type a message."); ?>', 'warning');
	  			return;
	  		} 
		    $("#submit_test_post").addClass("btn-progress");
		      $.ajax({
		       type:'POST' ,
		       url: base_url+"messenger_bot_broadcast/send_test_message",
		       data: {message:message,thread_ids:thread_ids},
		       success:function(response)
		       {  	    	 			
					$("#submit_test_post").removeClass("btn-progress");
					$("#test_message_response").html(response);		       	  
		       }
	    	});
	    });


    	$(document).on('click','#lead_first_name',function(){  
		  var textAreaTxt = $(".emojionearea-editor").html();
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
			
		  var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
		  $(".emojionearea-editor").html(textAreaTxt + txtToAdd );
			$(".emojionearea-editor").click(); 			
		});


		$(document).on('click','#lead_last_name',function(){  
		  var textAreaTxt = $(".emojionearea-editor").html();
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
			textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
		  var txtToAdd = " #LEAD_USER_LAST_NAME# ";
		  $(".emojionearea-editor").html(textAreaTxt + txtToAdd );
			$(".emojionearea-editor").click(); 			
		});


		$(document).on('change','#page,#label_ids,#excluded_label_ids,#user_gender,#user_time_zone,#user_locale',function(){     
		  var page_id=$("#page").val();
		  var user_gender=$("#user_gender").val();
		  var user_time_zone=$("#user_time_zone").val();
		  var user_locale=$("#user_locale").val();
		  var label_ids=$("#label_ids").val();
		  var excluded_label_ids=$("#excluded_label_ids").val();
		  var is_bot_subscriber = '0';

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

		  $("#submit_post").addClass('btn-progress');
		  $.ajax({
			  type:'POST' ,
			  url: base_url+"home/get_broadcast_summary",
			  data: {page_id:page_id,label_ids:label_ids,excluded_label_ids:excluded_label_ids,user_gender:user_gender,user_time_zone:user_time_zone,user_locale:user_locale,load_label:load_label,is_bot_subscriber:is_bot_subscriber},
			  dataType : 'JSON',
			  success:function(response){

			  	if(load_label=='1')
			    {
			    	$("#dropdown_con").removeClass('hidden');
			    	$("#first_dropdown").html(response.first_dropdown);
			    	$("#second_dropdown").html(response.second_dropdown);
			    }

			    $("#submit_post").removeClass("btn-progress");

			    $("#page_subscriber").html(response.pageinfo.current_subscribed_lead_count);
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


	});

</script>