<?php $is_demo=$this->is_demo;?>
<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-plug"></i> <?php echo $page_title; ?></h1>    
    <div class="section-header-button">
      <a class="btn btn-primary" href="<?php echo base_url('addons/upload');?>"><i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line('Install Add-on');?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>
  <?php if($this->session->flashdata('addon_uplod_success')!="") echo "<div class='alert alert-success text-center'><i class='fa fa-check'></i> ".$this->session->flashdata('addon_uplod_success')."</div>";?>

   <div class="section-body">
      <?php 
      if(!empty($add_on_list))
      {       
        $i=0;
        echo "<div class='row'>";
        foreach($add_on_list as $value)
        {
          $i++;
          //(removing .php from controller name, that makes moduleFolder/controller name)
          $module_controller=str_replace('.php','',strtolower($value['controller_name']));
          ?>
          <div class="col-12 col-sm-6 col-md-4">
            <?php 
              $asset_path=$module_controller.'/thumb.png'; 
              $thumb = get_addon_asset($type="image",$asset_path,$css_class="img-thumbnail profile-widget-picture","",$style="");
              if($thumb=="") $thumb ='<img src="'.base_url('assets/img/addon.jpg').'" class="img-thumbnail profile-widget-picture">';
            ?>
            <div class="card profile-widget">
                <div class="profile-widget-header">
                  <?php echo $thumb; ?>
                  <div class="profile-widget-items">
                    <div class="profile-widget-item">
                      <div class="profile-widget-item-value">                        
                        <span class='badge badge-light'>v<?php echo $value["version"]; ?></span>
                      </div>
                    </div>
                    <div class="profile-widget-item">
                      <div class="profile-widget-item-value">
                        <?php 
                        if($value['installed']=="0") echo "<span class='badge badge-light'><i class='fas fa-ban'></i> ".$this->lang->line("Inactive")."</span>";
                        else echo "<span class='badge badge-light'><i class='fas fa-check-circle'></i> ".$this->lang->line("Active")."</span>"; 
                        ?> 
                      </div>
                    </div>
                  </div>
                </div>
                <div class="profile-widget-description" style="padding-bottom: 0;">
                  <div class="profile-widget-name text-center"><?php echo $value['addon_name'];?></div>
                </div>
                <div class="card-footer text-center" style="padding-top: 10px;">

                  <?php if($value['installed'] == '0'): ?>
                    <a title="<?php echo $this->lang->line("activate"); ?>" class="btn btn-outline-primary activate_action" data-i='<?php echo $i; ?>' href="" data-href="<?php echo $module_controller.'/activate';?>"><i class="fa fa-check"></i> <?php echo $this->lang->line('activate');?></a>
                  <?php endif; ?>

                  <?php if($value['installed'] == '1'): ?>
                    <a title="<?php echo $this->lang->line("deactivate"); ?>" class="<?php if($this->is_demo=='1') echo 'disabled'; ?> btn btn-outline-dark deactivate_action" href="" data-i='<?php echo $i; ?>' data-href="<?php echo $module_controller.'/deactivate';?>"><i class="fa fa-ban"></i> <?php echo $this->lang->line('deactivate');?></a>
                  <?php endif; ?>
                  <a title="<?php echo $this->lang->line("delete"); ?>" class="<?php if($this->is_demo=='1') echo 'disabled'; ?> btn btn-outline-danger delete_action" href="" data-i='<?php echo $i; ?>' data-href="<?php echo $module_controller.'/delete';?>"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete');?></a>
                </div>
              </div>
            
          </div>            
          <?php 
        }
        echo "</div>";
      }
      else
      { ?>
        <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-question"></i> <?php echo $this->lang->line("No add-on uploaded"); ?></h4>
            </div>
            <div class="card-body">
              <div class="empty-state" data-height="400" style="height: 400px;">
                <div class="empty-state-icon">
                  <i class="fas fa-question"></i>
                </div>
                <h2><?php echo $this->lang->line("System could not find any add-on."); ?></h2>
                <p class="lead">
                  <?php echo $this->lang->line("No add-on found. Your add-on will display here once uploaded."); ?>
                  
                </p>
                <a class="btn btn-primary" href="<?php echo base_url('addons/upload');?>"><i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line('Upload Add-on');?></a>
              </div>
            </div>
          </div>

        <?php
      }
      ?>   
   </div>
