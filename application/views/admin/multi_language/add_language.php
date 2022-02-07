<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
			<div class="breadcrumb-item active"><a href="<?php echo base_url('multi_language/index'); ?>"><?php echo $this->lang->line("Language Editor"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<?php $this->load->view('admin/theme/message'); ?>
	<div class="section-body">
		<div class="card">
          <div class="card-footer bg-whitesmoke language_name_field">
          	  <br>
              <div class="form-group">
	              <div class="input-group mb-3">
	                <input type="text" class="form-control" id="language_name" name="language_name" placeholder="<?php echo $this->lang->line("language name"); ?>" aria-label="">
	                <div class="input-group-append">
	                  <button class="btn btn-primary" type="submit" id="save_language_name"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Language"); ?></button>
	                </div>
	              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item">
                <a id="main_tab" class="nav-link active" data-toggle="tab" href="#fbinboxer_languages_tab" role="tab" aria-selected="false"><?php echo $this->lang->line('System Languages'); ?></a>
              </li>              
              <li class="nav-item hidden">
                <a id="addon_tab" class="nav-link" data-toggle="tab" href="#addons_languages_tab" role="tab" aria-selected="true"> <?php echo $this->lang->line("Add-ons Languages"); ?></a>
              </li>
              <li class="nav-item">
                <a id="plugin_tab" class="nav-link" data-toggle="tab" href="#plugins_languages_tab" role="tab" aria-selected="false"><?php echo $this->lang->line("3rd Party Languages"); ?></a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active show" id="fbinboxer_languages_tab" role="tabpanel" aria-labelledby="main_tab">
                <section id="main_app_section">
					<div class="row" style="padding: 0px 0px 0 9px !important;">
						<?php  
						$i=0;
						foreach ($file_name as $value) :  ?>
							<div class="col-lg-3 col-md-3 col-sm-12 col-12 text-center language_file" file_type="main-application_<?php echo $i;?>" file_name="<?php echo $value; ?>">
								<div class="card">
				                  <div class="card-header langFile">
				                    <i class="far fa-file-alt"></i>&nbsp;<?php echo $value; ?>&nbsp;
									<i id="<?php echo str_replace(".php",'',$value); ?>" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
				                  </div>
				                </div>
							</div> <?php 
							$i++; 
						endforeach; 
						?>
					</div>
				</section>
              </div>
              
<!--               <div class="tab-pane fade hidden" id="addons_languages_tab" role="tabpanel" aria-labelledby="addon_tab">
                <section id="addon_section">
		      		<div class="row" style="padding: 0px 0px 0 9px !important;">
		      			<?php $i = 0;
		      			 foreach ($addons as $addon): ?>
		      			<div class="col-lg-3 col-xs-12 col-md-3 col-sm-12 text-center language_file" id="addons" file_type="add-on_<?php echo $i; ?>">
							<div class="card">
			                  <div class="card-header langFile">
			                    <i class="fa fa-tags"></i> &nbsp;<?php echo str_replace(array('.php','_','lang'), ' ', $addon); ?>
								<i id="<?php echo str_replace(".php",'',$value); ?>" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
								&nbsp;<i id="<?php echo str_replace(array('.php','_','lang'), array('','',''), $addon); ?>" style="color:#13d408; display: none;" class="fa fa-check-circle"></i>
			                  </div>
			                </div>
		      			</div>
		      			<?php $i++; endforeach; ?>
		      		</div>
				</section>
              </div> -->

              <div class="tab-pane fade" id="plugins_languages_tab" role="tabpanel" aria-labelledby="plugin_tab">
                <section id="plugin_section">
		      		<div class="row">
		      			<div class="language_file" file_type ="plugin_0" id="plugins">
		      				<div class="card">
		      					<div class="card-header langFile">
				                    <i class="fa fa-plug"></i> &nbsp;<?php echo $this->lang->line("Plugin Languages");?>
									&nbsp;<i id="plugins1" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
				                </div>
				            </div>
		      			</div>
		      		</div>
		      	</section> 
              </div>
            </div>
          </div>          
        </div>
	</div>
</section>




<?php $giveAname = $this->lang->line("Please put a language name & then save."); ?>

<script>
	var base_url = "<?php echo base_url(); ?>";

	function search_in_td(obj,td_id){  // obj = 'this' of jquery, td_id = id of the td
	  var filter=$(obj).val().toUpperCase();

 	 	if(filter != ""){
		  	$('#'+td_id+' td .text_key').each(function(){
		  		var content = $(this).text().trim();

		  		if (content.toUpperCase().indexOf(filter) > -1) {
		  			$(this).css('display','block');
		  			$(this).parent().parent().find('.text_value').css("display","block");
		  			$(this).parent().parent().css('display','table-row');
		  		}
		  		else {
		  			$(this).parent().parent().css('display','none');
		  		}

	  		});
	  	} else 
	  	{
	  		$('#'+td_id+' tbody tr').each(function(index, el) {
	  			$(this).css("display","table-row");
	  		});

	  	}
	}

	$(document).ready(function(){

		// save language name for all
		$(document).on('click', '#save_language_name', function(event) {
			event.preventDefault();
			var languageName = $('#language_name').val();

			// if the language name filed is empty
			if(languageName == '') {
				var giveAname = "<?php echo $giveAname; ?>";
				swal('<?php echo $this->lang->line("Warning")?>', giveAname, 'warning');
				return false;
			}

			$.ajax({
				url: base_url+'multi_language/save_language_name',
				type: 'POST',
				data: {languageName: languageName},
				success:function(response)
				{
					if(response == "1") 
					{
						swal('<?php echo $this->lang->line("Success")?>', '<?php echo $this->lang->line("Your data has been successfully saved.") ?>', 'success');

					} else if(response == '3') 
					{
						swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("Only characters and underscores are allowed.") ?>', 'error');
					}
					else 
					{
						swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("Sorry, this language already exists, you can not add this again.") ?>', 'error');
					}

				}
			});
	
		});


		// showing language files data from directory
		$(document).on('click', '.language_file', function(event) {
			event.preventDefault();

			var languageFieldSelect = $(this).attr('id');
			var languageName = $.trim($('#language_name').val());
			var fileType = $(this).attr('file_type');
			var base_url = "<?php echo base_url(); ?>";

			// if the language name filed is empty
			if(languageName == '') 
			{
				var giveAname = "<?php echo $giveAname; ?>";
				swal('<?php echo $this->lang->line("Warning")?>', giveAname, 'warning');
				return false;
			} 

			// loading processing img
			var loading = '<br><img src="'+base_url+'assets/pre-loader/color/Preloader_9.gif" class="center-block" height="30" width="30">';
			$('#response_status').html(loading);

		    $.ajax({
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_get_language_details",
		    	data: {fileType:fileType,languageName:languageName},
		    	dataType: 'JSON',
		    	success:function(response){
			    	if(response.result == "1") 
			    	{
	    		  		$('#language_file_modal').modal();
	    				$('#languageDataBody').html(response.langForm);
	    		  		$("#language_type_modal").html(fileType);
	    		  		$('#response_status').html('');
	    		  		$("#new_lang_val").html(languageName);

			    	} else
			    	{
			    		var giveAname = "<?php echo $giveAname; ?>";
			    		swal('<?php echo $this->lang->line("Warning")?>', giveAname, 'warning');
			    	}
		    	}
		    });
		});


		// saving language file with language folder name
		$(document).on('click', '.save_language_button', function(event) {
			event.preventDefault();

			var languageFieldSelect = $(this).attr('id');
			var languageName 		= $('#language_name').val();

			// if the language name filed is empty
			if(languageName == '') {
				var giveAname = "<?php echo $giveAname; ?>";
				swal('<?php echo $this->lang->line("Warning")?>', giveAname, 'warning');
				return false;
			}

			$('#saving_response').html('');
			$(this).addClass("btn-progress");

			// Generate the language folder name from input
			var folder_name = $("#language_folder_name").val(languageName);
			// detect the file type clicked
			var clickedFile = $("#language_file_id").val();
			var ftype 		= $("#language_type_modal").html();
			var base_url 	= "<?php echo base_url(); ?>";			

			var alldatas = new FormData($("#language_creating_form")[0]);

		    $.ajax({
		    	context: this,
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_language_file_saving",
		    	data: alldatas,
		    	dataType : 'JSON',
		    	cache: false,
		    	contentType: false,
		    	processData: false,
		    	success:function(response){
		    		$(this).removeClass("btn-progress");
		    		if(response.status=="1")
			        {
			        	iziToast.success({title: '',message: response.message,position: 'bottomRight'});
			        }
			        else
			        {
			        	iziToast.error({title: '',message: response.message,position: 'bottomRight'});
			        }
		    	}
		    });
		});

	});
</script>


<div class="modal fade" tabindex="-1" role="dialog" id="language_file_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document" style="min-width: 90%">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Language Translation");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<div class="section-title mt-0 d-none" id="language_type_modal"></div>
				<blockquote class="d-none" id="new_lang_val"></blockquote>

				<div class="row">
					<div id="response_status"></div>
				</div>
				<div class="row">
				    <div class="col-12 col-md-6">
				        <div class="form-group">
				            <input type="text" name="search_index" id="search_index" class="form-control" style="width:50%;" placeholder="<?php echo$this->lang->line('search...');?>" onkeyup="search_in_td(this,'add_language_form_table')">
				        </div>
				    </div>
				</div>
				<div class="row">
					<div class="col-12 col-sm-12 col-md-12 col-lg-12">
						<div id="languageDataBody">

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" form_id="language_creating_form" class="btn btn-primary btn-lg save_language_button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save"); ?></button>
				<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
			</div>
		</div>
	</div>
</div>


<?php $this->load->view("admin/multi_language/styles"); ?>