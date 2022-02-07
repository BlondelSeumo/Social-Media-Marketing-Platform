"use strict";
$(document).ready(function() {
  $(document).on('click','#submit',function(e){
    e.preventDefault();
    var purchase_code = $("#purchase_code").val().trim();
    if(purchase_code=='')
    {
        $("#purchase_code").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#purchase_code").removeClass('is-invalid');
    }

    var domain_name = base_url;

    $(this).addClass("btn-progress");
    $.ajax({
        context: this,
        type: "POST",
        url : base_url+'home/credential_check_action',
        data:{domain_name:domain_name,purchase_code:purchase_code},
        dataType: 'JSON',
        success:function(response)
        {
          $(this).removeClass("btn-progress");
          if(response == "success")
          {
            var link = base_url+'home/login';
            window.location.assign(link);
          }
          else 
          {
            var success_message=response.reason;
            var span = document.createElement("span");
            span.innerHTML = success_message;
            swal({ title:global_lang_error, content:span,icon:'error'});
          } 
        }
      });
  });
});