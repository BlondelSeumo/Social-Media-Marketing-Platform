<script type="text/javascript">
  <?php 
  if($this->session->userdata("is_mobile")=='1') echo 'var areWeUsingScroll = false;';
  else echo 'var areWeUsingScroll = true;';
  if($is_rtl) echo 'var is_rtl = true;';
  else echo 'var is_rtl = false;';
  ;?>
</script>

<?php include(APPPATH."views/include/js_variables.php");?>

<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>

<!-- General JS Scripts -->
<!-- <script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script> -->
<script src="<?php echo base_url(); ?>assets/modules/popper.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/tooltip.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/nicescroll/jquery.nicescroll.min.js"></script>

<script src="<?php echo base_url(); ?>assets/js/stisla.js"></script>


<!-- JS Libraies -->

<script src="<?php echo base_url(); ?>assets/modules/jquery.sparkline.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

<script src="<?php echo base_url(); ?>assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>

<script src="<?php echo base_url(); ?>assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>


<script src="<?php echo base_url(); ?>assets/modules/jquery-ui/jquery-ui.min.js"></script>

<script src="<?php echo base_url(); ?>assets/js/page/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/prism/prism.js"></script>

<script src="<?php echo base_url(); ?>assets/modules/sticky-kit.js"></script>



<script src="<?php echo base_url(); ?>assets/modules/dropzonejs/min/dropzone.min.js"></script>


<script src="<?php echo base_url(); ?>assets/modules/jqvmap/dist/maps/jquery.vmap.indonesia.js"></script>



<script src="<?php echo base_url(); ?>assets/modules/cleave-js/dist/cleave.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/cleave-js/dist/addons/cleave-phone.us.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/jquery-pwstrength/jquery.pwstrength.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/select2/dist/js/select2.full.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/jquery-selectric/jquery.selectric.min.js"></script>


<script src="<?php echo base_url(); ?>assets/modules/codemirror/lib/codemirror.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/codemirror/mode/javascript/javascript.js"></script>


<script src="<?php echo base_url(); ?>assets/modules/gmaps.js"></script>

<script src="<?php echo base_url(); ?>assets/modules/fullcalendar/main.min.js"></script>



<script src="<?php echo base_url(); ?>assets/modules/datatables/datatables.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>



<script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>



<script src="<?php echo base_url(); ?>assets/modules/izitoast/js/iziToast.min.js"></script>



<script src="<?php echo base_url(); ?>assets/modules/upload-preview/assets/js/jquery.uploadPreview.min.js"></script>



<!-- <script src="http://maps.google.com/maps/api/js?key=AIzaSyB55Np3_WsZwUQ9NS7DP-HnneleZLYZDNw&amp;sensor=true"></script> -->

<!-- js for ajax multiselect [zilani 02-07-2019] -->
<link rel="stylesheet" href="<?php echo base_url();?>plugins/multiselect_tokenize/jquery.tokenize.css" type="text/css" />
<script src="<?php echo base_url();?>plugins/multiselect_tokenize/jquery.tokenize.js" type="text/javascript"></script>

<!-- Scrollbar -->
<script src="<?php echo base_url();?>plugins/scrollbar/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>

<!-- Slimscroll -->
<script src="<?php echo base_url();?>plugins/perfect-scrollbar-1.4.0/dist/perfect-scrollbar.js"></script>

<!-- Alerfify https://alertifyjs.com/guide.html -->
<script src="<?php echo base_url('assets/modules/alertifyjs/alertify.min.js')?>"></script>

<!--Jquery Date Time Picker  https://github.com/xdan/datetimepicker-->
<script type="text/javascript" src="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.js"></script>

<!-- Emoji Library-->
<script src="<?php echo base_url();?>plugins/emoji/dist/emojionearea.js" type="text/javascript"></script>


