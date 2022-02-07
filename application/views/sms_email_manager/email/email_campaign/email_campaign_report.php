<?php 
    $this->load->view("include/upload_js");
    include("application/views/sms_email_manager/email/email_section_global_js.php");
    include("application/views/sms_email_manager/email/email_section_css.php");
?>


<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-eye"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("SMS/Email Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("sms_email_manager/email_campaign_lists"); ?>"><?php echo $this->lang->line("Email Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">
						<div class="row">
						    <div class="col-md-4 col-12">
						        <div class="card card-statistic-1">
						            <div class="card-icon bg-primary">
						                <i class="fas fa-info-circle"></i>
						            </div>
						            <div class="card-wrap">
						                <div class="card-header">
						                    <h4><?php echo $this->lang->line('Campaign'); ?> (<?php echo $cam_data[0]['status']; ?>)</h4>
						                </div>
						                <div class="card-body report_font_styles mt-2" id="email_campaign_name"><?php echo $cam_data[0]['campaign_name']; ?></div>
						            </div>
						        </div>
						    </div>
						    <div class="col-md-5 col-12">
						        <div class="card card-statistic-1">
						            <div class="card-icon bg-primary">
						                <i class="fas fa-plug"></i>
						            </div>
						            <div class="card-wrap">
						                <div class="card-header">
						                    <h4><?php echo $this->lang->line('Email API'); ?></h4>
						                </div>
						                <div class="card-body report_font_styles mt-2" id="api_name"><?php echo $cam_data[0]['email_api']; ?></div>
						            </div>
						        </div>
						    </div>
						    <div class="col-md-3 col-12">
						        <div class="card card-statistic-1">
						            <div class="card-icon bg-primary">
						                <i class="fas fa-paper-plane"></i>
						            </div>
						            <div class="card-wrap">
						                <div class="card-header">
						                    <h4><?php echo $this->lang->line('Sent'); ?> (<spoan><?php echo $cam_data[0]['sent_rate']."%"; ?></spoan>)</h4>
						                </div>
						                <div class="card-body report_font_styles mt-2" id="sent_state"><?php echo $cam_data[0]['sent']; ?></div>
						            </div>
						        </div>
						    </div>
						</div>

						<div class="row mb-3">

							<!-- Open rate section -->
							<?php if($this->config->item("enable_open_rate") == "1") { ?>
								<div class="col-12 col-md-4">
									<div class="card">
										<div class="card-header">
											<h4><i class="fas fa-star-half-alt"></i> <?php echo $this->lang->line('Campaign Open Rate'); ?></h4>
										</div>
										<div class="card-body">
											<div class="summary">
												<div class="summary-info">
													<h4><strong class="text-primary"><?php echo $cam_data[0]['number_of_unique_open'] ?></strong> 
														<sub class="text-muted"><small style="font-size:12px;"><?php echo $cam_data[0]['open_rate']; ?>%</small></sub>
													</h4>
													<span style="font-size:12px;font-weight:700;"><?php echo $this->lang->line('Opened'); ?>
														<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Open Rate"); ?>" data-content="<?php echo $this->lang->line("Email open rate is the percentage of the total number of delivered person who opened an email campaign."); ?>"><i class='fa fa-info-circle'></i> </a>
													</span>
													<div class="p-3">
														<div class="progress">
															<div class="progress-bar <?php if($cam_data[0]['open_rate'] != "100") echo "progress-bar-animated progress-bar-striped"; ?>" role="progressbar" data-width="<?php echo $cam_data[0]['open_rate']; ?>%" aria-valuenow="<?php echo $cam_data[0]['open_rate']; ?>" aria-valuemin="0" aria-valuemax="100">
																<?php echo $cam_data[0]['open_rate']; ?>%
															</div>
														</div>
													</div>
												</div>

												<div class="summary-item mt-0">
													<div class="card-body pr-0 pl-0 pb-0">
														<ul class="list-group list-group-flush">
															<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
																<span class="tittle-text"><?php echo $this->lang->line('Openers'); ?>
																	<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Openers"); ?>" data-content="<?php echo $this->lang->line("Number of people that opened the email, e.g. a person who opens the email 3 times will be counted as 1 opener."); ?>"><i class='fa fa-info-circle'></i> </a>
																</span>
																<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['number_of_unique_open']; ?></span>
															</li>
															<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
																<span class="tittle-text"><?php echo $this->lang->line('Total Opens'); ?>
																	<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Total Opens"); ?>" data-content="<?php echo $this->lang->line("Total number of email openings, e.g. if a person opens an email 3 times, this will be counted as 3 openings."); ?>"><i class='fa fa-info-circle'></i> </a>
																</span>
																<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['number_of_total_open']; ?></span>
															</li>
															<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
																<span class="tittle-text"><?php echo $this->lang->line('Click-to-open-ratio'); ?>
																	<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Click-To-Open-Rate"); ?>" data-content="<?php echo $this->lang->line("This figure represents the number of clickers divided by the number of openers. A good response rate is a sign that the message captured the attention of its recipients."); ?>"><i class='fa fa-info-circle'></i> </a>
																</span>
																<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['click_to_open_rate']; ?>%</span>
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
							
							<!-- Click Rate and unsubscribe section -->
							<?php if($this->config->item("enable_click_rate") == "1") { ?>
								<div class="col-12 <?php if($this->config->item("enable_open_rate") == "1") { echo "col-md-4"; } else { echo "col-md-6"; } ?>">
									<div class="card">
									  	<div class="card-header">
									    	<h4><i class="fas fa-mouse-pointer"></i> <?php echo $this->lang->line('Campaign Click Rate'); ?></h4>
									  	</div>
									  	<div class="card-body">
										    <div class="summary">
								    	    	<div class="summary-info">
								    	    		<h4><strong class="text-info"><?php echo $cam_data[0]['number_of_unique_clickers'] ?></strong> 
								    	    			<sub class="text-muted"><small style="font-size:12px;"><?php echo $cam_data[0]['click_rate']; ?>%</small></sub>
								    		    	</h4>
								    		    	<span style="font-size:12px;font-weight:700;"><?php echo $this->lang->line('Clicked'); ?>
								    		    		<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Click Rate"); ?>" data-content="<?php echo $this->lang->line("The email click rate (also known as an email click-through rate) is the percentage of delivered people who click on a link within an email."); ?>"><i class='fa fa-info-circle'></i> </a>
								    		    	</span>
								    		    	<div class="p-3">
								    		    		<div class="progress">
								    		    			<div class="progress-bar <?php if($cam_data[0]['click_rate'] != "100") echo "progress-bar-animated progress-bar-striped"; ?>" role="progressbar" data-width="<?php echo $cam_data[0]['click_rate']; ?>%" aria-valuenow="<?php echo $cam_data[0]['click_rate']; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #6bdc7e">
								    		    				<?php echo $cam_data[0]['click_rate']; ?>%
								    		    			</div>
								    		    		</div>
								    		    	</div>
								    	    	</div>
								    	    	<div class="summary-item mt-0">
								    	    		<div class="card-body pr-0 pb-0 pl-0">
								    	    			<ul class="list-group list-group-flush">
								    	    				<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
								    	    					<span class="tittle-text"><?php echo $this->lang->line('Clickers'); ?>
								    	    						<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Clickers"); ?>" data-content="<?php echo $this->lang->line("Unique number of clicks on the links in the email, e.g. if a person clicks on the links in the email 3 times, this will be counted as 1 click."); ?>"><i class='fa fa-info-circle'></i> </a>
								    	    					</span>
								    	    					<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['number_of_unique_clickers']; ?></span>
								    	    				</li>
								    	    				<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
								    	    					<span class="tittle-text"><?php echo $this->lang->line('Total Clicks'); ?>
								    	    						<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Total Clicks"); ?>" data-content="<?php echo $this->lang->line("Total number of clicks on the links in the email, e.g. if a person clicks on the links in the email 3 times, this will be counted as 3 clicks."); ?>"><i class='fa fa-info-circle'></i> </a>
								    	    					</span>
								    	    					<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['number_of_total_click']; ?></span>
								    	    				</li>
								    	    				<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
								    	    					<span class="tittle-text"><?php echo $this->lang->line('Last Clicked'); ?></span>
								    	    					<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['last_clicked_at']; ?></span>
								    	    				</li>
								    	    			</ul>
								    	    		</div>
								    	    	</div>
										    </div>
									  	</div>
									</div>
								</div>

								<div class="col-12 <?php if($this->config->item("enable_open_rate") == "1") { echo "col-md-4"; } else { echo "col-md-6"; } ?>">
									<div class="card">
									  <div class="card-header">
									    <h4><i class="fas fa-bell-slash"></i> <?php echo $this->lang->line('Unsubscribed'); ?></h4>
									  </div>
									  <div class="card-body">
									  	<div class="summary">
							  		    	<div class="summary-info">
							  		    		<h4><strong class="text-warning"><?php echo $cam_data[0]['total_unsubscribed'] ?></strong> 
							  		    			<sub class="text-muted"><small style="font-size:12px;"><?php echo $cam_data[0]['unsubscribe_rate']; ?>%</small></sub>
							  			    	</h4>
							  			    	<span style="font-size:12px;font-weight:700;"><?php echo $this->lang->line('Unsubscribed'); ?>
							  			    		<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Click Rate"); ?>" data-content="<?php echo $this->lang->line("An unsubscribe rate is a measure that indicates the percentage of users who have opted-out from the mailing list after an email campaign. "); ?>"><i class='fa fa-info-circle'></i> </a>
							  			    	</span>
							  			    	<div class="p-3">
							  			    		<div class="progress">
							  			    			<div class="progress-bar <?php if($cam_data[0]['unsubscribe_rate'] != "100") { echo "progress-bar-animated progress-bar-striped";} ?>" role="progressbar" data-width="<?php echo $cam_data[0]['unsubscribe_rate']; ?>%" aria-valuenow="<?php echo $cam_data[0]['unsubscribe_rate']; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color:#ff6060 ">
							  			    				<?php echo $cam_data[0]['unsubscribe_rate']; ?>%
							  			    			</div>
							  			    		</div>
							  			    	</div>
							  		    	</div>
							  		    	<div class="summary-item mt-0">
							  		    		<div class="card-body  pr-0 pb-0 pl-0">
							  		    			<ul class="list-group list-group-flush">
							  		    				<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
							  		    					<span class="tittle-text"><?php echo $this->lang->line('Unsubscribe Rate'); ?>
							  		    						<a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo $this->lang->line("Unsubscribe Rate"); ?>" data-content="<?php echo $this->lang->line("An unsubscribe rate is a measure that indicates the percentage of users who have opted-out from the mailing list after an email campaign."); ?>"><i class='fa fa-info-circle'></i> </a>
							  		    					</span>
							  		    					<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['unsubscribe_rate']; ?>%</span>
							  		    				</li>
							  		    				<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
							  		    					<span class="tittle-text"><?php echo $this->lang->line('Total Unsubscribed'); ?></span>
							  		    					<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['total_unsubscribed']; ?></span>
							  		    				</li>
							  		    				<li class="list-group-item d-flex justify-content-between align-items-center pl-0 pr-0">
							  		    					<span class="tittle-text"><?php echo $this->lang->line('Last Unsubscribed'); ?></span>
							  		    					<span class="badge badge-primary badge-pill"><?php echo $cam_data[0]['last_unsubscribed_at']; ?></span>
							  		    				</li>
							  		    			</ul>
							  		    		</div>
							  		    	</div>
									  	</div>
									  </div>
									</div>
								</div>
							<?php } ?>
							
						</div>

						<div class="row">
						    <div class="col-md-6 col-12">
						        <div class="input-group float-left" id="searchbox">
						            <!-- search by post type -->
						            <div class="input-group-prepend">
						                <select class="select2 form-control" id="rate_type" name="rate_type">
						                    <option value=""><?php echo $this->lang->line("All"); ?></option>
						                    <option value="open"><?php echo $this->lang->line("Opened"); ?></option>
						                    <option value="click"><?php echo $this->lang->line("Clicked"); ?></option>
						                    <option value="unsubscribe"><?php echo $this->lang->line("Unsubscribed"); ?></option>
						                </select>
						            </div>
						            <input type="text" id="report_search" name="report_search" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>">
						            <div class="input-group-append">
						                <button class="btn btn-primary" id="email_report_search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
						            </div>
						        </div>
						    </div>

						    <?php if($cam_data[0]['posting_status'] != "2") { ?>
							    <div class="col-6">
							        <div class="btn-group dropleft float-right">
							            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php echo $this->lang->line('Options'); ?> </button>  
							            <div class="dropdown-menu dropleft">
							                <a class="dropdown-item has-icon pointer" href="<?php echo base_url("sms_email_manager/edit_email_campaign_content/").$cam_data[0]['id']; ?>"><i class="fas fa-edit"></i> <?php echo $this->lang->line('Edit Content'); ?></a>
							                <?php if($cam_data[0]['posting_status'] == "3") { ?>
							                	<a class="dropdown-item has-icon pointer email_restart_button" id="email_restart_button" table_id="<?php echo $cam_data[0]['id']; ?>" href=""><i class="fas fa-sync"></i> <?php echo $this->lang->line('Force Resume'); ?></a>
							            	<?php } ?>
							            </div>
							        </div>
							    </div>
							<?php } ?>
						</div>

						<div class="row">
							<div class="col-12">
							    <div class="table-responsive2 data-card">
							        <input type="hidden" id="put_row_id" value='<?php echo $cam_data[0]['id']; ?>'>
							        <table class="table table-bordered" id="mytable_email_campaign_report">
							            <thead>
							                <tr>
							                    <th><?php echo $this->lang->line('#'); ?></th>  
							                    <th><?php echo $this->lang->line('id'); ?></th>  
							                    <th><?php echo $this->lang->line("First Name"); ?></th>
							                    <th><?php echo $this->lang->line("Last Name"); ?></th>
							                    <th><?php echo $this->lang->line("Email"); ?></th>
							                    <th><?php echo $this->lang->line('Sent At'); ?></th>
							                    <th><?php echo $this->lang->line('Response'); ?></th>

							                    <?php if($this->config->item("enable_open_rate") == "1") { ?>
							                        <th class="text-center"><?php echo $this->lang->line('Email Opened'); ?></th>
							                        <th class="text-center"><?php echo $this->lang->line('Total Opens'); ?></th>
							                        <th class="text-center"><?php echo $this->lang->line('Last Opened'); ?></th>
							                    <?php } ?>
							                    
							                    <?php if($this->config->item("enable_click_rate") == "1") { ?>
							                        <th class="text-center"><?php echo $this->lang->line('Link Clicked'); ?></th>
							                        <th class="text-center"><?php echo $this->lang->line('Total Clicks'); ?></th>
							                        <th class="text-center"><?php echo $this->lang->line('Unsubscribed'); ?></th>
							                        <th class="text-center"><?php echo $this->lang->line('Last Clicked'); ?></th>
							                    <?php } ?>
							                </tr>
							            </thead>
							            <tbody>
							            </tbody>
							        </table>
							    </div>
							</div>
						</div>

						<div class="row">
						    <div class="col-12">
						        <div class='section'>
						            <div class='section-title'>
						                <?php echo $this->lang->line('Message'); ?>
						            </div>
						            <div id="accordion">
						                <div class="accordion email_accordion">
						                    <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#panel-body-1" aria-expanded="false" style="padding:20px;">
						                        <h4><?php echo $this->lang->line('Click To see Message'); ?></h4>
						                    </div>
						                    <div class="accordion-body collapse p-0" id="panel-body-1" data-parent="#accordion">
						                        <div class="card">
						                            <div class="card-body" style="border:0.5px dotted #eee;padding:20px 40px;">
														<div class="row">
															<div class="col-6">
								                                <h4 class="email_subject fw_700" title="<?php echo $this->lang->line('Email Subject'); ?>">
								                                	<?php echo $cam_data[0]['email_subject']; ?>	
							                                	</h4>
															</div>

															<div class="col-6">
																<span style="font-size:12px;" class="float-right" title="<?php echo $this->lang->line('Sent At'); ?>"><i class="far fa-clock"></i> <?php echo $cam_data[0]['completed_at']; ?></span>
															</div>
														</div>

														<div id="borderDiv"></div>

														<div class="row">
															<div class="col-12">
																<div class="original_message pt-3 pb-3" style="word-wrap:break-word;"><?php echo $cam_data[0]['email_message']; ?></div>
															</div>
														</div>
						                                
														<div class="row">
															<div class="col-12">
								                                <?php if(isset($cam_data[0]['email_attachment']) && $cam_data[0]['email_attachment'] != '') {  ?>
									                                <div id="attachment_div">
									                                    <div id="borderDiv"></div>
									                                    <a class="btn btn-secondary" id="attachment_btn" title="<?php echo $cam_data[0]['email_attachment'] ?>"><i class="fas fa-paperclip"></i><br>
									                                        <span>Attachment</span>
									                                    </a>
									                                </div>
								                            	<?php } ?>
															</div>
														</div>

						                            </div>
						                        </div>
						                    </div>
						                </div>
						            </div>
						        </div>
						    </div>
						</div>         
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

