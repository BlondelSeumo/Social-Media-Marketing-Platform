<style>	

	/*vidcaster dashboard*/

	#msg_dashboard

	{

		margin-bottom: 10px;

	}

	#dashboard-top {

	    padding-top: 15px;

	    margin-top: 0px;

	    padding-bottom: 10px;

	    /*margin-bottom: 10px;*/

	}



	#dashboard-top .cmn {

	    position: relative;

	    height: 140px;

	    margin: 0 0 15px 0;

	    border-radius: 5px;

	    width: 100%;

	    float: left;

	}



	#dashboard-top .cmn .info {

	    color: #fff;

	    margin: 34px 15px 10px 0;

	    display: block;

	    position: relative;

	    text-align: right;

	    font-size: 51px;

	}   



	#dashboard-top .cmn .info span {

	    font-size: 12px;

	    margin-left: 3px;

	}   



	#dashboard-top .cmn .short-info {

	    text-align: center;

	    font-size: 14px;

	    margin-top: 0px;

	    z-index: 3;

	    color: #00AAA0;

	    position: absolute;

	    bottom: -17px;

	    width: 100%;

	    text-align: center;

	}   





	.top-icon {

	    position: absolute;

	    top: 15px;

	    left: 0;

	    width: 100%;

	    text-align: center;

	}



	.first-circle {

	    width: 100px;

	    height: 100px;

	    border-radius: 50%;

	    background: none;

	    margin-left: 10px;

	    padding: 10px 0;

	    display: block;

	}



	.second-circle {

	    width: 80px;

	    height: 80px;

	    border-radius: 50%;

	    background: #fff;

	    margin: 0 auto;

	    padding: 10px 0;

	}



	.third-circle {

	    /*background: #00AAA0;    */

	    width: 60px;

	    height: 60px;

	    border-radius: 50%;

	    margin: 0 auto;

	    text-align: center;

	    padding-top: 17px;

	}   





	.third-circle i {

	    color: #fff;

	    font-size: 26px;

	}   



	.more-info {

	    position: absolute;

	    bottom: -20px;

	    left: 0;

	    width: 100%;

	    text-align: center;

	    z-index: 0;

	}   



	.more-info a {

	    height: 40px;

	    cursor: default;

	    width: 220px;

	    padding: 10px 15px;

	    background: #fff;

	    color: #333333;

	    margin: 0 auto;

	    display: block;

	    text-align: center; 

	    border-radius: 20px;

	}   



	.transparent_background {

	    background-color: rgba(136, 211, 232, 0.2) !important; 

	    color: white;

	}



	.circle-square {

	    background: rgba(136, 211, 232, 0.2);

	    background: #0073b7;

	    margin-bottom: 20px;

	    height: 85px;

	    position: relative;

	    width: 83%;

	    margin-left: 17%;

	}



	.circle-square.b {

	    background: #dd4b39;

	}



	.circle-square.c {

	    background: #F79738;

	}



	.circle-square.d {

	    background: #00c0ef;

	}



	.circle-square .icon-div {

	    position: absolute;

	    top: 0px;

	    left: -17%;    

	    background: #0073b7;

	    border: 5px solid #fff;

	    width: 85px;

	    height: 85px;

	    border-radius: 50%;

	    text-align: center;

	    z-index: 3;

	}



	.circle-square.b .icon-div {

	    background: #dd4b39;

	}



	.circle-square.c .icon-div {

	    background: #F79738;

	}



	.circle-square.d .icon-div {

	    background: #00c0ef;

	}



	.circle-square .icon-div i {

	    font-size: 30px;

	    padding-top: 22px;

	    color: #fff;

	}



	.circle-square .stat-div {

	    position: absolute;

	    top: 10px;

	    right: 10px;

	    color: #fff;

	    font-size: 23px;

	}



	.circle-square .title-div {

	    position: absolute;

	    bottom: 10px;

	    right: 10px;

	    color: #fff;

	    font-size: 17px;

	}

	.dashboard-title {

		background: #fff;

		padding: 15px;

		/*color: #fff;*/

		text-align: center;

		border-radius: 3px;

	}	

