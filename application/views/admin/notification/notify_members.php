<?php $this->load->view('admin/theme/message'); ?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1> <i class="fa fa-envelope"></i> <?php echo $this->lang->line("send email to users"); ?> </h1>
</section>

<!-- Main content -->
<section class="content">  
  <div class="row">
    <div class="col-xs-12">
        <div class="grid_container" style="width:100%; height:874px;">
            <table 
            id="tt"  
            class="easyui-datagrid" 
            url="<?php echo base_url()."admin/notify_members_data_loader"; ?>" 
            
            pagination="true" 
            rownumbers="true" 
            toolbar="#tb" 
            pageSize="15" 
            pageList="[5,10,15,20,50,100]"  
            fit= "true" 
            fitColumns= "true" 
            nowrap= "true" 
            view= "detailview"
            idField="id"
            >
            
               <thead>
                 <tr>
                     <th field="id" checkbox="true"></th>                        
                     <th field="name" sortable="true"><?php echo $this->lang->line("name"); ?></th>   
                     <?php if($this->is_demo == '1') : ?>
                     <th field="email" formatter="is_demo_email" sortable="true"><?php echo $this->lang->line("email"); ?></th>
                     <?php else : ?>
                     <th field="email" sortable="true"><?php echo $this->lang->line("email"); ?></th>
                     <?php endif; ?>
                     <?php if($this->is_demo == '1') : ?>
                     <th field="mobile" formatter="is_demo_mobile" sortable="true" ><?php echo $this->lang->line("mobile"); ?></th>
                     <?php else : ?>
                     <th field="mobile" sortable="true" ><?php echo $this->lang->line("mobile"); ?></th>
                     <?php endif; ?>
                     
                     <th field="address" sortable="true" ><?php echo $this->lang->line("address"); ?></th>
                     <th field="status" formatter="status_check" ><?php echo $this->lang->line("status"); ?></th>                  
                 </tr>
               </thead>
            </table>                        
         </div>
  
       <div id="tb" style="padding:3px">

      <a class="btn btn-warning"  title="<?php echo $this->lang->line("send email"); ?>" onclick="sms_send_email_ui()">
        <i class="fa fa-send"></i> <?php echo $this->lang->line("send email"); ?>
    </a>

 <form class="form-inline" style="margin-top:20px">

                <div class="form-group">
                    <input id="first_name" name="first_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line("name"); ?>">
                </div> 

                <button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("search"); ?></button>
                      
            </form> 

        </div>        
    </div>
  </div>   
</section>






<!--Modal for Send SMS  Email-->
  
<div id="modal_send_sms_email" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 id="SMS" class="modal-title"> <i class="fa fa-send"></i> <?php echo $this->lang->line("send email"); ?></h4>
      </div>

      <div id="modalBody" class="modal-body">        
        <div id="show_message" class="text-center"></div>

        <div class="form-group">
          <label for="sms_content"><?php echo $this->lang->line("message subject"); ?> *</label><br/>
          <input type="text" id="sms_subject" required class="form-control"/>
        </div>

        <div class="form-group">
          <label for="sms_content"><?php echo $this->lang->line("message"); ?> *</label><br/>
          <textarea name="sms_content" required style="width:100%;height:200px;" id="sms_content"></textarea>
        </div>
     
      </div>

      <div class="modal-footer clearfix">
           <button id="send_sms_email" class="btn btn-warning pull-left" > <i class="fa fa-send"></i>  <?php echo $this->lang->line("send"); ?></button>
           <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
      </div>
    </div>
  </div>
</div>



<script>   

 function sms_send_email_ui()
    {
      $("#modal_send_sms_email").modal();
    }    

    $j("document").ready(function() {
        $( ".datepicker" ).datepicker();

        $("#send_sms_email").click(function(){      
                  
          var subject=$("#sms_subject").val();
          var content=$("#sms_content").val();         
          var rows = $j('#tt').datagrid('getSelections');
          var info=JSON.stringify(rows);  
          
          if(rows=="") 
          {
            $("#show_message").addClass("alert alert-warning");
            $("#show_message").html("<b><?php echo $this->lang->line("you did not select any user"); ?></b>");
            return;
          }
          
          if(subject=="" || content=="")
          {
            $("#show_message").addClass("alert alert-warning");
            $("#show_message").html("<?php echo $this->lang->line("something is missing"); ?>");
            return;
          }

          $(this).attr('disabled','yes');
          $("#show_message").addClass("alert alert-info");
          $("#show_message").show().html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line("sending email, please wait"); ?>');
          $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>admin/send_email_member",
          data:{content:content,info:info,subject:subject},
          success:function(response){
            $("#send_sms_email").removeAttr('disabled');                     
            $("#show_message").addClass("alert alert-info");
            $("#show_message").html(response);
          }
        });   
      }); 
    });  

    var base_url="<?php echo site_url(); ?>";

    function status_check(value, row, index)
    {
      var status = row.status; 
      var str = '';     

      if(status == "1")
        str = str+"<label class='label label-light'><i class='fa fa-check-circle green'></i> <?php echo $this->lang->line("active"); ?></label>";      

      if(status == '0')
        str = str+"<label class='label label-light'><i class='fa fa-remove red'></i> <?php echo $this->lang->line("inactive"); ?></label>";

      return str;
    }

    function is_demo_mobile(value, row, index)
    {
      return "**********";
    }

    function is_demo_email(value, row, index)
    {
      return "*****@*****";
    }
    
   
    function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
          first_name:       $j('#first_name').val(),
          is_searched:      1
        });
    }  
</script>
