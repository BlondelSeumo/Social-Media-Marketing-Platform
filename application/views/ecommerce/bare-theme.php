<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
		<title><?php echo isset($page_title) ? $page_title : $this->config->item('product_name');?></title>
		<link rel="shortcut icon" href="<?php echo (isset($favicon) && $favicon!="") ? $favicon : base_url('assets/img/favicon.png');?>">

		<!-- General CSS Files -->
		<?php if(isset($is_rtl) && $is_rtl==true) 
        { ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/rtl/bootstrap.min.css">
            <?php 
        } 
        else 
        { ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
            <?php
        } ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/v4-shims.min.css">
		<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/chocolat/dist/css/chocolat.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/dropzonejs/dropzone.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-daterangepicker/daterangepicker.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/select2/dist/css/select2.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/izitoast/css/iziToast.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">

		<!-- Template CSS -->
		<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css"> -->

		<?php 
		$PRIMARY_COLOR = isset($ecommerce_config['theme_color']) ? $ecommerce_config['theme_color'] : "var(--blue)";
        if($PRIMARY_COLOR=='') $PRIMARY_COLOR = 'var(--blue)';
        $is_guest_login = isset($ecommerce_config['is_guest_login']) ? $ecommerce_config['is_guest_login'] : "0";
        $font = isset($ecommerce_config['font']) ? $ecommerce_config['font'] : '"Trebuchet MS",Arial,sans-serif';
        if($font=='') $font = '"Trebuchet MS",Arial,sans-serif';
		include("application/views/admin/theme/style_theme.php");
		?>
		<style type="text/css">
			a.bg-primary:focus, a.bg-primary:hover, button.bg-primary:focus, button.bg-primary:hover{background-color: #000!important;}
			a:hover{color:var(--blue);}
			a.text-primary:focus, a.text-primary:hover{color:var(--blue) !important;}
            .border-primary{border-color: <?php echo $PRIMARY_COLOR; ?> !important;}
		</style>

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">
		<!-- Custom -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
        <?php if(isset($is_rtl) && $is_rtl==true) { ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/rtl.css">        
        <?php } ?>
		<!-- General JS Scripts -->
		<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/popper.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/tooltip.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/moment.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/stisla.js"></script>
        <script src="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
		<!-- JS Libraies -->
		<script src="<?php echo base_url(); ?>assets/modules/dropzonejs/min/dropzone.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap-daterangepicker/daterangepicker.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/select2/dist/js/select2.full.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/datatables/datatables.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/izitoast/js/iziToast.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.js"></script>
        <link href="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />

		<!-- Template JS File -->
		<script src="<?php echo base_url(); ?>assets/js/scripts.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/custom.js"></script>

        <script type="text/javascript">
          <?php
          if(isset($is_rtl) && $is_rtl==true) echo 'var is_rtl = true;';
          else echo 'var is_rtl = false;';
          ;?>
        </script>

        <style type="text/css">
            html{overflow-x: hidden;}
            @media only screen and (max-width: 760px) {
              #device-check{ display: none; }
            }
            body{
            	font-family: <?php echo $font;?> !important;
            }
            /*.header h4{font-family: Impact;}*/
            h1, h2, h3, h4, h5, h6 {font-weight: 500 !important; }
            .bg-light{background:#f8f9fa!important}
            .data-card .table td{border: none !important;padding:0 !important;}
            .data-card .table-bordered tbody{border: none !important;}
            table.dataTable.no-footer{border-bottom-width: 0 !important;}
            .data-card .dataTables_length,.data-card .dataTables_info{display: none  !important;}
            div.dataTables_wrapper div.dataTables_paginate ul.pagination{justify-content:center !important;}
             .modal{padding-right: 0 !important;}

             .modal-dialog {
              width: 100%;
              height: 100%;
              margin: 0;
              padding: 0;
            }

            .modal-content {
              /*height: auto;*/
              height: 100%;
              border-radius: 0;
            }
            .modal-body{height: calc(100% - 58px);overflow-y: auto;}
            .modal-footer{height: 58px;}
            .modal .form-group{margin-bottom: 10px;}
            .modal-header i{font-size: 20px;}  
            .refund_terms a{padding-left: 34px !important;}
            .list-group-flush {border:none;}
            .list-group-flush .list-group-item{border-color: #e4e6fc;}
            #dismiss {line-height: 0;}
            #dismiss i{font-size: 20px;}
            #sidebar .list-group-item{padding: 0;font-size: 12px;}
            #sidebar .list-group-item i{padding-right: 10px;}
            #sidebar a, #sidebar a:hover, #sidebar a:focus {
                /*color: inherit;*/
                text-decoration: none;
                transition: all 0.3s;
            }  
            #sidebar {
                width: 250px;
                position: fixed;
                top: 0;
                left: -250px;
                height: 100vh;
                z-index: 999;
                color: #fff;
                transition: all 0.3s;
                overflow-y: auto;
                box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.2);
            }
            #sidebar.active {
                left: 0;
            }
            #sidebar .sidebar-header {
                padding: 25px 15px 0 15px;
            }  
            #sidebar ul p {
                color: #fff;
                padding: 10px;
            }
            #sidebar ul li a {
                padding: 10px 10px 10px 15px;
                font-size: 1.2em;
                display: block;
            }
            a[data-toggle="collapse"] {
                position: relative;
            }
            a[aria-expanded="false"]::before, a[aria-expanded="true"]::before {
                content: '' !important;
                display: block;
                position: absolute;
                right: 20px;
                font-family: 'Glyphicons Halflings';
                font-size: 0.6em;
            }
            a[aria-expanded="true"]::before {
                content: '\e260';
            }
            ul ul a {
                font-size: 0.9em !important;
                padding-left: 30px !important;
                background: #6d7fcc;
            }
            .d-print-thermal{display: none;}
            .fa-star.text-small{font-size:10px;}   
        </style>


	</head>
	
	<body class="bg-light">
    <a id="login_form" class="d-none"></a> <!-- needed to open login modal -->
    <div id="device-check"></div>
    <?php
    if(!isset($show_search)) $show_search = false;
    if(!isset($show_header)) $show_header = false;
    $js_store_unique_id = isset($social_analytics_codes['store_unique_id']) ? $social_analytics_codes['store_unique_id'] : "";
    $js_store_id = isset($social_analytics_codes['store_id']) ? $social_analytics_codes['store_id'] : $social_analytics_codes['id'];
    $js_user_id = isset($social_analytics_codes['user_id']) ? $social_analytics_codes['user_id'] : $social_analytics_codes['user_id'];  
    $subscriberId=$this->session->userdata($js_store_id."ecom_session_subscriber_id");
	if($subscriberId=="")  $subscriberId = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
	if($subscriberId=='') $subscriberId = $this->uri->segment(4);
	$currentCart = isset($current_cart)?$current_cart:array();    
    $function = $this->uri->segment(2);

    $first_menu = "";
    if(isset($social_analytics_codes))
   	{
   		$store_link = base_url("ecommerce/store/".$social_analytics_codes['store_unique_id']);
   		if($subscriberId!='') $store_link.='?subscriber_id='.$subscriberId;
   		$store_name_logo = ($social_analytics_codes['store_favicon']!='') ? '<img alt="'.$social_analytics_codes['store_name'].'" class="rounded-circle" style="width:40px;height:40px;" src="'.base_url("upload/ecommerce/".$social_analytics_codes['store_favicon']).'">' : '';
   		$first_menu = !empty($store_name_logo) ? '<a class="mr-3" href="'.$store_link.'">'.$store_name_logo.'</a>' : '';
   	}

	?>
	  <div id="app">
	  	<?php if($show_header):?>
  		<div class="header w-100 pt-3 pb-3 bg-primary">
  		  <div class="container">		  
	  		  <ul class="list-unstyled">
	  		    <li class="media">
	  		      <?php echo $first_menu; ?>
	  		      <div class="media-body">
	  		         <h4 class="text-white pt-1"><?php echo isset($social_analytics_codes['store_name']) ? $social_analytics_codes['store_name'] : "";?></h4>
	  		      </div>
	  		    </li>
	  		  </ul>
  		  
	  		  <?php if($show_search):?>
	  		  <div class='m-0 mt-3 w-100 search_form'>
	  		    <div class="input-group">
	  		      <?php
	  		      $url_cat =  isset($_GET["category"]) ? $_GET["category"] : "";
	  		      ?>
	  		      <input type="text" onkeyup="search_product(this,'product-container')" autofocus="" class="form-control" name="search" id="search" value= "<?php echo $this->session->userdata('search_search');?>" 
	  		      placeholder="<?php echo $this->lang->line("Search"); ?>">
	  		    </div>
	  		  </div>
	  		  <?php endif; ?>
  		  </div>  
  		</div>
	  	<?php endif; ?>
  	
	    <div class="main-wrapper h-100">
			<div class="container" id="d-main-container">
				<?php 
					if(isset($body)) $this->load->view($body);
					else echo $output;
				?>
			</div>
			<?php if(isset($social_analytics_codes)) echo mec_sidebar($social_analytics_codes,$subscriberId,$currentCart); ?>
		</div>	  
	  </div>	   
    <?php 
    if(isset($social_analytics_codes)) 
    {
        $ecommerceConfig = array();
        if(isset($ecommerce_config)) $ecommerceConfig = $ecommerce_config;

        if($function=='order' && $this->session->userdata("logged_in")=='1') echo '';
        else echo mec_sticky_footer($social_analytics_codes,$subscriberId,$currentCart,$ecommerceConfig);
    }
    ?>
	</body>
