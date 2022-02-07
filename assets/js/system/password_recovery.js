"use strict";
$('document').ready(function(){
    var confirm_match=0;
    $(".password").on('keyup',function(){
      var new_pass=$("#new_password").val();
      var conf_pass=$("#new_password_confirm").val();

      if(new_pass=='' || conf_pass=='') 
      {
        return false;
      }

      if(new_pass==conf_pass)
      {
          confirm_match=1;
          $("#new_password").removeClass('is-invalid');
          $("#new_password_confirm").removeClass('is-invalid');
      }
      else
      {
          confirm_match=0;
          $("#new_password_confirm").addClass('is-invalid');
      }
    });

    $("#submit").on('click',function(e){
      e.preventDefault();

      var is_code=$("#code").val();
      var new_pass=$("#new_password").val();
      var conf_pass=$("#new_password_confirm").val();
     
      if(is_code=='')
      {
          $("#code").addClass('is-invalid');
          return false;
      }
      else
      {
          $("#code").removeClass('is-invalid');
      }

      if(new_pass=='' || conf_pass=='')
      {
          $("#new_password").addClass('is-invalid');
          return false;
      }
      else
      {
          $("#new_password").removeClass('is-invalid');
      }

      if(confirm_match=='1')
      {
          $("#new_password_confirm").removeClass('is-invalid');
      }
      else
      {
          $("#new_password_confirm").addClass('is-invalid');
          return false;
      }
      
      var code=$("#code").val();
      var newp=$("#new_password").val();
      var conf=$("#new_password_confirm").val();
      $(this).addClass('btn-progress');
      $.ajax({
        context: this,
        type:'POST',
        url: base_url+"home/recovery_check",
        data:{code:code,newp:newp,conf:conf},
        success:function(response)
        {
          $(this).removeClass('btn-progress');
          if(response=='0')
          {
           swal(global_lang_error, login_related_lang_password_reset_code_invalid, 'error');
          }
          else if(response=='1')
          {
            swal(global_lang_error, login_related_lang_password_reset_code_expired, 'error');
          }
          else
          { 
            var string='<div class="alert alert-primary alert-has-icon"><div class="alert-icon"><i class="far fa-check-circle"></i></div><div class="alert-body"><div class="alert-title"><a href="'+base_url+'home/login">'+login_related_lang_activation_login_here+'</a></div>'+login_related_lang_password_update_success+'</div></div>';
            $("#recovery_form").slideUp();
            $("#recovery_form").html(string);
            $("#recovery_form").slideDown();
          }
      }
      });
      
    });
});