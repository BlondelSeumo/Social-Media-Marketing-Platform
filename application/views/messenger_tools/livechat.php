<style>.select2{width: 100% !important;}</style>
<div id="dynamic_field_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="page_name">
                <div class="modal-body" style="padding-bottom:0">
                    <div class="row">
                          <div class="col-12"> 
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Select Facebook page / Instagram account"); ?></label>
                              <?php echo $page_dropdown;?>
                            </div>
                          </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 10px;">
                    <button id="submit" class="btn btn-primary btn-lg"><i class="fas fa-comment-alt"></i> <?php echo $this->lang->line('Live Chat'); ?></button>
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><?php echo $this->lang->line('Back'); ?></button>
                </div>
            </form>
        </div>
    </div>

</div> 

<script>
$(document).ready(function(){

    var base_url="<?php echo base_url(); ?>";
    // setTimeout(function(){  $('#dynamic_field_modal').modal('show'); }, 500);

    $('#submit').click(function(e) {
       e.preventDefault();
       var page_id = $('#page_id').val();

       if(page_id == '')
       {
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please select a Facebook page / Instagram account"); ?>', 'warning');
          return false;
       }
       else
       {
          var exp = page_id.split("-");
          var page_auto_id = 0;
          var social_media = 'fb';
          if(typeof(exp[0])!=='undefined') page_auto_id = exp[0];
          if(typeof(exp[1])!=='undefined') social_media = exp[1];

          var link = base_url+"message_manager/message_dashboard/"+page_auto_id;
          if(social_media=='ig') link = base_url+"message_manager/instagram_message_dashboard/"+page_auto_id;
          window.open(link, '_blank').focus();
       }         

    });

    $('#dynamic_field_modal').on("hidden.bs.modal", function (e) { 
        window.history.back();
    });
     

});
</script>