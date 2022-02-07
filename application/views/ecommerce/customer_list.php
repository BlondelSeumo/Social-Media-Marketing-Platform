<section class="section section_custom pt-2 pr-2">

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card no_shadow">
          <div class="card-body data-card p-0">
            <div class="row">
              <div class="col-12 col-md-10">
                <?php 

                echo 
                '<div class="input-group mb-3" id="searchbox">
                  <input type="text" class="form-control" autofocus id="search_value"  name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:30%;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_subscriber"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>

              <div class="col-12 col-md-2">
                  <a class="btn btn-outline-primary btn-lg float-right mr-1" href="<?php echo base_url('ecommerce/download_result'); ?>"><i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line("Download"); ?></a>                          
              </div>
            </div>

            <div class="table-responsive2">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th style="vertical-align:middle;width:20px">
                          <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                      </th>
                      <th><?php echo $this->lang->line("Avatar"); ?></th>  
                      <th><?php echo $this->lang->line("Subscriber ID"); ?></th>      
                      <th><?php echo $this->lang->line("First Name"); ?></th>      
                      <th><?php echo $this->lang->line("Last Name"); ?></th>      
                      <th><?php echo $this->lang->line("Email"); ?></th>     
                      <th><?php echo $this->lang->line("Actions"); ?></th>     
                      <th><?php echo $this->lang->line("Subscribed at"); ?></th>
                    </tr>
                  </thead>
                </table>
            </div>
          </div>
        </div>
      </div>       
        
    </div>
  </div>          

</section>


<script type="text/javascript">
    var base_url="<?php echo base_url();?>";

    $("document").ready(function(){
      var perscroll;
      var table1 = '';
      table1 = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 8, "desc" ]],
        pageLength: 10,
        ajax: {
            url: base_url+'ecommerce/customer_list_data',
            type: 'POST',
            data: function ( d )
            {
                // d.page_id = $('#page_id').val();
                d.search_value = $('#search_value').val();
            }
        },
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [          
          {
              targets: [0,2,3,7,8],
              className: 'text-center'
          },
          {
              targets: [0,1,2,7],
              sortable: false
          }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
             if(areWeUsingScroll)
             {
               if (perscroll) perscroll.destroy();
               perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
             }
         },
         scrollX: 'auto',
         fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
             if(areWeUsingScroll)
             { 
               if (perscroll) perscroll.destroy();
               perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
             }
         }
      });

      $(document).on('click', '#search_subscriber', function(e) {
          e.preventDefault(); 
          table1.draw(false);
      });

      $(document).on('click', '.change_password', function(e) {
        e.preventDefault();
        var email = $(this).attr('data-email');
        var id = $(this).attr('data-id');
        $("#user_email").val(email);
        $("#user_id").val(id);
        $("#password").val("");
        $("#confirm_password").val("");
        $("#change_password").modal();
      });

      var confirm_match=0;
      $(".password").keyup(function(){
        
          var new_pass=$("#password").val();
          var conf_pass=$("#confirm_password").val();

          if(new_pass=='' || conf_pass=='') 
          {
            return false;
          }

          if(new_pass==conf_pass)
          {
              confirm_match=1;
              $("#password").removeClass('is-invalid');
              $("#confirm_password").removeClass('is-invalid');
          }
          else
          {
              confirm_match=0;
              $("#confirm_password").addClass('is-invalid');
          }

      });

      $(document).on('click', '#save_change_password_button', function(e) {
        e.preventDefault();

        var id =  $("#user_id").val();
        var email =  $("#user_email").val();
        var password =  $("#password").val();
        var confirm_password =  $("#confirm_password").val();

        password = password.trim();
        confirm_password = confirm_password.trim();

        if(email=='')
        {
            $("#user_email").addClass('is-invalid');
            return false;
        }
        else $("#user_email").removeClass('is-invalid');

        if(password=='' || confirm_password=='')
        {
            $("#password").addClass('is-invalid');
            return false;
        }
        else
        {
            $("#password").removeClass('is-invalid');
        }

        if(confirm_match=='1')
        {
            $("#confirm_password").removeClass('is-invalid');
        }
        else
        {
            $("#confirm_password").addClass('is-invalid');
            return false;
        }

        $("#save_change_password_button").addClass("btn-progress");

        $.ajax({
        url: base_url+'ecommerce/change_user_password_action',
        type: 'POST',
        dataType: 'JSON',
        data: {id,password,confirm_password,email},
          success:function(response)
          {
            $("#save_change_password_button").removeClass("btn-progress");

            if(response.status == "1")  
              swal('<?php echo $this->lang->line("Success")?>',response.message, 'success')
             .then((value) => {
                 $("#change_password").modal('hide');
              });

            else  swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
          },
          error:function(response){
            var span = document.createElement("span");
            span.innerHTML = response.responseText;
            swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
          }
        });

      });

      $('#change_password').on("hidden.bs.modal", function (e) {
         table1.draw(false);
      });   
  });

</script>


<div class="modal fade" tabindex="-1" role="dialog" id="change_password" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-key"></i> <?php echo $this->lang->line("Change Password");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">  
              <form class="form-horizontal" action="" method="POST">
                <div id="wait"></div>
                <input id="user_id" value="" class="form-control" type="hidden">           
                <div class="form-group">
                  <label for="password"><?php echo $this->lang->line("Email"); ?> *  </label>   
                  <input id="user_email" value="" class="form-control" type="email"> 
                </div>          
                <div class="form-group">
                  <label for="password"><?php echo $this->lang->line("New Password"); ?> *  </label>                  
                  <input id="password" class="form-control password" type="password">             
                  <div class="invalid-feedback"><?php echo $this->lang->line("You have to type new password twice"); ?></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?php echo $this->lang->line("Confirm New Password"); ?> * </label>                  
                    <input id="confirm_password"  class="form-control password" type="password">             
                   <div class="invalid-feedback"><?php echo $this->lang->line("Passwords does not match"); ?></div>
                </div>
              </form>            
            </div>


            <div class="modal-footer bg-whitesmoke br">
              <button type="button" id="save_change_password_button" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save"); ?></button>
              <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
            </div>

        </div>
    </div>
</div>