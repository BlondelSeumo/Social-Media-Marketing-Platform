<style type="text/css">
  .space{height: 10px;}
  .select2{width: 100% !important;}
</style>

<?php $is_broadcaster_exist=$this->is_broadcaster_exist_deprecated; ?>
<?php $is_ultrapost_exist=$this->is_ultrapost_exist; ?>


<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-rss"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <a class="btn btn-primary" id="add_feed" data-toggle="modal" href='#add_feed_modal'><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('New Auto Posting Feed');?></a>
   </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('ultrapost'); ?>"><?php echo $this->lang->line("Facebook Poster"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class='text-center text-primary' style='padding:12px;border:.5px solid #dee2e6;background: #fff;'><?php echo $this->lang->line("RSS auto posting will be publised as Link post.It will post once any new feed comes to RSS feed after setting it in the system. It will not post any existing feeds during setup the campaign.") ?></div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <?php
            echo "<div class='table-responsive data-card'> 
            <table class='table table-bordered table-condensed' id='mytable'>";
              echo "<thead>";
                echo "<tr>";
                  echo "<th>".$this->lang->line("SN")."</th>";
                  echo "<th>".$this->lang->line("Feed Name")."</th>";
                  // echo "<th class='text-center'>".$this->lang->line("Feed Type")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Status")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Last Updated")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Last Feed")."</th>";
                  if($is_broadcaster_exist)
                  echo "<th class='text-center'>".$this->lang->line("Broadcast as Page")."</th>";
                  if($this->is_ultrapost_exist)
                  echo "<th>".$this->lang->line("Post as Pages")."</th>";                
              echo "</tr></thead>";

              echo "<tbody>";
                $i=0;
                foreach ($settings_data as $key => $value) 
                {
                  $i++;
                  if($value['last_pub_date']!="0000-00-00 00:00:00") $last_pub_date=date('j M H:i',strtotime($value['last_pub_date']));
                  else $last_pub_date =  "<i class='fas fa-times'></i>";

                  $page_names=json_decode($value['page_names'],true);
                  if(!is_array($page_names)) $page_names=array();
                  $page_names=array_values($page_names);
                  $page_names=implode(',', $page_names);
                  
                  $page_name=$value['page_name'];

                  if($page_names!="") $page_names="<a target='_BLANK' href='".base_url("ultrapost/text_image_link_video")."'>".$page_names."</a>";
                  if($page_name!="") $page_name="<a target='_BLANK' href='".base_url("messenger_broadcaster/quick_bulk_broadcast_report")."'>".$page_name."</a>";

                  $status='';
                  if($value['status']=='1') $status='<span class="text-success"><i class="fa fa-check-circle"></i> '.$this->lang->line("Active").'</span>';
                  else if($value['status']=='0') $status='<span class="text-danger"><i class="fa fa-times-circle"></i> '.$this->lang->line("Inactive").'</span>';
                  else $status='<span class="text-warning"><i class="fas fa-ban"></i> '.$this->lang->line("Disabled").'</span>';

                  echo "<tr>";
                    echo "<td nowrap>".$i."</td>";
                    echo "<td nowrap><a href='".$value['feed_url']."' target='_BLANK'>".$value['feed_name']."</a></td>";
                    // echo "<td class='text-center' nowrap>".strtoupper($value['feed_type'])."</td>";
                    echo "<td class='text-center' nowrap>".$status."</td>";
                    echo "<td class='text-center' nowrap>";

                    echo '
                    <div class="dropdown d-inline dropright">
                      <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>

                      <div class="dropdown-menu mini_dropdown text-center" style="width:250px !important">

                        <a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Settings").'" class="btn btn-circle btn-outline-primary campaign_settings"><i class="fas fa-cog"></i></a>';

                         if($value['status']=='1')
                         echo  '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Disable").'" class="btn btn-circle btn-outline-warning disable_settings"><i class="fas fa-ban"></i></a>';
                         else echo  '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Enable").'" class="btn btn-circle btn-outline-success enable_settings"><i class="fas fa-check-circle"></i></a>';

                         if($value['cron_status']=='1')
                         echo  '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Force Process").'" class="btn btn-circle btn-outline-warning force_process"><i class="fas fa-play"></i></a>';

                         echo '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Delete").'" class="btn btn-circle btn-outline-danger delete_settings"><i class="fas fa-trash-alt"></i></a>';

                         echo '<a href="" data-id="'.$value['id'].'" data-toggle="tooltip" title="'.$this->lang->line("Error").'" class="btn btn-circle btn-outline-secondary error_log"><i class="fas fa-bug"></i></a>';

                      echo '
                      </div>
                    </div>';

                     echo "<td class='text-center' nowrap>".date("d M H:i",strtotime($value["last_updated_at"]))."</td>";
                     echo "<td class='text-center' nowrap>".$last_pub_date."</td>";

                     if($is_broadcaster_exist)
                     echo "<td class='text-center' nowrap>".$page_name."</td>";

                    if($this->is_ultrapost_exist)
                     echo "<td nowrap>".$page_names."</td>";

                     
                  echo "</tr>";
                }
              echo "</tbody>";
            echo "</table></div>";
            ?>             
          </div>

        </div>
      </div>
    </div>
  </div>
