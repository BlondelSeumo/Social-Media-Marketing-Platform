<?php $is_demo=$this->is_demo;?>
<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-plug"></i> <?php echo $page_title; ?></h1>    
    <div class="section-header-button">
      <a class="btn btn-primary" href="<?php echo base_url('themes/upload');?>"><i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line('Install Theme');?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>
  <?php if($this->session->flashdata('theme_upload_success')!="") echo "<div class='alert alert-success text-center'><i class='fa fa-check'></i> ".$this->session->flashdata('theme_upload_success')."</div>";?>

   <div class="section-body">
      <?php 
      if(!empty($theme_list))
      {       
        $i=0;
        echo "<div class='row'>";
        foreach($theme_list as $value)
        {
          $i++;
          ?>
          <div class="col-12 col-sm-6 col-md-4">
            <?php 
              $asset_path=$value['thumb']; 
              $base64file = xit_theme_thumbs($asset_path);
              if($base64file=="") $thumb = base_url('assets/img/example-image.jpg');
              else $thumb = $base64file;

            ?>

            <div class="card">
              <div class="card-header">
                <h4>
                  <?php 
                    if($value['folder_name'] == $this->config->item('current_theme')) 
                      echo "<i class='fas fa-check-circle blue' title='".$this->lang->line('active')."'></i> "; 
                    echo $value['theme_name'];
                  ?>
                </h4>
              </div>
              <div class="card-body">
                <div class="chocolat-parent">
                  <a href="<?php echo $thumb; ?>" class="chocolat-image" title="<?php echo $value['theme_name'];?>">
                    <div data-crop-image="275">
                      <img alt="image" src="<?php echo $thumb; ?>" class="img-thumbnail" style="height: 250px;width:100%">
                    </div>
                  </a>
                </div>
                <div class="mb-2 text-muted"><?php echo $value['description']; ?></div>
              </div>
              <div class="card-footer text-center">
                <?php if($value['folder_name'] != $this->config->item('current_theme')): ?>
                  <a title="<?php echo $this->lang->line("activate"); ?>" class="btn btn-outline-primary activate_action" data-i='<?php echo $i; ?>' href="" data-unique-name="<?php echo $value['folder_name'];?>"><i class="fa fa-check"></i> <?php echo $this->lang->line('activate');?></a>

                <?php else: ?>
                  <a title="<?php echo $this->lang->line("deactivate"); ?>" class="<?php if($this->is_demo=='1' || count($theme_list)<=1) echo 'disabled'; ?> btn btn-outline-dark deactivate_action" href="" data-i='<?php echo $i; ?>' data-unique-name="<?php echo $value['folder_name'];?>"><i class="fa fa-ban"></i> <?php echo $this->lang->line('deactivate');?></a>
                <?php endif; ?>
                <?php if($value['folder_name'] != 'mordern'): ?>
                <a title="<?php echo $this->lang->line("delete"); ?>" class="<?php if($this->is_demo=='1') echo 'disabled'; ?> btn btn-outline-danger delete_action" href="" data-i='<?php echo $i; ?>' data-unique-name="<?php echo $value['folder_name'];?>"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete');?></a>
                <?php endif; ?>
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
              <h4><i class="fas fa-question"></i> <?php echo $this->lang->line("No Theme uploaded"); ?></h4>
            </div>
            <div class="card-body">
              <div class="empty-state" data-height="400" style="height: 400px;">
                <div class="empty-state-icon">
                  <i class="fas fa-question"></i>
                </div>
                <h2><?php echo $this->lang->line("System could not find any Theme."); ?></h2>
                <p class="lead">
                  <?php echo $this->lang->line("No Theme found. Your Theme will display here once uploaded."); ?>
                  
                </p>
                <a class="btn btn-primary" href="<?php echo base_url('themes/upload');?>"><i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line('Upload Theme');?></a>
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
       var folder_name = $(this).attr('data-unique-name');
       swal({
           title: '<?php echo $this->lang->line("Theme Activation"); ?>',
           text: '<?php echo $this->lang->line("Do you really want to activate this Theme?"); ?>',
           icon: 'info',
           buttons: true,
           dangerMode: true,
         })
         .then((willDelete) => {
           if (willDelete) 
           {
               $.ajax({
                  type:'POST' ,
                  url: base_url+"themes/active_deactive_theme",
                  data:{folder_name:folder_name,active_or_deactive:'active'},
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

    $(".deactivate_action").click(function(e){ 
       e.preventDefault();
       var folder_name = $(this).attr('data-unique-name');
       swal({
           title: '<?php echo $this->lang->line("Theme Deactivation"); ?>',
           text: '<?php echo $this->lang->line("Do you really want to deactivate this Theme? Your theme data will still remain"); ?>',
           icon: 'warning',
           buttons: true,
           dangerMode: true,
         })
         .then((willDelete) => {
           if (willDelete) 
           {
               $.ajax({
                  type:'POST' ,
                  url: base_url+"themes/active_deactive_theme",
                  data:{folder_name:folder_name,active_or_deactive:'deactive'},
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
       var folder_name = $(this).attr('data-unique-name');
       swal({
           title: '<?php echo $this->lang->line("Delete!"); ?>',
           text: '<?php echo $this->lang->line("Do you really want to delete this Theme? This process can not be undone."); ?>',
           icon: 'warning',
           buttons: true,
           dangerMode: true,
         })
         .then((willDelete) => {
           if (willDelete) 
           {
               $.ajax({
                  type:'POST' ,
                  url: base_url+"themes/delete_theme",
                  data:{folder_name:folder_name},
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