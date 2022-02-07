<script>
$(document).ready(function($) { 
  $(document).on('click', '.webhook_data', function(event) {
      event.preventDefault();
      var base_url = '<?php echo site_url();?>';
      var webhook_id = $(this).attr('data-id');
      $("#webhook_data .modal-body").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 50px"></i></div><br/>');      
      $("#webhook_data").modal();
      var is_ajax = '1';

      $.ajax({
        context: this,
        type:'POST' ,
        url:"<?php echo site_url();?>ecommerce/order",
        data:{webhook_id : webhook_id,is_ajax : is_ajax},
        success:function(response){         
          $("#webhook_data .modal-body").html(response);
        }
      });
    });   
});
</script>

<div class="modal fade" id="webhook_data" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-tasks"></i> <?php echo $this->lang->line("Activity"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>