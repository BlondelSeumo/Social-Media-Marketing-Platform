<div class="container-fluid">

  <?php if($this->session->flashdata('per_success')===1) { ?>
    <br/><div class="alert alert-success text-center fade_out" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been created successfully.");?></div>
  <?php } ?>

  <?php if($this->session->flashdata('per_success')===0) { ?>
    <br/><div class="alert alert-danger text-center fade_out"><i class="fa fa-remove"></i> <?php echo $this->session->flashdata('per_message');?></div>
  <?php } ?>

  <?php if($this->session->flashdata('per_update_success')===1) { ?>
    <br/><div class="alert alert-success text-center fade_out" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been updated successfully.");?></div>
  <?php } ?>

  <?php if($this->session->flashdata('menu_success')===1) { ?>
    <br/><div class="alert alert-success text-center fade_out" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been published successfully.");?></div>
  <?php } ?>

  <?php if($this->session->flashdata('perrem_success')===1) { ?>
    <br/><div class="alert alert-success text-center fade_out" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been removed successfully.");?></div>
  <?php } ?>

  <?php if($this->session->flashdata('perrem_success')===0) { ?>
    <br/><div class="alert alert-danger text-center fade_out"><i class="fa fa-remove"></i> <?php echo $this->session->flashdata('perrem_message');?></div>
  <?php } ?>

  <?php if($this->session->flashdata('bot_action')!='') { ?>
    <br/><div class="alert alert-info text-center fade_out"><i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('bot_action');?></div>
  <?php } ?>


  <?php $areyousure=$this->lang->line("are you sure"); ?>
  <?php 
    $started_button_enabled='';
    $started_button_enabled_msg="";
    if($page_info["started_button_enabled"]=='0')
    {
      $started_button_enabled=' disabled';
      $started_button_enabled_msg="<p style='text-decoration:none;'>".$this->lang->line("To create persistent menu you must enable get started button first. You can enable it going to 'Get Started Settings' menu from the left menu list.")."</p>";
    }        
   ?>
  <?php if($started_button_enabled_msg!="") echo "<div class='alert alert-danger text-center'>".$started_button_enabled_msg."</div>";?>
  <div class="box box-widget widget-user-2" >
    <div class="widget-user-header" style="border-radius: 0;">
      <div class="row">
        <?php if($iframe != '1') : ?>
        <div class="col-12 col-md-6">
          <div class="widget-user-image">
            <img class="img-circle" src="<?php echo $page_info['page_profile'];?>">
          </div>
          <h3 class="widget-user-username" style="margin-top:20px;"><a target="_BLANK" href="https://facebook.com/<?php echo $page_info['page_id'];?>"><?php echo $page_info['page_name'];?></a></h3>
        </div> 
        <?php endif; ?>    
        <div class="col-12 col-md-6">
           <?php 
           $createurl = base_url('messenger_bot/create_persistent_menu/'.$page_info['id']);
           if(isset($iframe) && $iframe=='1') $createurl.='/1';
           ?>
           <a class="btn btn-primary float-right <?php echo $started_button_enabled;?>" href="<?php echo $createurl;?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Create Persistent Menu');?></a>
        </div>       
      </div>
    </div>
    <div class="box-footer">

      <div class="row">
        <div class="col-12">
           <?php
             $action_menu = '';
             $publish_disabled='';
             $disabled_msg=$this->lang->line("menu settings for default locale is mandatory."); 
             if(!empty($menu_info)) 
             {             
              $local_array=array();
              foreach ($menu_info as $key => $value) 
              {
                array_push($local_array, $value['locale']);
              }
              if(!in_array('default', $local_array)) 
              {
                $publish_disabled='disabled';
              } 

              $action_menu = '<a href="#" data-toggle="dropdown" class="btn btn-outline-primary dropdown-toggle no_caret float-right"><i class="fas fa-caret-left"></i> '.$this->lang->line("Options").'</a> <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><div class="dropdown-title">'.$this->lang->line("Actions").'</div> <li><a class="dropdown-item has-icon" title="'.$disabled_msg.'" href="'.base_url('messenger_bot/publish_persistent_menu/'.$page_info['id'].'/'.$iframe).'" id="publish_menu"><i class="fa fa-check green"></i> '.$this->lang->line("Publish Persistent Menu").'</a></li><li><a class="dropdown-item has-icon remove_persistent_menu" href="'.base_url('messenger_bot/remove_persistent_menu/'.$page_info['id'].'/'.$iframe).'"><i class="fa fa-unlink red"></i> '.$this->lang->line('Remove Persistent Menu').'</a></li></ul>';

             } 
           ?>
        </div>      
      </div>
      <br>

      <?php 
        echo "<div class='table-responsive data-card'>";
          echo "<table class='table table-bordered table-condensed' id='mytable'>";
            echo "<thead>";
              echo "<tr>";
                echo "<th class='text-center'>".$this->lang->line("SN")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Locale")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Composer Input Disabled?")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
              echo "</tr>";
            echo "</thead>";

            echo "<tbody>";
              $i=0;      
              foreach ($menu_info as $key => $value) 
              {
                $i++;
                $composer_input=$this->lang->line('no');
                if($value['composer_input_disabled']=='1') $composer_input=$this->lang->line('yes');

                echo "<tr>";
                  echo "<td class='text-center'>".$i."</td>";
                  echo "<td class='text-center'>".$value['locale']."</td>";                  
                  echo "<td class='text-center'>".$composer_input."</td>";
                  echo "<td class='text-center'>";

                    $delete_class="dropdown-item has-icon delete_persistent_menu red";
                    $delete_href=base_url("messenger_bot/remove_persistent_menu_locale/".$value['id']."/".$page_info['id']."/".$iframe);
                    $delete_title=$this->lang->line("delete");
                    if($value['locale']=='default') 
                    {
                      $delete_class="dropdown-item has-icon delete_persistent_menu gray";
                      $delete_href="#";
                      $delete_title=$this->lang->line("Default persistent menu can not be deleted");
                    }

                    $editurl = base_url("messenger_bot/edit_persistent_menu/".$value['id']."/".$iframe);
                    echo '<a href="#" data-toggle="dropdown" class="btn btn-outline-primary btn-circle dropdown-toggle bot_actions no_caret"><i class="fas fa-briefcase"></i></a>                     
                    <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                     <div class="dropdown-title">'.$this->lang->line("Actions").'</div>
                     <li><a class="dropdown-item has-icon" href="'.$editurl.'"><i class="fas fa-edit"></i> '.$this->lang->line("Edit Persistent Menu").'</a></li>
                     <div class="dropdown-divider"></div>
                     <li><a class="'.$delete_class.'" href="'.$delete_href.'" title="'.$delete_title.'"><i class="fas fa-trash-alt"></i> '.$this->lang->line("Delete Persistent Mneu").'</a></li>
                    </ul>';

                  echo "</td>";
                echo "</tr>";
              }
            echo "</tbody>";
          echo "</table>";
        echo "</div>";      
      ?>
    </div>
  </div>

