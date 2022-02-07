<section class="section">
  <div class="section-header">
    <h1><i class="fa fa-edit"></i> <?php echo $page_title;?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot_broadcast'); ?>"><?php echo $this->lang->line("Messenger Broadcast");?></a></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot_broadcast/conversation_broadcast_campaign'); ?>"><?php echo $this->lang->line("Conversation Broadcast");?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title;?></div>
    </div>
    </div>
</section>

<style type="text/css">
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  .multi_layout{border:.5px solid #dee2e6;}
  .multi_layout .collef,.multi_layout .colmid{padding-left: 0px; padding-right: 0px;border-right: .5px solid #dee2e6;}
  .multi_layout .colmid .card-icon{border:.5px solid #dee2e6;}
  .multi_layout .colmid .card-icon i{font-size:30px !important;}
  .multi_layout .main_card{box-shadow: none !important;}
  .multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;}
  .multi_layout h6.page_name{font-size: 14px;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .card .card-header h4 a{font-weight: 700 !important;}
  .multi_layout .card-primary{margin-top: 35px;margin-bottom: 15px;}
  .multi_layout .product-details .product-name{font-size: 12px;}
  .multi_layout .margin-top-50 {margin-top: 70px;}
  .multi_layout .waiting {height: 100%;width:100%;display: table;}
  .multi_layout .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}

</style>


<div class="row multi_layout">

  <div class="col-12 col-md-7 col-lg-7 collef">
	  <form action="#" enctype="multipart/form-data" id="inbox_campaign_form" method="post">
	    <div class="card main_card">
	      <div class="card-header">        
	         <h4><i class="fas fa-envelope"></i> <?php echo $this->lang->line("Message Content"); ?></h4>       
	      </div>
	      <div class="card-body">

          <div class="text-center waiting">
            <i class="fas fa-spinner fa-spin blue text-center"></i>
          </div>

      		<input type="hidden" value="<?php echo $xdata[0]["id"];?>" class="form-control"  name="campaign_id" id="campaign_id">
      		
      		<div class="form-group">
      			<label>
      				<?php echo $this->lang->line("Message") ?>
              <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message"); ?>" data-content="<?php echo $this->lang->line("Message may contain texts, urls, emotions and any promotional content.You can personalize message by including subscriber name. Message supports spintax, example"); ?> : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
      			</label>
      			<span class='float-right'>
      				<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>' class='btn-outline btn-sm' id='lead_last_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("Last Name") ?></a>
      			</span>
      			<span class='float-right'> 
      				<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>' class='btn-outline btn-sm' id='lead_last_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("First Name") ?></a>
      			</span>
      			<div class="clearfix"></div>
      			<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"><?php echo $xdata[0]["campaign_message"];?></textarea>
      			
      		</div>  
	      </div>
	      <div class="card-footer">
	      	<button class="btn btn-lg btn-primary" id="submit_post" name="submit_post" type="button"><i class="fa fa-edit"></i> <?php echo $this->lang->line("edit") ?>  <?php echo $this->lang->line("message") ?> </button>
	      </div>
	    </div>  
    </form>       
  </div>

  <div class="col-12 col-md-5 col-lg-5 colmid" id="middle_column">
      <div class="card main_card">
        <div class="card-header">
          <h4><i class="fas fa-envelope"></i> <?php echo $this->lang->line("Test Message"); ?></h4>
        </div>
        <div class="card-body">
          <?php include(FCPATH."application/views/messenger_tools/bulk_message/send_test_message.php") ?>            
        </div>
     </div>
  </div>

  
  
</div>


<script>

 
	$("document").ready(function(){
	
		setTimeout(function() {
			$(".waiting").hide();
			$(".emojionearea-editor").blur();	
		}, 2000);


    $(document).on('click','#submit_post',function(){ 
                	
    		if($("#message").val()=="")
    		{
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Message is empty. Please type a message."); ?>', 'warning');
    			return;
    		}    

      	var report_link = base_url+"messenger_bot_broadcast/conversation_broadcast_campaign";
      	
      	var success_message = '<?php echo  $this->lang->line("Campaign have been updated successfully."); ?>'+" <a href='"+report_link+"'><?php echo $this->lang->line('See report here.'); ?></a>";

        $(this).addClass("btn-progress");
     	        	
	      var queryString = new FormData($("#inbox_campaign_form")[0]);
	      $.ajax({
           context: this,
		       type:'POST' ,
		       url: base_url+"messenger_bot_broadcast/edit_conversation_message_content_action",
		       data: queryString,
		       cache: false,
		       contentType: false,
		       processData: false,
		       success:function(response)
		       {  
              $(this).removeClass("btn-progress");

              var span = document.createElement("span");
              span.innerHTML = success_message;
              swal({ title:'<?php echo $this->lang->line("Campaign Updated"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});
		       }
	      	});

    });

  });

</script>



<?php //$this->load->view("messenger_tools/bulk_message/style");?>