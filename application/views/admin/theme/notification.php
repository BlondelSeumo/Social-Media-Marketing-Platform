  <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" style="margin-top: 5px" class="nav-link notification-toggle nav-link-lg mr-2 <?php if(count($annoucement_data)>0) echo 'beep'; ?>"><i class="far fa-bell"></i></a>
  <div class="dropdown-menu dropdown-list dropdown-menu-right">
    <div class="dropdown-header"><?php echo $this->lang->line('Notifications'); ?>
      
      <?php 
      if(count($annoucement_data)==0)  echo '<div class="float-right">'.$this->lang->line("Nothing new").'</div>'; 
      else echo '<div class="float-right">'.count($annoucement_data)." ".$this->lang->line("New").'</div>';
      ?>     
    
    </div>
    <div class="dropdown-list-content dropdown-list-icons">

      <?php 
      foreach($annoucement_data as $row) 
      { ?>

        <a href="<?php echo site_url().'announcement/details/'.$row['id'];?>" class="dropdown-item">
          <div class="dropdown-item-icon <?php echo "bg-".$row['color_class']; ?> text-white">
            <i class="<?php echo $row['icon']; ?>"></i>
          </div>
          <div class="dropdown-item-desc">
            <?php 
              if(strlen($row['title'])>50)
              echo substr($row['title'], 0, 50)."...";
              else echo $row['title'];
            ?>
            <div class="time"><?php echo date_time_calculator($row['created_at'],true);?></div>
          </div>
        </a>
      <?php 
      } ?> 
    </div>
    <div class="dropdown-footer text-center">
      <a href="<?php echo site_url().'announcement/full_list';?>"><?php echo $this->lang->line('View all');?> <i class="fas fa-chevron-right"></i></a>
    </div>
  </div>
</li>