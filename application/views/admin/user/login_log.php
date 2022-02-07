<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-history"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('admin/user_manager'); ?>"><?php echo $this->lang->line("User Manager"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>                  
                  <tr>
                    <th>#</th>      
                    <th><?php echo $this->lang->line("User"); ?></th>      
                    <th><?php echo $this->lang->line("Email"); ?></th>      
                    <th><?php echo $this->lang->line("Login Time"); ?></th>
                    <th><?php echo $this->lang->line("Login IP"); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sl=0;
                  foreach ($info as $key => $value) 
                  {
                     $sl++;
                     $id = $value['user_id'];
                     echo "<tr>";
                       echo "<td>".$sl."</td>";
                       echo "<td><a href='".base_url('admin/edit_user/'.$id)."'>".$value['user_name']."</a></td>";
                       echo "<td>".$value['user_email']."</td>";
                       echo "<td>".$value['login_time']."</td>";
                       echo "<td>".$value['login_ip']."</td>";
                     echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>


<?php
$drop_menu = '<div class="btn-group dropleft float-right"><button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  '.$this->lang->line("Options").'  </button>  <div class="dropdown-menu dropleft"> <a class="dropdown-item are_you_sure_datatable" data-refresh="0" href="'.base_url('admin/delete_user_log').'"><i class="fas fa-trash"></i> '.$this->lang->line("Delete old data").'</a></div> </div>';
?> 


<script>       
    var base_url="<?php echo site_url(); ?>";

    var drop_menu = '<?php echo $drop_menu;?>';
      setTimeout(function(){ 
        $("#mytable_filter").append(drop_menu); 
    }, 2000);
      
   
    $(document).ready(function() {

      var perscroll;
      var table = $("#mytable").DataTable({          
          processing:true,
          bFilter: true,
          order: [[ 3, "desc" ]],
          pageLength: 25,
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [            
            {
                targets: [3,4],
                className: 'text-center'
            },
            {
                targets: [0],
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

    });

   
 
</script>



<div class="modal fade" tabindex="-1" role="dialog" id="change_password" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-key"></i> <?php echo $this->lang->line("Change Password");?> (<span id="putname"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">  
              <form class="form-horizontal" action="<?php echo site_url().'admin/change_user_password_action';?>" method="POST">
                <div id="wait"></div>
                <input id="putid" value="" class="form-control" type="hidden">           
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
              <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal_send_sms_email" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Send Email");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="modalBody" class="modal-body">        
        <div id="show_message" class="text-center"></div>

        <div class="form-group">
          <label for="subject"><?php echo $this->lang->line("Subject"); ?> *</label><br/>
          <input type="text" id="subject" class="form-control"/>
          <div class="invalid-feedback"><?php echo $this->lang->line("Subject is required"); ?></div>
        </div>

        <div class="form-group">
          <label for="message"><?php echo $this->lang->line("Message"); ?> *</label><br/>
          <textarea name="message" style="height:300px !important;" class="form-control" id="message"></textarea>
          <div class="invalid-feedback"><?php echo $this->lang->line("Message is required"); ?></div>
        </div>
     
      </div>

      <div class="modal-footer bg-whitesmoke br">
           <button id="send_sms_email" class="btn btn-primary btn-lg" > <i class="fas fa-paper-plane"></i>  <?php echo $this->lang->line("Send"); ?></button>
            <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>