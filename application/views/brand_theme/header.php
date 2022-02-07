<div class="header">

	<div class="header_top">

		<div class="wrap">
		<?php 
		$logo_src= $info[0]["brand_logo"]; 
		if($logo_src!="") $logo_src=base_url("member/".$logo_src);
		else $logo_src=base_url("assets/images/logo.png");

		$logo_url= $info[0]["brand_url"]; 
		if($logo_url=="") 
		$logo_url=site_url();
		?>
			<div class="logo">
				<a href="<?php echo $logo_url;?>"><img style="height:50px" src="<?php echo $logo_src;?>"></a>
			</div>

		</div>

	</div>

</div>

<div class="header-margin"></div>