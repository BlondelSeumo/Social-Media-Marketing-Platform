<section class="section">
  <div class="row">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-stats">
          <div class="card-stats-title"><i class="fas fa-eye"></i> <?php echo $this->lang->line("Summary"); ?>
          </div>
          <div class="card-stats-items">
            <div class="card-stats-item">
              <div class="card-stats-item-count"><?php echo $curency_icon.$payment_today; ?></div>
              <div class="card-stats-item-label"><?php echo $this->lang->line("Today"); ?></div>
            </div>
            <div class="card-stats-item">
              <div class="card-stats-item-count"><?php echo $curency_icon.$payment_month; ?></div>
              <div class="card-stats-item-label"><?php echo $this->lang->line(date("M")); ?></div>
            </div>
            <div class="card-stats-item">
              <div class="card-stats-item-count"><?php echo $curency_icon.$payment_year; ?></div>
              <div class="card-stats-item-label"><?php echo $this->lang->line("Year"); ?></div>
            </div>
          </div>
        </div>
        <div class="card-icon shadow-primary bg-info">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4><?php echo $this->lang->line("Life Time")." ".$this->lang->line("Earning"); ?></h4>
          </div>
          <div class="card-body">
            <?php echo $curency_icon.$payment_life; ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-chart">
          <canvas id="month-chart" height="80"></canvas>
        </div>
        <div class="card-icon shadow-primary bg-primary">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4><?php echo date("M - Y")." ".$this->lang->line("Earning"); ?></h4>
          </div>
          <div class="card-body">
            <?php echo $curency_icon.$payment_month; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-chart">
          <canvas id="year-chart" height="80"></canvas>
        </div>
        <div class="card-icon shadow-primary bg-warning">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4><?php echo date("Y")." ".$this->lang->line("Earning"); ?></h4>
          </div>
          <div class="card-body">
           <?php echo $curency_icon.$payment_year; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="card" style="min-height: 420px">
        <div class="card-header">
          <h4><i class="fas fa-balance-scale"></i> <?php echo $this->lang->line("Earning Comparison")." : ".$year." ".$this->lang->line("Vs")." ".$lastyear; ?></h4>
        </div>
        <div class="card-body">
          <canvas id="comparison-chart" height="158"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h4><i class="fas fa-flag-checkered"></i> <?php echo $this->lang->line("Top Countries")." : ".$year." ".$this->lang->line("Vs")." ".$lastyear; ?></h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="text-title mb-2"><?php echo $year;?></div>
              <ul class="list-unstyled list-unstyled-border list-unstyled-noborder mb-0">
                <?php
                $count=1;
                foreach ($this_year_top as $key => $value) 
                { ?>  
                  <li class="media">
                    <img class="img-fluid mt-1 img-shadow" src="<?php echo base_url(); ?>assets/modules/flag-icon-css/flags/4x3/<?php echo strtolower($key);?>.svg" alt="image" width="40">
                    <div class="media-body ml-3">
                      <div class="media-title"><?php echo isset($country_names[$key]) ? $this->lang->line($country_names[$key]) : "-"; ?></div>
                      <div class="text-small text-muted"><?php echo $curency_icon.$value; ?> <i class="fas fa-caret-down text-danger"></i></div>
                    </div>
                  </li>
                <?php 
                $count++;
                if($count==5) break;
                } ?>                
              </ul>
            </div>
            <div class="col-sm-6 mt-sm-0 mt-4">
              <div class="text-title mb-2"><?php echo $lastyear;?></div>
              <ul class="list-unstyled list-unstyled-border list-unstyled-noborder mb-0">
                <?php
                $count=1;
                foreach ($last_year_top as $key => $value) 
                { ?>  
                  <li class="media">
                    <img class="img-fluid mt-1 img-shadow" src="<?php echo base_url(); ?>assets/modules/flag-icon-css/flags/4x3/<?php echo strtolower($key);?>.svg" alt="image" width="40">
                    <div class="media-body ml-3">
                      <div class="media-title"><?php echo isset($country_names[$key]) ? $this->lang->line($country_names[$key]) : "-"; ?></div>
                      <div class="text-small text-muted"><?php echo $curency_icon.$value; ?> <i class="fas fa-caret-down text-danger"></i></div>
                    </div>
                  </li>
                <?php 
                $count++;
                if($count==5) break;
                } ?>                
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php 
$max1 = (!empty($this_year_earning)) ? max($this_year_earning) : 0;
$max2 = (!empty($last_year_earning)) ? max($last_year_earning) : 0;
$steps = round(max(array($max1,$max2))/7);
if($steps==0) $steps = 1;
?>

<script type="text/javascript">

  var month_chart = document.getElementById("month-chart").getContext('2d');

  var month_chart_bg_color = month_chart.createLinearGradient(0, 0, 0, 70);
  month_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
  month_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

  var myChart = new Chart(month_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_keys($array_month));?>,
      datasets: [{
        label: '<?php echo $this->lang->line("Earning");?>',
        data: <?php echo json_encode(array_values($array_month)) ;?>,
        backgroundColor: month_chart_bg_color,
        borderWidth: 3,
        borderColor: 'rgba(63,82,227,1)',
        pointBorderWidth: 0,
        pointBorderColor: 'transparent',
        pointRadius: 3,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,1)',
      }]
    },
    options: {
      layout: {
        padding: {
          bottom: -1,
          left: -1
        }
      },
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
            beginAtZero: true,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            drawBorder: false,
            display: false,
          },
          ticks: {
            display: false
          }
        }]
      },
    }
  });

  var year_chart = document.getElementById("year-chart").getContext('2d');

  var year_chart_bg_color = year_chart.createLinearGradient(0, 0, 0, 80);
  year_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
  year_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

  var myChart = new Chart(year_chart, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_keys($array_year));?>,
      datasets: [{
        label:  '<?php echo $this->lang->line("Earning");?>',
        data: <?php echo json_encode(array_values($array_year));?>,
        borderWidth: 2,
        backgroundColor: year_chart_bg_color,
        borderWidth: 3,
        borderColor: 'rgba(63,82,227,1)',
        pointBorderWidth: 0,
        pointBorderColor: 'transparent',
        pointRadius: 3,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,1)',
      }]
    },
    options: {
      layout: {
        padding: {
          bottom: -1,
          left: -1
        }
      },
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
            beginAtZero: true,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            drawBorder: false,
            display: false,
          },
          ticks: {
            display: false
          }
        }]
      },
    }
  });

  var ctx = document.getElementById("comparison-chart").getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode(array_values($month_names));?>,
      datasets: [{
        label: '<?php echo $year; ?>',
        data: <?php echo json_encode(array_values($this_year_earning));?>,
        borderWidth: 2,
        backgroundColor: 'rgba(63,82,227,.8)',
        borderWidth: 0,
        borderColor: 'transparent',
        pointBorderWidth: 0,
        pointRadius: 3.5,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
      },
      {
        label: '<?php echo $lastyear; ?>',
        data: <?php echo json_encode(array_values($last_year_earning));?>,
        borderWidth: 2,
        backgroundColor: 'rgba(254,86,83,.7)',
        borderWidth: 0,
        borderColor: 'transparent',
        pointBorderWidth: 0 ,
        pointRadius: 3.5,
        pointBackgroundColor: 'transparent',
        pointHoverBackgroundColor: 'rgba(254,86,83,.8)',
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
            stepSize: <?php echo $steps; ?>,
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
</script>