</style>



<?php 

$themecolorcode="#607D8B";

$color1="#607D77";

$color2="#607D8B";

$color3="#999999";

$color4="#504C43";



if($loadthemebody=='skin-black')        { $themecolorcode="#1A2226"; $color1="#6C7A7D"; $color2="#55676A"; $color3="#303F42"; $color4="#222D32"; }



if($loadthemebody=='skin-blue-light')   { $themecolorcode="#397CA5"; $color1="#6497B1"; $color2="#005B96"; $color3="#03396C"; $color4="#011F4B"; }

if($loadthemebody=='skin-blue')         { $themecolorcode="#397CA5"; $color1="#6497B1"; $color2="#005B96"; $color3="#03396C"; $color4="#011F4B"; }



if($loadthemebody=='skin-green-light')  { $themecolorcode="#00A65A"; $color1="#49AB81"; $color2="#419873"; $color3="#398564"; $color4="#317256"; }

if($loadthemebody=='skin-green')        { $themecolorcode="#00A65A"; $color1="#49AB81"; $color2="#419873"; $color3="#398564"; $color4="#317256"; }



if($loadthemebody=='skin-purple-light') { $themecolorcode="#545096"; $color1="#572985"; $color2="#402985"; $color3="#292985"; $color4="#22226E"; }

if($loadthemebody=='skin-purple')       { $themecolorcode="#545096"; $color1="#572985"; $color2="#402985"; $color3="#292985"; $color4="#22226E"; }



if($loadthemebody=='skin-red-light')    { $themecolorcode="#DD4B39"; $color1="#FF5733"; $color2="#E53935"; $color3="#C70039"; $color4="#9E1B08"; }

if($loadthemebody=='skin-red')          { $themecolorcode="#DD4B39"; $color1="#FF5733"; $color2="#E53935"; $color3="#C70039"; $color4="#9E1B08"; }



if($loadthemebody=='skin-yellow-light') { $themecolorcode="#F39C12"; $color1="#FFCF75"; $color2="#FFB38A"; $color3="#FF9248"; $color4="#FDA63A"; }

if($loadthemebody=='skin-yellow')       { $themecolorcode="#F39C12"; $color1="#FFCF75"; $color2="#FFB38A"; $color3="#FF9248"; $color4="#FDA63A"; }



$gender_type_data[0]['color'] = $color1;

$gender_type_data[0]['highlight'] = $color1;

$gender_type_data[1]['color'] = $color4;

$gender_type_data[1]['highlight'] = $color4;



?>



<div class="well well_border_left">

	<h4 class="text-center"><i class='fa fa-dashboard'></i> <?php echo $this->lang->line("dashboard"); ?></h4>

</div>



