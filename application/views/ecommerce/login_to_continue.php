<p>
  <div class="alert alert-danger text-center">
    <?php echo $this->lang->line("You either have to purchase inside Facebook Messenger or have to login.")."<br><br><a href='' id='login_form' class='pointer btn btn-primary'>".$this->lang->line("Login to continue")."</a>"; ?>
  </div>
</p>

<script>
	var base_url="<?php echo site_url(); ?>";
	$("document").ready(function(){      
	});
</script>

<?php include(APPPATH."views/ecommerce/common_style.php"); ?>