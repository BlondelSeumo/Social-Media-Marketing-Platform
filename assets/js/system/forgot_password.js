"use strict";
$('document').ready(function(){
  $("#submit").on('click',function(e){
    e.preventDefault();

    var email=$("#email").val();
    if(email=='')
    {
        $("#email").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#email").removeClass('is-invalid');
    }
    $(this).addClass('btn-progress');
    $.ajax({
      context: this,
      type:'POST',
      url: base_url+"home/code_genaration",
      data:{email:email},
      success:function(response){
        $(this).removeClass('btn-progress');
        if(response=='0')
        {
          swal(global_lang_error, login_related_lang_password_invalid_email, 'error');
        }
        else
        {
          var string='<div class="alert alert-primary alert-has-icon"><div class="alert-icon"><i class="far fa-paper-plane"></i></div><div class="alert-body"><div class="alert-title">'+login_related_lang_password_link_sent+'</div>'+login_related_lang_password_link_sent_success+'</div></div>';
          $("#recovery_form").slideUp();
          $("#recovery_form").html(string);
          $("#recovery_form").slideDown();
        }
    }
    });
    
  });
});
