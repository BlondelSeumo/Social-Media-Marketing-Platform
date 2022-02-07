<style>
	.card { padding-top: 0 !important; }
	.card .media-body .media-title { margin-bottom: 0px !important; }
	.card .media-body .page_email { line-height: 12px !important; }
	.card .page_delete { margin-top:10px;margin-right:10px; padding: .1rem .5rem !important; }
	.card .right-button { margin-top:10px;margin-right:10px; padding: .1rem .5rem !important; }
	.card .enable_webhook { margin-top:10px; padding: .1rem .5rem !important; }
	.card .disable_webhook { margin-top:10px; padding: .1rem .5rem !important; }
	/* .profile-widget-header {margin-bottom: -18px !important;} */
	/* .profile-widget-header img { margin: -20px -5px 0 22px !important; } */
	/* .profile-widget-header h6 { text-align: left;margin-left: 20px; } */
	/*.profile-widget-header .delete_account { position: absolute;top:10px;right:10px;}*/
	.profile-widget .profile-widget-items:after{position: relative;}
	.list-unstyled .media{padding-right:10px;} 

	/* .profile-widget-item{border:none;} */
	.btn-circle{margin:0 !important;}

	@media (max-width: 575.98px)
	{
		.profile-widget { margin-top: 0 !important; }
	}

	.update_account {cursor: pointer;}
	
</style>
<style type="text/css">
.profile-widget .profile-widget-items .profile-widget-item{
	text-align: left !important;
	padding-left: 20px !important;
}
</style>
<?php $fb_login_button=str_replace("ThisIsTheLoginButtonForFacebook",$this->lang->line("Login with Facebook"), $fb_login_button); ?>

