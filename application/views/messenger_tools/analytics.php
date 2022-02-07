<section class="section">

	<div class="section-header">
	    <h1>
	    	<i class="fas fa-chart-pie"></i>
			<?php $fb_page_id=isset($page_info['page_id'])?$page_info['page_id']:""; ?>
			<?php $page_auto_id=isset($page_info['id'])?$page_info['id']:0; ?>
			<?php echo $this->lang->line("Bot Analytics");?> : 
			<a href="<?php echo "https://facebook.com/".$fb_page_id; ?>" target="_BLANK"><?php echo isset($page_info['page_name'])?$page_info['page_name']:""; ?></a>
	
	    </h1>
	    <div class="section-header-breadcrumb">
	      <div class="breadcrumb-item">
	      	<form method="POST" action="<?php echo base_url('messenger_bot_analytics/result/'.$page_auto_id); ?>">					
		      	<div class="input-group">
		      	  <div class="input-group-prepend">
		      	    <div class="input-group-text">
		      	      <i class="fas fa-calendar"></i>
		      	    </div>
		      	  </div>
		      	  <input type="text" class="form-control datepicker" value="<?php echo $from_date; ?>" id="from_date" name="from_date" style="width:115px">	
		      	  <input type="text" class="form-control datepicker" value="<?php echo $to_date	; ?>" id="to_date" name="to_date" style="width:115px">
		      	  <button class="btn btn-outline-primary" style="margin-left:1px" type="submit"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
		      	</div>
		    </form>
	      </div>
	    </div>
  	</div>


  	<div class="section-body">
	    <?php 
	    if($error_message=="")
	    { ?>	

			<div class="row">
				<div class="col-lg-4 col-12">
					<div class="card card-statistic-2">
					<div class="card-stats">
					  <div class="card-stats-title"><?php echo $this->lang->line("Latest Summary"); ?>
					  </div>
					  <div class="card-stats-items">
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_new_conversations_unique_summary['today']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("New"); ?></div>
					    </div>
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_blocked_conversations_unique_summary['today']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("Blocked"); ?></div>
					    </div>
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_reported_conversations_unique_summary['today']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("Reported"); ?></div>
					    </div>
					  </div><br><br>
					</div>
					
					</div>
				</div>

				<div class="col-lg-4 col-12">
				<div class="card card-statistic-2">
					<div class="card-stats">
					  <div class="card-stats-title"><?php echo $this->lang->line("7 Days Summary"); ?>
					  </div>
					  <div class="card-stats-items">
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_new_conversations_unique_summary['week']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("New"); ?></div>
					    </div>
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_blocked_conversations_unique_summary['week']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("Blocked"); ?></div>
					    </div>
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_reported_conversations_unique_summary['week']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("Reported"); ?></div>
					    </div>
					  </div><br><br>
					</div>

					</div>
				</div>

				<div class="col-lg-4 col-12">
					<div class="card card-statistic-2">
					<div class="card-stats">
					  <div class="card-stats-title"><?php echo $this->lang->line("30 Days Summary"); ?>
					  </div>
					  <div class="card-stats-items">
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_new_conversations_unique_summary['month']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("New"); ?></div>
					    </div>
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_blocked_conversations_unique_summary['month']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("Blocked"); ?></div>
					    </div>
					    <div class="card-stats-item">
					      <div class="card-stats-item-count"><?php echo $page_messages_reported_conversations_unique_summary['month']; ?></div>
					      <div class="card-stats-item-label"><?php echo $this->lang->line("Reported"); ?></div>
					    </div>
					  </div><br><br>
					</div>
					
					</div>
				</div>
			</div>

			<div class="row">
		      <div class="col-md-4 col-12">
		        <div class="card card-statistic-1">
		          <div class="card-icon bg-primary">
		            <i class="fas fa-users"></i>
		          </div>
		          <div class="card-wrap">
		            <div class="card-header">
		              <h4><?php echo $this->lang->line("Total Connections"); ?></h4>
		            </div>
		            <div class="card-body">
		              <?php echo $total_connections; ?>
		            </div>
		          </div>
		        </div>
		      </div>
		      <div class="col-md-4 col-12">
		        <div class="card card-statistic-1">
		          <div class="card-icon bg-warning">
		            <i class="fas fa-user-slash"></i>
		          </div>
		          <div class="card-wrap">
		            <div class="card-header">
		              <h4><?php echo $this->lang->line("Total Blocked"); ?></h4>
		            </div>
		            <div class="card-body">
		              <?php echo $page_messages_blocked_conversations_unique_summary['search']; ?>
		            </div>
		          </div>
		        </div>
		      </div>
		      <div class="col-md-4 col-12">
		        <div class="card card-statistic-1">
		          <div class="card-icon bg-danger">
		            <i class="fas fa-flag"></i>
		          </div>
		          <div class="card-wrap">
		            <div class="card-header">
		              <h4><?php echo $this->lang->line("Total Reported"); ?></h4>
		            </div>
		            <div class="card-body">
		             <?php echo $page_messages_reported_conversations_unique_summary['search']; ?>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>

		    <div class="row">
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line('Daily Unique New Conversations');?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Daily unique new conversations") ?>" data-content="<?php echo $this->lang->line("Daily: The number of messaging conversations on Facebook Messenger that began with people who had never messaged with your business before.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_messages_new_conversations_unique" height="182"></canvas>	       
			      </div>
			    </div>
			  </div>
			  <!-- DEPRECATED -->
			  <div class="col-12 col-lg-6" style="display: none;">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line('Daily Active Conversations');?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Daily active conversations") ?>" data-content="<?php echo $this->lang->line("The number of daily active conversations between users and the page") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_messages_active_threads_unique" height="182"></canvas>	       
			      </div>
			    </div>
			  </div>

		  	  <div class="col-12 col-lg-6">
		          <div class="card">
		            <div class="card-header">
		              <h4>
		              	<?php echo $this->lang->line('Messaging Connections');?>
		              	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Messaging connections") ?>" data-content="<?php echo $this->lang->line("Daily: The number of people your business can send messages to.") ?>"><i class='fas fa-info-circle'></i> </a>
		  			 </h4>
		            </div>
		            <div class="card-body">
		              <canvas id="page_messages_total_messaging_connections" height="182"></canvas>	       
		            </div>
		          </div>
		  	  </div>

			</div>

			<div class="row">
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line('Daily Unique Blocked Conversations');?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Daily unique blocked conversations") ?>" data-content="<?php echo $this->lang->line("Daily: The number of conversations with the Page that have been blocked") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_messages_blocked_conversations_unique" height="182"></canvas>	       
			      </div>
			    </div>
			  </div>
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line('Daily Unique Reported Conversations');?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Daily unique reported conversations") ?>" data-content="<?php echo $this->lang->line("Daily: The number of conversations from your Page that have been reported by people for reasons such as spam, or containing inappropriate content") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_messages_reported_conversations_unique" height="182"></canvas>	       
			      </div>
			    </div>
			  </div>		
			</div>

			<!-- DEPRECATED -->
			<div class="row" style="display: none;">
			  <div class="col-12 col-lg-8">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line('Daily Unique Reported Conversations by Type');?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Daily unique reported conversations by type") ?>" data-content="<?php echo $this->lang->line("Daily: The number of conversations from your Page that have been reported by people for reasons such as spam, or containing inappropriate content broken down by report type") ?>"><i class='fas fa-info-circle'></i> </a>
					</h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_messages_reported_conversations_by_report_type_unique" height="182"></canvas>	       
			      </div>
			    </div>
			  </div>
			  <div class="col-12 col-lg-4">
		        <div class="card">
		          <div class="card-header">
		            <h4>
		            	<?php echo $this->lang->line('Total Reported');?>
		            </h4>
		          </div>
		          <div class="card-body">
		            <canvas id="page_messages_reported_conversations_by_report_type_pie" height="410"></canvas>	       
		          </div>
		        </div>
			  </div>		
			</div>

			<div class="row">
			  <div class="col-12">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line('Daily Unique Reported vs Blocked Conversations');?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Daily unique reported vs blocked conversations") ?>" data-content="<?php echo $this->lang->line("Conversations from your Page that have been reported by people vs conversations with the page that have been blocked") ?>"><i class='fas fa-info-circle'></i> </a>
					</h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_messages_reported_vs_blocked_conversations" height="100"></canvas>	       
			      </div>
			    </div>
			  </div>
			  		
			</div>

	    <?php 
		}
	    else 
		{ 
			echo '
			<div class="card">
              <div class="card-header">
                <h4>'.$this->lang->line("Something Went Wrong").'</h4>
              </div>
              <div class="card-body">
                <div class="empty-state" data-height="400" style="height: 400px;">
                  <div class="empty-state-icon bg-danger">
                    <i class="fas fa-times"></i>
                  </div>
                  <h2>'.$this->lang->line("Something Went Wrong").'</h2>
                  <p class="lead">
                   '.$error_message.'
                  </p>
                </div>
              </div>
            </div>';
		
		} ?>
    </div>

