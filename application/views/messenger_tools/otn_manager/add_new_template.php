<?php
  $redirect_url = site_url('messenger_bot/otn_template_manager/');
  $THEMECOLORCODE = "#607D8B";
?>

<style type="text/css">

  label.css-label {
    background-image:url(<?php echo base_url('assets/images/csscheckbox.png'); ?>);
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    color: <?php echo $THEMECOLORCODE; ?> !important;
    font-size: 15px !important;
  }
  .css-label-container{padding:10px;border:1px dashed <?php echo $THEMECOLORCODE; ?>;border-radius: 5px;}

  <?php if($iframe=='1') echo '
  .card-primary .card-body,.card-primary .card-header,.card-primary .card-footer{padding:15px;}
  .card-secondary .card-body,.card-secondary .card-header,.card-secondary .card-footer{padding:12px;}';
  ?>
</style>

<?php if($iframe !='1') : ?>
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add an OTN PostBack Template");?></h1>
    <div class="section-header-breadcrumb">
       <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot/otn_template_manager'); ?>"><?php echo $this->lang->line('OTN Post-back Manager'); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>
<?php endif; ?>

  <div class="card no_shadow ">
    <div class="card-body <?php if($iframe=='1') echo 'padding-0';?>">

      <div class="row">
        <div class="<?php if($is_iframe=="1") echo 'col-12'; else echo 'col-12 col-lg-12';?>">
          <form action="#" method="post" id="messenger_bot_form" style="padding-left: 0;">
            <div class="row"> 
              <div class="<?php if($default_page == '') echo 'col-12 col-sm-6'; else echo 'col-12'; ?>"> 
                <div class="form-group">
                  <label><?php echo $this->lang->line("Template Name"); ?></label>
                  <input type="text" name="bot_name" id="bot_name" class="form-control">
                </div>       
              </div> 
              <?php if($default_page != '') : ?>
                <input type="hidden" name="page_table_id" id="page_table_id" value="<?php echo $default_page; ?>">
              <?php else : ?>
              <div class="col-12 col-sm-6"> 
                <div class="form-group">
                  <label><?php echo $this->lang->line("Choose a Page"); ?></label>
                  <?php 
                    $page_list[''] = "Please select a page";
                    echo form_dropdown('page_table_id',$page_list,$default_page,'id="page_table_id" class="form-control select2"'); 
                  ?>
                </div>       
              </div>
              <?php endif; ?>
            </div>

            <div class="row"> 
              <?php if($default_child_postback_id == '') : ?>
              <div class="col-12 col-sm-6">
                <div class="form-group">
                  <label>
                    <?php echo $this->lang->line("Select a reply PostBack"); ?>
                  </label>
                  <select class="form-control push_postback select2"  name="reply_postback_id" id="reply_postback_id">
                    <option value=""><?php echo $this->lang->line('Please select a page first.'); ?></option>
                  </select>

                  <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                  <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                  
                </div>
              </div>              
              
              <div class="col-12 col-sm-6"> 
                <div class="form-group" id="postback_section">
                  <label>
                    <?php echo $this->lang->line("OTN PostBack id"); ?>
                    <a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <input type="text" name="template_postback_id" id="template_postback_id" class="form-control">
                </div>       
              </div>  
              <?php else : ?>
                  <input type="hidden" name="template_postback_id" id="template_postback_id" value="<?php echo urldecode($default_child_postback_id); ?>">
                  <input type="hidden" name="postback_type" value="child" id="child_postback">
              <?php endif; ?>
            </div>
            <br/>

            

            <?php 
            $first_col= "col-12 col-sm-6";
            if(!$this->is_drip_campaigner_exist && !$this->is_sms_email_drip_campaigner_exist)  $first_col="col-12";              
            $popover='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Labels").'" data-content="'.$this->lang->line("If you choose labels, then when user click on this PostBack they will be added in those labels, that will help you to segment your leads & broadcasting from Messenger Broadcaster. If you don't want to add labels for this PostBack , then just keep it blank as it is.").'"><i class="fa fa-info-circle"></i> </a>';
            echo '<div class="row">
              <div class="'.$first_col.'"> 
                  <div class="form-group">
                    <label style="width:100%" class="show_label hidden">
                    '.$this->lang->line("Choose Labels").' '.$popover.'
                    <a class="blue float-right pointer" page_id_for_label="" id="create_label_postback"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create Label").'</a>  
                    </label>
                    <span id="first_dropdown"></span>                                  
                  </div>       
              </div>';

              if($this->is_drip_campaigner_exist || $this->is_sms_email_drip_campaigner_exist)
              {
                $popover2='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Sequence Campaign").'" data-content="'.$this->lang->line("Choose any drip or sequence campaign to set when user click on this postback button. Keep it blank if you don't want to set.").'"><i class="fa fa-info-circle"></i> </a>';
                 echo '
                  <div class="col-12 col-sm-6 hidden dropdown_con"> 
                      <div class="form-group">
                        <label style="width:100%">
                        '.$this->lang->line("Choose Sequence Campaigns").' '.$popover2.'
                        </label>
                        <span id="dripcampaign_dropdown"></span>                                  
                      </div>       
                  </div>';
              }
              echo '</div>';                  
            ?>                
            
            <br/><br/>
            <div class="row">
              <div class="col-6">
                <button id="submit" class="btn btn-lg btn-primary"><i class="fa fa-send"></i> <?php echo $this->lang->line('submit'); ?></button>
              </div>
              <?php if($iframe != '1') : ?>
              <div class="col-6">
                <a class="btn btn-lg btn-secondary float-right" href="<?php echo base_url("messenger_bot/otn_template_manager"); ?>"><i class="fas fa-times"></i> <?php echo $this->lang->line('Back'); ?></a>
              </div>
              <?php endif; ?>
            </div>



          </form>

        </div>


      </div>

    </div>
  </div>

