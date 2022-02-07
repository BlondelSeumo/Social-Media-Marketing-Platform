<?php
$redirect_url = site_url("messenger_bot/otn_template_manager/{$bot_info['page_id']}/1");
  $THEMECOLORCODE = "#607D8B";
?>


<?php if($iframe!='1') : ?>
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit OTN PostBack Template");?></h1>
    <div class="section-header-breadcrumb">
       <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot/otn_template_manager'); ?>"><?php echo $this->lang->line('OTN Post-back Manager'); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>
<?php endif; ?>
  <div class="card <?php if($iframe=='1') echo 'no_shadow'; ?>">
    <div class="card-body <?php if($iframe=='1') echo 'padding-0'; ?>">

          <div class="row">
            <div class="col-12">
              <form action="#" method="post" id="messenger_bot_form" style="padding-left: 0;">
                <input type="hidden" name="id" id="id" value="<?php echo  $bot_info['id'];?>">      
                
                <div class="row" <?php if($is_default=='default') echo "style='display: none;'"; ?> > 
                  <div class="col-12 col-sm-6"> 
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Template Name"); ?></label>
                      <input type="<?php if($is_default=='default') echo 'hidden'; else echo 'text'; ?>" name="bot_name" value="<?php if(set_value('bot_name')) echo set_value('bot_name');else {if(isset($bot_info['template_name'])) echo $bot_info['template_name'];}?>" id="bot_name" class="form-control">
                    </div>       
                  </div>
                  <div class="col-12 col-sm-6"> 
                    <div class="form-group">
                      <label>
                        <?php echo $this->lang->line("Select a reply PostBack"); ?>
                      </label>
                      <div>                              
                        <select class="form-control push_postback select2 dropdown-item" id="reply_postback_id" name="reply_postback_id">
                          <?php 
                            if(isset($bot_info['reply_postback_id']))
                              $selected_value = $bot_info['reply_postback_id'];
                            else
                              $selected_value = '';
                            foreach($postback_dropdown as $key=>$value)
                            {
                              $is_selected = ($key == $selected_value) ? 'selected' : '';
                              echo "<option value='".$key."' ".$is_selected.">".$value."</option>";
                            }
                          ?>
                        </select>
                        <a href="" class="add_template float-left" page_id_add_postback="<?php echo $bot_info['page_id']; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                        <a href="" class="ref_template float-right" page_id_ref_postback="<?php echo $bot_info['page_id']; ?>"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                      </div>


                    </div>      
                  </div>
                </div>

                <br/>
                <div class="row"> 

                  <div class="col-12 col-sm-6 d-none"> 
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Selected page"); ?></label>
                      <?php 
                        $page_list[''] = "Please select a page";

                        $page_select_extra_class = ' hidden';
                        $page_select_default_value = $bot_info['page_id'];

                        if (isset($action_type) && $action_type == 'clone') {
                            $page_select_extra_class = ' select2';
                        }
                        echo form_dropdown('page_table_id',$page_list,$page_select_default_value,'id="page_table_id" class="form-control'. $page_select_extra_class .'"'); 
                        $pagename="";;
                        foreach ($page_list as $key => $value) 
                        {
                          if($key==$bot_info['page_id'])   $pagename=$value;                    
                        }
                        if (!(isset($action_type) && $action_type == 'clone')) {
                            echo " : <b>".$pagename."</b>";
                        }
                      ?>
                    </div>       
                  </div>

                  <div class="col-12 col-sm-6"> 
                    <div class="form-group" id="postback_section">
                      <label><?php echo $this->lang->line("OTN Postback ID"); ?>
                        <?php if (isset($action_type) && $action_type == 'clone'): ?>
                          <a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                        <?php endif ?>
                      </label>

                      <input 
                        type="<?php echo (!(isset($action_type) && $action_type == 'clone')) ? "hidden" : ""; ?>" 
                        name="template_postback_id" 
                        id="template_postback_id" 
                        value="<?php 
                          if(set_value('otn_postback_id')) echo set_value('otn_postback_id');
                          elseif ((isset($action_type) && $action_type == 'clone')) echo "";
                          else {if(isset($bot_info['otn_postback_id'])) echo $bot_info['otn_postback_id'];}
                        ?>" 
                        class="form-control push_postback"
                        >
                      <?php 
                          if ((!(isset($action_type) && $action_type == 'clone'))) {
                              echo " : <b>"; if(set_value('otn_postback_id')) echo set_value('otn_postback_id');else {if(isset($bot_info['otn_postback_id'])) echo $bot_info['otn_postback_id'];} echo "</b>";
                          }
                      ?>
                    </div>       
                  </div>

                </div>

            
                <?php 
                  $first_col= "col-12 col-sm-6";
                  if(!$this->is_drip_campaigner_exist && !$this->is_sms_email_drip_campaigner_exist)  $first_col="col-12";  
                  
                  $display = '';
                  if($is_default=='default') $display = "style='display: none;'";

                  $popover='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Labels").'" data-content="'.$this->lang->line("If you choose labels, then when user click on this PostBack they will be added in those labels, that will help you to segment your leads & broadcasting from Messenger Broadcaster. If you don't want to add labels for this PostBack , then just keep it blank as it is.").'"><i class="fa fa-info-circle"></i> </a>';

                  echo '<div class="row" '.$display.'>
                  <div class="'.$first_col.'"> 
                      <div class="form-group">
                        <label style="width:100%">
                        '.$this->lang->line("Choose Labels")." ".$popover.'
                        <a class="blue float-right pointer" page_id_for_label="'.$bot_info['page_id'].'" id="create_label_postback"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create Label").'</a>  
                        </label>';
                        
                        $broadcaster_labels=$bot_info['label_id'];
                        $broadcaster_labels=explode(',', $broadcaster_labels);

                        $str ='<span id="first_dropdown"><select multiple="" class="form-control select2" id="label_ids" name="label_ids[]">';
                        $str .= '<option value="">'.$this->lang->line('Select Labels').'</option>';
                        foreach ($info_type as  $value)
                        {
                            $search_key = $value['id'];
                            $search_type = $value['group_name'];
                            $selected='';
                            if(in_array($search_key, $broadcaster_labels)) $selected='selected="selected"';

                            $str.=  "<option value='{$search_key}' {$selected}>".$search_type."</option>";  
                        }
                        $str.= '</select></span>';
                        echo $str;
                                                     
                  echo '</div>       
                  </div>';

                  if($this->is_drip_campaigner_exist || $this->is_sms_email_drip_campaigner_exist)
                  {
                    $popover2='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Sequence Campaign").'" data-content="'.$this->lang->line("Choose any drip or sequence campaign to set when user click on this postback button. Keep it blank if you don't want to set.").'"><i class="fa fa-info-circle"></i> </a>';
                  echo 
                  '<div class="col-12 col-sm-6"> 
                      <div class="form-group">
                        <label style="width:100%">
                        '.$this->lang->line("Choose Sequence Campaigns")." ".$popover2.'
                        </label>';
                        
                        $dripcampaign_id=$bot_info['drip_campaign_id'];
                        $dripcampaign_id=explode(',', $dripcampaign_id);

                        $str ='<span id="dripcampaign_dropdown"><select class="form-control select2" id="drip_campaign_id" name="drip_campaign_id[]">';
                        $str .= '<option value="">'.$this->lang->line('Select').'</option>';
                        foreach ($dripcampaign_list as  $value)
                        {
                            $search_key = $value['id'];
                            $search_type = $value['campaign_name'];
                            $selected='';
                            if(in_array($search_key, $dripcampaign_id)) $selected='selected="selected"';

                            $str.=  "<option value='{$search_key}' {$selected}>".$search_type."</option>";  
                        }
                        $str.= '</select></span>';
                        echo $str;
                                                     
                  echo '</div>       
                  </div>';
                }
                echo '</div>';
                ?>                
                

                <br/><br/>
                <div class="row">
                  <div class="col-6">
                    <button id="submit" class="btn btn-lg btn-primary"><i class="fa fa-send"></i> <?php echo (!(isset($action_type) && $action_type == 'clone')) ? $this->lang->line('Update') : $this->lang->line("Clone");; ?></button>
                  </div>
                  <?php if($iframe != '1') : ?>
                  <div class="col-6">
                    <a class="btn btn-lg btn-secondary float-right" href="<?php echo base_url("messenger_bot/otn_template_manager"); ?>"><i class="fas fa-step-backward"></i> <?php echo $this->lang->line('Back'); ?></a>
                  </div>
                  <?php endif; ?>
                </div>
              </form>
            </div>
           
          </div>
          <br>
          <div id="submit_status" class="text-center"></div>


   </div> <!-- end of card body -->
  </div>

