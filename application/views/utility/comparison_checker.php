<style type="text/css">
	.waiting {height: 100%;width:100%;display: table;}
	.waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:10px 0;}
	@media (max-width: 575.98px) { 
	
	 .card .card-stats .card-stats-item {
	     padding: 5px 10px!important;
	 }
	 .card .card-stats .card-stats-item .card-stats-item-count{
	 	font-size: 12px!important;
	 }

	}
</style>
<section class="section section_custom">
	<div class="section-header">
		<h1><i class="far fa fa-globe"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url('search_tools/index'); ?>"><?php echo $this->lang->line('Search Tools'); ?></a></div>
			<div class="breadcrumb-item"><?php echo $this->lang->line('Website Comparison'); ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card main_card">
			
					<div class="card-body">
					 	
						<div class="row">
						  <div class="form-group col-12 col-md-5" style="padding: 10px;">
						    <label>
						       <?php echo $this->lang->line("Website"); ?>
						    </label>
						    <input class="form-control"  id="domain_name1" autocomplete="off" type="text" placeholder="https://example.com">                 
						  </div>  
						  <div class="form-group col-12 col-md-5" style="padding: 10px;">
						    <label>
						      <?php echo $this->lang->line("Competitor Website"); ?>
						    </label>
						    <input type="text" autocomplete="off" id="domain_name2" class="form-control" placeholder="https://example2.com">                      
						  </div> 

						  <div class="form-group col-12 col-md-2 text-center" style="padding: 10px;margin-top: 28px">
						  	<button class="btn btn-lg btn-primary action_button" style="padding: 0.55rem 2.5rem;"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
						  </div>
						  
						</div> 
						




					</div>
				</div>
			</div>
		</div>
		<div id="custom_spinner"></div>
		<div class="row">
			

			<div class="col-lg-6 col-md-6 col-sm-12 one">

			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 two">
			</div>

			
		</div>

	</div>
</section>




<script>
	var base_url="<?php echo site_url(); ?>";
	$("document").ready(function()	{
	



		$(document).on('click', '.action_button', function(event) {
		    event.preventDefault();

		    var url1 = $("#domain_name1").val();
		    var url2 = $("#domain_name2").val();
		    $("#custom_spinner").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div><br/>');
		    $.ajax({
		    	type:'POST' ,
		    	url:"<?php echo site_url();?>search_tools/comparison_action",
		    	data: {url1:url1,url2:url2},
		    	dataType:"json",
		    	success:function(response){

		    		if (response.empty == 'empty' && response.empty1 == 'empty1') {
		    				swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please Enter URL '); ?>", 'error');
		    				$("#custom_spinner").html("");
		    				return false;
		    		}
		    		if(response.status == '0'){
		    			swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('limit has been exceeded. you can no longer use this feature. '); ?>", 'error');
		    		}
		    		$("#custom_spinner").html("");
		    		$(".one").html(response.output1);
		    		$(".two").html(response.output2);
		    		if(response.empty1 == 'empty1')
		    			$('.two').html("");
		    		if(response.empty == 'empty')
		    			$(".one").html("");


		    	}
		    });
		   

		});




	});
</script>