</html>

<script type="text/javascript">
    var is_mobile = areWeUsingScroll = false;
	var subscriber_id = '<?php echo $subscriberId;?>';
	var store_id = '<?php echo $js_store_id;?>';
    var store_unique_id = '<?php echo $js_store_unique_id;?>';
    var js_user_id = '<?php echo $js_user_id;?>';
    var is_in_iframne = false;
    if ( window.location !== window.parent.location ) is_in_iframne = true;   

    $(document).ready(function () {
        if( $('#device-check').css('display')=='none') {
           is_mobile = true;       
        }
        if(!is_mobile || is_in_iframne)
        {
            // $("#sidebar").niceScroll();
            // $(".modal-body").niceScroll();
            $(".category_container").niceScroll();
        }

        $(".print-options").click(function () {
            var id  = $(this).attr('id');
            var contents = $("#print-area").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
            //Create a new HTML document.

            //Append the external CSS file.
            if(id=="mobile-print")
              frameDoc.document.write('<link href="<?php echo base_url();?>assets/css/print/ecommerce-thermal-mobile-print.css" rel="stylesheet" type="text/css" />');            
            else if(id=="thermal-print")
              frameDoc.document.write('<link href="<?php echo base_url();?>assets/css/print/ecommerce-thermal-print.css" rel="stylesheet" type="text/css" />');
            else frameDoc.document.write('<link href="<?php echo base_url();?>assets/css/print/ecommerce-print.css" rel="stylesheet" type="text/css" />');
            //Append the DIV contents.
            frameDoc.document.write(contents);
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 1000);
        });


        $(document).on('click', '#login_form', function(e) {
            e.preventDefault();
        	$("#LoginModal").modal();
        });

        $(document).on('click', '#register_form', function(e) {
            e.preventDefault();
            $("#RegisterModal").modal();
        });

        $(document).on('click', '#login_submit', function(e) {

            var email = $("#login_email").val();
            var password = $("#login_password").val();
            if(email=="" || password==""){
                swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line("Email and password are required."); ?>", 'error'); 
                return;
            }
            $("#login_submit").addClass('btn-progress');

            $.ajax({
              context: this,
              type:'POST',
              dataType:'JSON',
              url:"<?php echo site_url();?>ecommerce/login_action",
              data:{email,password,store_id},
              success:function(response){
                $("#login_submit").removeClass('btn-progress'); 
                if(response.status=='0')
                swal("<?php echo $this->lang->line('Error'); ?>", response.message, 'error');
                else location.reload();
              }
            });
            e.preventDefault();
        });

        $(document).on('click', '#register_submit', function(e) {

            var register_first_name = $("#register_first_name").val();
            var register_last_name = $("#register_last_name").val();
            var register_email = $("#register_email").val();
            var register_password = $("#register_password").val();
            var register_password_confirm = $("#register_password_confirm").val();

            if(register_first_name=="" || register_last_name=="" || register_email=="" || register_password=="" || register_password_confirm==""){
                swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line("Please fill the required fields."); ?>", 'error'); 
                return;
            }
            $("#register_submit").addClass('btn-progress');

            $.ajax({
              context: this,
              type:'POST',
              dataType:'JSON',
              url:"<?php echo site_url();?>ecommerce/register_action",
              data:{register_first_name,register_last_name,register_email,register_password,register_password_confirm,store_id,js_user_id},
              success:function(response){
                $("#register_submit").removeClass('btn-progress'); 
                if(response.status=='0')
                swal("<?php echo $this->lang->line('Error'); ?>", response.message, 'error');
                else location.reload();
              }
            });
            e.preventDefault();
        });

        $(document).on('click', '#guest_register_form', function(e) {            
            $(this).addClass('btn-progress');
            $.ajax({
              context: this,
              type:'POST',
              dataType:'JSON',
              url:"<?php echo site_url();?>ecommerce/guest_login_action",
              data:{store_id,js_user_id},
              success:function(response){
                $(this).removeClass('btn-progress'); 
                if(response.status=='0')
                swal("<?php echo $this->lang->line('Error'); ?>", response.message, 'error');
                else location.reload();
              }
            });
            e.preventDefault();
        });


        $(document).on('click', '#logout', function(e) {       
            e.preventDefault();
            $.ajax({
              context: this,
              type:'POST',
              data:{store_id,store_unique_id,subscriber_id},
              url:"<?php echo site_url();?>ecommerce/logout",
              success:function(response){ 
                if(response=='1') location.reload();
                else window.location.replace(response);
              }
            });
        });

        $(document).on('click', '#sidebarCollapse', function(e) {
            if(!$("#sidebar").hasClass('active'))
            {
                $('#sidebar').addClass('active');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            }
            else $('#sidebar').removeClass('active');
        });

        $(document).on('click', '#showProfile', function(e) {
        	e.preventDefault();
        	$("#save_profile").addClass('btn-progress');
        	$("#profileModal").modal();
        	$.ajax({
        	  context: this,
        	  type:'POST',
        	  url:"<?php echo site_url();?>ecommerce/get_buyer_profile",
        	  data:{subscriber_id:subscriber_id,store_id:store_id},
        	  success:function(response){
        	    $("#profileModalBody").html(response);
              $("#save_profile").removeClass('btn-progress');
              if($("#profileModal select[name=country").length>0)
        	    {
                setTimeout(function(){ 
                 $("#profileModal select[name=country]").trigger('change');  
                }, 500); 
              }
              else $("#save_profile").hide();	    
        	  }
        	});
        });

        $(document).on('change', '#profileModal #country', function(e) {
          var setval = $("#profileModal select[name=country] option:selected").attr('phonecode');         
          if( $("#profileModal input[name=mobile]").val()=="")
          {
          	$("#profileModal #phonecode_val").html(setval);
          	$("#profileModal #phonecode_val").parent().removeClass('d-none');
          }
          else
          {
          	$("#profileModal #phonecode_val").html('');
          	$("#profileModal #phonecode_val").parent().addClass('d-none');
          }
        });

        $(document).on('change', '#deliveryAddressModalBody #country', function(e) {
          var setval = $("#deliveryAddressModal select[name=country] option:selected").attr('phonecode');         
          if( $("#deliveryAddressModal input[name=mobile]").val()=="")
          {
            $("#deliveryAddressModal #phonecode_val").html(setval);
            $("#deliveryAddressModal #phonecode_val").parent().removeClass('d-none');
          }
          else
          {
            $("#deliveryAddressModal #phonecode_val").html('');
            $("#deliveryAddressModal #phonecode_val").parent().addClass('d-none');
          }
        });



        $(document).on('click', '#save_profile', function(e) {
        	e.preventDefault();
        	var first_name = $("#profileModal input[name=first_name]").val();
        	var last_name = $("#profileModal input[name=last_name]").val();
        	var street = $("#profileModal input[name=street]").val();
        	var state = $("#profileModal input[name=state]").val();
        	var city = $("#profileModal input[name=city]").val();
        	var zip = $("#profileModal input[name=zip]").val();
        	var country = $("#profileModal select[name=country]").val();
        	var country_code = $("#profileModal select[name=country] option:selected").attr('phonecode');
        	var email = $("#profileModal input[name=email]").val();
        	var mobile = $("#profileModal input[name=mobile]").val();

            if(first_name=="" || last_name=="" || street==""){
                swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line("Please input the required fields."); ?>", 'error'); 
                return;
            }
        	$("#save_profile").addClass('btn-progress');

        	$.ajax({
        	  context: this,
        	  type:'POST',
        	  dataType:'JSON',
        	  url:"<?php echo site_url();?>ecommerce/save_profile_data",
        	  data:{subscriber_id,store_id,first_name,last_name,street,state,city,zip,country,email,mobile,country_code},
        	  success:function(response){
        	    $("#save_profile").removeClass('btn-progress');
        	    if(response.status=='1') 
        	    {
        	    	iziToast.success({title: "",message: response.message,position: 'bottomRight',timeout: 3000});
        	    	$("#profileModal").modal('hide');
                    $("#sidebarCollapse").click();
                    if (typeof load_address_list !== "undefined") load_address_list();
        	    }
        	    else
                {
                    var span = document.createElement("span");
                    span.innerHTML = response.message;
                    swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {             
                       $("#login_form").trigger('click');
                    });
                }
        	  }
        	});

        });


        $(document).on('click', '#showAddress', function(e) {
        	e.preventDefault();
            var data_close = $(this).attr('data-close');
        	$("#save_address").addClass('d-none');
            $("#save_address").attr('data-close',data_close);
        	$("#new_address").removeClass('d-none')
        	$("#new_address").addClass('btn-progress');
        	$("#deliveryAddressModal").modal();
        	$.ajax({
        	  context: this,
        	  type:'POST',
        	  url:"<?php echo site_url();?>ecommerce/get_buyer_address_list",
        	  data:{subscriber_id:subscriber_id,store_id:store_id,data_close},
        	  success:function(response){
        	    $("#deliveryAddressModalBody").html(response);
        	    $("#new_address").removeClass('btn-progress');
              if($("#deliveryAddressModalBody .list-group").length==0)
              $("#new_address").hide();
        	  }
        	});
        });

        $(document).on('click', '#new_address', function(e) {
        	e.preventDefault();
        	$("#new_address").addClass('btn-progress');
        	$.ajax({
        	  context: this,
        	  type:'POST',
        	  url:"<?php echo site_url();?>ecommerce/get_buyer_address",
        	  data:{subscriber_id:subscriber_id,store_id:store_id,operation:'add'},
        	  success:function(response){
        	    $("#deliveryAddressModalBody").html(response);
        	    $("#save_address").removeClass('d-none');
        	    $("#new_address").addClass('d-none');
        	    $("#new_address").removeClass('btn-progress');
                setTimeout(function(){ 
                 $("#deliveryAddressModal select[name=country]").trigger('change');  
                }, 500);  
        	  }
        	});
        });

        $(document).on('click', '.saved_address_row', function(e) {
        	e.preventDefault();
        	var data_close = $(this).attr('data-close');
            var id = $(this).attr('data-id');
        	var profile_address = $(this).attr('data-profile');
        	if(profile_address=='1')
        	{
        		$("#deliveryAddressModal").modal('hide');
        		setTimeout(function(){
        		 $("#showProfile").trigger('click');
        		}, 500);  
        		return;
        	}
        	$("#new_address").addClass('btn-progress');
        	$.ajax({
        	  context: this,
        	  type:'POST',
        	  url:"<?php echo site_url();?>ecommerce/get_buyer_address",
        	  data:{subscriber_id:subscriber_id,store_id:store_id,operation:'edit',id:id},
        	  success:function(response){
        	    $("#deliveryAddressModalBody").html(response);
        	    $("#save_address").removeClass('d-none');
                $("#save_address").attr('data-close',data_close);
        	    $("#new_address").addClass('d-none');
        	    $("#new_address").removeClass('btn-progress');
        	  }
        	});
        });

        $(document).on('click', '#save_address', function(e) {
        	e.preventDefault();
            var data_close = $(this).attr('data-close');
            if(typeof(data_close)==='undefined') data_close='0';
        	var first_name = $("#deliveryAddressModal input[name=first_name]").val();
        	var last_name = $("#deliveryAddressModal input[name=last_name]").val();
        	var street = $("#deliveryAddressModal input[name=street]").val();
        	var state = $("#deliveryAddressModal input[name=state]").val();
        	var city = $("#deliveryAddressModal input[name=city]").val();
        	var zip = $("#deliveryAddressModal input[name=zip]").val();
        	var country = $("#deliveryAddressModal select[name=country]").val();
        	var country_code = $("#deliveryAddressModal select[name=country] option:selected").attr('phonecode');
        	var email = $("#deliveryAddressModal input[name=email]").val();
        	var mobile = $("#deliveryAddressModal input[name=mobile]").val();
        	// var note = $("#deliveryAddressModal input[name=note]").val();
        	var title = $("#deliveryAddressModal input[name=title]").val();
        	var id = $("#deliveryAddressModal input[name=id]").val();

        	if(first_name=="" || last_name=="" || street==""){
        		swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line("Please input the required fields."); ?>", 'error'); 
        		return;
        	}
        	$("#save_address").addClass('btn-progress');
        	$.ajax({
        	  context: this,
        	  type:'POST',
        	  dataType:'JSON',
        	  url:"<?php echo site_url();?>ecommerce/save_address",
        	  data:{subscriber_id,store_id,first_name,last_name,street,state,city,zip,country,email,mobile,country_code,title,id},
        	  success:function(response){
        	    $("#save_address").removeClass('btn-progress');
        	    if(response.status=='1') 
        	    {
        	    	iziToast.success({title: "",message: response.message,position: 'bottomRight',timeout: 3000});
        	    	$("#deliveryAddressModal").modal('hide');
        	    	setTimeout(function(){ 
        	    	 if(data_close=='0')
                     {
                        $("#showAddress").trigger('click');
        	    	    $("#sidebarCollapse").click();
                     }
                     $("#save_address").attr('data-close','0');
                     if (typeof load_address_list !== "undefined") load_address_list();
        	    	}, 500);         	    	
        	    }
        	    else
                {
                    var span = document.createElement("span");
                    span.innerHTML = response.message;
                    swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {             
                      $("#login_form").trigger('click');
                    });
                }
        	  }
        	});

        });

        $(document).on('click', '#delete_address', function(e) {
            e.preventDefault();

            swal({title: '<?php echo $this->lang->line("Delete Address"); ?>',
             text: '<?php echo $this->lang->line("Do you really want to delete this address?"); ?>',
             icon: 'warning',
             buttons: true,
             dangerMode: true,
           })
           .then((willDelete) => {
             if (willDelete) 
             {
                 var id  = $(this).attr('data-id');
                 $("#delete_address").addClass('btn-progress');
                 $.ajax({
                   context: this,
                   type:'POST',
                   url:"<?php echo site_url();?>ecommerce/delete_address",
                   data:{subscriber_id:subscriber_id,id:id},
                   success:function(response){
                     iziToast.success({title: "",message: '<?php echo $this->lang->line("Address has been deleted successfully.");?>',position: 'bottomRight',timeout: 3000});
                     $("#deliveryAddressModal").modal('hide');
                     setTimeout(function(){ 
                         $("#showAddress").trigger('click');
                         $("#sidebarCollapse").click();
                     }, 500);  
                   }
                 });
             } 
           });
            
        });


    });