</section>


<?php

$steps = 10;

$page_messages_new_conversations_unique_data_label = array_column($page_messages_new_conversations_unique, 'date');
$page_messages_new_conversations_unique_data_value = array_column($page_messages_new_conversations_unique, 'value');
$page_messages_new_conversations_unique_steps = (!empty($page_messages_new_conversations_unique_data_value)) ? round(max($page_messages_new_conversations_unique_data_value)/$steps) : 1;
if($page_messages_new_conversations_unique_steps==0) $page_messages_new_conversations_unique_steps = 1;


$page_messages_active_threads_unique_data_label = array_column($page_messages_active_threads_unique, 'date');
$page_messages_active_threads_unique_data_value = array_column($page_messages_active_threads_unique, 'value');
$page_messages_active_threads_unique_steps = (!empty($page_messages_active_threads_unique_data_value)) ? round(max($page_messages_active_threads_unique_data_value)/$steps) : 1;
if($page_messages_active_threads_unique_steps==0) $page_messages_active_threads_unique_steps = 1;


$page_messages_blocked_conversations_unique_data_label = array_column($page_messages_blocked_conversations_unique, 'date');
$page_messages_blocked_conversations_unique_data_value = array_column($page_messages_blocked_conversations_unique, 'value');
$page_messages_blocked_conversations_unique_steps = (!empty($page_messages_blocked_conversations_unique_data_value)) ? round(max($page_messages_blocked_conversations_unique_data_value)/$steps) : 1;
if($page_messages_blocked_conversations_unique_steps==0) $page_messages_blocked_conversations_unique_steps = 1;


