<br>
<br>
<div class="row">
  <div class="webview-form col-12 d-flex justify-content-center">
    <div class="col-12 col-sm-12 col-md-8 col-lg-6 col-lg-6">
      <div class="card">
        <form id="webview-form">
          <div class="card-header">
            <h4><?php echo $this->lang->line('What is your Birthday?'); ?></h4>
          </div>

          <input type="hidden" name="subscriber_id" value="<?php echo $subscriber_id; ?>">

          <div class="card-body">
            <!-- renders form -->

            <div class="form-group"><label><?php echo $this->lang->line('Birth Date'); ?></label><input placeholder="YYYY-MM-DD" name="birthdate" type="text" class="datepicker_x form-control" placeholder=""  />
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
      timepicker:false
    });

    $('.timepicker_x').datetimepicker({
      datepicker:false,
      format:'H:i:s'
    });


//PSID variable comes from bare-them.php file. 

    $('.select2').select2({ width: '100%' });

    $("form").on('submit',function(event){
      event.preventDefault();

      if( $("input[name='birthdate']").val()=='')
      {
        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please select your birth date."); ?>', 'error');
        return false;
      }

     var psid_from_url=$("input[name='subscriber_id']").val();
     if(psid_from_url=='')
        $("input[name='subscriber_id']").val(PSID);


      $("#webview_submit_button").attr('disabled',true);

      var form_data = $(this).serialize();
      $.ajax({
         url:base_url+"webview_builder/birthdate_submit",
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