<section class="section">
	<div class="section-header">
	  <h1><i class="fa fa-facebook-official"></i> <?php echo $this->lang->line("Connect Facebook & Instagram") ?></h1>
	  <!-- <div class="section-header-breadcrumb">
	    <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
	    <div class="breadcrumb-item"><?php echo $page_title; ?></div>
	  </div> -->
	</div>

	<?php 
		if($this->session->userdata('success_message') == 'success')
		{
			echo '<div class="alert alert-success" role="alert">
					  <h4 class="alert-heading">'.$this->lang->line('Well Done!').'</h4>
					  <p>'.$this->lang->line('Your account has been imported successfully.').'</p>
					  <p class="mb-0">'.$this->lang->line('Whenever you need to refresh access token or sync new data just login with Facebook again.').'</p>
					</div><br/>';
			$this->session->unset_userdata('success_message');
		}

		if($this->session->userdata('limit_cross') != '')
		{
			echo "<div class='alert alert-danger text-center'><i class='fas fa-times'></i> ".$this->session->userdata('limit_cross')."</div><br/>";
			$this->session->unset_userdata('limit_cross');
		}
		$is_demo=$this->is_demo;

		if($show_import_account_box==0)
		{						
			echo "<div class='alert alert-danger text-center'><i class='fas fa-times-circle'></i>". $this->lang->line('Due to system configuration change you have to delete one or more imported Facebook accounts and import again. Please check the following accounts and delete the account that has warning to delete.')."</div><br>";			
		}

		if($is_demo && $this->session->userdata("user_type")=="Admin")  
		{
			echo '<div class="alert alert-warning text-center">Account import has been disabled in admin account because you will not be able to unlink the Facebook account you import as admin. If you want to test with your own accout then <a href="'.base_url('home/sign_up').'" target="_BLANK">sign up</a> to create your own demo account then import your Facebook account there.</div>';
		}
		
	?>
	
	<div class="section-body">
		<?php
		if($existing_accounts != '0') {?>	
			<div class="float-right">
				<p data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("You must be logged in your facebook account for which you want to refresh your access token. for synch your new page, simply refresh your token. if any access token is restricted for any action, refresh your access token.");?>"> <?php if($this->config->item('developer_access') != '1') echo $fb_login_button; ?></p>
			</div>
	    <?php } ?>

	    <div class="clearfix"></div>

		<?php if($existing_accounts != '0') : ?>		
			<div>			
				<div class="row">
				<?php $i=0; foreach($existing_accounts as $value) : ?>
					<div class="col-12">
						
						<?php $profile_picture="https://graph.facebook.com/me/picture?access_token={$value['user_access_token']}&width=150&height=150"; ?>

				    	<div class="card profile-widget my-0">
				    		<div class="profile-widget-header">
    		                    <img src="<?php echo $profile_picture; ?>" class="img-thumbnail profile-widget-picture">
    		                    <div class="profile-widget-items">
    		                      <div class="profile-widget-item">
    		                        <div class="profile-widget-item-label mb-2">
    		                        	<?php echo count($value['page_list']); ?> <?php echo $this->lang->line("pages"); ?> 
    		                        	<?php if($this->config->item('facebook_poster_group_enable_disable')=='1') :?>
    		                        	& <?php echo count($value['group_list']); ?> <?php echo $this->lang->line("groups"); ?>
	    		                        <?php endif; ?>
    		                        </div>
    		                        <div class="profile-widget-item-value">
    		                        	  <?php  echo $value['name']; ?>
    		                        	  <input type="text" class="form-control float-right mr-3" style="width:150px;margin-top: -15px" onkeyup="search_in_class(this,'page_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search'); ?>...">

    		                        	  <button class="delete_account btn btn-outline-danger btn-sm ml-2" table_id="<?php echo $value['userinfo_table_id']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("Do you want to remove this account from our database? you can import again.");?>"><i class="fas fa-dumpster"></i> <?php echo $this->lang->line("Unlink");?></button>
    		                        	</div>
    		                        </div>

    		                    </div>
    		                </div>

			    	  	</div>
						
						<div class="card bg-body no_shadow">
						  <div class="card-body p-0">
						    <div class="summary">
						    	<?php
						    		if($value['need_to_delete'] == 1)
						    		{
						    			echo "<div class='alert alert-danger text-center'><i class='fa fa-close'></i> ".$this->lang->line('you have to delete this account.')."</div>";
						    		} 
						    	?>
						    	<?php 
						    		if($value['validity'] == 'no')
						    		{
						    			echo "<div class='alert alert-danger text-center'><i class='fa fa-close'></i> ".$this->lang->line('your login validity has been expired.')."</div>";
						    		}
						    	?>
						     <div class="summary-item">
						      	<!-- page lists -->
						        <h6 class="mt-3"><?php echo $this->lang->line('Page List') ?> <span class="text-muted">(<?php echo count($value['page_list']); ?> <?php echo $this->lang->line("pages"); ?>)</span></h6>
						      	<div style="max-height: 610px;overflow-y:auto;" class="nicescroll row">								        
						        	<?php 
						        		foreach($value['page_list'] as $page_info) : ?>
						        		<div class="col-12 col-md-6 col-lg-4 page_list_ul">
							        		<div class="card author-box">
			        		                  <div class="card-body pl-3 pr-2">
			        		                    <div class="author-box-left">
			        		                      <a target="_BLANK" href="https://facebook.com/<?php echo $page_info['page_id'];?>"><img alt="image" src="<?php echo $page_info['page_profile'];?>" class="rounded-circle author-box-picture mt-2" style="width: 63px !important;"></a>
			        		                      <div class="clearfix"></div>
			        		                      <a target="_BLANK" class="btn btn-outline-primary mt-3" href="<?php echo base_url('messenger_bot_analytics/result/').$page_info['id'];?>" class="btn"><i class="fas fa-chart-pie"></i> <?php echo $this->lang->line("Analytics");?></a>
			        		                    </div>
			        		                    <div class="author-box-details">
			        		                      <div class="author-box-name">
			        		                        <a target="_BLANK" href="https://facebook.com/<?php echo $page_info['page_id'];?>" ><?php echo $page_info['page_name']; ?></a>
			        		                      </div>
			        		                      <div class="author-box-job">
			        		                      	<i class="fas fa-at"></i> <?php echo $page_info['page_email']; ?>
			        		                  	  </div>
			        		                      <div class="author-box-job">
			        		                      	<small><i class="fas fa-circle text-success"></i> <?php echo $page_info['page_id']; ?></small>
			        		                      </div>
			        		                      <?php if(isset($page_info['has_instagram']) && $page_info['has_instagram'] == '1') : ?>
								              		<div class="mt-2">
							              				<i class="fab fa-instagram instagram"></i> 
							              				<a class="instagram" href="https://www.instagram.com/<?php echo $page_info['insta_username']; ?>" target="_BLANK"><?php echo $page_info['insta_username']; ?></a> 
							              				<i class="fas fa-sync-alt update_account text-warning pl-3" table_id="<?php echo $page_info['id'];?>" title="<?php echo $this->lang->line("Sync Instagram Account");?>" data-placement="right" data-toggle="tooltip"></i>
							              				<!-- <b><?php echo $this->lang->line('Media'); ?></b> : <span id="media_count_<?php echo $page_info['id'];?>"><?php echo custom_number_format($page_info['insta_media_count']); ?></span> | 
							              				<b><?php echo $this->lang->line('Followers'); ?></b> : <span id="follower_count_<?php echo $page_info['id'];?>"><?php echo custom_number_format($page_info['insta_followers_count']); ?></span> -->
								              		</div>
									              <?php endif; ?>
			        		                     
			        		                      <div class="mt-3"></div>

	      						              		<?php if($page_info['bot_enabled'] == '1') :?>
	      						              			<button style="margin-right:5px !important;" class="btn-sm btn btn-circle btn-outline-danger delete_full_bot" bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" already_disabled="no" title="<?php echo $this->lang->line("Delete Bot Connection & all settings.");?>" data-placement="right" data-toggle="tooltip">
	      			              			              	<i class="fas fa-eraser"></i> 
	      			              		              	</button>
	      			              		            <?php elseif($page_info['bot_enabled'] == '2'): ?>
	      			              		            	<button style="margin-right:5px !important;" class="btn-sm btn btn-circle btn-outline-danger delete_full_bot" bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" already_disabled="yes" title="<?php echo $this->lang->line("Delete Bot Connection & all settings.");?>" data-placement="right" data-toggle="tooltip">
	      			              			              	<i class="fas fa-eraser"></i> 
	      			              		              	</button>
	      						              		<?php endif; ?>

	                    		                      	<?php if($page_info['bot_enabled']=='0') : ?>
	                    									<button style="margin-right:5px !important;"  restart='0' bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" class="btn btn-sm btn-outline-primary btn-circle enable_webhook" title="<?php echo $this->lang->line("Enable Bot Connection");?>" data-placement="left" data-toggle="tooltip"><i class="fas fa-plug"></i></button>
	                    								<?php elseif($page_info['bot_enabled']=='1') : ?>
	                    									<button style="margin-right:5px !important;"  restart='0' bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" class="btn btn-sm btn-outline-dark btn-circle disable_webhook" title="<?php echo $this->lang->line("Disable Bot Connection");?>" data-placement="left" data-toggle="tooltip"><i class="fas fa-power-off"></i></button>
	                    								<?php else : ?>
	                    									<button style="margin-right:5px !important;"  restart='1' bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" class="btn btn-sm btn-outline-primary btn-circle enable_webhook" title="<?php echo $this->lang->line("Re-start Bot Connection");?>" data-placement="left" data-toggle="tooltip"><i class="fas fa-toggle-on"></i></button>
	                    								<?php endif; ?>									              	  											
	      											
	      											<?php if($page_info['bot_enabled'] == 1) :?>
	      												<button style="margin-right:5px !important;" class="btn-sm btn btn-outline-danger btn-circle right-button disabled" table_id="<?php echo $page_info['id']; ?>" title="<?php echo $this->lang->line("To enable delete button, first disable bot connection.");?>" data-placement="right" data-toggle="tooltip">
	      		              			              	  	<i class="fas fa-trash-alt"></i> 
	      		              		              	  	</button>
	      											<?php else : ?>
	      	              								<button style="margin-right:5px !important;" class="btn-sm btn btn-outline-danger btn-circle page_delete" table_id="<?php echo $page_info['id']; ?>" title="<?php echo $this->lang->line("Delete this page from database.");?>" data-placement="right" data-toggle="tooltip">
	      			              			              	  	<i class="fas fa-trash-alt"></i> 
	      			              		              	</button>            	  	
	      											<?php endif; ?>
			        		                    </div>
			        		                  </div>
			        		                </div>
										</div>
						          	<?php endforeach; ?>
						    	</div>

								<!-- group lists -->
								<?php if($this->config->item('facebook_poster_group_enable_disable') == '1') : ?>
									<div class="clearfix"></div>
					                <h6 class="mt-3"><?php echo $this->lang->line('Group List') ?> <span class="text-muted">(<?php echo count($value['group_list']); ?> <?php echo $this->lang->line("groups"); ?>)</span></h6>
					              	<div style="max-height: 310px;overflow-y:auto;" class="nicescroll row">
				        	        	<?php foreach($value['group_list'] as $group_info) : ?>
				        	        		<div class="col-12 col-md-6 page_list_ul">	
					        	        		<ul class="list-unstyled list-unstyled-border">
						        			        <li class="media bg-white p-4">
						        			            
						        			            <img alt="image" class="mr-3 rounded-circle" width="40" src="<?php echo $group_info['group_profile']; ?>">
						        			            
						        			            <div class="media-body">
						        			              	<div class="media-right">
						        			              	  	<a href="#" class="btn-circle btn btn-outline-danger group_delete" table_id="<?php echo $group_info['id']; ?>" title="<?php echo $this->lang->line("Do you want to remove this group from our database?");?>" data-placement="left" data-toggle="tooltip">
							        			              	  	<i class="fas fa-trash-alt"></i> 
						        			              	  	</a>
						        			              	</div>

						        			              	<div class="media-title"><a target="_BLANK" href="https://facebook.com/<?php echo $group_info['group_id'];?>" ><?php echo $group_info['group_name']; ?></a>
						        			              	</div>

							        			            <div class="text-small text-muted">
							        			                <i class="fas fa-circle"></i> </b> <?php echo $group_info['group_id']; ?>
							        			            </div> 
						        			            </div>
						        			        </li>
						        	        	</ul>
					        	        	</div>
				        	          	<?php endforeach; ?>
					            	</div>
								<?php endif; ?>
						      </div>
						    </div>
						  </div>
						</div>


					</div>

				<?php
					$i++;
					if($i%2 == 0)
						echo "</div><div class='row'>";
					endforeach;				
				?>
				</div> 
			</div>
		<?php else : ?>
			<div class="card" id="nodata">
			  <div class="card-body">
			    <div class="empty-state">
			      <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
			      <h2 class="mt-0"><?php echo $this->lang->line("You haven not connected any account yet.")?></h2>
			      <br/>
			      <h4>
			      	<div class="text-center">
			      		<p data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("you must be logged in your facebook account for which you want to refresh your access token. for synch your new page, simply refresh your token. if any access token is restricted for any action, refresh your access token.");?>"> <?php if($this->config->item('developer_access') != '1') echo $fb_login_button; ?></p>
			      	</div>
			      </h4>
			    </div>
			  </div>
			</div>
		<?php endif; ?>
	</div>
