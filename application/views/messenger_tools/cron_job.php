<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-clock-o"></i> <?php echo $this->lang->line("cron job"); ?></h4>
</div>
<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
	    <?php
		if($api_key!="") { ?>
			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Download Profile Pic");?> [<?php echo $this->lang->line("every 2 hours"); ?>]
						<br><i><small><?php echo $this->lang->line("If you do not want to download subscriber profile pictures then you do not need this cron job.");?></small></i>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("messenger_bot/download_profile_pic")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>	
			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Update Profile Information");?> [<?php echo $this->lang->line("every 5 minutes"); ?>]
						<br><i><small><?php echo $this->lang->line("If you don't migrate facebook lead to bot subscirber then you do not need this cron job.");?></small>&nbsp;&nbsp;<small style="color: red;font-size: 12px;"><?php echo $this->lang->line("[ Set This Cron Job Once BOT Inboxer APP Is APPROVED & LIVE ]") ?></small></i>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("messenger_bot/update_first_name_last_name")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>			
		<?php } else echo "<p class='text-center'><a class='btn btn-lg btn-primary' href='".base_url('native_api/index')."'><i class='fa fa-key'></i> ".$this->lang->line("generate API key")."</a></";?>


   </section>
</section>