$page_messages_reported_conversations_unique_data_label = array_column($page_messages_reported_conversations_unique, 'date');
$page_messages_reported_conversations_unique_data_value = array_column($page_messages_reported_conversations_unique, 'value');
$page_messages_reported_conversations_unique_steps = (!empty($page_messages_reported_conversations_unique_data_value)) ? round(max($page_messages_reported_conversations_unique_data_value)/$steps) : 1;
if($page_messages_reported_conversations_unique_steps==0) $page_messages_reported_conversations_unique_steps = 1;


$page_messages_reported_conversations_by_report_type_unique_labels = array_column($page_messages_reported_conversations_by_report_type_unique, 'date');
$page_messages_reported_conversations_by_report_type_unique_spam_values = array_column($page_messages_reported_conversations_by_report_type_unique, 'spam');
$page_messages_reported_conversations_by_report_type_unique_inappropriate_values = array_column($page_messages_reported_conversations_by_report_type_unique, 'inappropriate');
$page_messages_reported_conversations_by_report_type_unique_other_values = array_column($page_messages_reported_conversations_by_report_type_unique, 'other');
$max1= (!empty($page_messages_reported_conversations_by_report_type_unique_spam_values)) ? max($page_messages_reported_conversations_by_report_type_unique_spam_values) : 0;
$max2= (!empty($page_messages_reported_conversations_by_report_type_unique_inappropriate_values)) ? max($page_messages_reported_conversations_by_report_type_unique_inappropriate_values) : 0;
$max3= (!empty($page_messages_reported_conversations_by_report_type_unique_other_values)) ? max($page_messages_reported_conversations_by_report_type_unique_other_values) : 0;
$max_array = array($max1,$max2,$max3);
$page_messages_reported_conversations_by_report_type_unique_steps = round(max($max_array)/$steps);
if($page_messages_reported_conversations_by_report_type_unique_steps==0) $page_messages_reported_conversations_by_report_type_unique_steps = 1;


$page_messages_reported_conversations_by_report_type_pie_values = array_column($page_messages_reported_conversations_by_report_type_pie, 'value');