</section>


<div class="modal fade" id="delete_confirmation" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-flag"></i> <?php echo $this->lang->line("Deletion Report") ?></h4>
            </div>
            <div class="modal-body" id="delete_confirmation_body">                

            </div>
        </div>
    </div>
</div>

<?php 
    
    $doyouwanttodelete = $this->lang->line("Do you want to delete this group from database?");
    $ifyoudeletethispage = $this->lang->line("If you delete this page, all the campaigns corresponding to this page will also be deleted. Do you want to delete this page from database?");
    $ifyoudeletethisaccount = $this->lang->line("If you delete this account, all the pages, groups and all the campaigns corresponding to this account will also be deleted form database. do you want to delete this account from database?");
    $facebooknumericidfirst = $this->lang->line("Please enter your facebook numeric id first");
    $ifyoudeletethisgroup = $this->lang->line("If you delete this group, all the campaigns corresponding to this group will also be deleted. Do you want to delete this group from database?");

?>


<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
	
	$("document").ready(function() {
		var base_url = "<?php echo base_url(); ?>";

		// instagram section
		$(document).on('click','.update_account',function(){
			var table_id = $(this).attr('table_id');
			$(this).removeClass('fas fa-sync-alt');
			$(this).addClass('fas fa-spinner');
			$.ajax({
				context: this,
				type:'POST' ,
				url:"<?php echo site_url();?>instagram_reply/update_your_account_info",
				dataType: 'json',
				data:{table_id:table_id},
				success:function(response){ 
					
					$(this).removeClass('fas fa-spinner');
					$(this).addClass('fas fa-sync-alt');

					if(response.status == 1)
					{
						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                              $("#media_count_"+table_id).text(response.media_count);
                              $("#follower_count_"+table_id).text(response.follower_count);
                            });
					}
					else
					{
						swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				},
				error:function(response){
					$(this).removeClass('fas fa-spinner');
					$(this).addClass('fas fa-sync-alt');
                    var span = document.createElement("span");
                    span.innerHTML = response.responseText;
                    swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
                }
			});
		});


		// sweet alert + confirmation
		$(document).on('click','.enable_webhook',function(){
			var restart = $(this).attr('restart');
			if(restart == 1)
			{
				var confirm_str = "<?php echo $this->lang->line("Do you really want to re-start Bot Connection for this page?"); ?>";
				var confirm_alert = '<?php echo $this->lang->line("Re-start Bot Connection"); ?>';
			}
			else
			{
				var confirm_str = "<?php echo $this->lang->line("Do you really want to enable Bot Connection for this page?"); ?>";
				var confirm_alert = '<?php echo $this->lang->line("Enable Bot Connection"); ?>';
			}
			swal({
				title: confirm_alert,
				text: confirm_str,
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					var page_id = $(this).attr('bot-enable');
					
					$(this).removeClass('btn-outline-primary');
					$(this).addClass('btn-primary');
					$(this).addClass('btn-progress');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo site_url();?>social_accounts/enable_disable_webhook",
						dataType: 'json',
						data:{page_id:page_id,enable_disable:'enable',restart:restart},
						success:function(response){ 
							$(this).removeClass('btn-progress');
							$(this).removeClass('btn-primary');
							$(this).addClass('btn-outline-primary');
							if(response.status == 1)
							{
								swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
				         			  location.reload();
									});
							}
							else
							{
								var success_message=response.message;
								var span = document.createElement("span");
								span.innerHTML = success_message;
								swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span, icon:'error'});
							}
						}
					});
				} 
			});


		});

		$(document).on('click','.disable_webhook',function(){

			swal({
				title: '<?php echo $this->lang->line("Disable Bot Connection"); ?>',
				text: '<?php echo $this->lang->line("Do you really want to disable Bot Connection for this page?"); ?>',
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					var page_id = $(this).attr('bot-enable');
					var restart = $(this).attr('restart');

					$(this).removeClass('btn-outline-dark');
					$(this).addClass('btn-dark');
					$(this).addClass('btn-progress');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo site_url();?>social_accounts/enable_disable_webhook",
						dataType: 'json',
						data:{page_id:page_id,enable_disable:'disable',restart:restart},
						success:function(response){ 
							$(this).removeClass('btn-progress');
							$(this).removeClass('btn-dark');
							$(this).addClass('btn-outline-dark');
							if(response.status == 1)
							{
								swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
				         			  location.reload();
									});
							}
							else
							{
								swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
							}
						}
					});
				} 
			});


		});


		$(document).on('click','.delete_full_bot',function(){
			var confirm_str = "<?php echo $this->lang->line("By proceeding, it will delete all settings of messenger bot, auto reply campaign, posting campaign, subscribers and all campaign reports of this page. This data can not be retrived. It will not delete the page itself from the system."); ?>";
			swal({
				title: '<?php echo $this->lang->line("Delete Bot Connection & all settings"); ?>',
				text: confirm_str,
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					var page_id = $(this).attr('bot-enable');
				    var already_disabled = $(this).attr('already_disabled');

				    $(this).removeClass('btn-outline-danger');
				    $(this).addClass('btn-danger');
					$(this).addClass('btn-progress');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo site_url();?>social_accounts/delete_full_bot",
						dataType: 'json',
						data:{page_id:page_id,already_disabled:already_disabled},
						success:function(response){ 
							$(this).removeClass('btn-progress');
							$(this).removeClass('btn-danger');
							$(this).addClass('btn-outline-danger');
							if(response.status == 1)
							{
								swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
				         			  location.reload();
									});
							}
							else
							{
								swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
							}
						}
					});
				} 
			});


		});



		$(document).on('click','.group_delete',function(e){
			e.preventDefault();
			var ifyoudeletethisgroup = "<?php echo $ifyoudeletethisgroup; ?>";
  			var group_table_id = $(this).attr('table_id');
			swal({
				title: '<?php echo $this->lang->line("Warning!"); ?>',
				text: ifyoudeletethisgroup,
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					$(this).removeClass('btn-outline-danger');
				    $(this).addClass('btn-danger');
					$(this).addClass('btn-progress');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo site_url();?>social_accounts/group_delete_action",
						dataType: 'json',
						data:{group_table_id:group_table_id},
						success:function(response){ 
							$(this).removeClass('btn-progress');
							$(this).removeClass('btn-danger');
							$(this).addClass('btn-outline-danger');
							if(response.status == 1)
							{
								swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
				         			  location.reload();
									});
							}
							else
							{
								swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
							}
						}
					});
				} 
			});


		});



		$(document).on('click','.page_delete',function(){
			var ifyoudeletethispage = "<?php echo $ifyoudeletethispage; ?>";
			swal({
				title: '<?php echo $this->lang->line("Are you sure"); ?>',
				text: ifyoudeletethispage,
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					var page_table_id = $(this).attr('table_id');

					$(this).removeClass('btn-outline-danger');
				    $(this).addClass('btn-danger');
					$(this).addClass('btn-progress');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo site_url();?>social_accounts/page_delete_action",
						dataType: 'json',
						data:{page_table_id : page_table_id},
						success:function(response){ 
							if(response.status == 1)
							{
								$(this).removeClass('btn-progress');
								$(this).removeClass('btn-danger');
								$(this).addClass('btn-outline-danger');
								
								swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
				         			  location.reload();
									});
							}
							else
							{
								swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
							}
						}
					});
				} 
			});


		});



		$(document).on('click','.delete_account',function(){
			var ifyoudeletethisaccount = "<?php echo $ifyoudeletethisaccount; ?>";
			swal({
				title: '<?php echo $this->lang->line("Are you sure"); ?>',
				text: ifyoudeletethisaccount,
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) 
				{
					var user_table_id = $(this).attr('table_id');
					$(this).removeClass('btn-outline-danger');
				    $(this).addClass('btn-danger');
					$(this).addClass('btn-progress');

					$.ajax({
						context: this,
						type:'POST' ,
						url:"<?php echo site_url();?>social_accounts/account_delete_action",
						dataType: 'json',
						data:{user_table_id : user_table_id},
						success:function(response){ 
							
							$(this).removeClass('btn-progress');
							$(this).removeClass('btn-danger');
							$(this).addClass('btn-outline-danger');

							if(response.status == 1)
							{
								swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
				         			  location.reload();
									});
							}
							else
							{
								swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
							}
						}
					});
				} 
			});


		});


		// $('#delete_confirmation').on('hidden.bs.modal', function () { 
		// 	location.reload(); 
		// });


		$("#submit").click(function(){
			var facebooknumericidfirst = "<?php echo $facebooknumericidfirst; ?>";
			var fb_numeric_id = $("#fb_numeric_id").val().trim();
			if(fb_numeric_id == '')
			{
				alert(facebooknumericidfirst);
				return false;
			}

			var loading = '<br/><br/><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block"><br/>';
        	$("#response").html(loading);

			$.ajax
			({
			   type:'POST',
			   // async:false,
			   url:base_url+'social_accounts/send_user_roll_access',
			   data:{fb_numeric_id:fb_numeric_id},
			   success:function(response)
			    {
			        $("#response").html(response);
			    }
			       
			});
		});

		
		$(document.body).on('click','#fb_confirm',function(){
			var loading = '<br/><br/><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block"><br/>';
        	$("#response").html(loading);
			$.ajax
			({
			   type:'POST',
			   // async:false,
			   url:base_url+'social_accounts/ajax_get_login_button',
			   data:{},
			   success:function(response)
			    {
			        $("#response").html(response);
			    }
			       
			});
		});


	});
	
</script>