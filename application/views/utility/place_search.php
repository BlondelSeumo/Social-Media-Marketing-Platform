<style type="text/css">
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  .multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;}
  .multi_layout .list-group li{padding: 25px 10px 12px 25px;}
  .multi_layout{border:.5px solid #dee2e6;}
  .multi_layout .collef,.multi_layout .colmid{padding-left: 0px; padding-right: 0px;border-right: .6px solid #dee2e6;border-bottom: .6px solid #dee2e6;}
  .multi_layout .colmid .card-icon{border:.5px solid #dee2e6;}
  .multi_layout .colmid .card-icon i{font-size:30px !important;}

  .multi_layout .collef .makeScroll{max-height:430px;overflow:auto;}
  .multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;z-index: 0;}
  .multi_layout .list-group .list-group-item:first-child{border-top:none;}
  .multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
  .multi_layout .list-group .list-group-item.active{border:.5px solid var(--blue);}
  .multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;}
  .multi_layout h6.page_name{font-size: 14px;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .card-primary{margin-top: 35px;margin-bottom: 15px;}
  .multi_layout .product-details .product-name{font-size: 12px;}
  .multi_layout .margin-top-50 {margin-top: 70px;}
  .multi_layout .waiting {height: 100%;width:100%;display: table;}
  .multi_layout .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:10px 0;}
  .waiting {padding-top: 200px;}
  .check_box{position: absolute !important;top: 0 !important;right: 0 !important;margin: 3px;}
  .check_box_background{position: absolute;height: 60px;width: 60px;top: 0;right: 0;font-size: 13px;}
  .profile-widget { margin-top: 0;}
  .profile-widget .profile-widget-items:after {content: ' ';position: absolute;bottom: 0;left: 0px;right: 0;height: 1px;background-color: #f2f2f2;}
  .profile-widget .profile-widget-items:before {content: ' ';position: absolute;top: 0;left: 0px;right: 0;height: 1px;background-color: #f2f2f2;}
  .profile-widget .profile-widget-items .profile-widget-item {flex: 1;text-align: center;padding: 10px 0;}
  .article .article-header {overflow: unset !important;}
  .description_info {padding: 20px;line-height: 17px;font-size: 13px;margin: 0;}
  .option_dropdown {position: absolute;top: 0;left: 0;height: 20px;width: 22px;background-color: #f7fefe;border-radius: 24%;padding-top: 0px;margin-top: 3px;margin-left: 3px;border: 1px solid #4e6e7e;}
  .video_option_background{position: absolute;height: 60px;width: 60px;top: 0;left: 0;}
  .selectric .label {min-height: 0 !important;}
  .opt_btn{border-radius: 30px !important;padding-left: 25px !important;padding-right: 25px !important;}
  .generic_message_block textarea{height: 100px !important;}
  .filter_message_block textarea{height: 100px !important;margin-bottom: 30px;}
  .single_card .card-body .form-group{margin-bottom: 10px;}
  .single_card .card-body{padding-bottom: 0 !important;}
  .bootstrap-tagsinput{height: 100px !important;}
  .profile-widget .profile-widget-items .profile-widget-item .profile-widget-item-value {font-weight: 300;font-size: 13px;
  }
  .article .article-header{height: 100px!important;}
  .media .media-title {font-size: 12px!important}
  .icon_color{color:var(--blue)!important;}
  .bck_clr{background: #ffffff;}

</style>
<section class="section">
  <div class="section-header">
    <h1><i class="fa fa-search-location"></i> <?php echo $page_title;?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('search_tools/index'); ?>"><?php echo $this->lang->line('Search Tools'); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title;?></div>
    </div>
  </div>
</section>

<div class="row multi_layout">

  <div class="col-12 col-md-4 col-lg-3 collef">
    <div class="card main_card">
      <form method="POST" id="video_search_form_data">
        <div class="card-header">
          <div class="col-12 padding-0">
            <h4><i class="fas fa-search"></i> <?php echo $this->lang->line("Search"); ?></h4>
          </div>

        </div>
        <div class="card-body">

          <div class="form-group">

            <input type="text" class="form-control" name="keyword" id="keyword" autofocus autocomplete="off"  placeholder="<?php echo $this->lang->line('Keyword...'); ?>">
          </div>

          <div class="form-group">
            <label class="form-label"><?php echo $this->lang->line('Results Limit'); ?> <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line("Result may be less than or more than limit depending on how many found."); ?>" data-original-title="<?php echo $this->lang->line('Results Limit'); ?>"><i class="fa fa-info-circle"></i></a></label>
            <div class="selectgroup w-100">
              <label class="selectgroup-item">
                <input type="radio" name="limit" id="limit" value="100" class="selectgroup-input" checked>
                <span class="selectgroup-button"><?php echo $this->lang->line('100'); ?></span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="limit" id="limit" value="150" class="selectgroup-input">
                <span class="selectgroup-button"><?php echo $this->lang->line('150'); ?></span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="limit" id="limit" value="200" class="selectgroup-input">
                <span class="selectgroup-button"><?php echo $this->lang->line('200'); ?></span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="limit" id="limit" value="300" class="selectgroup-input">
                <span class="selectgroup-button"><?php echo $this->lang->line('300'); ?></span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label><?php echo $this->lang->line('Latitude'); ?></label>
            <input type="text" value="" class="form-control" name="latitude" id="latitude">
          </div>
          
          <div class="form-group">
            <label><?php echo $this->lang->line('Longitude'); ?></label>
            <input type="text" value="" class="form-control" name="longitude" id="longitude">
            <a href="https://www.latlong.net/" target="_blank"> <?php echo $this->lang->line('How to find lat & long ?'); ?></a>
          </div>
          <div class="form-group">
            <label> <?php echo $this->lang->line('Distance') ?></label>
            <select class="form-control select2" id="distance" name="distance" style="width:100%">
              <option value=""><?php echo $this->lang->line('Select Distance'); ?></option> 
              <option value="1000"><?php echo $this->lang->line('1000 m'); ?></option> 
              <option value="1200"><?php echo $this->lang->line('1200 m'); ?></option> 
              <option value="1500"><?php echo $this->lang->line('1500 m'); ?></option> 
              <option value="1600"><?php echo $this->lang->line('1600 m'); ?></option> 
              <option value="1700"><?php echo $this->lang->line('1700 m'); ?></option> 
              <option value="1800"><?php echo $this->lang->line('1800 m'); ?></option> 
              <option value="2000"><?php echo $this->lang->line('2000 m'); ?></option> 
            </select>
          </div>
          


        </div>

        <div class="card-footer bg-whitesmoke">
          <button class="btn btn-primary btn-md" id="search_btn" type="submit"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
          <button class="btn btn-secondary btn-md float-right" onclick="goBack('search_tools/placeSearch')" type="button"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel"); ?></button>
        </div>

      </form>
    </div>          
  </div>

  <div class="col-12 col-md-8 col-lg-9 colmid">
    <div id="custom_spinner"></div>

    <div id="middle_column_content" style="background: #fff!important;">
      

      <div class="card">
        <div class="card-header">
          <h4> <i class="fas fa-search-location"></i> <?php echo $this->lang->line('Search Results'); ?></h4>

        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-6 col-lg-12 bck_clr" id="nodata">

        <div class="empty-state">
          <img class="img-fluid" src="<?php echo base_url("assets/img/drawkit/revenue-graph-colour.svg"); ?>" style="height: 250px" alt="image">
          <h2 class="mt-0"><?php echo $this->lang->line("Search place in facebook through left sidebar filter"); ?></h2>

        </div>

      </div>
    </div>
  </div>
  <div class="modal fade" tabindex="-1" role="dialog" id="detail_modal">
    <div class="modal-dialog modal-md" role="document" style="min-width: 45%;">
      <div class="modal-content">
        <div class="banner">
         
        </div>
        <div class="modal-body" id="details_data">

        </div>
        <div class="modal-footer bg-whitesmoke br">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

</div>
<script src="<?php echo base_url();?>plugins/scrollreveal/scrollreveal.js" type="text/javascript"></script>
<script type="text/javascript">



  $("document").ready(function(){

    $(document).on('click', '#search_btn', function(event) {
      event.preventDefault();

      var form_data = new FormData($("#video_search_form_data")[0]);
      var base_url="<?php echo base_url(); ?>";
      var user_id="<?php echo $this->user_id;?>";

      var keyword = $("#keyword").val();
      var latitude = $("#latitude").val();
      var longitude = $("#longitude").val();
      if(keyword == ''){
        swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please Enter Keyword'); ?>", 'error');
        return false;
      }
      if(latitude == ''){
        swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please Enter latitude'); ?>", 'error');
        return false;
      }
      if(longitude == ''){
        swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please Enter longitude'); ?>", 'error');
        return false;
      }

      $('#middle_column_content').html("");
      $("#search_btn").addClass('btn-progress');


      $("#custom_spinner").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div><br/>');

      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>search_tools/place_search_action",
        data: form_data,
        contentType: false,
        cache:false,
        processData: false,
        //dataType:"json",
        success:function(response){

          $("#search_btn").removeClass('btn-progress');
          $("#custom_spinner").html("");
          $("#middle_column_content").html(response);
         // $("#middle_column_content").html(response);
         
          
        }
      });

    });

    $(document).on('click', '.details', function(event){
        event.preventDefault();

        let single_details = $(this).find('textarea').text();
        single_details = JSON.parse(single_details);
        //console.log(single_details);
        let cover_image;

       
        if (single_details.cover==undefined){
            cover_image = '<img style="width: 100%; max-height: 200px;" src="<?php echo base_url('assets/img/news/img06.jpg') ?>" >';
            $('.banner').html(cover_image);
        }
        else{

            cover_image = single_details.cover.source;
            image = '<img style="width: 100%; max-height: 200px;" src='+cover_image+'>';
            $('.banner').html(image);
            
        }

        let website;
        if(single_details.website==undefined)
          website = '';
        else
          website = single_details.website;

        let phone;
        if(single_details.phone == undefined)
          phone = '';
        else
          phone = single_details.phone;

        let zip_code; 
        if(single_details.location==undefined)
          zip_code = '';
        else
          zip_code = single_details.location.zip;

        let categoris = single_details.category_list.map(function(item) {

            return item.name;
           

         });

        let checkins;
        if(single_details.checkins == undefined)
          checkins = 0;
        else
          checkins = single_details.checkins;

        let varified;
        if(single_details.is_verified)
          varified = "Yes";
        else
          varified = "No";

        let is_always_open;
        if(single_details.is_always_open)
          is_always_open = "Yes";
        else
          is_always_open ="No";
        let amex;
        let cash_only;
        let discover;
            let mastercard;
            let visa;
        let payment_methods = [];
        if(single_details.payment_options == undefined){
          payment_methods.push();
        }
        else{
          
           if(single_details.payment_options.amex == 1)
              payment_methods.push("amex");
            else
              payment_methods.push();

            if(single_details.payment_options.cash_only == 1)
              payment_methods.push("cash_only");
            else
              payment_methods.push();

            if(single_details.payment_options.discover == 1)
              payment_methods.push("discover");
            else
              payment_methods.push();

            if(single_details.payment_options.mastercard == 1)
              payment_methods.push("mastercard");
            else
              payment_methods.push();

            if(single_details.payment_options.visa == 1)
              payment_methods.push("visa");
            else
              payment_methods.push();
        }

        let about;
        if(single_details.about == undefined){
          about ="";
        }
        else{
          about = single_details.about;
        }

        let price = [];
        if(single_details.price_range == undefined){
          price.push();
        }
        else {
          if(single_details.price_range.length == 1)
            price.push("menus are inexpensive");
          if(single_details.price_range.length == 2)
            price.push("menus are moderately priced");
          if(single_details.price_range.length == 3)
            price.push("menus are expensive")
        }

        let oval_rating;
        if(single_details.overall_star_rating == undefined){
          oval_rating = "";
        }
        else{
          oval_rating = single_details.overall_star_rating;
        }

        let rating_count;
        if(single_details.rating_count == undefined){
          rating_count = "";
        }
        else{
          rating_count = single_details.rating_count;
        }

        let is_parmanently_closed;
        if(single_details.is_parmanently_closed)
          is_parmanently_closed = "Yes";
        else
          is_parmanently_closed = "No";

        let hours;
        let hoursinter;
        if(single_details.hours == undefined){
          hours = "";
        }
        else{
          hours = single_details.hours;

           hoursinter = hours.map(function(item1) {

              return item1.key;
             

           });

          console.log(hoursinter);

        }


        let data = '<div class="font-weight-bold mb-3 mt text-center"> <h6><a style="word-break:break-all;" href="'+single_details.link+'" target="_blank"> '+single_details.name+' </a></h6></div><div class="row"> <div class="col-sm-12"> <ul class="list-unstyled list-unstyled-border"> <div class="row pt-2 pb-2"> <div class="col-12 col-md-6"> <li class="media"> <i class="mr-3 fa fa-map-marker icon_color"></i> <div class="media-body"> <div class="media-title"><a style="word-break:break-all;" target="_blank" href="'+website+'">'+website+'</a></div><div class="text-muted text-small">'+single_details.location.country+' <div class="bullet"></div>'+single_details.location.city+'</div></div></li></div><div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-list mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"><?php echo $this->lang->line('Categories'); ?> </div><div class="text-muted text-small" title="<?php echo $this->lang->line('Categories'); ?>"> '+categoris.join(',')+' </div></div></li></div></div><div class="row pt-2 pb-2"> <div class="col-12 col-md-6"> <li class="media"> <i class="mr-3 fa fa-mobile icon_color"> </i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Phone'); ?></div><div class="text-muted text-small" title="<?php echo $this->lang->line('Phone'); ?>">'+phone+' </div></div></li></div><div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-window-close-o mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"><?php echo $this->lang->line('Is parmanently closed'); ?> ? </div><div class="text-muted text-small"> '+is_parmanently_closed+'</div></div></li></div></div><div class="row pt-2 pb-2"> <div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-map-pin mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Checkins'); ?> </div><div class="text-muted text-small"> '+checkins+' </div></div></li></div><div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-window-maximize mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"><?php echo $this->lang->line('Is Always Open'); ?> ? </div><div class="text-muted text-small"> '+is_always_open+'</div></div></li></div></div><div class="row pt-2 pb-2"> <div class="col-12 col-md-6"><li class="media"> <i class="fa fa-thumbs-up mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Like'); ?> </div><div class="text-muted text-small"> '+single_details.engagement.social_sentence+' </div></div></li></div><div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-star-o mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Over all star rating'); ?> </div><div class="text-muted text-small"> '+oval_rating+' </div></div></li></div></div><div class="row pt-2 pb-2"> <div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-check-circle mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('is verified'); ?> ? </div><div class="text-muted text-small"> '+varified+' </div></div></li></div><div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-star mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Rating Count'); ?> </div><div class="text-muted text-small"> '+rating_count+' </div></div></li></div></div><div class="row pt-2 pb-2"> <div class="col-12 col-md-6"> <li class="media"> <i class="fa fa-credit-card mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Payment Options'); ?> </div><div class="text-muted text-small" title="<?php echo $this->lang->line('Payment Methods'); ?>"> '+payment_methods+' </div></div></li></div><div class="col-12 col-md-6"> <li class="media"> <i class="fa fa fa-bars mr-3 icon_color"></i> <div class="media-body"> <div class="media-title"> <?php echo $this->lang->line('Price range'); ?> </div><div class="text-muted text-small">'+price+' </div></div></li></div></div></div></ul> </div><div class="row"><div class="col-sm-12"> <div class="media-body"> <div class="media-title"> <i class="fa fa-align-justify mr-3 icon_color"></i> <?php echo $this->lang->line('About'); ?> </div><p style="word-break: break-all;" class="text-muted text-small mt-2">'+about+'</p></div></div></div></div>';
        $("#details_data").html(data);


        $("#detail_modal").modal();

    });
  });  

</script>