</section>





<?php
  $somethingwentwrong = $this->lang->line("something went wrong, please try again.");  
  $doyoureallywanttodeletethisbot = $this->lang->line("Do you really want to delete this settings?");
  $doyoureallywanttodisablethisbot = $this->lang->line("Do you really want to disable this settings?");
  $doyoureallywanttoenablethisbot = $this->lang->line("Do you really want to enable this settings? This operation may take few time.");
  $areyousure=$this->lang->line("are you sure"); 
?>

<script type="text/javascript">
  $("document").ready(function(){
    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    var base_url="<?php echo site_url(); ?>";
    var areyousure="<?php echo $areyousure;?>";
    var is_broadcaster_exist="<?php echo $is_broadcaster_exist;?>";
    var is_ultrapost_exist="<?php echo $is_ultrapost_exist;?>";
    var doyoureallywanttodeletethisbot="<?php echo $doyoureallywanttodeletethisbot;?>";
    var doyoureallywanttodisablethisbot="<?php echo $doyoureallywanttodisablethisbot;?>";
    var doyoureallywanttoenablethisbot="<?php echo $doyoureallywanttoenablethisbot;?>";
    var somethingwentwrong="<?php echo $somethingwentwrong;?>";

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
    
    var table = $("#mytable").DataTable({
        language: 
        {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
          {
              targets: [5],
              sortable: false
          }
        ]
    });

    (function ($, undefined) {
        $.fn.getCursorPosition = function() {
            var el = $(this).get(0);
            var pos = 0;
            if('selectionStart' in el) {
                pos = el.selectionStart;
            } else if('selection' in document) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            return pos;
        }
    })(jQuery);

    $(document).on('click', '#title_variable', function(event) {
      // event.preventDefault();
        
        let textAreaTxt = $(".emojionearea-editor").html();
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>") {
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
        }

        var txtToAdd = " #TITLE# ";
        var new_text = textAreaTxt + txtToAdd;
        $(".emojionearea-editor").html(new_text);
        $(".emojionearea-editor").click();


    });



    $(document).on('click','.campaign_settings',function(e){ 
      e.preventDefault();
      var id=$(this).attr('data-id');
       $.ajax({
          type:'POST' ,
          url: base_url+"autoposting/campaign_settings",
          data: {id:id},
          dataType: 'JSON',
          success:function(response)
          {  
            if(response.status=='0') $("#settings_modal .modal-footer").hide();
            else $("#settings_modal .modal-footer").show();
            $("#feed_setting_container").html(response.html);
            $("#put_feed_name").html(" : "+response.feed_name);                       
            $("#settings_modal").modal();
          }
      });
     
    });

    $(document).on('click','#save_settings',function(e){ 
      e.preventDefault();

      var post_to_pages = $("#post_to_pages").val();
      var post_to_groups = $("#post_to_groups").val();
      var broadcast_pages='';

      if(is_broadcaster_exist=='1')
      broadcast_pages = $("#page").val();

      if(post_to_pages=='' && broadcast_pages=='')
      {
        swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select pages to publish the feed');?>" , 'error');
        return;
      }

      if(post_to_pages!='')
      {
        var posting_start_time=$("#posting_start_time").val();
        var posting_end_time=$("#posting_end_time").val();
        var rep1 = parseFloat(posting_start_time.replace(":", "."));
        var rep2 = parseFloat(posting_end_time.replace(":", "."));
        var rep_diff=rep2-rep1;

        if(posting_start_time== '' ||  posting_end_time== ''){
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select post between times');?>" , 'error');
          return false;
        }

        if(rep1 >= rep2 || rep_diff<1.0)
        {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Post between start time must be less than end time and need to have minimum one hour time span');?>" , 'error');
          return false;
        }
      }

      if(broadcast_pages!='')
      {
        var broadcast_start_time=$("#broadcast_start_time").val();
        var broadcast_end_time=$("#broadcast_end_time").val();
        var rep1 = parseFloat(broadcast_start_time.replace(":", "."));
        var rep2 = parseFloat(broadcast_end_time.replace(":", "."));
        var rep_diff=rep2-rep1;

        if(broadcast_start_time== '' ||  broadcast_end_time== ''){
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select broadcast between times');?>" , 'error');
          return false;
        }

        if(rep1 >= rep2 || rep_diff<1.0)
        {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Broadcast between start time must be less than end time and need to have minimum one hour time span');?>" , 'error');
          return false;
        }
      }


      // var loading = '<img src="'+base_url+'assets/pre-loader/custom_lg.gif" class="center-block">';
      // $("#submit_status").show();
      var queryString = new FormData($("#campaign_settings_form")[0]);
      $("#save_settings").addClass("btn-progress");
      // var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
      // $("#submit_response").attr('class','').html(loading);
      var id=$(this).attr('data-id');
       $.ajax({
          type:'POST' ,
          url: base_url+"autoposting/create_campaign",
          dataType: 'JSON',
          data: queryString,
          cache: false,
          contentType: false,
          processData: false,
          success:function(response)
          { 
            
            if(response.status=='1') 
            swal('<?php echo $this->lang->line("Success"); ?>', response.message , 'success');
            else swal('<?php echo $this->lang->line("Error"); ?>', response.message , 'error');
            
            // $("#submit_response").html('');
            $("#save_settings").removeClass("btn-progress");
            // $("#submit_status").hide();
          }
      });
     
    });

    $(document).on('click','.enable_settings',function(e){ 
      e.preventDefault();

      $(this).addClass('disabled');
      var id = $(this).attr('data-id');
      var media_type = 'rss';

      swal({
        title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
        text: '<?php echo $this->lang->line("Do you really want to delete this campaign?"); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
            $.ajax({
               type:'POST' ,
               url: base_url+"autoposting/enable_settings",
               data: {id, media_type},
               dataType:'JSON',
               success:function(response)
               {  
                 if(response.status=='0') 
                 {
                   $("#enable"+id).removeClass('disabled');
                   iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message,position: 'bottomRight'});
                 }
                 else 
                 {
                    iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been enabled successfully."); ?>',position: 'bottomRight'});
                    setTimeout(function(){ location.reload(); }, 1000);
                    
                 }
               }
           });
        } 
      });
     
    });

    $(document).on('click','.disable_settings',function(e){ 
      e.preventDefault();

      var id=$(this).attr('data-id');

      swal({
        title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
        text: '<?php echo $this->lang->line("Do you really want to delete this campaign?"); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
            $.ajax({
               type:'POST' ,
               url: base_url+"autoposting/disable_settings",
               data: {id:id},
               success:function(response)
               {  
                 iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been disabled successfully."); ?>',position: 'bottomRight'});
                 setTimeout(function(){ location.reload(); }, 1000);
               }
           });
        } 
      });
     
    });

    $(document).on('click','.force_process',function(e){ 
      e.preventDefault();

      var id=$(this).attr('data-id');

      swal({
        title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
        text: '<?php echo $this->lang->line("Do you really want to force process this campaign? This can be helpful if your RSS posting tools have stopped for some unknown reasons and not responding."); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
            $.ajax({
               type:'POST' ,
               url: base_url+"autoposting/force_process",
               data: {id:id},
               success:function(response)
               {  
                 iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been processed by force successfully."); ?>',position: 'bottomRight'});
                 setTimeout(function(){ location.reload(); }, 1000);
               }
           });
        } 
      });
     
    });

    $(document).on('click','.delete_settings',function(e){ 
      e.preventDefault();

      var id=$(this).attr('data-id');

      swal({
        title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
        text: '<?php echo $this->lang->line("Do you really want to delete this campaign?"); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/delete_settings",
              data: {id:id},
              success:function(response)
              {  
                iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
                setTimeout(function(){ location.reload(); }, 1000);
              }
          });
        } 
      });
     
    });

    $(document).on('click','#add_feed_submit',function(){ 

      var feed_type = $("input[name='feed_type']:checked").val();
      var feed_name = $("#feed_name").val();
      var feed_url = $("#feed_url").val();
      if(feed_type=='')
      {
        swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select feed type');?>" , 'error');
        return;
      }
      if(feed_name=='')
      {
        swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select feed type name');?>" , 'error');
        return;
      }
      if(feed_url=='')
      {
        swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Feed URL can not be empty');?>" , 'error');
        return;
      }
      $("#add_feed_submit").addClass('btn-progress');
      // $("#loader").removeClass('hidden');
      var queryString = new FormData($("#add_feed_form")[0]);
      $.ajax({
          type:'POST' ,
          url: base_url+"autoposting/add_feed_action",
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          success:function(response)
          {  
            if(response.status=='1') 
            {
               swal('<?php echo $this->lang->line("Success"); ?>', response.message , 'success');
            }
            else 
            {
              swal('<?php echo $this->lang->line("Error"); ?>', response.message , 'error');
            }
            // $("#loader").addClass('hidden');
            $("#add_feed_submit").removeClass('btn-progress');
          }

      });
    }); 

    $('#add_feed_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });
    $('#settings_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });


    $(document).on('click','.error_log',function(e){ 
      e.preventDefault();
      $("#error_loading").removeClass('hidden');
      $("#error_modal_container").html("");
      $("#error_modal").modal();
      var id=$(this).attr('data-id');
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/error_log",
              data: {id:id},
              success:function(response)
              {  
                $("#error_modal_container").html(response);
                $("#error_loading").addClass('hidden');
              }
          });     
    });

    $(document).on('click','.clear_log',function(e){ 
      e.preventDefault();      
      var id=$(this).attr('data-id');
      swal({
        title: '<?php echo $this->lang->line("Clear Log"); ?>',
        text: '<?php echo $this->lang->line("Do you really want to clear log?"); ?>',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/clear_log",
              data: {id:id},
              success:function(response)
              {  
                $("#error_modal").modal('toggle');
                swal('<?php echo $this->lang->line("Clear Log"); ?>', "<?php echo $this->lang->line('Log has been cleared successfully.');?>" , 'success');
              }
          });
        } 
      });

    });


  });
