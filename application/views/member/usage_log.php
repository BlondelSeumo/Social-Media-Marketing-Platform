<?php $cur_month=date("n"); ?>
<?php $cur_year=date("Y"); ?>
<?php 
if($cur_month==1) $month="";
else if($cur_month==2) $month="cal_jan";
else if($cur_month==3) $month="cal_feb";
else if($cur_month==4) $month="cal_ma";
else if($cur_month==5) $month="cal_apr";
else if($cur_month==6) $month="cal_may";
else if($cur_month==7) $month="cal_jun";
else if($cur_month==8) $month="cal_jul";
else if($cur_month==9) $month="cal_aug";
else if($cur_month==10) $month="cal_sep";
else if($cur_month==11) $month="cal_oct";
else if($cur_month==12) $month="cal_nov";
$unlimited_module_array=array();
?>

<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-user-clock"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("Payment"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></a></div>
		</div>
	</div>

	<div class="section-body">

       <div class="row">
         <div class="col-md-4 col-sm-4 col-12">
           <div class="card card-statistic-1">
             <div class="card-icon bg-primary">
               <i class="fas fa-shopping-bag"></i>
             </div>
             <div class="card-wrap">
               <div class="card-header">
                 <h4><?php echo $this->lang->line("Package Name")?></h4>
               </div>
               <div class="card-body"> 
                 <?php echo $package_name;?>
               </div>
             </div>
           </div>
         </div>
         <div class="col-md-4 col-sm-4 col-12">
           <div class="card card-statistic-1">
             <div class="card-icon bg-warning">
               <i class="fas fa-dollar-sign"></i>
             </div>
             <div class="card-wrap">
               <div class="card-header">
                 <h4><?php echo $this->lang->line("Price"); ?></h4>
               </div>
               <div class="card-body">
                  <?php if($price=="Trial") $price=0; ?>
                  <?php echo $curency_icon; ?> <?php echo $price;?> / 
                  <?php echo $validity;?> <?php echo $this->lang->line("Days")?> 
               </div>
             </div>
           </div>
         </div>
         <div class="col-md-4 col-sm-4 col-12">
           <div class="card card-statistic-1">
             <div class="card-icon bg-danger">
               <i class="fas fa-stopwatch"></i>
             </div>
             <div class="card-wrap">
               <div class="card-header">
                 <h4><?php echo $this->lang->line('Expiry Date'); ?></h4>
               </div>
               <div class="card-body">                 
                 <?php echo date("d M Y",strtotime($this->session->userdata("expiry_date"))); ?>      
               </div>
             </div>
           </div>
         </div>
       </div>

	  <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-th-list"></i> <?php echo $this->lang->line('Usage Details'); ?></h4>
          </div>
          <div class="card-body">

            <div class="table-responsive">
            	<table class="table table-bordered table-condensed">
            		<tr>
            			<th></th>
            			<th><?php echo $this->lang->line("Modules");?></th>
            			<th class="text-center"><?php echo $this->lang->line("Limit");?></th>
            			<th class="text-center"><?php echo $this->lang->line("Used");?></th>
            		</tr>
            		<?php 
            		$no_limit_modules=array();
            		$not_monthly_modules=array();
                        $bulk_limit_modules=array();
            		foreach($info as $module) 
            		{
            			if($module['limit_enabled']=='0') $no_limit_modules[]=$module['module_id'];
            			if($module['extra_text']=='') $not_monthly_modules[]=$module['module_id'];
                              if($module['bulk_limit_enabled']=='1') $bulk_limit_modules[]=$module['module_id'];
            		}
            		$i=0;

            		foreach($info as $row)
            		{
            			$i++;
            			$row_class="";
            			if(in_array($row["module_id"],$this->module_access)) $row_class="allowed";
            			echo "<tr class='".$row_class."'>";
            			echo "<td class='text-center'>";
            			echo $i;
            			echo "</td>";
            			echo "<td>";
            			echo $row["module_name"];
            			echo "</td>";

            			$str="";
            			if(!in_array($row["module_id"],$this->module_access)) // no access and skip
            			{
            				$str="<i class='fa fa-remove'></i>";
            				echo "<td colspan='3' class='text-center'>{$str}</td>";
            				echo "</tr>";
            				continue;
            			}


            			if(in_array($row["module_id"], $no_limit_modules))
            			{
            				echo "<td class='text-center'>-</td>";
            			}
            			else
            			{
            				$bulk_limit_print=$bulk_limit[$row["module_id"]];
            				if($bulk_limit_print==0) $bulk_limit_print=$this->lang->line("Unlimited");

            				echo "<td class='text-center'>";
            				if($monthly_limit[$row["module_id"]]=="0") $monthly_limit[$row["module_id"]]=$this->lang->line("Unlimited");
            				if(isset($monthly_limit[$row["module_id"]])) 
            				{
            					echo $monthly_limit[$row["module_id"]];
            					if($monthly_limit[$row["module_id"]]>0 && $row["extra_text"]!="") echo " / ".$this->lang->line($row["extra_text"]);
            					if(in_array($row["module_id"],$bulk_limit_modules)) echo " [".$this->lang->line('Bulk Limit')." : ". $bulk_limit_print."]";
            				}
            				echo "</td>";
            			}

            			echo "<td class='text-center' >";

            			if($row["extra_text"]=="") // not monthly modules
            			{
            				if(isset($not_monthy_module_info[$row["module_id"]])) echo $not_monthy_module_info[$row["module_id"]];
            				else echo "0";
            			}
            			else if(in_array($row["module_id"], $no_limit_modules))
            			{
            				echo "-";
            			}
            			else
            			{
            				if($str!="") echo $str;
            				else
            				{
            					if(isset($row["usage_count"])) echo $row["usage_count"];
            					else echo "0";
            				}
            			}
            			echo "</td>";
            			echo "</tr>";
            			} ?>
            	</table>  
            </div>
          </div>
        </div>
		                    
	</div>
</section>