<!-- Custom Universal JS -->
<script>

  // $("document").ready(function(){
  //   $('.modal').on('shown.bs.modal', function () { 
  //     $(this).find('.table-responsive').attr('style','overflow','scroll !important;');
  //   });
  // });

  function goBack(link,insert_or_update,add_base_url) //used to go back to list as crud
  {
    
    // insert_or_update does not have any effect from v6.0
    if (typeof(insert_or_update)==='undefined') insert_or_update = 0;
    if (typeof(add_base_url)==='undefined') add_base_url = 1;

    var mes='';
    mes="<?php echo $this->lang->line('Your data may not be saved.');?>";
    swal({
      title: "<?php echo $this->lang->line("Do you want to go back?");?>",
      text: mes,
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => 
    {
      if (willDelete) 
      {
        if(add_base_url==1)
        link="<?php echo site_url();?>"+link;
        window.location.assign(link);
      } 
    });
  }

  $(document).ready(function() {
    
    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

    $(document).on('change','#selected_global_media_type',function(e){

        // var media_type = 'fb';
        // if($(this).val()=='1') media_type = 'ig';
        
        $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>home/switch_to_media",
          data:{},
          success:function(response)
          {
            location.reload();
          }
        }); 
    });

    $(document).on('click','.are_you_sure',function(e){
      e.preventDefault();
      var link = $(this).attr("href");
      var mes='<?php echo $this->lang->line("Do you really want to delete it?");?>';  
      swal({
        title: "<?php echo $this->lang->line("Are you sure?");?>",
        text: mes,
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => 
      {
        if (willDelete) 
        {
          window.location.href = link;
        } 
      });
    });

    $(document).on('click','.are_you_sure_datatable',function(e){
      e.preventDefault();
      var link = $(this).attr("href");
      var refresh = $(this).attr("data-refresh");
      var csrf_token = $(this).attr('csrf_token');
      if (typeof(csrf_token)==='undefined') csrf_token = '';
      var mes='<?php echo $this->lang->line("Do you really want to delete it?");?>';  
      swal({
        title: "<?php echo $this->lang->line("Are you sure?");?>",
        text: mes,
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => 
      {
        if (willDelete) 
        {
            $(this).addClass('btn-progress btn-danger').removeClass('btn-outline-danger');
            $.ajax({
              context: this,
              url: link,
              type: 'POST',
              dataType: 'JSON',
              data: {csrf_token:csrf_token},
                success:function(response)
                {
                  $(this).removeClass('btn-progress btn-danger').addClass('btn-outline-danger');
                  if(response.status == 1)  
                  {
                    iziToast.success({title: '',message: response.message,position: 'bottomRight'});
                    if(refresh!='0')
                    {
                      if($(this).hasClass('non_ajax')) $(this).parent().parent().hide();
                      else $('#mytable').DataTable().ajax.reload();
                    }
                  }
                  else iziToast.error({title: '',message: response.message,position: 'bottomRight'});
                }
            });
        } 
      });
    });

    $(".account_switch").click(function(e){
      e.preventDefault();
      var id=$(this).attr('data-id');
      $.ajax({
        url: '<?php echo site_url("social_accounts/fb_rx_account_switch");?>',
        type: 'POST',
        data: {id:id},
        success:function(response){
            location.reload(); 
        }
      })
      
    });

    // $("#account_switch_select").change(function(e){
    //   e.preventDefault();
    //   var id=$(this).val();
    //   $.ajax({
    //     url: '<?php echo site_url("social_accounts/fb_rx_account_switch");?>',
    //     type: 'POST',
    //     data: {id:id},
    //     success:function(response){
    //         location.reload(); 
    //     }
    //   })
      
    // });

    $(".gmb_account_switch").click(function(e){
      e.preventDefault();
      var id=$(this).attr('data-id');
      $.ajax({
        url: '<?php echo site_url("social_accounts/gmb_account_switch");?>',
        type: 'POST',
        data: {id:id},
        success:function(response){
            location.reload(); 
        }
      })
      
    });

    $(".language_switch").click(function(e){
      e.preventDefault();
      var language=$(this).attr('data-id');
      $.ajax({
        url: '<?php echo site_url("home/language_changer");?>',
        type: 'POST',
        data: {language:language},
        success:function(response){
            location.reload(); 
        }
      });      
    });

    $("#datatableSelectAllRows").change(function(){
      if ($(this).is(':checked')) 
      $(".datatableCheckboxRow").prop("checked",true);
      else
      $(".datatableCheckboxRow").prop("checked",false);
    });
  });
</script>


<!-- scrollbar -->
<!-- theme:"rounded-dark",
theme: "dark", "light",
theme: "light-2", "dark-2",
theme: "light-thick", "dark-thick",
theme: "light-thin", "dark-thin",
theme "rounded", "rounded-dark", "rounded-dots", "rounded-dots-dark",
theme "3d", "3d-dark", "3d-thick", "3d-thick-dark",
theme: "minimal", "minimal-dark",
theme "light-3", "dark-3",
theme "inset", "inset-dark", "inset-2", "inset-2-dark", "inset-3", "inset-3-dark", -->
<script>
  //iframe auto higth
  function resizeIframe(obj) {

    setTimeout(function(){
      var cacl_height = obj.contentWindow.document.body.scrollHeight;
      if(parseFloat(cacl_height)<800) cacl_height = '800';
      obj.style.height =  cacl_height + 'px';
    }, 3000);


    $(obj).contents().on("mousedown, mouseup, click", function(){
        setTimeout(function(){
          var cacl_height2 = obj.contentWindow.document.body.scrollHeight;
          if(parseFloat(cacl_height2)<800) cacl_height2 = '800';
          obj.style.height = cacl_height2 + 'px';
        }, 500);
    });
  }

  <?php if($this->session->userdata("is_mobile")=='0') : ?>
  $(document).ready(function() { 
     
      $(".xscroll").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"rounded-dark",
        axis: "x"
      });
      $(".yscroll").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"rounded-dark"
      });
      $(".xyscroll").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"rounded-dark",
        axis:"yx"
      });

      $("div:not(.data-card) > .table-responsive").niceScroll();      

      $(".nicescroll,.makeNiceScroll").niceScroll();
      $(".makeNiceScroll").niceScroll();
      $(".makeScroll,.video-widget-info,.account_list").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"rounded-dark"
      });

      $('#xxx').on('hide.bs.dropdown', function (e) {
          if (e.clickEvent) {
            e.preventDefault();
          }
      })
  });
