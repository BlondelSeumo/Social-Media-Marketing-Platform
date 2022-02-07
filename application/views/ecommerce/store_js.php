<script>
function get_button()
{        
    // if($("#page").val()=="")
    // {
    //   swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please select a page.'); ?>", 'error');
    //   return false;
    // }

    if($("#store_name").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store name.'); ?>", 'error');
      return false;
    }

    if($("#store_email").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store email.'); ?>", 'error');
      return false;
    }

    if($("#store_country").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store country.'); ?>", 'error');
      return false;
    }

    if($("#store_state").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store state.'); ?>", 'error');
      return false;
    }

    if($("#store_city").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store city.'); ?>", 'error');
      return false;
    }


    if($("#store_address").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store address.'); ?>", 'error');
      return false;
    }
    

    if($("#store_zip").val()=="")
    {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please put your store postal code.'); ?>", 'error');
      return false;
    }

    
    $('#get_button').addClass('btn-progress');
    var dashboard = base_url+"ecommerce";
    
    var queryString = new FormData($("#plugin_form")[0]);
  
    $.ajax({
      type:'POST' ,
      url: action_url,
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
          swal(success_title, response.message, 'success').then((value) => {parent.location.reload();});
          // $("#get_button").attr('disabled',true);
        }
        else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
      }

    });

}

function get_button2()
{  
    $('#get_button2').addClass('btn-progress');
    var dashboard = base_url+"ecommerce";
    
    var queryString = new FormData($("#plugin_form")[0]);
  
    $.ajax({
      type:'POST' ,
      url: action_url,
      data: queryString,
      dataType : 'JSON',
      cache: false,
      contentType: false,
      processData: false,
      success:function(response)
      {  
        $("#get_button2").removeClass('btn-progress');
        if(response.status=='1') 
        { 
          swal(success_title, response.message, 'success').then((value) => {parent.location.reload();});
          // $("#get_button").attr('disabled',true);
        }
        else swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
      }

    });

}

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

$(document).ready(function($) { 

  // var today = new Date();
  $('.datepicker_x').datetimepicker({
    theme:'light',
    format:'Y-m-d H:i:s',
    formatDate:'Y-m-d H:i:s',
    // minDate: today
  });

  $('.visual_editor').summernote({
      height: 180,
      minHeight: 180,
      toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline','italic','clear']],
          // ['fontname', ['fontname']],
          // ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link']],
          ['view', ['codeview']]
      ]
    });

  $(".xscroll1").mCustomScrollbar({
  autoHideScrollbar:true,
  theme:"light-thick",
  axis: "x"
  });


  $(document).on('click','#variables',function(e){
    e.preventDefault();          
    var success_message= '{{store_name}}<br/>{{store_url}}<br/>{{order_no}}<br/>{{order_url}}<br/>{{checkout_url}}<br>{{my_orders_url}}<br/>{{last_name}}<br/>{{first_name}}<br/>{{email}}<br/>{{mobile}}';
    var span = document.createElement("span");
    span.innerHTML = success_message;
    swal({ title:'<?php echo $this->lang->line("Variables"); ?>', content:span,icon:'info'});     
  }); 

  Dropzone.autoDiscover = false;
  var names = ['logo', 'favicon'];
  $('.dropzone').each(function(index){
    var current_name = names[index];
    var elem = $('#store_'+current_name);
    var that = this;
    $(this).dropzone({
      url: '<?php echo base_url('ecommerce/upload_store_'); ?>'+current_name,
      maxFilesize:1,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".png,.jpg,.jpeg",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(elem).val(data.filename);
        }
      },
      removedfile: function(file) {
        var filename = $(elem).val();
        if('' !== filename)
        {
          $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: { filename },
            url: '<?php echo base_url('ecommerce/delete_store_'); ?>'+current_name,
            success: function(data) {
              $(elem).val('');
            }
          });
          $("#store-"+current_name+"-dropzone").find(file.previewElement).remove();
        }
      },
    })
  });
  
});
</script>