$page_messages_reported_vs_blocked_conversations_labels =  array_column($page_messages_reported_vs_blocked_conversations, 'date');
$page_messages_reported_vs_blocked_conversations_reported_values =  array_column($page_messages_reported_vs_blocked_conversations, 'reported');
$page_messages_reported_vs_blocked_conversations_blocked_values =  array_column($page_messages_reported_vs_blocked_conversations, 'blocked');
$max1= (!empty($page_messages_reported_vs_blocked_conversations_reported_values)) ? max($page_messages_reported_vs_blocked_conversations_reported_values) : 0;
$max2= (!empty($page_messages_reported_vs_blocked_conversations_blocked_values)) ? max($page_messages_reported_vs_blocked_conversations_blocked_values) : 0;
$max_array = array($max1,$max2,$max3);
$page_messages_reported_vs_blocked_conversations_steps = round(max($max_array)/$steps);
if($page_messages_reported_vs_blocked_conversations_steps==0) $page_messages_reported_vs_blocked_conversations_steps = 1;



$page_messages_total_messaging_connections_label = array_column($page_messages_total_messaging_connections, 'date');
$page_messages_total_messaging_connections_value = array_column($page_messages_total_messaging_connections, 'value');
$page_messages_total_messaging_connections_steps = (!empty($page_messages_total_messaging_connections_value)) ? round(max($page_messages_total_messaging_connections_value)/$steps) : 1;
if($page_messages_total_messaging_connections_steps==0) $page_messages_total_messaging_connections_steps = 1;

?>