<?php endif; ?>

</script>


<?php if(!isset($iframe) || (isset($iframe) && $iframe!='1')) 
{
  $this->load->view("include/fb_px");
  $this->load->view("include/google_code");
}
?>


<!-- Template JS File -->
<script src="<?php echo base_url(); ?>assets/js/scripts.js"></script>
<script src="<?php echo base_url(); ?>assets/js/custom.js"></script>

<!-- HTML search -->
<script type="text/javascript">
  function search_in_class(obj,class_name){
      var filter=$(obj).val().toUpperCase();
      $('.'+class_name).each(function(){
        var content=$(this).text().trim();

        if (content.toUpperCase().indexOf(filter) > -1) {
          $(this).css('display','');
        }
        else $(this).css('display','none');
      });
  }
  
  function search_in_ul(obj,ul_id){  // obj = 'this' of jquery, ul_id = id of the ul 
    var filter=$(obj).val().toUpperCase();
    $('#'+ul_id+' li').each(function(){
      var content=$(this).text().trim();

      if (content.toUpperCase().indexOf(filter) > -1) {
        $(this).css('display','');
      }
      else $(this).css('display','none');
    });

  }

   function search_in_div(obj,ul_id){  // obj = 'this' of jquery, ul_id = id of the ul 
    var filter=$(obj).val().toUpperCase();
    $('#'+ul_id+' div').each(function(){
      var content=$(this).text().trim();

      if (content.toUpperCase().indexOf(filter) > -1) {
        $(this).css('display','');
      }
      else $(this).css('display','none');
    });
  }
</script>