</script>

<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-bug"></i> <?php echo $this->lang->line("Error Log") ?></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
         <div class="text-center waiting hidden" id="error_loading"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>
         <div id="error_modal_container"></div>    
      </div>
      <div class="modal-footer" style="padding-left: 30px;padding-right: 30px;">
        <button type="button" class="btn-lg btn btn-default float-right" data-dismiss="modal" id="close_settings"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close");?></button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="settings_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-cog"></i> <?php echo $this->lang->line("Campaign Settings") ?> <span id="put_feed_name"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="feed_setting_container">
        
      </div>
      <div class="modal-footer" style="padding-left: 30px;padding-right: 30px;">
        <button type="button" class="btn-lg btn btn-default" data-dismiss="modal" id="close_settings"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close");?></button>
        <button type="button" class="btn-lg btn btn-primary" id="save_settings" style="margin-left: 0;"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaign");?></button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="add_feed_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-rss"></i> <?php echo $this->lang->line("Auto-Posting Feed") ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="#" enctype="multipart/form-data" id="add_feed_form">
        <div class="modal-body">
            <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>
            <div id="response"></div>
            <div class="space"></div>
            <div class='hidden'> <!-- hidden temporarily, will be needed in future -->
                <label class="margin-bottom-label" style="color: rgb(0, 0, 0);">
                <?php echo $this->lang->line("Feed Type") ?> *
                </label>
                <div class="space"></div>
                <?php 
                foreach ($feed_types as $key => $value) 
                {
                   $is_checked=$is_default_label='';
                   if($value=='rss')
                   {
                      $is_checked='checked';
                      $is_default_label='default-label';
                   }
                   echo '<input type="radio" class="css-checkbox" '.$is_checked.' name="feed_type" value="'.$value.'" id="feed_type'.$value.'" style="color: rgb(0, 0, 0);"> <label for="feed_type'.$value.'" class="css-label triple-label '.$is_default_label.'" style="color: rgb(0, 0, 0);"> <i class="checkicon fa fa-check" style="display: none;"></i> '.ucfirst($value).'</label>';
                } ?>
                <div class="space"></div>
            </div>

            <label class="margin-bottom-label" style="color: rgb(0, 0, 0);">
              <?php echo $this->lang->line("Feed Name") ?> *
            </label>
            <div class="space"></div>
            <input type="text" name="feed_name" id="feed_name" class="form-control">

            <div class="space"></div>
            <div class="space"></div>

            <label class="margin-bottom-label" style="color: rgb(0, 0, 0);">
              <?php echo $this->lang->line("RSS Feed URL") ?> *
            </label>
            <div class="space"></div>
            <input type="text" name="feed_url" id="feed_url" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-lg btn btn-primary" id="add_feed_submit"><i class='fa fa-plus-circle'></i> <?php echo $this->lang->line('Add Feed');?></button>
          <button type="button" class="btn-lg btn btn-default" data-dismiss="modal"><i class='fas fa-times'></i> <?php echo $this->lang->line('Close');?></button>
        </div>
      </form>
    </div>
  </div>
</div>
