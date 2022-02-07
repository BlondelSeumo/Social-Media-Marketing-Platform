<script>
var base_url="<?php echo site_url(); ?>";
function ecommerceGoBack() //used to go back to list as crud
{
  var mes='';
  mes="<?php echo $this->lang->line('Your data may not be saved.');?>";
  swal({
    title: "<?php echo $this->lang->line("Do you want to go back?");?>",
    text: mes,
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => 
  {
    if (willDelete) 
    {
      parent.location.reload();
    } 
  });
}

function reset_values()
{
  var mes='';
  mes="<?php echo $this->lang->line('Do you really want restore default settings?');?>";
  swal({
    title: "<?php echo $this->lang->line("Restore Default Settings");?>",
    text: mes,
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => 
  {
    if (willDelete) 
    {
      window.location.assign('<?php echo base_url("ecommerce/reset_notification/".$store_id);?>');
    } 
  });
}

$(document).ready(function($) { 

  $('.visual_editor').summernote({
      height: 320,
      minHeight: 320,
      toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline','italic','clear']],
          ['fontname', ['fontname']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link']],
          ['view', ['codeview']]
      ]
  });

  $(document).on('click','#variables',function(e){
    e.preventDefault();          

    var success_message= '{{store_name}}<br/>{{store_url}}<br/>{{order_no}}<br/>{{order_status}}<br/>{{invoice_url}}<br/>{{my_orders_url}}<br/>{{update_note}}<br/>{{last_name}}<br/>{{first_name}}';
    var span = document.createElement("span");
    span.innerHTML = success_message;
    swal({ title:'<?php echo $this->lang->line("Variables"); ?>', content:span,icon:'info'});     
  }); 

  $(document).on('click','#get_button',get_button);
  function get_button()
  {       

    $('#get_button').addClass('btn-progress');
    
    var queryString = new FormData($("#plugin_form")[0]);
  
    $.ajax({
      type:'POST' ,
      url: base_url+"ecommerce/notification_settings_action",
      data: queryString,
      dataType : 'JSON',
      cache: false,
      contentType: false,
      processData: false,
      success:function(response)
      {  
        $("#get_button").removeClass('btn-progress');
        if(response.status=='1') 
        { 
          swal('<?php echo $this->lang->line("Settings Updated"); ?>', response.message, 'success').then((value) => { parent.window.location.assign('<?php echo base_url("ecommerce/store_list") ?>');});
        }
        else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
      }

    });

  } 
  
});
</script>