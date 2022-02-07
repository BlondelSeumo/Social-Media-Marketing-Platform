<?php include(APPPATH.'views/ecommerce/store_style.php'); ?>
<div id="put_script"></div>
<section class="section">

	<?php
	$qr_code = json_decode($xdata['qr_code'],true);
	$msg_fore_color = isset($qr_code['msg_fore_color']) ? $qr_code['msg_fore_color'] : "#000000";
	$msg_back_color = isset($qr_code['msg_back_color']) ? $qr_code['msg_back_color'] : "#FFFFFF";
	$out_fore_color = isset($qr_code['out_fore_color']) ? $qr_code['out_fore_color'] : "#000000";
	$out_back_color = isset($qr_code['out_back_color']) ? $qr_code['out_back_color'] : "#FFFFFF";
	$options[''] = $this->lang->line("Store")." : ".$xdata['store_name'];
	foreach ($ecommerce_cart_pickup_points as $key => $value)
	{
		$options[$value['id']] =  $this->lang->line("Delievry point")." - ".$value['point_name']." : ".$value['point_details'];
	}

	?>

	<div class="section-body mt-2">
			<form action="#" enctype="multipart/form-data" id="plugin_form">
				<div class="row">
					<div class="col-12">
						<div class="form-group">
							<label><?php echo $this->lang->line("Select store or delivery point");?></label>
							<?php echo form_dropdown('selector', $options,$pickup_point_id,"class='form-control' id='selector'"); ?>
							<input type="hidden" name="store_id" id="store_id" value="<?php echo $xdata['id']; ?>">				
						</div>
					</div>

					<?php if($qr_img['messenger_qr']!="") : ?>
					<div class="col-12 col-md-6">						
						<div class="card" style="border:1px solid #dee2e6">
							<div class="card-header text-center">
								<h6 class="full_width"><?php echo $this->lang->line("Messenger QR Code"); ?>
								</h6>
							</div>				
							<div class="card-body" style="min-height: 680px">
								<div class="form-group">
			                      <div class="input-group">
			                        <div class="input-group-prepend"><span class="input-group-text"><?php echo $this->lang->line("Foreground"); ?></span></div>
			                        <input type="color" value="<?php echo $msg_fore_color; ?>" class="form-control" name="msg_fore_color" id="msg_fore_color">
			                         <div class="input-group-prepend"><span class="input-group-text"><?php echo $this->lang->line("Background"); ?></span></div>
			                        <input type="color" value="<?php echo $msg_back_color; ?>" class="form-control" name="msg_back_color" id="msg_back_color">
			                      </div>
			                    </div>
								<div class="text-center">
									<a href="<?php echo base_url('ecommerce/download_qr/'.$qr_img['messenger_qr']);?>" class="btn btn-sm mb-2 btn-outline-primary"><i class="fas fa-download"></i> <?php echo $this->lang->line("Download"); ?></a><br>
									<img style="width:250px;" src="<?php echo base_url('upload/qrc/'.$qr_img['messenger_qr']);?>">
									<pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $qr_img['messenger_link']; ?></span></code></pre>
								</div>
								<br>
								<p class="mt-3 mb-3">
									<b><?php echo $this->lang->line("Welcome Message"); ?></b>									
									<a id="variables" class="float-right text-warning pointer"><i class="fas fa-circle"></i> <?php echo  $this->lang->line("Variables"); ?></a>
								</p>
		                 		<div class="reminder_block">
		                 			<span class="block4">
		                 				<textarea style="height: 50px" data-toggle="tooltip" title="<?php echo $this->lang->line('After scanning QR code this message will be displayed inside Messenger. Click to edit text.'); ?>" name="msg_text" id="msg_text"><?php echo isset($qr_code['msg_text']) ? $qr_code['msg_text'] : "Hi {{first_name}}, welcome to our store."; ?></textarea>	                         	
		                 				<p>
		                 				<input data-toggle="tooltip" title="<?php echo $this->lang->line('Clicking this button will load your store inside Messenger. Click to edit button name.'); ?>" value="<?php echo isset($qr_code['msg_btn']) ? $qr_code['msg_btn'] : 'START'; ?>" class="btn btn-block bg-white" name="msg_btn" id="msg_btn"/>
		                 				</p>
		                 			</span>
		                 		</div>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<div class="col-12 <?php if($qr_img['messenger_qr']!="") echo 'col-md-6';?>">				
						<div class="card" style="border:1px solid #dee2e6">
							<div class="card-header text-center" style="">
								<h6 class="full_width">
									<?php echo $this->lang->line("Outside QR Code"); ?>
								</h6>
							</div>				
							<div class="card-body" style="min-height: 680px">
								<div class="form-group">
			                      <div class="input-group">
			                        <div class="input-group-prepend"><span class="input-group-text"><?php echo $this->lang->line("Foreground"); ?></span></div>
			                        <input type="color" value="<?php echo $out_fore_color; ?>" class="form-control" name="out_fore_color" id="out_fore_color">
			                         <div class="input-group-prepend"><span class="input-group-text"><?php echo $this->lang->line("Background"); ?></span></div>
			                        <input type="color" value="<?php echo $out_back_color; ?>" class="form-control" name="out_back_color" id="out_back_color">
			                      </div>
			                    </div>
								<div class="text-center">
									<a href="<?php echo base_url('ecommerce/download_qr/'.$qr_img['public_qr']);?>" class="btn btn-sm mb-2 btn-outline-primary"><i class="fas fa-download"></i> <?php echo $this->lang->line("Download"); ?></a><br>
									<img style="width:250px;" src="<?php echo base_url('upload/qrc/'.$qr_img['public_qr']);?>">
									<pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $qr_img['public_link']; ?></span></code></pre>
								</div>
							</div>
						</div>
					</div>					
				</div>
			</form>

			<div class="row">
				<div class="col-12">
					<div class="card no_shadow">
						<div class="card-footer p-0">  
							<button class="btn btn-lg btn-primary" id="get_button" name="get_button" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
							<button class="btn btn-lg btn-light float-right" onclick="ecommerceGoBack()" type="button"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel");?></button>
					    </div>
					</div>
				</div>
			</div>

		
	</div>
