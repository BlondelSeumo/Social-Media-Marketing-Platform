<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-bell"></i> <?php echo $page_title; ?></h1>
    <?php if($this->session->userdata("user_type")=="Admin") 
    { ?>
      <div class="section-header-button">
       <a class="btn btn-primary"  href="<?php echo site_url('announcement/add');?>">
          <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Announcement"); ?>
       </a> 
      </div>
    <?php 
    } ?>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>
  <?php 
  if($this->session->flashdata('mark_seen_success')!='')
  echo "<div class='alert alert-success text-center'><i class='fas fa-check-circle'></i> ".$this->session->flashdata('mark_seen_success')."</div>"; 
  ?>

  <div class="section-body">

    <div class="row">      
      <div class="col-12 col-md-7">
        <div class="input-group mb-3" id="searchbox">
          <div class="input-group-prepend">
              <select class="select2 form-control" id="seen_type">
                <option value="0"><?php echo $this->lang->line("Unseen"); ?></option>
                <option value="1"><?php echo $this->lang->line("Seen"); ?></option>
                <option value=""><?php echo $this->lang->line("Everything"); ?></option>
              </select>
            </div>
          <input type="text" class="form-control" id="search" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
          <div class="input-group-append">
            <button class="btn btn-primary" id="search_submit" type="button"><i class="fas fa-search"></i> <?php echo $this->lang->line('Search'); ?></button>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-5">
        <button class="btn btn-outline-primary btn-lg float-right" id="mark_seen_all"><i class="fas fa-eye-slash"></i> <?php echo $this->lang->line("Mark all unseen as seen"); ?></button>
      </div>
    </div>

    <div class="activities">
        <div id="load_data" style="width: 100%;"></div>      
    </div> 


    <div class="text-center" id="waiting" style="width: 100%;margin: 30px 0;">
      <i class="fas fa-spinner fa-spin blue" style="font-size:60px;"></i>
    </div>  

    <div class="card" id="nodata" style="display: none">
      <div class="card-body">
        <div class="empty-state">
          <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
          <h2 class="mt-0"><?php echo $this->lang->line("We could not find any data.") ?></h2>
        </div>
      </div>
    </div>
 

    <button class="btn btn-outline-primary float-right" style="display: none;" id="load_more" data-limit="10" data-start="0"><i class="fas fa-book-reader"></i> <?php echo $this->lang->line("Load More"); ?></button>
      
  </div>
</section>


<script>       
    var base_url="<?php echo site_url(); ?>";
    var counter=0;
    $(document).ready(function() {      

        setTimeout(function() {          
          var start = $("#load_more").attr("data-start");   
          load_data(start,false,false);
        }, 1000);


        $(document).on('click', '#load_more', function(e) {
          var start = $("#load_more").attr("data-start");   
          load_data(start,false,true);
        });

        $(document).on('change', '#seen_type', function(e) {
          var start = '0';
          load_data(start,true,false);
        });


        $(document).on('click', '#search_submit', function(e) {
          var start = '0';
          load_data(start,true,false);
        });

        function load_data(start,reset,popmessage) 
        {
          var limit = $("#load_more").attr("data-limit");        
          var search = $("#search").val();
          var seen_type = $("#seen_type").val();
          $("#waiting").show();
          if(reset) 
          {
            $("#search_submit").addClass("btn-progress");
            counter = 0;
          }
          $.ajax({
            url: base_url+'announcement/list_data',
            type: 'POST',
            dataType : 'JSON',
            data: {start:start,limit:limit,search:search,seen_type:seen_type},
              success:function(response)
              {
                $("#waiting").hide();
                $("#nodata").hide();
                $("#search_submit").removeClass("btn-progress");

                counter += response.found; 
                $("#load_more").attr("data-start",counter); 
                if(!reset)  $("#load_data").append(response.html);
                else $("#load_data").html(response.html);

                if(response.found!='0') $("#load_more").show();                
                else 
                {
                  $("#load_more").hide();
                  if(popmessage) 
                  {
                    swal("<?php echo $this->lang->line('No data found') ?>", "", "warning");
                    $("#nodata").hide();
                  }
                  else $("#nodata").show();
                }
              }
          });
        }

        $(document).on('click', '.mark_seen', function(e) {
          e.preventDefault();
          var link = $(this).attr("href");
          
          $(this).addClass('btn-progress');
          $.ajax({
            context: this,
            url: link,
            type: 'POST',
            dataType: 'JSON',
            data: {},
              success:function(response)
              {
                $(this).removeClass('btn-progress');
                if(response.status == "1")  
                {
                  iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: response.message,position: 'bottomRight'});
                  $(this).parent().parent().parent().hide();
                }
                else iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message,position: 'bottomRight'});
              }
          });
        });

        $(document).on('click', '#mark_seen_all', function(e) {
            e.preventDefault();
            var mes='<?php echo $this->lang->line("Do you really want to mark all unseen notifications as seen?");?>';  
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
                  $(this).addClass('btn-progress');
                  $.ajax({
                    context: this,
                    url: base_url+"announcement/mark_seen_all",
                    type: 'POST',
                    dataType: 'JSON',
                    data: {},
                      success:function(response)
                      {
                        $(this).removeClass('btn-progress');
                        if(response.status == "1")  
                        {
                          location.reload();
                        }
                        else iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message,position: 'bottomRight'});
                      }
                  });
              } 
            });
        
        }); 

        $(document).on('click', '.delete_annoucement', function(e) {
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
                  $(this).addClass('btn-progress');
                  $.ajax({
                    context: this,
                    url: link,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {},
                    success:function(response)
                    {
                      $(this).removeClass('btn-progress');
                      if(response.status == "1")  
                      {
                          iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: response.message,position: 'bottomRight'});
                          $(this).parent().parent().parent().parent().parent().hide();
                      }
                      else iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message,position: 'bottomRight'});
                      }                      
                  });
                } 
            });
        
        });

    });
</script>