<section class="section section_custom">
  <?php 
    if($this->session->flashdata('success_message')==1)
    echo "<div class='alert alert-success text-center'><i class='fas fa-check-circle'></i> ".$this->lang->line("Your data has been successfully stored into the database.")."</div><br>";
  ?>

  <div class="section-body">
    <div class="row mt-3">
      <div class="col-12">
          <form action="<?php echo base_url("ecommerce/business_hour_settings_action"); ?>" method="POST">
            <div class="card no_shadow">
              <div class="card-body p-0">
                <div class="form-group pl-1">
                  <label for=""><?php echo $this->lang->line('Always Open');?></label>
                  <br>
                  <?php 
                  $always_open =empty($xvalue)?"1":"0";
                  ?>
                  <label class="custom-switch mt-2">
                    <input type="checkbox" name="always_open" value="1" class="custom-switch-input"  <?php if($always_open=='1') echo 'checked'; ?>>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                    <span class="red"><?php echo form_error('always_open'); ?></span>
                  </label>
                </div>

                <div id="schedule">
                  <?php
                  $i=0;
                  foreach ($days_list as $key => $value)
                  { ?>
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="min-width:60px;max-width: 200px;"><?php echo $this->lang->line($value); ?></span></div>
                        <input type="hidden" name="schedule_day[]" value="<?php echo $key;?>">
                        <?php

                          if(isset($xvalue[$i]['start_time']) && isset($xvalue[$i]['end_time']))
                          {
                            $default_start = $xvalue[$i]['start_time'];
                            $default_end = $xvalue[$i]['end_time'];
                          }
                          else
                          {
                             $default_start='10:00';
                            $default_end='20:00';
                          }


                          $days_time_list[''] = $this->lang->line("OFF Day");
                          echo form_dropdown('start_time[]', $days_time_list,$default_start,'class="form-control d-inline start_time" style="max-width200px !important"');
                          echo '<div class="input-group-prepend"><span class="input-group-text">'.$this->lang->line("to").'</span></div>';
                          echo form_dropdown('end_time[]', $days_time_list,$default_end,'class="form-control d-inline end_time" style="max-width200px !important"');
                        ?>
                      </div>
                    </div>                 
                  <?php $i++;
                  }
                  ?> 
                </div>                  
              </div>

              <div class="card-footer p-0">
                <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
              </div>
            </div>
          </form>
      </div>
    </div>
  </div>
</section>


<script type="text/javascript">
  var always_open = "<?php echo $always_open;?>";
  $(document).ready(function($) { 
    if(always_open=='1') $("#schedule").hide();
    else $("#schedule").show();
    $(document).on('change','.start_time',function(e){
      if($(this).val()=="") $(this).next().next().val('');
      else $(this).next().next().val('18:00');
    });
    $(document).on('click','[name="always_open"]',function(e){
     if ($(this).is(':checked'))
     {
        $("#schedule").hide();
     }
     else
     {
       $("#schedule").show();
     }
    }); 
  });
</script>