</script>


<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-user-circle"></i> <?php echo $this->lang->line("Profile"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body" id="profileModalBody">
       
      </div>
      <div class="modal-footer p-0">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button> -->
        <button type="button" id="save_profile" class="btn btn-primary btn-lg btn-block no_radius p-3 m-0"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save Profile"); ?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deliveryAddressModal" tabindex="-1" role="dialog" aria-labelledby="deliveryAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deliveryAddressModalLabel"><i class="fas fa-truck"></i> <?php echo $this->lang->line("Delivery Address"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body" id="deliveryAddressModalBody">
       
      </div>
      <div class="modal-footer p-0">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button> -->
        <button type="button" id="new_address" class="btn btn-primary btn-lg btn-block no_radius p-3 m-0"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Address"); ?></button>
        <button type="button" id="save_address" data-close="0" class="btn btn-primary btn-lg btn-block no_radius p-3 m-0 d-none"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save Address");?> </button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="TermsModal" tabindex="-1" role="dialog" aria-labelledby="TermsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="TermsModalLabel"><i class="lnr lnr-license"></i> <?php echo $this->lang->line("Terms of Use"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body text-justify" id="TermsModalBody">
       	<?php echo $social_analytics_codes['terms_use_link'];?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="RefundModal" tabindex="-1" role="dialog" aria-labelledby="RefundModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="RefundModalLabel"><i class="lnr lnr-book"></i> <?php echo $this->lang->line("Refund Policy"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body text-justify" id="RefundModalBody">
       	<?php echo $social_analytics_codes['refund_policy_link'];?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="LoginModal" tabindex="-1" role="dialog" aria-labelledby="LoginModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="LoginModalLabel"><i class="fas fa-sign-in-alt"></i> <?php echo $this->lang->line("Login"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body text-justify" id="LoginModalBody">
            <form method="POST" action="#" class="needs-validation" novalidate="" _lpchecked="1">
              <div class="form-group">
                <label for="email"><?php echo $this->lang->line("Email");?>*</label>
                <input type="email" class="form-control" id="login_email" name=""  placeholder="<?php echo $this->lang->line("Email"); ?>" required autofocus autocomplete="off">
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <div class="d-block">
                    <label for="password" class="control-label"><?php echo $this->lang->line("Password");?>*</label>
                </div>
                <input  type="password" id="login_password" class="form-control" name="" placeholder="<?php echo $this->lang->line("Password"); ?>" required autocomplete="off">
                <div class="invalid-feedback"></div>

              <div class="form-group mt-2">
                <button type="submit" id="login_submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                  <?php echo $this->lang->line("Login");?>
                </button>
              </div>
            </form>
            <div class="<?php echo $is_guest_login=='1' ? '' : 'text-center'; ?> mt-4 mb-3">
              <div class="text-job text-muted"><a href="" class="pointer <?php echo $is_guest_login=='1' ? 'float-left' : ''; ?>" id="register_form"><i class="fas fa-user-circle"></i> <?php echo $this->lang->line("Register Now");?></a></div>
              <div class="text-job text-muted"><a href="" class="pointer <?php echo $is_guest_login=='1' ? 'float-right' : 'd-none'; ?>" id="guest_register_form"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line("Checkout as Guest");?></a></div>
            </div>  
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="RegisterModal" tabindex="-1" role="dialog" aria-labelledby="RegisterModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="RegisterModalLabel"><i class="fas fa-sign-in-alt"></i> <?php echo $this->lang->line("Register"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body text-justify" id="RegisterModalBody">
            <form method="POST" action="#" class="needs-validation" novalidate="" _lpchecked="1">

              <div class="form-group">
                <label for=""><?php echo $this->lang->line("Name");?>*</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("First Name"); ?>" id="register_first_name" name="" required autofocus autocomplete="off">
                    <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("Last Name"); ?>" id="register_last_name" name="" required autofocus autocomplete="off">
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <label for="email"><?php echo $this->lang->line("Email");?>*</label>
                <input type="email" class="form-control" placeholder="<?php echo $this->lang->line("Email"); ?>" id="register_email" name="" required autofocus autocomplete="off">
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <div class="d-block">
                    <label for="password" class="control-label"><?php echo $this->lang->line("Password");?>*</label>
                </div>
                <div class="input-group">
                    <input  type="password" id="register_password" placeholder="<?php echo $this->lang->line("Password"); ?>" class="form-control" name="" required autocomplete="off">
                    <input  type="password" id="register_password_confirm" placeholder="<?php echo $this->lang->line("Confirm Password"); ?>" class="form-control" name="" required autocomplete="off">
                </div>
                <div class="invalid-feedback"></div>

              <div class="form-group mt-2">
                <button type="submit" id="register_submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                  <?php echo $this->lang->line("Register");?>
                </button>
              </div>
            </form> 
      </div>
    </div>
  </div>
</div>




<?php if(isset($social_analytics_codes["pixel_id"]) && !empty($social_analytics_codes["pixel_id"])):?>
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '<?php echo $social_analytics_codes["pixel_id"];?>');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=<?php echo $social_analytics_codes['pixel_id'];?>&ev=PageView&noscript=1"
/></noscript>
<?php endif; ?>


<?php if(isset($social_analytics_codes["google_id"]) && !empty($social_analytics_codes["google_id"])):?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $social_analytics_codes['google_id'];?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php echo $social_analytics_codes["google_id"];?>');
</script>
<?php endif; ?>