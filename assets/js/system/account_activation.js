"use strict";
  $('document').ready(function(){

    $("#submit").on('click',function(e){
      e.preventDefault();

      $("#msg").removeAttr('class');
      $("#msg").html("");

      var code=$("#code").val();
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

      if(code=='')
      {
          $("#code").addClass('is-invalid');
          return false;
      }
      else
      {
          $("#code").removeClass('is-invalid');
      }
      
      $(this).addClass('btn-progress');
      $.ajax({
        context: this,
        type:'POST',
        url: base_url+"home/account_activation_action",
        data:{code:code,email:email},
        success:function(response){
              $(this).removeClass('btn-progress');
              if(response == 0)
              {
                swal(global_lang_error, login_related_lang_activation_code_not_match, 'error');
              }
              if(response == 2)
              {
                var string='<div class="alert alert-primary alert-has-icon"><div class="alert-icon"><i class="far fa-check-circle"></i></div><div class="alert-body"><div class="alert-title"><a href="'+base_url+'home/login">'+login_related_lang_activation_login_here+'</a></div>'+login_related_lang_activation_success+'</div></div>';
                $("#recovery_form").slideUp();
                $("#recovery_form").html(string);
                $("#recovery_form").slideDown();
              }
          }
      });
      
    });
  });