<div class="container-fluid">



	<div  class="row" id="msg_dashboard">		

		<div id='dashboard-top'>

			<div class="col-xs-12 col-sm-6 col-md-3">

				<div class=" cmn bg-a">

					<div class='top-icon'>

						<div class='first-circle'>

							<div class='second-circle'>

								<div class='third-circle'>

									<i class='fa fa-android'></i>

								</div>	

							</div>

						</div>

					</div>

					<h3 class='info'><?php echo $total_enabled_bot; ?></h3>

					<h5 class='short-info'><?php echo $this->lang->line('Bot enabled') ?></h5>

					<div class='more-info'><a></a></div>				

				</div>

			</div>

			

			<div class="col-xs-12 col-sm-6 col-md-3">

				<div class="cmn bg-b">

					<div class='top-icon'>

						<div class='first-circle'>

							<div class='second-circle'>

								<div class='third-circle bg-b'>

									<i class='fa fa-bars'></i>

								</div>	

							</div>

						</div>

					</div>

					<h3 class='info'><?php echo $total_enabled_persistent_menu; ?></h3>

					<h5 class='short-info'><?php echo $this->lang->line('Persistent menu enabled') ?></h5>

					<div class='more-info'><a></a></div>				

				</div>

			</div>

			

			

			<div class="col-xs-12 col-sm-6 col-md-3">

				<div class=" cmn bg-c">

					<div class='top-icon'>

						<div class='first-circle'>

							<div class='second-circle'>

								<div class='third-circle bg-c'>

									<i class='fa fa-users'></i>

								</div>	

							</div>

						</div>

					</div>

					<h3 class='info'><?php echo $total_subscribers; ?></h3>

					<h5 class='short-info'><?php echo $this->lang->line('Total subscribers') ?></h5>

					<div class='more-info'><a></a></div>				

				</div>

			</div>

			

			<div class="col-xs-12 col-sm-6 col-md-3">

				<div class="cmn bg-d">

					<div class='top-icon'>

						<div class='first-circle'>

							<div class='second-circle'>

								<div class='third-circle bg-d'>

									<i class='fa fa-envelope'></i>

								</div>	

							</div>

						</div>

					</div>

					<h3 class='info'><?php echo $total_emails; ?></h3>

					<h5 class='short-info'><?php echo $this->lang->line('Total email(QuickReply)') ?></h5>

					<div class='more-info'><a></a></div>				

				</div>

			</div>

		</div>

	</div>

	<br/>


	<div class="row">

		<div class="col-xs-12 col-sm-12 col-md-6">

			<!-- AREA CHART -->

			<div class="box box-primary">

				<div class="box-header with-border">

				<h3 class="box-title"><i class="fa fa-group"></i> <?php echo $this->lang->line('Total bot subscribers report for last 30 days'); ?></h3>

					<div class="box-tools pull-right">

						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>

					</div>

				</div>

				<div class="box-body">

					<div class="chart">

						<div class="chart" id="div_for_line" style="height: 300px;"></div>

					</div>

				</div><!-- /.box-body -->

			</div><!-- /.box -->

		</div>

		<?php

			$line = $total_subscribers_data;	

		?>



		<div class="col-xs-12 col-sm-12 col-md-6">

			<!-- AREA CHART -->

			<div class="box box-primary">

				<div class="box-header with-border">

				<h3 class="box-title"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('Daywise email gain (QuickReply) for last 30 days'); ?></h3>

					<div class="box-tools pull-right">

						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>

					</div>

				</div>

				<div class="box-body">

					<div class="chart">

						<div class="chart" id="div_for_line_email" style="height: 300px;"></div>

					</div>

				</div><!-- /.box-body -->

			</div><!-- /.box -->

		</div>

	</div>

	<?php

		$day_wise_total_email = $day_wise_total_email;	

	?>

	



</div>

<?php 

    

    $male_subscriber = $this->lang->line("Male subscriber");

    $female_subscriber = $this->lang->line("Female subscriber");

    $subscriber = $this->lang->line("Subscribers");

    $quickreply_email = $this->lang->line("QuickReply email gain");



?>

<script>

	$j("document").ready(function(){

		

		var subscriber = "<?php echo $subscriber; ?>";

		Morris.Line({

	  		element: 'div_for_line',

	  		data: <?php echo json_encode($line); ?>,

	  		xkey: 'date',

	  		ykeys: ['subscribers'],

	  		labels: [subscriber],

	  		lineColors: ['#3c8dbc'],

	        hideHover: 'auto',

	        lineWidth: 1

		});



		var quickreply_email = "<?php echo $quickreply_email; ?>";

		Morris.Line({

	  		element: 'div_for_line_email',

	  		data: <?php echo json_encode($day_wise_total_email); ?>,

	  		xkey: 'date',

	  		ykeys: ['emails'],

	  		labels: [quickreply_email],

	  		lineColors: ['#FF8000'],

	        hideHover: 'auto',

	        lineWidth: 1

		});



	});

</script>