<?php if($iframe!='1') : ?>
</section>   
<?php endif; ?>



<?php 
$areyousure=$this->lang->line("are you sure"); 
$somethingwentwrong = $this->lang->line("something went wrong.");  
$doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this bot?");
?>

<script type="text/javascript">

  $(document).ready(function(){

    $(".dropdown-item").select2({
      tags: true,
      width: '100%'
    });

  });

</script>


<script type="text/javascript">

  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  var areyousure="<?php echo $areyousure;?>";


  <?php foreach($page_list as $key=>$value) : ?>    
    var js_array_<?php echo $key ?> = [<?php echo ""; ?>];
  <?php endforeach; ?> 




  $(document).ready(function() {


    $(document).on('change','#page_table_id',function(){  
      page_change_action();
    });

    // getting postback list and making iframe
    $('#add_template_modal').on('shown.bs.modal',function(){ 
      var page_id=$(".add_template").attr("page_id_add_postback");
      var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
      $(this).find('iframe').attr('src',iframe_link); 
    });  

    // refresh_template("0");
    // $("#loader").addClass('hidden');
    // getting postback list and making iframe
    // 
    $(document).on('click','.add_template',function(e){
        e.preventDefault();
        var current_id=$(this).prev().prev().attr("id");
        var page_id=$(this).attr("page_id_add_postback");
        if(page_id=="")
        {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
          return false;
        }
        $("#add_template_modal").attr("current_id",current_id);
        $("#add_template_modal").modal();
    });

    $(document).on('click','.ref_template',function(e){
      e.preventDefault();
      var current_val=$(this).prev().prev().prev().val();
      var current_id=$(this).prev().prev().prev().attr("id");
      var page_id=$(this).attr("page_id_ref_postback");
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_otn_postback_refresh",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response).val(current_val);
         }
       });
    });

    $('#add_template_modal').on('hidden.bs.modal', function (e) { 
      var current_id=$("#add_template_modal").attr("current_id");
      var page_id=$(".add_template").attr("page_id_add_postback");
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_otn_postback_refresh",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response);
         }
       });
    });



    function page_change_action()
    {
      var page_id=$('#page_table_id').val();
      if(page_id=='') return;

      $(".add_template").attr("page_id_add_postback",page_id);
      $(".ref_template").attr("page_id_ref_postback",page_id);
      
      $.ajax({
        type:'POST' ,
        url: base_url+'messenger_bot/get_otn_reply_postback',
        data: {page_auto_id:page_id},
        dataType : 'JSON',
        success:function(response){
          setTimeout(function(){
            $(".push_postback").html(response.dropdown);
          },500); 
        }
      });
      
      $('.show_label').addClass('hidden');
      $.ajax({
        type:'POST' ,
        url: base_url+'messenger_bot/get_label_dropdown',
        data: {page_id:page_id},
        dataType : 'JSON',
        success:function(response){
          $("#create_label_postback").attr("page_id_for_label",page_id); // put page_table_id for create label
          $('.show_label').removeClass('hidden');
          setTimeout(function(){
            $('#first_dropdown').html(response.first_dropdown);
          },500);      
        }
      });

      $('.dropdown_con').addClass('hidden');
      var is_drip_campaigner_exist='<?php echo $this->is_drip_campaigner_exist;?>';
      var is_sms_email_drip_campaigner_exist='<?php echo $this->is_sms_email_drip_campaigner_exist;?>';
      if(is_drip_campaigner_exist==false && is_sms_email_drip_campaigner_exist==false) return;

      $.ajax({
        type:'POST' ,
        url: base_url+'messenger_bot/get_drip_campaign_dropdown',
        data: {page_id:page_id},
        dataType : 'JSON',
        success:function(response){
          $('.dropdown_con').removeClass('hidden');
          setTimeout(function(){
            $('#dripcampaign_dropdown').html(response.dropdown_value);
          },500);    
                
        }
      });
      // $('.dropdown_con').removeClass('hidden');
    }

    // create an new label and put inside label list
    $(document).on('click','#create_label_postback',function(e){
      e.preventDefault();
      
      var page_id=$(this).attr('page_id_for_label');

      swal("<?php echo $this->lang->line('Label Name'); ?>", {
        content: "input",
        button: {text: "<?php echo $this->lang->line('New Label'); ?>"},
      })
      .then((value) => {
        var label_name = `${value}`;
        if(label_name!="" && label_name!='null')
        {
          $("#save_changes").addClass("btn-progress");
          $.ajax({
            context: this,
            type:'POST',
            dataType:'JSON',
            url:"<?php echo site_url();?>home/common_create_label_and_assign",
            data:{page_id:page_id,label_name:label_name},
            success:function(response){
                $("#save_changes").removeClass("btn-progress");
                if(response.error) {

                  var span = document.createElement("span");
                  span.innerHTML = response.error;

                  swal({
                    icon: 'error',
                    title: '<?php echo $this->lang->line('Error'); ?>',
                    content:span,
                  });

                } else {
                  var newOption = new Option(response.text, response.id, true, true);
                  $('#label_ids').append(newOption).trigger('change');
                }
            }
          });
        }
      });

    });



    $(document).on('click','#submit',function(e){   
      e.preventDefault();

      var bot_name = $("#bot_name").val();
      var template_postback_id = $("#template_postback_id").val();
      var reply_postback_id = $("#reply_postback_id").val();

      var page_table_id = $("#page_table_id").val();
      var new_variable_name = "js_array_"+page_table_id;

      if(bot_name == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Give Template Name')?>", 'warning');
        return;
      }

      if(page_table_id == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select a page')?>", 'warning');
        return;
      }

      if(template_postback_id == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please give a postback ID')?>", 'warning');
        return;
      }

      if(reply_postback_id == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select a reply postback ID')?>", 'warning');
        return;
      }

      
      $(this).addClass('btn-progress');

      $("input:not([type=hidden])").each(function(){
        if($(this).is(":visible") == false)
          $(this).attr("disabled","disabled");
      });

      
      var iframe="<?php echo $iframe;?>";
      var temp_url = base_url+"messenger_bot/otn_edit_template_action"

      var queryString = new FormData($("#messenger_bot_form")[0]);
        $.ajax({
          context: this,
          type:'POST' ,
          url: temp_url,
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          success:function(response){
              $(this).removeClass('btn-progress');
              if(response.status=="1")
              {
                if(iframe=='1') 
                {
                  $(this).attr('disabled','disabled');
                  swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success');
                }
                else
                {
                  var link="<?php echo $redirect_url; ?>";
                  swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                    window.location.assign(link);
                  });

                }

              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
          }

        });

    });

  }); 
</script>

<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body"> 
        <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-info"></i> <?php echo $this->lang->line('campaign error'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="alert text-center alert-warning" id="error_modal_content">
          
        </div>
      </div>
    </div>
  </div>
</div>