<script>


	var page_messages_new_conversations_unique_data = document.getElementById("page_messages_new_conversations_unique").getContext('2d');
	var page_messages_new_conversations_unique_data_label = <?php echo json_encode($page_messages_new_conversations_unique_data_label); ?>;
	var page_messages_new_conversations_unique_data_value = <?php echo json_encode($page_messages_new_conversations_unique_data_value); ?>;
	var page_messages_new_conversations_unique_chart = new Chart(page_messages_new_conversations_unique_data, {
	  type: 'line',
	  data: {
	    labels: page_messages_new_conversations_unique_data_label,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Unique New Conversations"); ?>',
	      data: page_messages_new_conversations_unique_data_value,
	      borderWidth: 2,
	      borderColor: 'var(--blue)',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: 'var(--blue)',
	      pointRadius: 2
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },
	        ticks: {
	          stepSize: <?php echo $page_messages_new_conversations_unique_steps; ?>
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        }
	      }]
	    },
	  }
	});



	var page_messages_active_threads_unique_data = document.getElementById("page_messages_active_threads_unique").getContext('2d');
	var page_messages_active_threads_unique_data_label = <?php echo json_encode($page_messages_active_threads_unique_data_label); ?>;
	var page_messages_active_threads_unique_data_value = <?php echo json_encode($page_messages_active_threads_unique_data_value); ?>;
	var page_messages_active_threads_unique_chart = new Chart(page_messages_active_threads_unique, {
	  type: 'line',
	  data: {
	    labels: page_messages_active_threads_unique_data_label,
	    datasets: [{
	       label: '<?php echo $this->lang->line("Active Conversations"); ?>',
	      data: page_messages_active_threads_unique_data_value,
	      borderWidth: 2,
	      borderColor: '#63ed7a',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: '#63ed7a',
	      pointRadius: 2
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },
	        ticks: {
	          stepSize: <?php echo $page_messages_active_threads_unique_steps; ?>
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        }
	      }]
	    },
	  }
	});



	var page_messages_blocked_conversations_unique_data = document.getElementById("page_messages_blocked_conversations_unique").getContext('2d');
	var page_messages_blocked_conversations_unique_data_label = <?php echo json_encode($page_messages_blocked_conversations_unique_data_label); ?>;
	var page_messages_blocked_conversations_unique_data_value = <?php echo json_encode($page_messages_blocked_conversations_unique_data_value); ?>;
	var page_messages_blocked_conversations_unique_chart = new Chart(page_messages_blocked_conversations_unique, {
	  type: 'bar',
	  data: {
	    labels: page_messages_blocked_conversations_unique_data_label,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Blocked Conversations"); ?>',
	      data: page_messages_blocked_conversations_unique_data_value,
	      borderWidth: 2,
	      backgroundColor: 'rgba(20, 71, 118, .5)',
	      borderColor: 'rgba(20, 71, 118, .5)',
	      borderWidth: 0,
	      pointBackgroundColor: '#ffffff',
	      pointRadius: 4
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          drawBorder: false,
	          color: '#f2f2f2',
	        },
	        ticks: {
	          beginAtZero: true,
	          stepSize: <?php echo $page_messages_blocked_conversations_unique_steps; ?>
	        }
	      }],
	      xAxes: [{
	        ticks: {
	          display: false
	        },
	        gridLines: {
	          display: false
	        }
	      }]
	    },
	  }
	});



	var page_messages_reported_conversations_unique_data = document.getElementById("page_messages_reported_conversations_unique").getContext('2d');
	var page_messages_reported_conversations_unique_data_label = <?php echo json_encode(array_column($page_messages_reported_conversations_unique, 'date')); ?>;
	var page_messages_reported_conversations_unique_data_value = <?php echo json_encode(array_column($page_messages_reported_conversations_unique, 'value')); ?>;
	var page_messages_reported_conversations_unique_chart = new Chart(page_messages_reported_conversations_unique, {
	  type: 'line',
	  data: {
	    labels: page_messages_reported_conversations_unique_data_label,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Reported Conversations"); ?>',
	      data: page_messages_reported_conversations_unique_data_value,
	      borderWidth: 2,
	      backgroundColor: 'rgba(254,86,83,.3)',
	      borderColor: '#fc544b',
	      borderWidth: 1,
	      pointBackgroundColor: '#ffffff',
	      pointRadius: 2
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          drawBorder: false,
	          color: '#f2f2f2',
	        },
	        ticks: {
	          beginAtZero: true,
	          stepSize: <?php echo $page_messages_reported_conversations_unique_steps; ?>
	        }
	      }],
	      xAxes: [{
	        ticks: {
	          display: false
	        },
	        gridLines: {
	          display: false
	        }
	      }]
	    },
	  }
	});



	var page_messages_reported_conversations_by_report_type_unique = document.getElementById("page_messages_reported_conversations_by_report_type_unique").getContext('2d');
  	var page_messages_reported_conversations_by_report_type_unique_chart = new Chart(page_messages_reported_conversations_by_report_type_unique, {
	    type: 'line',
	    data: {
	      labels: <?php echo json_encode($page_messages_reported_conversations_by_report_type_unique_labels);?>,
	      datasets: [{
	        label: "<?php echo $this->lang->line('Spam');?>",
	        data: <?php echo json_encode($page_messages_reported_conversations_by_report_type_unique_spam_values);?>,
	        borderWidth: 2,
	        backgroundColor: 'rgba(63,82,227,.6)',
	        borderWidth: 0,
	        borderColor: 'transparent',
	        pointBorderWidth: 0,
	        pointRadius: 2.5,
	        pointBackgroundColor: 'rgba(63,82,227,.7)',
	        pointHoverBackgroundColor: 'rgba(63,82,227,.7)',
	      },
	      {
	        label: "<?php echo $this->lang->line('Inappropriate');?>",
	        data: <?php echo json_encode($page_messages_reported_conversations_by_report_type_unique_inappropriate_values);?>,
	        borderWidth: 2,
	        backgroundColor: 'rgba(20, 71, 118, .5)',
	        borderWidth: 0,
	        borderColor: 'transparent',
	        pointBorderWidth: 0 ,
	        pointRadius: 2.5,
	        pointBackgroundColor: 'rgba(20, 71, 118, .7)',
	        pointHoverBackgroundColor: 'rgba(20, 71, 118, .7)',
	      },
	      {
	        label: "<?php echo $this->lang->line('Others');?>",
	        data: <?php echo json_encode($page_messages_reported_conversations_by_report_type_unique_other_values);?>,
	        borderWidth: 2,
	        backgroundColor: 'rgba(254,86,83,.5)',
	        borderWidth: 0,
	        borderColor: 'transparent',
	        pointBorderWidth: 0 ,
	        pointRadius: 2.5,
	        pointBackgroundColor: 'rgba(254,86,83,.7)',
	        pointHoverBackgroundColor: 'rgba(254,86,83,.7)',
	      }
	      ]
	    },
	    options: {
	      legend: {
	        display: false
	      },
	      scales: {
	        yAxes: [{
	          gridLines: {
	            // display: false,
	            drawBorder: false,
	            color: '#f2f2f2',
	          },
	          ticks: {
	            beginAtZero: true,
	            stepSize: <?php echo $page_messages_reported_conversations_by_report_type_unique_steps; ?>,
	            callback: function(value, index, values) {
	              return value;
	            }
	          }
	        }],
	        xAxes: [{
	          gridLines: {
	            display: false,
	            tickMarkLength: 15,
	          }
	        }]
	      },
	    }
  	});



	var page_messages_reported_conversations_by_report_type_pie = document.getElementById("page_messages_reported_conversations_by_report_type_pie").getContext('2d');
    var page_messages_reported_conversations_by_report_type_pie_chart = new Chart(page_messages_reported_conversations_by_report_type_pie, {
		type: 'pie',
		data: {
			datasets: [{
				data: <?php echo json_encode($page_messages_reported_conversations_by_report_type_pie_values); ?>,
				backgroundColor: [
				'rgba(63,82,227,.7)',
				'rgba(20, 71, 118, .7)',
				'rgba(254,86,83,.7)'
				],
				label: 'Dataset 1'
			}],
			labels: [
			"<?php echo $this->lang->line('Spam');?>",
			"<?php echo $this->lang->line('Inappropriate');?>",
			"<?php echo $this->lang->line('Others');?>"
			],
		},
		options: {
			responsive: true,
			legend: {
				position: 'bottom',
			},
		}
	});



	var page_messages_reported_vs_blocked_conversations = document.getElementById("page_messages_reported_vs_blocked_conversations").getContext('2d');
  	var page_messages_reported_vs_blocked_conversations_chart = new Chart(page_messages_reported_vs_blocked_conversations, {
	    type: 'line',
	    data: {
	      labels: <?php echo json_encode($page_messages_reported_vs_blocked_conversations_labels);?>,
	      datasets: [{
	        label: "<?php echo $this->lang->line('Reported');?>",
	        data: <?php echo json_encode($page_messages_reported_vs_blocked_conversations_reported_values);?>,	        
	        borderWidth: 2,
	        backgroundColor: 'rgba(254,86,83, .5)',
	        borderWidth: 0,
	        borderColor: 'transparent',
	        pointBorderWidth: 0 ,
	        pointRadius: 2.5,
	        pointBackgroundColor: 'rgba(254,86,83, .7)',
	        pointHoverBackgroundColor: 'rgba(254,86,83, .7)'
	      },
	      {
	        label: "<?php echo $this->lang->line('Blocked');?>",
	        data: <?php echo json_encode($page_messages_reported_vs_blocked_conversations_blocked_values);?>,
	        borderWidth: 2,
	        backgroundColor: 'rgba(20, 71, 118,.5)',
	        borderWidth: 0,
	        borderColor: 'transparent',
	        pointBorderWidth: 0,
	        pointRadius: 2.5,
	        pointBackgroundColor: 'rgba(20, 71, 1187,.7)',
	        pointHoverBackgroundColor: 'rgba(20, 71, 118,.7)'
	      }]
	    },
	    options: {
	      legend: {
	        display: false
	      },
	      scales: {
	        yAxes: [{
	          gridLines: {
	            // display: false,
	            drawBorder: false,
	            color: '#f2f2f2',
	          },
	          ticks: {
	            beginAtZero: true,
	            stepSize: <?php echo $page_messages_reported_vs_blocked_conversations_steps; ?>,
	            callback: function(value, index, values) {
	              return value;
	            }
	          }
	        }],
	        xAxes: [{
	          gridLines: {
	            display: false,
	            tickMarkLength: 15,
	          }
	        }]
	      },
	    }
	});


	var page_messages_total_messaging_connections = document.getElementById("page_messages_total_messaging_connections").getContext('2d');
	var page_messages_total_messaging_connections_label = <?php echo json_encode($page_messages_total_messaging_connections_label); ?>;
	var page_messages_total_messaging_connections_value = <?php echo json_encode($page_messages_total_messaging_connections_value); ?>;
	var page_messages_total_messaging_connections_chart = new Chart(page_messages_total_messaging_connections, {
	  type: 'line',
	  data: {
	    labels: page_messages_total_messaging_connections_label,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Conversations"); ?>',
	      data: page_messages_total_messaging_connections_value,
	      borderWidth: 2,
	      borderColor: 'var(--blue)',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: 'var(--blue)',
	      pointRadius: 2
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },
	        ticks: {
	          stepSize: <?php echo $page_messages_total_messaging_connections_steps; ?>
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        }
	      }]
	    },
	  }
	});



</script>