</section>


<script>
var base_url="<?php echo site_url(); ?>";
function ecommerceGoBack() //used to go back to list as crud
{
  var mes='';
  mes="<?php echo $this->lang->line('Your data may not be saved.');?>";
  swal({
    title: "<?php echo $this->lang->line("Do you want to go back?");?>",
    text: mes,
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => 
  {
    if (willDelete) 
    {
      parent.location.reload();
    } 
  });
}

$(document).ready(function($) { 
  $(document).on('click','#variables',function(e){
    e.preventDefault();          

    var success_message= '{{last_name}}<br/>{{first_name}}';
    var span = document.createElement("span");
    span.innerHTML = success_message;
    swal({ title:'<?php echo $this->lang->line("Variables"); ?>', content:span,icon:'info'});     
  });

  $(document).on('change','input[type=color]',function(e){
   	var id = $(this).attr('id');
   	var pickup_point_id = $("#selector").val();
   	get_button("save_generate",pickup_point_id);   	
  }); 

  $(document).on('change','#selector',function(e){
  	var pickup_point_id = $("#selector").val();
   	get_button("save_generate",pickup_point_id);   	
  }); 

  $(document).on('click','#get_button',get_button);
  function get_button(action,pickup_point_id)
  {       
  	if(typeof(action)==='undefined') action = "save";
  	if(typeof(pickup_point_id)==='undefined') pickup_point_id = "";
    $('#get_button').addClass('btn-progress');
    var store_id = $("#store_id").val();
    var redirect_to = '<?php echo base_url("ecommerce/qr_code/")?>'+store_id;
    if(pickup_point_id!='') redirect_to = redirect_to+"/"+pickup_point_id;

    var queryString = new FormData($("#plugin_form")[0]);
  
    $.ajax({
      type:'POST' ,
      url: base_url+"ecommerce/qr_code_action",
      data: queryString,
      dataType : 'JSON',
      cache: false,
      contentType: false,
      processData: false,
      success:function(response)
      {  
        $("#get_button").removeClass('btn-progress');
        if(response.status=='1') 
        { 
          if(action=="save_generate") window.location.assign(redirect_to);
          else swal('<?php echo $this->lang->line("Settings Updated"); ?>', response.message, 'success').then((value) => { parent.window.location.assign('<?php echo base_url("ecommerce/store_list") ?>');});
        }
        else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
      }

    });

  } 
  
});
</script>