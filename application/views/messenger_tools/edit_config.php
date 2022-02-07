<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
     	<div class="box box-info">
		    	<div class="box-header">
		         <h3 class="box-title"><i class="fa fa-cogs"></i> <?php echo $this->lang->line('general settings')." : ".$this->lang->line('messenger bot');?></h3>
		        </div><!-- /.box-header -->
		       		<!-- form start -->
		       	<br>
		    <form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url().'messenger_bot/edit_config';?>" method="POST">
		        <div class="box-body">
		           
		           <div class="form-group">
		             	<label class="col-xs-12 col-sm-12 col-md-4 control-label" for="backup_mode" style="margin-top: -7px;"><?php echo $this->lang->line('give access to user to set their own facebook app');?></label>
	             		<div class="col-xs-12 col-sm-12 col-md-7">	             			
	               			<?php	
	               			$backup_mode = $this->config->item('bot_backup_mode');
	               			if($backup_mode == 1) $selected = 'yes';
	               			else $selected = 'no';
	               			$user_access['no']=$this->lang->line('no');
	               			$user_access['yes']=$this->lang->line('yes');
							echo form_dropdown('backup_mode',$user_access,$selected,'class="form-control" id="backup_mode"');  ?>		          
	             			<span class="red"><?php echo form_error('backup_mode'); ?></span>
	             		</div>
		           </div> 	

		           <div class="form-group">
		             	<label class="col-xs-12 col-sm-12 col-md-4 control-label" for=""><?php echo $this->lang->line("persistent menu copyright text");?> 
		             	</label>
	             		<div class="col-sm-9 col-md-4 col-lg-7">
	             			<?php 
		             			$persistent_menu_copyright_text=$this->config->item('persistent_menu_copyright_text'); 
	             			?>
	               			<input name="persistent_menu_copyright_text" value="<?php echo $persistent_menu_copyright_text;?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('persistent_menu_copyright_text'); ?></span>
	             		</div>
		           </div>

		           <div class="form-group">
		             	<label class="col-xs-12 col-sm-12 col-md-4 control-label" for=""><?php echo $this->lang->line("persistent menu copyright URL");?> 
		             	</label>
	             		<div class="col-sm-9 col-md-4 col-lg-7">
	             			<?php 
		             			$persistent_menu_copyright_url=$this->config->item('persistent_menu_copyright_url');
	             			?>
	               			<input name="persistent_menu_copyright_url" value="<?php echo $persistent_menu_copyright_url;?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('persistent_menu_copyright_url'); ?></span>
	             		</div>
		           </div>

		           <div class="form-group">
		             	<label class="col-xs-12 col-sm-12 col-md-4 control-label" for=""><?php echo $this->lang->line("User login type");?> 
		             	</label>
	             		<div class="col-sm-8 col-md-8 col-lg-7">
	             			<?php 
	             			if($this->config->item('has_manage_page_approval') == '') $has_manage_page_approval='1';
	             			else $has_manage_page_approval = $this->config->item('has_manage_page_approval');
	             			?>
	               			<input type="radio" name="has_manage_page_approval" value="1" <?php if($has_manage_page_approval == '1') echo 'checked'; ?> id="op1"> <label for="op1"><?php echo $this->lang->line("I want manage_page permission approval. Use plain login"); ?></label>
	               			<br/>
	               			<input type="radio" name="has_manage_page_approval" value="0" <?php if($has_manage_page_approval == '0') echo 'checked'; ?> id="op2"> <label for="op2"><?php echo $this->lang->line("I do not want manage_page permission approval. Keep user as tester of my app"); ?></label>
	             			<span class="red"><?php echo form_error('has_manage_page_approval'); ?></span>
	             		</div>
		           </div>

		           <?php if($this->is_messenger_bot_analytics_exist) { ?>
		           <div class="form-group">
		             	<label class="col-xs-12 col-sm-12 col-md-4 control-label" for=""><?php echo $this->lang->line("Enable Analytics");?> 
		             	</label>
	             		<div class="col-sm-8 col-md-8 col-lg-7">
	             			<?php 
	             			if($this->config->item('has_read_insight_approval') == '') $has_read_insight_approval='1';
	             			else $has_read_insight_approval = $this->config->item('has_read_insight_approval');
	             			?>
	               			<input type="radio" name="has_read_insight_approval" value="1" <?php if($has_read_insight_approval == '1') echo 'checked'; ?> id="op1"> <label for="op1"><?php echo $this->lang->line("I want read_insight permission approval"); ?></label>
	               			<br/>
	               			<input type="radio" name="has_read_insight_approval" value="0" <?php if($has_read_insight_approval == '0') echo 'checked'; ?> id="op2"> <label for="op2"><?php echo $this->lang->line("I do not want read_insight permission approval"); ?>(<?php echo $this->lang->line("Analytics will not work"); ?>)</label>
	             			<span class="red"><?php echo form_error('has_read_insight_approval'); ?></span>
	             		</div>
		           </div>
		       	   <?php } ?>

		     

		           
		         		               
		           </div> <!-- /.box-body --> 

		           	<div class="box-footer">
		            	<div class="form-group">
		             		<div class="col-sm-12 text-center">
		              			<button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
	              				<button  type="button" class="btn btn-default btn-lg" onclick='goBack("messenger_bot/configuration",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
		             		</div>
		           		</div>
		         	</div><!-- /.box-footer -->         
		        </div><!-- /.box-info -->       
		    </form>     
     	</div>
   </section>
</section>