<?php if($iframe !='1') : ?>
</section>
<?php endif; ?>



          



<?php 
  $somethingwentwrong = $this->lang->line("something went wrong.");  
  $doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this bot?");
  $areyousure=$this->lang->line("are you sure");
?>

<script type="text/javascript">

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
        $(".show_label #create_label_postback").attr("page_id_for_label",page_id); // put page_table_id for create label
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



  $(document).ready(function(e){
   
    $(".push_postback").select2({
      tags: true,
      width: '100%'
    });


    var default_page = "<?php echo $default_page; ?>";
    if(default_page != '') page_change_action();


    $(document).on('change','#page_table_id',function(){  
      page_change_action();
    });


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


    // getting postback list and making iframe
    $('#add_template_modal').on('shown.bs.modal',function(){ 
      var page_id=$(".add_template").attr("page_id_add_postback");
      var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
      $(this).find('iframe').attr('src',iframe_link); 
    });   
    refresh_template("0");
    $("#loader").addClass('hidden');
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
         url: base_url+"messenger_bot/get_postback",
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


    function refresh_template(is_from_add_button='1')
    {
       var page_id=$(this).attr("page_id_ref_postback");
       if(page_id=="")
       {
         alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_otn_postback_refresh",
         data: {page_id:page_id,order_by:"template_name",is_from_add_button:is_from_add_button},
         success:function(response){
           $(".push_postback").html(response);
         }
       });
     }

  });
</script>


<script type="text/javascript">
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  
  <?php foreach($page_list as $key=>$value) : ?>    
    var js_array_<?php echo $key ?> = [<?php echo ""; ?>];
  <?php endforeach; ?> 


  var areyousure="<?php echo $areyousure;?>";
  
  $(document).ready(function() {
    
    function hasDuplicates(array) {
      var valuesSoFar = Object.create(null);
      for (var i = 0; i < array.length; ++i) {
        var value = array[i];
        if (value in valuesSoFar) {
          return true;
        }
        valuesSoFar[value] = true;
      }
      return false;
    }


    $(document).on('click','#submit',function(e){   
      e.preventDefault();

      var bot_name = $("#bot_name").val();
      var template_postback_id = $("#template_postback_id").val();
      var reply_postback_id = $("#reply_postback_id").val();

      var reg = /^[0-9a-z_ -]+$/i;
      var output = reg.test(template_postback_id);
      if(output === false)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your main PostBack Id')?>', 'warning');
        return;
      }

      var page_table_id = $("#page_table_id").val();
 

      var keyword_type = $("input[name=keyword_type]:checked").val();

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

      var queryString = new FormData($("#messenger_bot_form")[0]);
      $.ajax({
        context: this,
        type:'POST' ,
        url: base_url+"messenger_bot/otn_create_template_action",
        data: queryString,
        dataType : 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success:function(response){
          $(this).removeClass('btn-progress');
          if(response.status == '1')
          {
            var link="<?php echo site_url('messenger_bot/otn_template_manager/'); ?>"+page_table_id+'/1';            
            swal('<?php echo $this->lang->line("Success"); ?>', "<?php echo $this->lang->line('Template has been created successfully.'); ?>", 'success').then((value) => {
              window.location.assign(link);
            });
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


<?php if($is_iframe=="1") echo '<link rel="stylesheet" type="text/css" href="'.base_url('css/bot_template.css').'">'; ?>