</div>



<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";
  var action_menu = '<?php echo $action_menu;?>';
  setTimeout(function(){ 
    $("#mytable_filter").append(action_menu);
  }, 1000);

  setTimeout(function(){ 
    $(".fade_out").fadeOut();
  }, 3000);

  var table = $("#mytable").DataTable({
      language: 
      {
        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
      },
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      columnDefs: [
        {
            targets: [3],
            sortable: false
        }
      ]
  });

  $(document).ready(function(){
    $("#publish_menu").click(function(e){
      var publish_disabled="<?php echo $publish_disabled;?>";
      if(publish_disabled=='disabled')
      {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $disabled_msg;?>",function(){});
        e.preventDefault();
      }
    });


    $(document).on('click','.remove_persistent_menu',function(e){
      e.preventDefault();
      var link = $(this).attr("href");
      swal({
        title: '<?php echo $this->lang->line("Warning!"); ?>',
        text: '<?php echo $this->lang->line("Are you sure that you want to remove persistent menu from Facebook?"); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          window.location.href = link;
        } 
      });
    });

    $(document).on('click','.delete_persistent_menu',function(e){
      e.preventDefault();
      var link = $(this).attr("href");
      var title = $(this).attr("title");
      if(link == '#')
      {
        swal('<?php echo $this->lang->line("Warning!"); ?>', title, 'warning');
        return;
      }
      swal({
        title: '<?php echo $this->lang->line("Warning!"); ?>',
        text: '<?php echo $this->lang->line("Do you really want to remove this item?"); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          window.location.href = link;
        } 
      });
    });

  });
</script>

