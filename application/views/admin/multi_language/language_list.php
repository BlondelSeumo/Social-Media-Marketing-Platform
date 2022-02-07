<style>.no_hover:hover{text-decoration: none;}</style>
<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-language"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<?php $this->load->view('admin/theme/message'); ?>
	<div class="section-body">
		<div class="card">
          <div class="card-footer bg-whitesmoke">
              <a class="btn btn-primary btn-lg add text-center" href="<?php echo base_url('multi_language/create_new_lang'); ?>">
					<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add New Language'); ?>
			  </a>
              <a class="btn btn-outline-danger delete btn-lg float-right text-center" href="#">
				<i class="fa fa-trash"></i> <?php echo $this->lang->line('Delete Language'); ?>
			 </a>
          </div>
          <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item">
                <a id="systemTab" class="nav-link active" data-toggle="tab" href="#fbinboxer_languages_tab" role="tab" aria-selected="false"><?php echo $this->lang->line('System Languages').' '." (".count($lang).")"; ?></a>
              </li>              
              <li class="nav-item hidden">
                <a id="addonTab" class="nav-link" data-toggle="tab" href="#addons_languages_tab" role="tab" aria-selected="true"> <?php echo $this->lang->line("Add-ons Languages")." (".count($addons).")"; ?></a>
              </li>
              <li class="nav-item">
                <a id="pluginTab" class="nav-link" data-toggle="tab" href="#plugins_languages_tab" role="tab" aria-selected="false"><?php echo $this->lang->line("3rd Party Languages")." (".count($plugins_files).")"; ?></a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active show" id="fbinboxer_languages_tab" role="tabpanel" aria-labelledby="systemTab">
                <section id="main_application_section">
					<div class="row">
						<?php 
						$i =0;
						foreach($lang as $lang_name) :  ?>
						<div class="col-12 col-md-3 col-lg-3 text-center">
							<input type="hidden" name="folder_type" value="main-application_<?php echo $i;?>">
							<div class="card card">
			                  <div class="card-header">
			                    <h4><i class="fa fa-folder-open"></i> <?php echo $lang_name; ?></h4>
			                  </div>
			                  <div class="card-footer">
			                    <a href="<?php echo base_url("multi_language/edit_language/".$lang_name."/main_app"); ?>" class="float-left btn-sm btn btn-outline-warning edit_btn">
			                    	<i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit"); ?>
			                    </a>
			                    <a target="_blank" class="btn btn-sm btn-outline-primary float-right" title="<?php echo $this->lang->line("Download this as backup") ?>" href="<?php echo base_url("multi_language/downloading_language_folder_zip/".$lang_name."/main_app"); ?>">
									<i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line("Download"); ?>
								</a>
			                  </div>
			                </div>
						</div>
						<?php $i++; endforeach; ?>
						
					</div>
				</section>
              </div>
              
              <div class="tab-pane fade hidden" id="addons_languages_tab" role="tabpanel" aria-labelledby="addonTab">
                <section id="addon_section">
					<div class="row">
						<?php 
						$i = 0;
						foreach($addons as $addon_name) :  ?>
						<div class="col-12 col-md-3 col-lg-3">
							<div class="card card">
			                  <div class="card-header">
			                    <h4><i class="fa fa-tags"></i> <?php echo str_replace("_"," ",$addon_name); ?></h4>
			                  </div>
			                  <div class="card-footer">
			                    <div class="action_btn" file_type="add-on_<?php echo $i;?>">
	      							<a href="<?php echo base_url("multi_language/edit_language/".$addon_name."/addon"); ?>" class="float-left btn-sm btn btn-outline-warning edit_btn">
	      								<i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit"); ?>
	      							</a>
									<a id="addons" class="btn btn-sm btn-outline-primary float-right download_addon" addonname="<?php echo $addon_name; ?>" href="" title="<?php echo $this->lang->line("Download this as backup") ?>"><i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line("Download"); ?>
									</a>
								</div>
			                  </div>
			                </div>
						</div>
						<?php $i++; endforeach; ?>
					</div>
				</section>
              </div>

              <div class="tab-pane fade" id="plugins_languages_tab" role="tabpanel" aria-labelledby="pluginTab">
                <section id="plugin_section">
		      		<div class="row">
		      			<?php 
		      			$i=0;
		      			foreach($plugins_files as $file_name) :  ?>

		      			<div class="col-12 col-md-3 col-lg-3 text-center">
							<div class="card card">
			                  <div class="card-header">
			                    <h4><i class="fa fa-folder-open"></i> <?php echo str_replace(".json","",$file_name); ?></h4>
			                  </div>
			                  <div class="card-footer">
			                    <div class="action_btn" file_type="plugin_<?php echo $i;?>">
				                    <?php $file_name = str_replace('.json','',$file_name); ?>
		      						<a title="<?php echo $this->lang->line("Update this language") ?>" href="<?php echo base_url("multi_language/edit_language/".$file_name."/plugin"); ?>" class="float-left btn-sm btn btn-outline-warning edit_btn">
		      							<i class="fas fa-edit"></i> <?php echo $this->lang->line("edit"); ?>
		      						</a>
				                    <a target="_blank" class="btn btn-sm btn-outline-primary float-right" title="<?php echo $this->lang->line("Download this as backup") ?>" href="<?php echo base_url("multi_language/downloading_language_folder_zip/".$file_name."/plugin"); ?>">
										<i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line("Download"); ?>
									</a>
								</div>
			                  </div>
			                </div>
		      			</div>
		      			<?php $i++; endforeach; ?>
		      		</div>
		      	</section> 
              </div>
            </div>
          </div>          
        </div>
	</div>
