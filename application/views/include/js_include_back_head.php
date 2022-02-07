<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/modules/chart.min.js"></script> -->
<!-- <script src="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script> -->
<script type="text/javascript">
	function search_in_ul(obj,ul_id){  // obj = 'this' of jquery, ul_id = id of the ul 
		var filter=$(obj).val().toUpperCase();
		$('#'+ul_id+' li').each(function(){
			var content=$(this).text().trim();

			if (content.toUpperCase().indexOf(filter) > -1) {
				$(this).css('display','');
			}
			else $(this).css('display','none');
		});

	}
</script>