</section>



    



<script>
  var base_url = "<?php echo base_url(); ?>";
  var is_demo = "<?php echo $is_demo; ?>";
  $("document").ready(function(){

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;}); 

    $(".activate_action").click(function(e){ 
       e.preventDefault();
       var action = $(this).attr('data-href');
       var datai = $(this).attr('data-i');
       $("#href-action").val(action);      
       $(".put_add_on_title").html($("#get_add_on_title_"+datai).html());       
       $("#activate_action_modal_refesh").val('0');      
       $("#activate_action_modal").modal();       
    });

    $('#activate_action_modal').on('hidden.bs.modal', function () { 
      if($("#activate_action_modal_refesh").val()=="1")
      location.reload(); 
    })

    $("#activate_submit").click(function(){    
       if(is_demo=='1') 
       {
         alertify.alert('<?php echo $this->lang->line("Alert");?>','Permission denied',function(){ });
         return false;
       }        
       var action = base_url+$("#href-action").val();
       var purchase_code=$("#purchase_code").val(); 

       $("#activate_submit").addClass('disabled');
       $("#activate_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
       var loading = '<img src="'+base_url+'assets/pre-loader/color/Preloader_9.gif" class="center-block" height="30" width="30">';
       $("#activate_action_modal_msg").html(loading);

       $.ajax({
             type:'POST' ,
             url: action,
             data:{purchase_code:purchase_code},
             dataType:'JSON',
             success:function(response)
             {
                $("#activate_action_modal_msg").html('');

                if(response.status == '1')
                {
                  swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success')
                  .then((value) => {
                    location.reload();
                  });
                }
                else
                {
                  swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                }
             }
         });        
    });

    $(".deactivate_action").click(function(e){ 
       e.preventDefault();
       if(is_demo=='1') 
       {
         alertify.alert('<?php echo $this->lang->line("Alert");?>','Permission denied',function(){ });
         return false;
       } 
       var action = base_url+$(this).attr('data-href');

       swal({
            title: '<?php echo $this->lang->line("Deactive Add-on?"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to deactive this add-on? Your add-on data will still remain."); ?>',
            icon: 'error',
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) 
            {
                $.ajax({
                   type:'POST' ,
                   url: action,
                   dataType:'JSON',
                   success:function(response)
                   {
                      if(response.status == '1')
                      {
                        swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success')
                        .then((value) => {
                          location.reload();
                        });
                      }
                      else
                      {
                        swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                      }
                   }
               }); 
            } 
          });
    });


    $(".delete_action").click(function(e){ 
       e.preventDefault();
       if(is_demo=='1') 
       {
         alertify.alert('<?php echo $this->lang->line("Alert");?>','Permission denied',function(){ });
         return false;
       } 
       var action =  base_url+$(this).attr('data-href');

        swal({
            title: '<?php echo $this->lang->line("Delete Add-on?"); ?>',
            text: '<?php echo $this->lang->line("Do you really want to delete this add-on? This process can not be undone."); ?>',
            icon: 'error',
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) 
            {
                $.ajax({
                   type:'POST' ,
                   url: action,
                   dataType:'JSON',
                   success:function(response)
                   {
                      if(response.status == '1')
                      {
                        swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success')
                        .then((value) => {
                          location.reload();
                        });
                      }
                      else
                      {
                        swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                      }
                   }
               }); 
            } 
          });
    });

  

  });
</script>


<div class="modal fade" tabindex="-1" role="dialog" id="activate_action_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-check"></i> <?php echo $this->lang->line("activate");?>  <span class="put_add_on_title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">             
        
              <div id="activate_action_modal_msg" class="text-center"></div>
              <div class="form-group">
                <label>
                  <?php echo $this->lang->line("add-on purchase code");?>
                </label>
                <input type="text" class="form-control" name="purchase_code" id="purchase_code">
                <input type="hidden" id="href-action" value="">
                <input type="hidden" id="activate_action_modal_refesh" value="0">
              </div>
           
            </div>

            <div class="modal-footer bg-whitesmoke">
              <button type="button" id="activate_submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("Activate"); ?></button>
              <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
            </div>
        </div>
    </div>
</div>




<style type="text/css">
  .profile-widget .profile-widget-picture {margin-top: -25px;}
</style>