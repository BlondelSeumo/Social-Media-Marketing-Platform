<br>
<br>
<?php
$pclass = 'p-4';
$pclasscol = '';
if(isset($_GET['type']) && $_GET['type']=='datetime')
{
  $pclass = 'p-2';
  $pclasscol = 'p-0';
  echo '<style type="text/css">.xdsoft_inline{width: 100% !important;}.xdsoft_datepicker{width:calc(100% - 75px) !important;</style>';
}
else if(isset($_GET['type']) && $_GET['type']=='date') echo '<style type="text/css">.xdsoft_inline,.xdsoft_datepicker{width: 100% !important;}</style>';
else if(isset($_GET['type']) && $_GET['type']=='time') echo '<style type="text/css">.xdsoft_inline,.xdsoft_timepicker{width: 100% !important;}.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box{height:300px !important;}</style>';
?>

<div class="row">
  <div class="webview-form col-12 d-flex justify-content-center">
    <div class="col-12 col-sm-12 col-md-8 col-lg-6 col-lg-6 <?php echo $pclasscol; ?>">
      <div class="card">
        <form id="webview-form">
          <div class="card-header <?php echo $pclass; ?>">
            <h4><?php echo $field_title; ?></h4>
          </div>

          <input type="hidden" name="subscriber_id" value="<?php echo $subscriber_id; ?>">

          <div class="card-body <?php echo $pclass; ?>">
            <!-- renders form -->

            <div class="form-group"><input placeholder="<?php echo $placeholder; ?>" name="select_date" id="select_date" type="text" class="<?php echo $picker_class; ?> form-control no_radius" placeholder=""  />
            </div>
            <div class="form-group text-left"><button id="webview_submit_button" type="submit" class="btn-primary btn"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line('Submit'); ?></button></div> 
            

          </div>
        </form>
      </div>
    </div>
  </div>  
</div>

<script>
  $(document).ready(function() {

    var base_url = "<?php echo base_url(); ?>";

    $('.datepicker_x').datetimepicker({
      theme:'light',
      format:'Y-m-d',
      formatDate:'Y-m-d',
      timepicker:false,
      inline:true,
    });

    $('.timepicker_x').datetimepicker({
      datepicker:false,
      format:'H:i',
      inline:true,
    });

    $('.datetimepicker_x').datetimepicker({
      format:'Y-m-d H:i',
      inline:true,
    });

    
    setTimeout(function(){
      $("#select_date").removeAttr('style');
    }, 500);

    $("form").on('submit',function(event){
      event.preventDefault();

      if( $("input[name='select_date']").val()=='')
      {
        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please select a Date."); ?>', 'error');
        return false;
      }

     var psid_from_url=$("input[name='subscriber_id']").val();
     if(psid_from_url=='')
        $("input[name='subscriber_id']").val(PSID);


      $("#webview_submit_button").attr('disabled',true);

      var form_data = $(this).serialize();
      $.ajax({
         url:base_url+"webview_builder/user_input_date_submit",
         method:"POST",
         data:form_data,
         dataType:'JSON',
         success:function(response)
          {
            $("#webview_submit_button").removeAttr('disabled');
              if(response.error=='1'){
              swal('<?php echo $this->lang->line("Error"); ?>', response.error_message, 'error');
            }
            else{

              if(PSID === undefined) swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Submitted Successfully"); ?>', 'success');
            }

            MessengerExtensions.requestCloseBrowser(function success() {
           
            }, function error(err) {
              
            });



          }            

      });

    });

  });
</script>

