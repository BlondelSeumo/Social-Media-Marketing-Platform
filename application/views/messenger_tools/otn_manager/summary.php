<div class="form-group">
	<?php 
        $pop="";
        if($this->uri->segment(2)=="create_quick_broadcast_campaign" || $this->uri->segment(2)=="edit_quick_broadcast_campaign") 
        $pop =  $this->lang->line("This campaign is totally handled by Facebook for each send. So actual send may differ for various reason. As for example, if any subscriber did not interact with your bot for many days like 2 months or page sent private reply of comment but he never replied back, in those cases, those subscribers will not be eligible for quick broadcasting. While targeting by label it may happen that some subscribers have label in Facebook but have not been assigned label in our system, they are eligible for quick broadcasting.");
    ?> 
    <label class="full_width">
    	<?php if($pop!='') {?>
    		<a class="float-right" data-toggle="popover" href="#" data-placement="top" data-trigger="focus" title="<?php echo $this->lang->line('Campaign Reach');?>" data-content="<?php echo $pop;?>"><i class="fas fa-info-circle"></i></a>
    	<?php } ?>
    </label>
  	<ul class="list-group">
  	  <?php if($this->uri->segment(2)=="create_quick_broadcast_campaign" || $this->uri->segment(2)=="edit_quick_broadcast_campaign") 
  	  { ?>
	      <li class="list-group-item d-flex justify-content-between align-items-center">
	        <?php echo $this->lang->line("Estimated Reach"); ?> 
	        <span class="badge badge-status" id="page_subscriber">0</span>
	      </li>
	      <li class="list-group-item d-flex justify-content-between align-items-center active">
	        <?php echo $this->lang->line("Targeted Reach"); ?>
	        <span class="badge badge-status" id="targetted_subscriber">0</span>
	      </li>
	  <?php 
	  } 
	  else 
	  { ?>
	  	<li class="list-group-item d-flex justify-content-between align-items-center">
        	<?php echo $this->lang->line("Page Subscribers"); ?> 
        	<span class="badge badge-status" id="page_subscriber">0</span>
      	</li>
	    <li class="list-group-item d-flex justify-content-between align-items-center active">
	        <?php echo $this->lang->line("Targeted Reach"); ?>
	        <span class="badge badge-status" id="targetted_subscriber">0</span>
	    </li>
	  <?php 
	}?>
	</ul>
</div>

<div class="d-none d-lg-inline col-lg-3 prview_div" style="">

  <div id="text_preview_div" style="">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/text.png')) echo site_url()."assets/images/preview/text.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/text.png"; ?>" class="img-rounded" alt="Text Preview">
  </div>

  <div id="image_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/image.png')) echo site_url()."assets/images/preview/image.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/image.png"; ?>" class="img-rounded" alt="Image Preview">
  </div>

  <div id="audio_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/mp3.png')) echo site_url()."assets/images/preview/mp3.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/mp3.png"; ?>" class="img-rounded" alt="Audio Preview">
  </div>

  <div id="video_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/video.png')) echo site_url()."assets/images/preview/video.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/video.png"; ?>" class="img-rounded" alt="Video Preview">
  </div>

  <div id="file_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/file.png')) echo site_url()."assets/images/preview/file.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/file.png"; ?>" class="img-rounded" alt="File Preview">
  </div>

  <div id="quick_reply_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/quick_reply.png')) echo site_url()."assets/images/preview/quick_reply.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/quick_reply.png"; ?>" class="img-rounded" alt="Quick Reply Preview">
  </div>

  <div id="text_with_buttons_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/button.png')) echo site_url()."assets/images/preview/button.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/button.png"; ?>" class="img-rounded" alt="Text With Buttons Preview">
  </div>

  <div id="generic_template_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/generic.png')) echo site_url()."assets/images/preview/generic.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/generic.png"; ?>" class="img-rounded" alt="Generic Template Preview">
  </div>

  <div id="carousel_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/carousel.png')) echo site_url()."assets/images/preview/carousel.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/carousel.png"; ?>" class="img-rounded" alt="Carousel Template Preview">
  </div>

  <div id="list_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/list.png')) echo site_url()."assets/images/preview/list.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/list.png"; ?>" class="img-rounded" alt="List Template Preview">
  </div>

  <div id="media_preview_div" style="display: none;">
    <img src="<?php if(file_exists(FCPATH.'assets/images/preview/media.png')) echo site_url()."assets/images/preview/media.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/media.png"; ?>" class="img-rounded" alt="Media Template Preview">
  </div>

</div>


