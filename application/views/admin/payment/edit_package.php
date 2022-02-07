<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-edit"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('payment/package_manager'); ?>"><?php echo $this->lang->line("Package Manager"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">

      <form class="form-horizontal" action="<?php echo site_url().'payment/edit_package_action';?>" method="POST">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        <input name="id" value="<?php echo $value[0]["id"];?>"  class="form-control" type="hidden">              
        <input name="is_default" value="<?php echo $value[0]["is_default"];?>"  class="form-control" type="hidden">   
        
        <div class="card">
       
          <div class="card-body">
             
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="name"> <?php echo $this->lang->line("Package Name")?> *</label>
                  <input name="name" value="<?php echo $value[0]["package_name"];?>"  class="form-control" type="text">
                  <span class="red"><?php echo form_error('name'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="price"><?php echo $this->lang->line("Price")?> - <?php echo isset($payment_config[0]['currency']) ? $payment_config[0]['currency'] : 'USD'; ?> *</label>              
                    <?php 
                    if($value[0]['is_default']=="1") 
                    { ?>
                      <select name="price" id="price_default" class="form-control select2">
                          <option  value="Trial" <?php if( $value[0]["price"]=="Trial") echo 'selected="yes"'; ?>><?php echo $this->lang->line("Trial");?></option>
                          <option  value="0" <?php if( $value[0]["price"]=="0") echo 'selected="yes"'; ?>><?php echo $this->lang->line("Free");?></option>
                       </select>
                     <?php
                    }
                    else
                    { ?>
                         <input name="price" value="<?php echo $value[0]["price"];?>"  class="form-control" type="text">
                    <?php
                    }
                    ?>
                  <span class="red"><?php echo form_error('price'); ?></span>               
                </div>
              </div>
            </div>         

             <div class="form-group" id="hidden">
               <label for="price"><?php echo $this->lang->line("Validity");?> *</label>              
                <div class="row">
                  <div class="col-6">
                    <input type="text" name="validity_amount" value="<?php echo $validity_amount; ?>" class="form-control">
                  </div>
                  <div class="col-6">
                    <?php echo form_dropdown('validity_type', $validity_type, $validity_type_info, 'class="form-control select2" style="width:100%"'); ?>
                  </div>
                </div>
               <span class="red"><?php echo form_error('validity_amount'); ?></span>
              
             </div>

             <div class="row">
               <div class="col-12 col-md-6">
                 <div class="form-group">
                   <label for="visible" ><i class="fas fa-hand-holding-usd"></i>  <?php echo $this->lang->line('Available to Purchase');?></label>
                     
                     <div class="form-group">
                       <?php 
                       $visible = $value[0]["visible"];
                       if($visible == '') $visible='1';
                       ?>
                       <label class="custom-switch mt-2">
                         <input type="checkbox" name="visible" value="1" class="custom-switch-input"  <?php if($visible=='1') echo 'checked'; ?>>
                         <span class="custom-switch-indicator"></span>
                         <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                         <span class="red"><?php echo form_error('visible'); ?></span>
                       </label>
                     </div>
                 </div> 
               </div>

               <div class="col-12 col-md-6">
                 <div class="form-group" id="highlight_container">
                   <label for="highlight" ><i class="far fa-lightbulb"></i> <?php echo $this->lang->line('Highlighted Package');?></label>
                     
                     <div class="form-group">
                       <?php 
                       $highlight = $value[0]["highlight"];
                       if($highlight == '') $highlight='0';
                       ?>
                       <label class="custom-switch mt-2">
                         <input type="checkbox" name="highlight" value="1" class="custom-switch-input"  <?php if($highlight=='1') echo 'checked'; ?>>
                         <span class="custom-switch-indicator"></span>
                         <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                         <span class="red"><?php echo form_error('highlight'); ?></span>
                       </label>
                     </div>
                 </div> 
               </div>
             </div>

             <div class="form-group">
               <label for=""><?php echo $this->lang->line("Modules")?> *</label>   
                <?php $mandatory_modules = array(65,66,199,200); ?>
               <div class="table-responsive">
                  <table class="table table-bordered">
                   <?php                  
                    $current_modules=array();
                    $current_modules=explode(',',$value[0]["module_ids"]); 
                    $monthly_limit=json_decode($value[0]["monthly_limit"],true);
                    $bulk_limit=json_decode($value[0]["bulk_limit"],true);

                    echo "<tr>"; 
                        echo "<th class='info' width='20px'>"; 
                          echo $this->lang->line("#");         
                        echo "</th>";
                        echo "<th class='text-center info' width='20px'>"; 
                          echo '<input class="regular-checkbox" id="all_modules" type="checkbox"/><label for="all_modules"></label>';         
                        echo "</th>";                       
                        echo "<th class='info'>"; 
                          echo $this->lang->line("Module");         
                        echo "</th>";
                        echo "<th class='text-center info' colspan='2'>"; 
                          echo $this->lang->line("Usage Limit");         
                        echo "</th>";
                        echo "<th class='text-center info' colspan='2'>"; 
                          echo $this->lang->line("Bulk Limit");         
                        echo "</th>";
                     echo "</tr>"; 
                    
                    $SL=0;
                    foreach($modules as $module) 
                    {  
                     $SL++;
                     echo "<tr>"; 
                        echo "<td class='text-center'>".$SL."</td>";   
                        echo "<td class='text-center'>";
                        $check_module = '';
                        if(is_array($current_modules) && in_array($module['id'], $current_modules)) $check_module='checked';  ?>
                          <input  name="modules[]" id="box<?php echo $SL;?>" class="modules regular-checkbox <?php if(in_array($module['id'], $mandatory_modules)) echo 'mandatory';?>" <?php echo $check_module; ?> <?php if(in_array($module['id'], $mandatory_modules)) echo 'checked onclick="return false;"';?> type="checkbox" value="<?php echo $module['id']; ?>"/> <?php

                          $style="style='cursor:pointer;'";
                          if(in_array($module['id'], $mandatory_modules)) $style = "style='border-color:var(--blue);cursor:pointer;' title='".$this->lang->line('This is a mandatory module and can not be unchecked.')."' data-toggle='tooltip'";

                           echo "<label for='box".$SL."' ".$style."></label>";                
                        echo "</td>";

                        echo "<td>".$module['module_name']."</td>"; 

                        $xmonthly_val=0;
                        $xbulk_val=0;
                     
                        if(in_array($module["id"],$current_modules))
                        {
                          $xmonthly_val=$monthly_limit[$module["id"]];
                          $xbulk_val=$bulk_limit[$module["id"]];
                        }  

                        if($module["limit_enabled"]=='0')
                        {
                          $disabled=" readonly";
                          $limit=$this->lang->line("Unlimited");
                          $style='background:#ddd';
                        }
                        else
                        {
                            $disabled="";
                            $limit=$module['extra_text'];
                            $style='';
                        }


                        echo "<td align='center'>".$limit."</td><td align='center'><input type='number' ".$disabled." class='form-control' value='".$xmonthly_val."' min='0' style='width:70px; ".$style."' name='monthly_".$module['id']."'></td>";
                      
                        if($module["bulk_limit_enabled"]=="0")
                        {
                          $disabled=" readonly";
                          $limit="";
                          $style='background:#ddd';

                        }
                        else
                        {
                            $disabled="";
                            $limit="";
                            $style='';
                        }
                        $xval=$xbulk_val;

                        echo "<td align='center'><input type='number' class='form-control' ".$disabled." value='".$xval."'  min='0' style='width:70px; ".$style."' name='bulk_".$module['id']."'></td>";
                      echo "</tr>";                 
                    }                
                    ?>            
                  </table> 
               </div>      
               <span class="red" ><?php echo "<br/><br/>".form_error('modules[]'); ?></span>
             </div>    
          </div>
          <div class="card-footer bg-whitesmoke">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("payment/package_manager",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>  
    </div>
  </div>
</section>

          


<script type="text/javascript">
  $(document).ready(function() {
    if($("#price_default").val()=="0") $("#hidden").hide();
    else $("#validity").show();
    $("#all_modules").change(function(){
      if ($(this).is(':checked')) 
      $(".modules:not(.mandatory)").prop("checked",true);
      else
      $(".modules:not(.mandatory)").prop("checked",false);
    });
    $("#price_default").change(function(){
      if($(this).val()=="0") $("#hidden").hide();
      else $("#hidden").show();
    });
  });
</script>

<style type="text/css" media="screen">
  table label{margin-top: 10px;}
</style>