</section>


<?php
	// Retrieve the selected language from session
	$selectedlanguage = $this->session->userdata("selected_language"); 
?>

<script>
	var base_url = "<?php echo base_url(); ?>";
	$(document).ready(function($) {

		// getting addon language folders to download
		$(document).on('click', '.download_addon', function(event) {
			event.preventDefault();

			var base_url    = "<?php echo base_url(); ?>";
			var addon 	    = $(this).attr("addonname");
			var clickedtype = $(this).attr("id");

			$.ajax({
				url: base_url +"multi_language/get_addon_folders_to_download",
				type: 'POST',
				data: {addon: addon},
				success:function(response)
				{
					if(response)
					{
						$("#language_file_modal").modal();
						$('#languageDataBody').html(response);
						$("#addon_names").html(addon);
						$("#addon_type").html(clickedtype);
						$(".modal-title").html('<?php echo '<i class="fa fa-download"></i>'." ".$this->lang->line('Download Language') ?>');
					} else
					{
						$("#addon_names").html('');
						$("#addon_type").html('');
					}
				}
			})
		});

		// getting language folders to delete from all
		$(document).on('click', '.delete', function(event) {
			event.preventDefault();

			var base_url = "<?php echo base_url(); ?>";

			$.ajax({
				url: base_url+'multi_language/get_all_languages_to_delete',
				type: 'POST',
				data: {param1: 'value1'},
				success:function(response)
				{
					$("#language_file_modal").modal();
					$("#languageDataBody").html(response);
					$(".modal-title").html('<?php echo '<i class="fa fa-trash"></i>'." ".$this->lang->line('Delete Language') ?>');
					$("#addon_names").html('');
					$("#addon_type").html('');

				}
			})
		});


		// deleting the language from all, main,plugin,addons
		$(document).on('click', '.delete_language', function(event) 
		{
			event.preventDefault();
			var langname = $(this).html();
			var selectedLang = <?php echo '"'.$selectedlanguage.'"'; ?>;

			if(langname == 'english')
			{
				swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("Sorry, english language can not be deleted.") ?>', 'error');
				return;
			}

			if(langname == selectedLang)
			{
				swal('<?php echo $this->lang->line("Error") ?>', '<?php echo $this->lang->line("This is your default language, it can not be deleted.") ?>', 'error');
				return;
			}

			var that_parent = $(this).parent().parent().parent().parent();


			swal({
		      title: '<?php echo $this->lang->line("Delete Language?"); ?>',
		      text: '<?php echo $this->lang->line("Do you really want to delete this language? It will delete all files of this language."); ?>',
		      icon: 'warning',
		      buttons: true,
		      dangerMode: true,
		    })
		    .then((willDelete) => {
		      if (willDelete) {
			     $.ajax({
			     	url: base_url+'multi_language/delete_language_from_all',
			     	type: 'POST',
			     	data: {langname: langname},
			     	success:function(response)
			     	{
			     		if(response =='1')
			     		{
			     			// swal('<?php echo $this->lang->line("Success")?>', '<?php echo $this->lang->line("Your language file has been successfully deleted.")?>', 'success');
			     			iziToast.success({title: '',message: '<?php echo $this->lang->line("Your language file has been successfully deleted.")?>',position: 'bottomRight'});
			     			$(that_parent).addClass('d-none');

			     		}
			     		else
			     		{
			     			// swal('<?php echo $this->lang->line("Error")?>', '<?php echo $this->lang->line("Something went wrong, please try again.")?>', 'error');
			     			iziToast.success({title: '',message: '<?php echo $this->lang->line("Something went wrong, please try again.")?>',position: 'bottomRight'});
			     			$(that_parent).removeClass('d-none');
			     		}
			     	}
			     })
		      } 
		    });
	
		});


		// if delete modal reload the location else no reload
		$('#modal_close').on('click', function(event) 
		{
			event.preventDefault();

			console.log("dsdsdfs");

			var download_modal = $("#addon_type").html();
			if(download_modal == "addons")
			{
				//no reload
				var tab = $("#addonTab").attr("href");

			} else {
				// if delete modal then do reload
				location.reload();
			}
		});

	});
</script>


<div class="modal fade" tabindex="-1" role="dialog" id="language_file_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-trash"></i> <?php echo $this->lang->line("Select Language");?></h5>
                <button type="button" class="close" id='modal_close' data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
            </div>
          	<div class="modal-body">             
				
				<div class="row">
					<div id="response_status"></div>
				</div>

	            <div class="row">
	                <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center">
						<div class="d-none" id="addon_names"></div>
						<blockquote class="d-none" id="addon_type"></blockquote>
	                	<div id="languageDataBody">							
	                	</div>
	                </div>
	            </div>
           
            </div>
        </div>
    </div>
</div>




<?php $this->load->view("admin/multi_language/styles"); ?>