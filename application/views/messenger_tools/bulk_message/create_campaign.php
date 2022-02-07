<section class="section">
  <div class="section-header">
    <h1><i class="fa fa-plus-circle"></i> <?php echo $page_title;?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot_broadcast'); ?>"><?php echo $this->lang->line("Broadcasting");?></a></div>
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

  <div class="col-12 col-md-7 col-lg-8 collef">
	  <form action="#" enctype="multipart/form-data" id="inbox_campaign_form" method="post">
	    <div class="card main_card">
	      <div class="card-header">        
	         <h4><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Campaign Details"); ?></h4>       
	      </div>
	      <div class="card-body">

	      	<div class="row">
      			<div class="col-12 col-md-6">
      				<div class="form-group">
			      		<label>
			      			<?php echo $this->lang->line("Campaign Name") ?> 
			      		</label>
			      		<input type="text" class="form-control"  name="campaign_name" id="campaign_name">
			      	</div>
			     </div>
      			<div class="col-12 col-md-6">
  					<div class="form-group">      				  
  				      <label>
  				      	<?php echo $this->lang->line("Select Page") ?>
         	      </label>
  				      <select class="form-control select2" id="page" name="page"> 
  				        <option value=""><?php echo $this->lang->line("Select Page");?></option> 
  				        <?php                          
  				          foreach($page_info as $key=>$val)
  				          { 
  				            $id=$val['id'];
  				            $page_name=$val['page_name'];
  				            echo "<option value='{$id}' data-count='".$val['current_subscribed_lead_count']."'>{$page_name}</option>";               
  				          }
  				         ?>           
  				      </select>
  					</div>
      			</div>
			    </div>    


          <div class="card card-primary">
            <div class="card-header">
              <h4>
                <?php echo $this->lang->line("Targeting Options");?>
                <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Targeting Options"); ?>" data-content="<?php echo $this->lang->line("You can send to specific labels, also can exclude specific labels. Gender, timezone and locale data are only available for bot subscribers meaning targeting by gender/timezone/locale  will only work for subscribers that have been migrated as bot subscribers or come through messenger bot in our system."); ?>"><i class='fa fa-info-circle'></i> </a>                
              </h4>
            </div>
            <div class="card-body">

              <div class="row hidden" id="dropdown_con">
                <div class="col-12 col-md-6" >
                  <div class="form-group">
                    <label style="width:100%">
                      <?php echo $this->lang->line("Target Labels") ?>
                      <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Choose Labels"); ?>" data-content="<?php echo $this->lang->line("If you do not want to send to all page subscriber then you can target by labels."); ?>"><i class='fa fa-info-circle'></i> </a>
                    </label>
                    <span id="first_dropdown"><?php echo $this->lang->line("Loading labels..."); ?></span>                                
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label style="width:100%">
                      <?php echo $this->lang->line("Exclude Labels") ?>
                      <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Exclude Labels"); ?>" data-content="<?php echo $this->lang->line("If you do not want to send to a specific label, you can mention it here. Unsubscribe label will be excluded automatically."); ?>"><i class='fa fa-info-circle'></i> </a>
                    </label>
                    <span id="second_dropdown"><?php echo $this->lang->line("Loading labels..."); ?></span>                 
                  </div>
                </div>
              </div>


              <div class="row">

                  <div class="form-group col-12 col-md-3">
                    <label>
                      <?php echo $this->lang->line("Gender"); ?>
                      
                      </label>
                    <?php
                    $gender_list = array(""=>$this->lang->line("Select"),"male"=>"Male","female"=>"Female");
                    echo form_dropdown('user_gender',$gender_list,'',' class="form-control select2" id="user_gender"'); 
                    ?>
                  </div>


                  <div class="form-group col-12 col-md-5">
                    <label><?php echo $this->lang->line("Time Zone") ?></label>
                    <?php
                    $time_zone_numeric[''] = $this->lang->line("Select");
                    echo form_dropdown('user_time_zone',$time_zone_numeric,'',' class="form-control select2" id="user_time_zone"'); 
                    ?>
                  </div>

                  <div class="form-group col-12 col-md-4">
                    <label><?php echo $this->lang->line("Locale") ?></label>
                    <?php
                    $locale_list[''] = $this->lang->line("Select");
                    echo form_dropdown('user_locale',$locale_list,'',' class="form-control select2" id="user_locale"'); 
                    ?>
                  </div>
              </div>

            </div>
          </div>
          <br><br>

         

      		<div class="form-group">
      			<label>
      				<?php echo $this->lang->line("Message") ?>
              <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message"); ?>" data-content="<?php echo $this->lang->line("Message may contain texts, urls, emotions and any promotional content.You can personalize message by including subscriber name. Message supports spintax, example"); ?> : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
      			</label>
      			<span class='float-right'>
      				<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>' class='btn-outline btn-sm' id='lead_last_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("Last Name") ?></a>
      			</span>
      			<span class='float-right'> 
      				<a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>' class='btn-outline btn-sm' id='lead_first_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("First Name") ?></a>
      			</span>
      			<div class="clearfix"></div>
      			<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
      		</div>  


      		<div class="row">
      			<div class="form-group col-12 col-md-5">
      				<label>
      					<?php echo $this->lang->line("Delay Time (Sec)") ?>
      					 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Delay Time") ?>" data-content="<?php echo $this->lang->line("delay time is the delay between two successive message send. It is very important because without a delay time Facebook may treat bulk sending as spam. Keep it '0' to get random delay.") ?>"><i class='fa fa-info-circle'></i> </a>
      				</label>
      				<br/>
      				<input name="delay_time" value="0" min="0" class="form-control"  id="delay_time" type="number">
      			</div>

      			<div class="form-group col-12 col-md-7">
      				<label>
      					<?php echo $this->lang->line("Sending Time") ?>
      					 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Sending Time") ?>" data-content="<?php echo $this->lang->line("If you schedule a campaign, system will automatically process this campaign at mentioned time and time zone. Schduled campaign may take upto 1 hour longer than your schedule time depending on server load.") ?>"><i class='fa fa-info-circle'></i> </a>
      				</label><br>

      				<label class="custom-switch mt-2">
      				  <input type="checkbox" name="schedule_type" value="later" class="custom-switch-input">
      				  <span class="custom-switch-indicator"></span>
      				  <span class="custom-switch-description"><?php echo $this->lang->line('Send Later');?></span>
      				  <span class="red"><?php echo form_error('schedule_type'); ?></span>
      				</label>
      			</div>
      		</div>

      		<div class="row">
      			<div class="form-group schedule_block_item col-12 col-md-5">
      				<label><?php echo $this->lang->line("Schedule Time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date, time and time zone when you want to start this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
      				<input placeholder="<?php echo $this->lang->line("Choose Time");?>"  name="schedule_time" id="schedule_time" class="form-control datepicker_x" type="text"/>
      			</div>

      			<div class="form-group schedule_block_item col-12 col-md-7">
      				<label><?php echo $this->lang->line("Schedule Time Zone") ?></label>
      				<?php
      				$time_zone[''] = $this->lang->line("Select");
      				echo form_dropdown('time_zone',$time_zone,$this->config->item('time_zone'),' class="form-control select2" id="time_zone"'); 
      				?>
      			</div>
      		</div>     		

	      </div>

	      <div class="card-footer">
	      	<button class="btn btn-lg btn-primary" id="submit_post" name="submit_post" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaign") ?> </button>
	      </div>
	    </div>  
    </form>       
  </div>

  <div class="col-12 col-md-5 col-lg-4 colmid" id="middle_column">
	  	<div class="card main_card">
	      <div class="card-header">
	        <h4><i class="fas fa-envelope"></i> <?php echo $this->lang->line("Summary & Test Message"); ?></h4>
	      </div>
	      <div class="card-body">
  		    <?php include(FCPATH."application/views/messenger_tools/bulk_message/send_test_message.php") ?>  	         
	      </div>
	   </div>
  </div>
</div>




<script>
 
	$("document").ready(function(){

		$(".schedule_block_item").hide();

    $(document).on('change','input[name=schedule_type]',function(){    
    	if($("input[name=schedule_type]:checked").val()=="later")
    		$(".schedule_block_item").show();
    	else 
    	{
    		$("#schedule_time").val("");
    		$("#time_zone").val("");
    		$(".schedule_block_item").hide();
    	}
    }); 

    $(document).on('click','#submit_post',function(){ 
 
      if($("#page").val()=="")
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select a page"); ?>', 'warning');
        return;
      }
             	
  		var message = $("#message").val();

      if(message=="")
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Message is empty. Please type a message."); ?>', 'warning');
        return;
      }    
  
    	var schedule_type = $("input[name=schedule_type]:checked").val();
    	var schedule_time = $("#schedule_time").val();
    	var time_zone = $("#time_zone").val();
    	if(schedule_type=='later' && (schedule_time=="" || time_zone==""))
    	{
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select schedule time/time zone."); ?>', 'warning');
    		return;
    	}

      var page_subscriber = parseInt($("#page_subscriber").html());     
      if(page_subscriber==0 || page_subscriber=="")
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Page does not have any subscriber to send message."); ?>', 'warning');
        return;
      }

    	$(this).addClass('btn-progress');

    	var report_link = base_url+"messenger_bot_broadcast/conversation_broadcast_campaign";
    	var success_message = "<?php echo $this->lang->line('Campaign have been submitted successfully.'); ?> <a href='"+report_link+"'><?php echo $this->lang->line('See report here.'); ?></a>";

      var queryString = new FormData($("#inbox_campaign_form")[0]);
      $.ajax({
         context:this,
	       type:'POST' ,
	       url: base_url+"messenger_bot_broadcast/create_conversation_campaign_action",
	       data: queryString,
	       cache: false,
	       contentType: false,
	       processData: false,
         dataType:'JSON',
	       success:function(response)
	       {  
            $(this).removeClass('btn-progress');
            if(response.status=='1')
            {
              var span = document.createElement("span");
              span.innerHTML = success_message;
              swal({ title:'<?php echo $this->lang->line("Campaign Submitted"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});
            }
            else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error').then((value) => {window.location.href=report_link;});
	       }
      	});

    });

  });

</script>


<?php //$this->load->view("messenger_tools/bulk_message/style");?>