<style type="text/css">
	.prview_div div{text-align: right}
	.prview_div img{border: 1px solid #ccc;}
	.select2{width: 100% !important;}
	.popover{min-width: 330px !important;}
</style>

<?php 
if($this->uri->segment(2)=="edit_quick_broadcast_campaign" || $this->uri->segment(2)=="edit_subscriber_broadcast_campaign") { ?>
<script type="text/javascript">
	var xlabels = "<?php echo $xdata['label_ids'];?>"; 
	var xexcluded_label_ids = "<?php echo $xdata['excluded_label_ids'];?>";
</script>
<?php }?>

<?php 
$is_quick_broadcast = "0";
if($this->uri->segment(2)=="create_quick_broadcast_campaign" || $this->uri->segment(2)=="edit_quick_broadcast_campaign") $is_quick_broadcast = "1";
?>

<script type="text/javascript">
	var base_url="<?php echo base_url();?>";

	$("document").ready(function(){	
	
		var today = new Date();
		$('.datepicker_x').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today
		});
	
		
		$(document).on('click','.lead_first_name',function(){
			
			var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
			
			var txtToAdd = " {{first_name}} ";
			var new_text = textAreaTxt + txtToAdd;
			$(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
			$(this).parent().next().next().next().children('.emojionearea-editor').click();
		});

		$(document).on('click','.lead_last_name',function(){

			var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
			
			var txtToAdd = " {{last_name}} ";
			var new_text = textAreaTxt + txtToAdd;
			$(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
			$(this).parent().next().next().next().next().children('.emojionearea-editor').click();
			
		});

		$(document).on('change','#page,#label_ids,#excluded_label_ids,#user_gender,#user_time_zone,#user_locale,#broadcast_type,#otn_postback_ids',function(){     
		  var page_id=$("#page").val();
		  var user_gender=$("#user_gender").val();
		  var user_time_zone=$("#user_time_zone").val();
		  var user_locale=$("#user_locale").val();
		  var label_ids=$("#label_ids").val();
		  var excluded_label_ids=$("#excluded_label_ids").val();
		  var otn_postback_ids=$("#otn_postback_ids").val();
		  var broadcast_type=$("#broadcast_type").val();
		  var is_bot_subscriber = '1';
		  var is_quick_broadcast = '<?php echo $is_quick_broadcast;?>';
		  var hidden_id=$("#hidden_id").val();

		  if(typeof(broadcast_type)==='undefined') broadcast_type = "";
		  if(typeof(label_ids)==='undefined') label_ids = "";
		  if(typeof(excluded_label_ids)==='undefined') excluded_label_ids = "";
		  if(typeof(otn_postback_ids)==='undefined') otn_postback_ids = "";
		  if(typeof(hidden_id)==='undefined') hidden_id = "0";

		  var load_label='0';
		  var load_otn_postback='0';
		  if($(this).attr('id')=='page') 
		  {
		  	load_otn_postback='1';	
		  	load_label='1';		  
		  }

		  if(load_label=='1')
		  {
		  	$("#dropdown_con").removeClass('hidden');
		  	$("#first_dropdown").html('<?php echo $this->lang->line("Loading labels..."); ?>');
	      	$("#second_dropdown").html('<?php echo $this->lang->line("Loading labels..."); ?>');
	      }

	      if(load_otn_postback=='1')
	      {
	      	$("#otn_postback_div").removeClass('hidden');
	      	$("#otn_postback_section").html('<?php echo $this->lang->line("Loading OTN templates..."); ?>');
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
			  url: base_url+"home/get_otn_broadcast_summary",
			  data: {page_id:page_id,label_ids:label_ids,excluded_label_ids:excluded_label_ids,user_gender:user_gender,user_time_zone:user_time_zone,user_locale:user_locale,load_label:load_label,is_bot_subscriber:is_bot_subscriber,broadcast_type:broadcast_type,load_otn_postback:load_otn_postback,otn_postback_ids:otn_postback_ids,hidden_id:hidden_id},
			  dataType : 'JSON',
			  success:function(response){

			  	if(load_label=='1')
			    {
			    	$("#dropdown_con").removeClass('hidden');
			    	$("#first_dropdown").html(response.first_dropdown);
			    	$("#second_dropdown").html(response.second_dropdown);
			    }

			    if(load_otn_postback=='1')
			    {
			    	$("#otn_postback_div").removeClass('hidden');
			    	$("#otn_postback_section").html(response.otn_postback_str);
			    }

			    $("#submit_post").removeClass("btn-progress");

			    var estimated_reach = response.pageinfo.estimated_reach;
			    if(estimated_reach=="") estimated_reach='0';
			    if(is_quick_broadcast=='1') $("#page_subscriber").html(estimated_reach);
			    else $("#page_subscriber").html(response.pageinfo.total_subscriber_count);

          		$("#targetted_subscriber").html(response.pageinfo.subscriber_count);

          		if(typeof(response.pageinfo.estimated_reach)!=='undefined')
		        {
		          $("#fb_page_id").val(response.pageinfo.page_id);
		          $(".push_postback").html(response.push_postback);
		        }
          		
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

<?php include("application/views/messenger_tools/otn_manager/message_tag_modal.php") ?>


