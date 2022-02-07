<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-edit"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
			<div class="breadcrumb-item active"><a href="<?php echo base_url('multi_language/index'); ?>"><?php echo $this->lang->line("Language Editor"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<?php $this->load->view('admin/theme/message'); ?>
	<div class="section-body">
		<?php 
		if($languageName == "main_app") 
		{ ?>
			<div class="card">
	          <div class="card-footer bg-whitesmoke language_name_field">
	          	<?php 
	          	if($languagename != "english")
	          	{ ?>
		          	<br>
		            <div class="form-group">
			            <div class="input-group mb-3" id="languagename_field">
			                <input type="text" class="form-control" id="language_name" name="language_name" value="<?php echo $languagename; ?>">
			                <div class="input-group-append">
			                  <button class="btn btn-primary" type="submit" id="update_language_name"><i class="fas fa-save"></i> <?php echo $this->lang->line("Update Language"); ?></button>
			                </div>
			            </div>
		            </div>
		        <?php 
	          	} 
	          	else 
	          	{ ?>
	          		<input type="hidden" name="language_name" id="language_name" value="<?php echo $languagename; ?>">
	          		<div class="not_english text-center alert alert-warning">
	          			<?php echo $this->lang->line("English language name can not be updated. You Can update the content if you like."); ?>
	          		</div>
	          	<?php 
	            } ?>
	          </div>

	          <div class="card-header">
	          	<h4 class="text-center" style="width: 100%"><?php echo $this->lang->line('System Languages')." : ".$languagename." (".count($folderFiles)." ".$this->lang->line('files').")"; ?></h4>
	          </div>

	          <div class="card-body">
	          	<?php 
	          	if(!empty($folderFiles)) 
	          	{
	          		$i = 0;
	          		echo '<div class="row">';
	          		foreach ($folderFiles as $value) 
	          		{ ?>

		          		<div class="col-lg-3 col-12 text-center allFiles" file_type="main-application_<?php echo $i;?>" file_name="<?php echo $value; ?>">
							<div class="card">
			                  <div class="card-header pointer">
			                    <i class="far fa-file-alt"></i>&nbsp;<?php echo $value; ?>&nbsp;
								<i id="<?php echo str_replace(".php",'',$value); ?>" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
			                  </div>
			                </div>
						</div>
	          		<?php $i++;
	          		}
	          		echo '</div>';
	          	}
	          	else
	          	{ ?>
	          		<div class="text-center alert alert-warning">
	          			<?php echo $this->lang->line("English language name can not be updated. You Can update the content if you like."); ?>
	          		</div>
	          	<?php 
	          	} ?>
	          </div> 

	        </div>
	    <?php 
		}

		else  if($languageName == "plugin") 
		{ ?>
			<div class="card">

	          <div class="card-header">
	          	<h4 class="text-center" style="width: 100%"><?php echo $this->lang->line('3rd Party Languages')." : ".$plugin_file; ?></h4>
	          </div>

	          <div class="card-body">
	          	<input type="hidden" id="language_name" name="language_name" value="<?php echo $plugin_file; ?>" class="form-control text-center">
			
				<div class="col-lg-6 col-12 text-center allFiles" file_type="plugin_0" id="plug">
					<div class="card">
	                  <div class="card-header pointer">
	                    <i class="fas fa-plug"></i>&nbsp;<?php echo $plugin_file; ?>&nbsp;
						<i id="plugins1" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
	                  </div>
	                </div>
				</div>
	          </div> 

	        </div>
	    <?php 
		} 

		else if($languageName == "addon") 
		{ ?>
<!-- 			<div class="card">

	          <div class="card-header">
	          	<h4 class="text-center" style="width: 100%"><?php echo ucfirst(str_replace("_",' ',$languagename))." ".$this->lang->line('Add-on Languages')." (".count($module_language_folders)." ".$this->lang->line('files').")"; ?></h4>
	          </div>

	          <div class="card-body">
	          	<input type="hidden" id="language_name" name="language_name" value="<?php echo $languagename; ?>" class="form-control text-center" style="font-size: 14px;">

	          	<?php
	          	if(!empty($module_language_folders))
	          	{
	          		$i = 0;
	          		echo '<div class="row">';
	          		foreach ($module_language_folders as $value)
	          		{ ?>
		          		<div class="col-lg-3 col-12 text-center allFiles" file_type="add-on_<?php echo $i; ?>" folderName="<?php echo $value; ?>" id="addons">
		          			
		          			<div class="card">
			                  <div class="card-header pointer">
			                    <i class="fa fa-folder-open"></i>&nbsp;<?php echo ucfirst($value); ?>&nbsp;
								<i id="<?php echo $value; ?>" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
			                  </div>
			                </div>
		          		</div>
		          		
		          		<?php 
		          		$i++;
	          		}
	          		echo '</div>';
	          	}
	          	else
	          	{ ?>
          			<div class="text-center alert alert-warning">
	          			<?php echo $this->lang->line("This language folder is empty. No files to show"); ?>
	          		</div>
	          	<?php
	          	}?>
	          </div> 
	        </div> -->
	    <?php 
		} ?>
	</div>
</section>


<?php 
$giveAname = $this->lang->line("Please put a language name & save it first.");
$editable_language = $this->uri->segment(3);
?>

<script>

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

		// updating language name
		$(document).on('click', '#update_language_name', function(event) {
			event.preventDefault();

			var base_url 	 = '<?php echo base_url(); ?>';
			var languagename = $("#language_name").val();
			var pre_value 	 = '<?php echo $editable_language; ?>';


			if(languagename == '')
			{
				var giveAname = "<?php echo $giveAname; ?>";
				swal('<?php echo $this->lang->line("Warning")?>', giveAname, 'warning');
				return false;
			}

			if(languagename === pre_value)
			{
				swal('<?php echo $this->lang->line("Warning")?>', '<?php echo $this->lang->line("This language already exist, no need to update.") ?>', 'warning');

			} 
			else 
			{
				$.ajax({
					url: base_url+'multi_language/updating_language_name',
					type: 'POST',
					dataType:'JSON',
					data: {languagename: languagename,pre_value:pre_value},
					success:function(response)
					{
						if(response.status =="1")
						{
							var name = response.new_name;
							var currentUrl = base_url+"multi_language/edit_language/"+name+"/main_app";
							location.assign(currentUrl);
							
						} 
						else if(response.status =='3') 
						{
							swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("Only characters and underscores are allowed.") ?>', 'error');
						} else
						{
							swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("This language is already exist, please try with different one.") ?>', 'error');
						}
					}
				});
			}
		});


		// showing language files data from directory
		$(document).on('click', '.allFiles', function(event) {
			event.preventDefault();

			// getting which file is clicked
			var fileType 			= $(this).attr('file_type');
			var languageFieldSelect = $(this).attr('id');
			var languageName 		= '<?php echo $editable_language; ?>';
			var langname_existance  = $("#language_name").val();
			var addonLangName		= $(this).attr("folderName");
			var base_url 			= "<?php echo base_url(); ?>";

			// if the language name filed is empty
			if(languageFieldSelect == "main_app") 
			{
				if(langname_existance == '') 
				{
					var giveAname = "<?php echo $giveAname; ?>";
					swal('<?php echo $this->lang->line("Warning")?>', giveAname, 'warning');
					return false;
				}
			}

			// loading processing img
			var loading = '<br><img src="'+base_url+'assets/pre-loader/color/Preloader_9.gif" class="center-block" height="30" width="30">';
			$('#response_status').html(loading);

		    $.ajax({
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_get_lang_file_data_update",
		    	dataType:'JSON',
		    	data: {fileType:fileType,languageName:languageName,langname_existance:langname_existance},
		    	success:function(response)
		    	{
			    	if(response.status == "1") 
			    	{
			    		$('#language_file_modal').modal();
			  			$('#languageDataBody').html(response.langForm);
			    		$('#response_status').html('');
			    		$("#languName").html(languageName);
			    		$("#addon_languName").html(addonLangName);

			    	} else if(response.status == "3")
			    	{
			    		swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("Your given name has not updated, please update the name first.") ?>', 'error');
			    	} else
			    	{
			    		$('#response_status').html(loading);
			    	}
		    	}

		    });

		});
		

		// saving language file with language folder name
		$(document).on('click', '.update_language_button', function(event) {
			event.preventDefault();

			var languageName = $('#language_name').val();

			// if the language name filed is empty
			if(languageName == '') 
			{
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
			var base_url 	= "<?php echo base_url(); ?>";


			
			var alldatas = new FormData($("#language_creating_form")[0]);

		    $.ajax({
		    	context: this,
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_updating_lang_file_data",
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
        <h5 class="modal-title"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit Language Translation");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		

		<div class="row">
			<div id="response_status"></div>
		</div>
		
        <div class="row">
            <div class="col-12">
            	<div class="section-title mt-0 d-none" id="languName"></div>
				<blockquote class="d-none" id="addon_languName"></blockquote>

				<div class="row">
				    <div class="col-12 col-md-6">
				        <div class="form-group">
				            <input type="text" name="search_update_index" id="search_update_index" class="form-control" style="width:50%;" placeholder="<?php echo$this->lang->line('search...');?>" onkeyup="search_in_td(this,'update_language_form_table')">
				        </div>
				    </div>
				</div>
            	<div id="languageDataBody">
				
            	</div>
            </div>
        </div>
      </div>
      <div class="modal-footer bg-whitesmoke br">
        <button form_id="language_creating_form" class="btn btn-primary btn-lg update_language_button"><i class="fas fa-save" aria-hidden="true"></i>  <?php echo $this->lang->line("Save"); ?> </button>
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>






<?php $this->load->view("admin/multi_language/styles"); ?>