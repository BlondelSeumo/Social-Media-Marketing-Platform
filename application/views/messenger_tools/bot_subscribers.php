<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-user-circle"></i> <?php echo $page_title;?></h1>
    <!-- <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('subscriber_manager'); ?>"><?php echo $this->lang->line("Subscriber Manager");?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title;?></div>
    </div> -->
  </div>

  <?php $this->load->view('admin/theme/message'); ?>       

</section>

<?php if(empty($page_info))
{ ?>
   
<div class="card" id="nodata">
  <div class="card-body">
    <div class="empty-state">
      <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
      <h2 class="mt-0"><?php echo $this->lang->line("We could not find any page.");?></h2>
      <p class="lead"><?php echo $this->lang->line("Please import account if you have not imported yet.")."<br>".$this->lang->line("If you have already imported account then enable bot connection for one or more page to continue.") ?></p>
      <a href="<?php echo base_url('social_accounts'); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Continue");?></a>
    </div>
  </div>
</div>

<?php 
}
else
{ ?>
  <div class="row multi_layout2">

    <div class="col-12 col-md-4 col-lg-3 collef">
      <div class="card main_card">
        <div class="card-header">
          <div class="col-6 padding-0">
            <h4> 
            <?php echo ($this->session->userdata('selected_global_media_type') =='ig') ? $this->lang->line("Accounts") : $this->lang->line("Pages"); ?></h4>
          </div>
          <div class="col-6 padding-0">            
            <input type="text" class="form-control float-right" id="search_page_list" onkeyup="search_in_ul(this,'page_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>">
          </div>
        </div>
        <div class="card-body padding-0">
          <div class="makeScroll">
            <ul class="list-group" id="page_list_ul">
              <?php 
              $i=0; 
              foreach($page_info as $value)
              { 

                $last_lead_sync = $this->lang->line("Never");
                if($value['last_lead_sync']!='0000-00-00 00:00:00') $last_lead_sync = date_time_calculator($value['last_lead_sync'],true);
                ?>
                <li class="list-group-item page_list_item"  page_table_id="<?php echo $value['id']; ?>">
                  <div class="row">
                    <div class="col-3 col-md-2"><img width="45px" class="rounded-circle" src="<?php echo $value['page_profile'];?>"></div>
                    <div class="col-9 col-md-10">
                      <h6 class="page_name"><?php echo  $this->session->userdata('selected_global_media_type')=="ig" ?  $value['insta_username']:  $value['page_name'];?></h6>
                      <small class="gray"><?php echo $value['page_id']; ?></small>
                      <!-- <code class="pl-2 text-dark text-small" data-toggle="tooltip" title="<?php echo $this->lang->line('Last Scanned') ?>"><i class="far fa-clock"> <?php echo $last_lead_sync; ?></i></code> -->
                      </div>
                    </div>
                </li> 
                <?php } ?>                
            </ul>
          </div>
        </div>
      </div>          
    </div>

    <div class="col-12 col-md-8 col-lg-9 colmid" id="middle_column">
      
      <div id="middle_column_content">

        <div class="card main_card">
            <div class="card-header p-2">
              <h4 class="full_width" id="middle_column_content_title">
              </h4>
            </div>
            <div class="card-body p-2" id="middle_column_content_body">
            </div>
            <div class="text-center waiting">
              <i class="fas fa-spinner fa-spin blue text-center"></i>
            </div>

            <div class="row mt-0">
              <div class="col-12 col-sm-12 col-md-9 py-0 pl-lg-3 pr-lg-0 p-sm-2">
                <div class="card no_shadow">
                  <div class="card-body data-card py-0 px-2">
                    <div class="row">
                      <div class="col-12 col-md-10">
                        <?php 

                        echo 
                        '<div class="input-group" id="searchbox">
                          <div class="input-group-prepend d-none">
                          <input id="page_id">
                          </div>
                          <div class="input-group-prepend" id="label_dropdown">
                         </div>

                          <div class="input-group-prepend d-none d-md-block">
                            <select class="form-control select2" id="gender" name="gender">
                              <option value="">'.$this->lang->line("Gender").'</option>
                              <option value="male">'.$this->lang->line("Male").'</option>
                              <option value="female">'.$this->lang->line("Female").'</option>
                            </select>
                          </div>

                          <select class="form-control select2" id="email_phone_birth" name="email_phone_birth[]" multiple="multiple" style="max-width:30%;">
                            <option value="has_phone">'.$this->lang->line("Has Phone").'</option>
                            <option value="has_email">'.$this->lang->line("Has Email").'</option>
                            <option value="has_birthdate">'.$this->lang->line("Has Birth Date").'</option>
                          </select>

                          <input type="text" class="form-control" autofocus id="search_value" name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:30%;">
                          <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="search_subscriber"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                          </div>
                        </div>'; ?>                                          
                      </div>

                      <div class="col-12 col-md-2">

                        <div class="btn-group dropleft float-right">
                          <button type="button" class="btn btn-outline-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <?php echo $this->lang->line("Options"); ?>
                          </button>
                          <div class="dropdown-menu dropleft" style="width:auto !important">
                            <a class="dropdown-item" href="#" button_id=""  id="migrate_list"><i class="fas fa-file-export"></i> <?php echo $this->lang->line('Migrate page conversations as subscribers'); ?></a>
                            <a class="dropdown-item" href="<?php echo base_url('subscriber_manager/download_result'); ?>"><i class="fas fa-cloud-download-alt"></i> <?php echo $this->lang->line("Download search result"); ?></a>
                            
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" id="assign_group" href=""><i class="fas fa-user-tag"></i> <?php echo $this->lang->line("Assign labels to selected subscribers"); ?></a>

                            <?php if($this->sms_email_drip_exist) : ?>
                              <?php if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, array(270,271))) > 0 ) :  ?>
                                <a class="dropdown-item" id="assign_sms_email_sequence" href=""><i class="fas fa-plug"></i> <?php echo $this->lang->line("Assign sequence to selected subscribers"); ?></a>
                              <?php endif; ?>
                            <?php endif; ?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item red" id="bulk_delete_contact" href=""><i class="fas fa-trash"></i> <?php echo $this->lang->line("Delete Subscriber"); ?></a>
                          </div>
                        </div>                          
                      </div>
                    </div>

                    <div class="table-responsive2">
                        <input type="hidden" id="put_page_id">
                        <table class="table table-bordered" id="mytable">
                          <thead>
                            <tr>
                              <th>#</th>      
                              <th style="vertical-align:middle;width:20px">
                                  <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                              </th>
                              <th><?php echo $this->lang->line("Avatar"); ?></th>      
                              <th><?php echo addon_exist($module_id=320,$addon_unique_name="instagram_bot") ? $this->lang->line("Page/Account") : $this->lang->line("Page Name"); ?></th>    
                              <th><?php echo $this->lang->line("Subscriber ID"); ?></th>      
                              <th><?php echo $this->lang->line("First Name"); ?></th>      
                              <th><?php echo $this->lang->line("Last Name"); ?></th>      
                              <th><?php echo $this->lang->line("Full Name"); ?></th>
                              <th><?php echo $this->lang->line("Actions"); ?></th>      
                              <th><?php echo $this->lang->line("Quick Info"); ?></th>      
                              <th><?php echo $this->lang->line("Label/Tag"); ?></th>      
                              <th><?php echo $this->lang->line("Thread ID"); ?></th>      
                              <th><?php echo $this->lang->line("Synced at"); ?></th>
                              <th><?php echo $this->lang->line("Social Media"); ?></th>
                            </tr>
                          </thead>
                        </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-sm-12 col-md-3 py-0 pl-lg-0 pr-lg-3 p-sm-2">
                  <div class="card no_shadow">
                    <div class="card-body data-card py-0 px-2">
                      <div class="row">                
                          <div class="col-12">
                              <a class="btn btn-outline-primary add_label btn-block btn-lg mb-1"  href="#">
                                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Label"); ?>
                              </a> 
                          </div>
                          <div class="col-12">
                              <div class="input-group float-left" id="searchbox">
                                  <input type="text" class="form-control" id="searching" name="searching" autofocus placeholder="<?php echo $this->lang->line('Search Labels...'); ?>" aria-label="" aria-describedby="basic-addon2">
                              </div>
                          </div>
                      </div>
                      <div class="table-responsive2">
                        <table class="table table-bordered bg-white" id="mytablelabel">
                          <thead>
                            <tr>
                              <th>#</th>      
                              <th><?php echo $this->lang->line("ID"); ?></th>      
                              <th><?php echo $this->lang->line("Label"); ?></th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                      </div>             
                    </div>

                  </div>
              </div>    
                
            </div>

        </div>
        
      </div>
      
    </div>

    
    
  </div>
<?php 
} ?>

<?php     
    $doyouwanttodeletethiscontact = $this->lang->line("Do you want to delete this subscriber?");
    $youhavenotselected = $this->lang->line("You have not selected any subscriber to assign label. You can choose upto");
    $youhavenotselectanysubscribertoassignsequence = $this->lang->line("You have not selected any subscriber to assign sms/email sequence campaign. You can choose upto");
    $youhavenotselected2 = $this->lang->line("You have not selected any subscriber to delete.");
    $leadsatatime = $this->lang->line("subscribers at a time.");
    $youcanselectupto = $this->lang->line("You can select upto");
    $leadsyouhaveselected = $this->lang->line(",you have selected");
    $leads = $this->lang->line("subscribers.");
    $youhavenotselectedany = $this->lang->line("You have not selected any subscriber to delete. you can choose upto");
    $youhavenotselectedanyleadtoassigngroup = $this->lang->line("You have not selected any subscriber to assign label.");
    $youhavenotselectedanyleadtoassigndripcampaign = $this->lang->line("You have not selected any subscriber to assign sequence campaign.");
    $youhavenotselectedanyleadgroup = $this->lang->line("You have not selected any label.");
    $youhavenotselectedanysequence = $this->lang->line("You have not selected any sequence campaign.");
    $pleasewait = $this->lang->line("Please wait...");
    $groupshavebeenassignedsuccessfully = $this->lang->line("Labels have been assigned successfully");
    $sequencehavebeenassignedsuccessfully = $this->lang->line("Sequence campaign have been assigned successfully");
    $contactshavebeendeletedsuccessfully = $this->lang->line("Subscribers have been deleted successfully");
    $somethingwentwrongpleasetryagain = $this->lang->line("Something went wrong, please try again."); 
    $ig_bot_exists = addon_exist($module_id=320,$addon_unique_name="instagram_bot") ? '1' : '0';   

    $disabledsuccessfully = $this->lang->line("Backgound scanning has been disabled successfully.");
    $enabledsuccessfully = $this->lang->line("Backgound scanning has been enabled successfully.");
?>



<script type="text/javascript">
    var is_webview_exist = "<?php echo $this->is_webview_exist; ?>"
    var base_url="<?php echo base_url();?>";    
    var youhavenotselected = "<?php echo $youhavenotselected;?>";
    var youhavenotselectanysubscribertoassignsequence = "<?php echo $youhavenotselectanysubscribertoassignsequence; ?>";
    var youhavenotselected2 = "<?php echo $youhavenotselected2;?>";
    var leadsatatime = "<?php echo $leadsatatime;?>";
    var youcanselectupto = "<?php echo $youcanselectupto;?>";
    var leadsyouhaveselected = "<?php echo $leadsyouhaveselected;?>";
    var leads = "<?php echo $leads;?>";
    var youhavenotselectedanyleadtoassigngroup = "<?php echo $youhavenotselectedanyleadtoassigngroup; ?>";
    var youhavenotselectedanyleadtoassigndripcampaign = "<?php echo $youhavenotselectedanyleadtoassigndripcampaign; ?>";
    var youhavenotselectedanyleadgroup = "<?php echo $youhavenotselectedanyleadgroup; ?>";
    var pleasewait = "<?php echo $pleasewait; ?>";
    var groupshavebeenassignedsuccessfully = "<?php echo $groupshavebeenassignedsuccessfully; ?>";
    var sequencehavebeenassignedsuccessfully = "<?php echo $sequencehavebeenassignedsuccessfully; ?>";
    var contactshavebeendeletedsuccessfully = "<?php echo $contactshavebeendeletedsuccessfully; ?>";
    var auto_selected_page = "<?php echo $auto_selected_page; ?>";
    var auto_selected_subscriber = "<?php echo $auto_selected_subscriber; ?>";
    var youhavenotselectedanysequence = "<?php echo $youhavenotselectedanysequence; ?>";
    var ig_bot_exists = "<?php echo $ig_bot_exists; ?>";

    setTimeout(function(){ 
      $('#search_date_range').daterangepicker({
        ranges: {
          '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
          '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
          '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      }, function (start, end) {
        $('#search_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
      });
    }, 3000);

    function get_page_details(elem)
    {
      $('.multi_layout2 #middle_column .waiting').show();
      $('.multi_layout2 #middle_column_content_body').hide();

      var page_table_id = $(elem).attr('page_table_id');
      if(typeof(page_table_id)==='undefined') {
        elem =  $(".list-group li:first");
        page_table_id = $(elem).attr('page_table_id');
      }

      $('.page_list_item').removeClass('active');
      $(elem).addClass('active');   


      $("#page_id").val(page_table_id).blur();  

      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>subscriber_manager/get_page_details",
        data:{page_table_id:page_table_id},
        dataType:'JSON',
        success:function(response){
          $(".multi_layout2 #middle_column_content_title").html(response.title);
          $(".multi_layout2 #middle_column_content_body").html(response.middle_column_content).show();
          $("#put_page_label_list").html(response.dropdown);
          $('.multi_layout2 #middle_column .waiting').hide();   
        }
      });
    }
    

    $("document").ready(function(){

        var perscroll_label;
        var table_label = '';
        var perscroll;
        var table1 = '';
        //if(auto_selected_page!='' && auto_selected_page!='0' ) $('#page_id').val(auto_selected_page).trigger('change');

        setTimeout(function(){ 
            table_label = $("#mytablelabel").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 2, "asc" ]],
            pageLength: 10,
            ajax: {
              "url": base_url+'subscriber_manager/contact_group_data',
              "type": 'POST',
                    data: function ( d )
                    {
                        d.page_id = $('#page_id').val();
                        d.searching = $('#searching').val();
                    }
            },
            responsive: true,
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
            {
              targets: [0,1],
              visible: false
            },
            {
              targets: [0,1],
              sortable: false
            }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                 if(areWeUsingScroll)
                 {
                   if (perscroll_label) perscroll_label.destroy();
                   perscroll_label = new PerfectScrollbar('#mytablelabel_wrapper .dataTables_scrollBody');
                 }
             },
             scrollX: 'auto',
             fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                 if(areWeUsingScroll)
                 { 
                   if (perscroll_label) perscroll_label.destroy();
                   perscroll_label = new PerfectScrollbar('#mytablelabel_wrapper .dataTables_scrollBody');
                 }
             }
          });
        }, 1000);
        
      

      var hideCol = [3,10,11,12,13];
      if(selected_global_media_type=='ig') {
        hideCol.push(5);
        hideCol.push(6);
      }
      else hideCol.push(7);
      
      setTimeout(function(){ 
          table1 = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: false,
          order: [[ 12, "desc" ]],
          pageLength: 10,
          ajax: {
              url: base_url+'subscriber_manager/bot_subscribers_data',
              type: 'POST',
              data: function ( d )
              {
                  d.page_id = $('#page_id').val();
                  d.search_value = $('#search_value').val();
                  d.label_id = $('#label_id').val();
                  d.email_phone_birth = $('#email_phone_birth').val();
                  d.gender = $('#gender').val();
              }
          },
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
                targets: hideCol,
                visible: false
            },
            {
                targets: [0,2,4,8,9,10,11,12],
                className: 'text-center'
            },
            {
                targets: [0,1,2,8,10],
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
      }, 1000);
      

      $(document).on('blur', '#page_id', function(e) {
          var page_id =$(this).val();
          var return_social_media_by_force = '0';

          $.ajax({
            context: this,
            type:'POST',
            dataType:'JSON',
            url:"<?php echo site_url();?>home/switch_to_page",
            data:{page_id,return_social_media_by_force},
            success:function(response){  
                $.ajax({
                context: this,
                type:'POST',
                url:"<?php echo site_url();?>subscriber_manager/get_label_dropdown",
                data:{page_id:page_id},
                success:function(response){
                   $("#label_dropdown").html(response);
                   if(table1!='') table1.draw(false);               
                   if(table_label!='') table_label.draw(false);
                   // $("#page_err").text("");
                }
              });
            }
          });

          
      });

      if(auto_selected_subscriber!='' && auto_selected_subscriber!='0') 
      {
        $("#search_value").val(auto_selected_subscriber);
        $("#search_subscriber").click();
      }
      
      $(document).on('click', '#search_subscriber', function(e) {
          e.preventDefault(); 
          table1.draw(false);
      });

      $(document).on('change', '#label_id', function(e) {
          table1.draw(false);
      });

      $(document).on('change', '#gender', function(e) {
          table1.draw(false);
      });

      $(document).on('change', '#email_phone_birth', function(e) {
          table1.draw(false);
      });


      $(document).on('click','#assign_group',function(e){
          e.preventDefault();
          var upto = 500;
          var selected_page=$("#page_id").val(); // database id
          var ids = [];
          $(".datatableCheckboxRow:checked").each(function ()
          {
              ids.push(parseInt($(this).val()));
          });
          var selected = ids.length;

          if(selected_page=="")
          {
            swal('<?php echo $this->lang->line("Warning") ?>',"<?php echo $this->lang->line('To assign labels in bulk you have to search by any page first.');?>", 'warning');
              return;
          }

          if(ids=="") 
          {
            swal('<?php echo $this->lang->line("Warning") ?>', youhavenotselected+" "+upto+" "+leadsatatime, 'warning');
            return;
          } 
          if(selected>upto) 
          {
              swal('<?php echo $this->lang->line("Warning") ?>',youcanselectupto+" "+upto+" "+leadsyouhaveselected+" "+selected+" "+leads, 'warning');
              return;
          }
          
          $.ajax({
            type:'POST' ,
            url: "<?php echo site_url(); ?>subscriber_manager/get_label_dropdown_multiple",
            data:{selected_page:selected_page},
            success:function(response)
            {
               $("#get_labels").html(response);
            }
          });  

          $("#assign_group_modal").modal();         
      });

      $(document).on('click', '#assign_sms_email_sequence', function(event) {
        event.preventDefault();
        var upto = 500;
        var selected_page=$("#page_id").val();
        var ids = [];

        $(".datatableCheckboxRow:checked").each(function ()
        {
            ids.push(parseInt($(this).val()));
        });
        var selected = ids.length;

        if(selected_page=="")
        {
          swal('<?php echo $this->lang->line("Warning") ?>',"<?php echo $this->lang->line('To assign sequence in bulk you have to search by any page first.');?>", 'warning');
            return;
        }

        if(ids=="") 
        {
          swal('<?php echo $this->lang->line("Warning") ?>', youhavenotselectanysubscribertoassignsequence+" "+upto+" "+leadsatatime, 'warning');
          return;
        } 
        if(selected>upto) 
        {
            swal('<?php echo $this->lang->line("Warning") ?>',youcanselectupto+" "+upto+" "+leadsyouhaveselected+" "+selected+" "+leads, 'warning');
            return;
        }
        
        $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>subscriber_manager/get_sequence_campaigns",
          data:{selected_page:selected_page},
          success:function(response)
          {
             $("#sequence_campaigns").html(response);
          }
        });  

        $("#assign_sqeuence_campaign_modal").modal(); 

      });

      $(document).on('click','#assign_group_submit',function(e){
          e.preventDefault();
          swal({
             title: '<?php echo $this->lang->line("Assign Label"); ?>',
             text: '<?php echo $this->lang->line("Do you really want to assign selected labels to your selected subscribers? Please be noted that bulk assigning labels will replace subscribers previous labels if any."); ?>',
             icon: 'warning',
             buttons: true,
             dangerMode: true,
           })
           .then((willDelete) => {
             if (willDelete) 
             {
                 var ids = [];
                 $(".datatableCheckboxRow:checked").each(function ()
                 {
                     ids.push(parseInt($(this).val()));
                 });
                 var selected = ids.length;        
                 
                 
                 if(ids=="") 
                 {
                   swal('<?php echo $this->lang->line("Warning") ?>', youhavenotselected+" "+upto+" "+leadsatatime, 'warning');
                   return;
                 } 

                 var group_id=$("#label_ids").val();
                 var page_id=$("#page_id").val();
                 var count=group_id.length;
                 
                 if(count==0) 
                 {
                   swal('<?php echo $this->lang->line("Error") ?>', youhavenotselectedanyleadgroup, 'error');
                   return;
                 } 

                 $("#assign_group_submit").addClass("btn-progress");

                 $.ajax({
                   type:'POST' ,
                   url: "<?php echo site_url(); ?>subscriber_manager/bulk_group_assign",
                   data:{ids:ids,group_id:group_id,page_id:page_id},
                   success:function(response)
                   {
                    $("#assign_group_submit").removeClass("btn-progress");
                    swal('<?php echo $this->lang->line("Label Assign") ?>', groupshavebeenassignedsuccessfully+" ("+selected+")", 'success')
                    .then((value) => {
                     $("#assign_group_modal").modal('hide');  
                     table1.draw(false);
                     table_label.draw(false);
                    });

                   }
                 });         
             } 
           });        
      });

      $(document).on('click','#assign_sequence_submit',function(e){
        e.preventDefault();

        var ids = [];
        $(".datatableCheckboxRow:checked").each(function ()
        {
            ids.push(parseInt($(this).val()));
        });
        var selected = ids.length;        
        
        if(ids=="") 
        {
          swal('<?php echo $this->lang->line("Warning") ?>', youhavenotselectanysubscribertoassignsequence+" "+upto+" "+leadsatatime, 'warning');
          return;
        } 

        var sequence_id = $("#sequence_ids").val();
        var page_id = $("#page_id").val();
        var count = sequence_id.length;
        
        if(count==0) 
        {
          swal('<?php echo $this->lang->line("Error") ?>', youhavenotselectedanysequence, 'error');
          return;
        } 

        $("#assign_sequence_submit").addClass("btn-progress");

        $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>subscriber_manager/bulk_sequence_campaign_assign",
          data:{ids:ids,sequence_id:sequence_id,page_id:page_id},
          success:function(response)
          {
            $("#assign_sequence_submit").removeClass("btn-progress");
            swal('<?php echo $this->lang->line("Sequence Campaign Assign") ?>', sequencehavebeenassignedsuccessfully+" ("+selected+")", 'success')
            .then((value) => {
              $("#assign_sqeuence_campaign_modal").modal('hide');  
              table1.draw(false);
            });

          }
        });  

      });

      $(document).on('click','#bulk_delete_contact',function(e){
          e.preventDefault();
          var ids = [];
          var page_id=$("#page_id").val();

          $(".datatableCheckboxRow:checked").each(function ()
          {
              ids.push(parseInt($(this).val()));
          });
          var selected = ids.length;   

          if(page_id=="")
          {
            swal('<?php echo $this->lang->line("Warning") ?>',"<?php echo $this->lang->line('To delete subscribers in bulk you have to search by any page first.');?>", 'warning');
              return;
          }     
          if(ids=="") 
          {
            swal('<?php echo $this->lang->line("Warning") ?>', youhavenotselected2, 'warning');
            return;
          } 

          swal({
             title: '<?php echo $this->lang->line("Delete Subscribers"); ?>',
             text: '<?php echo $this->lang->line("Do you really want to delete selected subscribers?"); ?>',
             icon: 'error',
             buttons: true,
             dangerMode: true,
           })
           .then((willDelete) => {
             if (willDelete) 
             {
                 $.ajax({
                   type:'POST' ,
                   url: "<?php echo site_url(); ?>subscriber_manager/delete_bulk_subscriber",
                   data:{ids:ids,page_id:page_id},
                   success:function(response)
                   {
                    swal('<?php echo $this->lang->line("Delete Subscribers") ?>', contactshavebeendeletedsuccessfully+" ("+selected+")", 'success')
                    .then((value) => {                   
                     table1.draw(false);
                    });

                   }
                 });         
             } 
           });        
      });    


      $(document).on('click','.subscriber_actions_modal',function(e){
          e.preventDefault();
          
          var id=$(this).attr('data-id');
          var subscribe_id=$(this).attr('data-subscribe-id');
          var page_id=$(this).attr('data-page-id'); // auto id
          $("#search_subscriber_id").val(subscribe_id);
      
          var social_media = 'ig';
          if (page_id.indexOf('fb') > -1) social_media = 'fb';

          $("#subscriber_actions_modal").modal();
          get_subscriber_action_content(id,subscribe_id,page_id); 
          var user_input_flow_exist = "<?php echo $user_input_flow_exist; ?>";
          if(user_input_flow_exist == 'yes')
          {
            get_subscriber_flowdata(id,subscribe_id,page_id);
            get_subscriber_customfields(id,subscribe_id,page_id);
          }
          else
          {
            $("#flowanswers-tab,#customfields-tab").hide();
          }

          if(is_webview_exist) {
            get_subscriber_formdata(id,subscribe_id,page_id);
          }
          else $("#formdata-tab").hide();

          $("#default-tab").click();
      });     

     
      function get_subscriber_flowdata(id,subscribe_id,page_id)
      {
        $(".flowanswers_div").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');

        $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>subscriber_manager/get_subscriber_inputflow_data",
          data:{id:id,page_id:page_id,subscribe_id:subscribe_id},
          success:function(response)
          {
            $(".flowanswers_div").html(response);
          }
        }); 
      }

      function get_subscriber_customfields(id,subscribe_id,page_id)
      {
        $(".customfields_div").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');

        $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>subscriber_manager/get_subscriber_customfields_data",
          data:{id:id,page_id:page_id,subscribe_id:subscribe_id},
          success:function(response)
          {
            $(".customfields_div").html(response);
          }
        }); 
      }

      function get_subscriber_formdata(id,subscribe_id,page_id)
      {
        $(".formdata_div").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');

        $.ajax({
          type:'POST' ,
          url: "<?php echo site_url(); ?>subscriber_manager/get_subscriber_formdata",
          data:{id:id,page_id:page_id,subscribe_id:subscribe_id},
          success:function(response)
          {
            $(".formdata_div").html(response);
          }
        }); 
      }

      var table2 = '';

      $(document).on('change', '#search_status', function(e) {
          table2.draw();
      });

      $(document).on('change', '#search_date_range_val', function(e) {
          e.preventDefault();
          table2.draw();
      });

      $(document).on('keypress', '#search_value2', function(e) {
        if(e.which == 13) $("#search_action").click();
      });

      $(document).on('click', '#search_action', function(event) {
        event.preventDefault(); 
        table2.draw();
      });

      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var id = $(this).attr('id');
        if(id=='purchase-tab' ) setTimeout(function(){ get_purchase_data(); }, 1000);
      });


      function get_purchase_data()
      {
        var perscroll2;
        if (table2 == '')
        {
          table2 = $("#mytable2").DataTable({
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 10, "desc" ]],
            pageLength: 10,
            ajax: {
                url: base_url+'subscriber_manager/my_orders_data',
                type: 'POST',
                data: function ( d )
                {
                    d.search_subscriber_id = $('#search_subscriber_id').val();
                    d.search_status = $('#search_status').val();
                    d.search_value = $('#search_value2').val();
                    d.search_date_range = $('#search_date_range_val').val();
                }
            },
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
              {
                  targets: [1,3,6,7,11],
                  visible: false
              },
              {
                  targets: [5,7,8,9,10,11],
                  className: 'text-center'
              },
              {
                  targets: [3,4],
                  className: 'text-right'
              },
              {
                  targets: [2,8,9],
                  sortable: false
              }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                   if(areWeUsingScroll)
                   {
                     if (perscroll2) perscroll2.destroy();
                     perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                   }
               },
               scrollX: 'auto',
               fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                   if(areWeUsingScroll)
                   { 
                     if (perscroll2) perscroll2.destroy();
                     perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                   }
               }
          });
        } 
        else table2.draw();
    }

     $(document).on('click','#migrate_list',function(e){
      
      e.preventDefault();

      swal({
           title: '<?php echo $this->lang->line("Migrate Conversations as Bot Subscriber"); ?>',
           text: '<?php echo $this->lang->line("Do you really want to migrate all of your page converasations as bot subscribers?"); ?>',
           icon: 'warning',
           buttons: true,
           dangerMode: true,
         })
         .then((willDelete) => {
           if (willDelete) 
           {
               var base_url = '<?php echo site_url();?>';
               $(this).parent().prev().addClass('btn-progress');

               var user_page_id = $("#migrate_list").attr('button_id');

               $.ajax({
                 context: this,
                 type:'POST' ,
                 url:"<?php echo site_url();?>subscriber_manager/migrate_lead_to_bot",
                 dataType: 'json',
                 data:{},
                 success:function(response){ 
                    $(this).parent().prev().removeClass('btn-progress');
                    if(response.status == '1')
                    {
                      swal('<?php echo $this->lang->line("Migration Successful"); ?>', response.message, 'success');
                    }
                    else
                    {
                      swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                    }
                 }
               });
           } 
         });
    }); 

    $('#subscriber_actions_modal').on('hidden.bs.modal', function () { 
      table1.draw(false);
    });
    $('#subscriber_actions_modal').on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
    }); 
     
      

    $(document).on('click', '.add_label', function(event) {
        event.preventDefault();
        $("#name_err").text("");
        // $("#page_err").text("");
        $("#group_name").val("");
        // $("#page_id").val("").change();
        $("#add_label").modal();
    });

    // create new label
    $(document).on('click', '#create_label_main', function(event) {
        event.preventDefault();

        $("#name_err").text("");
        // $("#page_err").text("");

        group_name = $("#group_name").val();
        selected_page_id = $("#page_id").val();

        if(group_name == '') {
            $("#name_err").text("<?php echo $this->lang->line('Name is Required') ?>")
            return false;
        }
        // if(selected_page_id == '') {
        //     $("#page_err").text("<?php echo $this->lang->line('Page is Required') ?>")
        //     return false;
        // }

        $(this).addClass('btn-progress');
        var that = $(this);

        $.ajax({
            url: '<?php echo base_url('subscriber_manager/ajax_label_insert'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {group_name:group_name,selected_page_id:selected_page_id},
            success: function(response) {
                $("#result_status").html('');
                $("#result_status").css({"background":"","padding":"","margin":""});

                if(response.status =="0")
                {   
                    var errorMessage = JSON.stringify(response,null,10);
                    swal('<?php echo $this->lang->line("Error"); ?>',errorMessage, "error");
                    // iziToast.error({title: '',message: response.message,position: 'bottomRight'});
                    $("#result_status").css({"background":"#EEE","margin":"10px"});

                } else if(response.status=='1')
                {
                    iziToast.success({title: '',message: response.message,position: 'bottomRight'});
                }

                table_label.draw();
                $(that).removeClass('btn-progress');
            }
        });

    });

    $(document).on('keyup', '#group_name', function(event) {
        event.preventDefault();
        $("#name_err").text("");
    });

    $(document).on('keyup', '#searching', function(event) {
        table_label.draw();
    });


    // delete label
    $(document).on('click', '.delete_label', function(event) {
        event.preventDefault();

        swal({
            title: '<?php echo $this->lang->line("Delete Label"); ?>',
            text: '<?php echo $this->lang->line("Do you want to delete this label?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) 
            {
                var table_id = $(this).attr("table_id");
                var social_media = $(this).attr("social_media");

                $(this).addClass('btn-danger btn-progress').removeClass('btn-outline-danger');
                var that = $(this);

                $.ajax({
                    url: '<?php echo base_url('subscriber_manager/ajax_delete_label'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {table_id:table_id,social_media:social_media},
                    success: function(response) {
                        if(response.status == 'successfull')
                        {
                            iziToast.success({title: '',message: response.message,position: 'bottomRight'});

                        } else if(response.status == 'failed')
                        {
                            swal("<?php echo $this->lang->line('Error') ?>", response.message, "error")

                        } else if(response.status == 'error')
                        {
                            var errorMessage = JSON.stringify(response,null,10);
                            swal('<?php echo $this->lang->line("Error"); ?>',errorMessage, "error");
                        } else if(response.status == 'wrong')
                        {
                            swal('<?php echo $this->lang->line("Error"); ?>',response.message, "error");
                        }

                        table_label.draw();
                        $(that).removeClass('btn-danger btn-progress').addClass('btn-outline-danger');
                    }
                });
            } 
        });

    });

    $('#add_label').on('hidden.bs.modal', function() { 
        $("#name_err").text("");
        // $("#page_err").text("");
        $("#group_name").val("");
        // $("#page_id").val("").change();
        table_label.draw();
    });

    $("document").ready(function(){

    $(".page_list_item").click(function(e) {
      e.preventDefault();
      get_page_details(this);      
    });  

    $(document).on('click','.import_data',function(e){
      e.preventDefault();
      var id=$(this).attr('id');
      $("#start_scanning").attr("data-id",id);      
      $("#import_lead_modal").modal();
    });

    $(document).on('click','.subscriber_info_modal',function(e){
      e.preventDefault();
      $("#subscriber_info_modal").modal();
    });

    $(document).on('click','#start_scanning',function(e){
      e.preventDefault();
      var id=$(this).attr('data-id');
      var scan_limit=$("#scan_limit").val();
      var folder=$("#folder").val();
      $("#start_scanning").addClass('btn-progress');
      $(".auto_sync_lead_page").addClass('disabled');
      $(".user_details_modal").addClass('disabled');
      $("#scan_load").attr('class','');
      $.ajax({
        context:this,
        type:'POST' ,
        url:"<?php echo site_url();?>subscriber_manager/import_lead_action",
        data:{id:id,scan_limit:scan_limit,folder:folder},
        dataType:'JSON',
        success:function(response){
         $("#start_scanning").removeClass('btn-progress');

         if(response.status == '1')
         {
           swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success');
         }
         else
         {
           swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
         }
        }
      });

    });

    $(document).on('click','.auto_sync_lead_page',function(e){
      e.preventDefault();
      var page_id = $(this).attr('auto_sync_lead_page_id');
      var operation = $(this).attr('enable_disable');
      var base_url = '<?php echo site_url();?>';

      var disabledsuccessfully = '<?php echo $disabledsuccessfully;?>';
      var enabledsuccessfully = '<?php echo $enabledsuccessfully;?>';

      $(".import_data").addClass('disabled');
      $(".auto_sync_lead_page").addClass('disabled');
      $(".user_details_modal").addClass('disabled');
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>subscriber_manager/enable_disable_auto_sync",
        data:{page_id:page_id,operation:operation},
        success:function(response)
        { 
           if(operation=="0") iziToast.success({title: '',message: disabledsuccessfully,position: 'bottomRight'});
           else iziToast.success({title: '',message: enabledsuccessfully,position: 'bottomRight'});

           $(".page_list_item.active").click();
        }
      });

    });

    $('.modal').on("hidden.bs.modal", function (e) { 
      if ($('.modal:visible').length) { 
          $('body').addClass('modal-open');
      }
  
  });

  });  

  $("document").ready(function(){  
    $('#import_lead_modal').on('hidden.bs.modal', function () { 
      $(".page_list_item.active").click();
    });
  });

  $("document").ready(function(){
    setTimeout(function(){ 
    var session_value = "<?php echo $this->session->userdata('sync_subscribers_get_page_details_page_table_id'); ?>";
    var elem;
    if(session_value=='')  elem = $(".list-group li:first");    
    else elem = $("li[page_table_id='"+session_value+"']");
    get_page_details(elem);
    }, 500);
    
  });


  });

      
  
 
</script>

<?php include(FCPATH.'application/views/messenger_tools/subscriber_actions_common_js.php');?>



<style type="text/css">
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  .multi_layout{border:1px solid #dee2e6;border-top-width: 0;}
  .multi_layout .collef{padding-left: 0px; padding-right: 0px;border-right: 1px solid #dee2e6;}
  .multi_layout .colmid{padding-left: 0px; padding-right: 0px;}
  .multi_layout .card-statistic-1{border:1px solid #dee2e6;border-radius: 4px;}
  .multi_layout .main_card{min-height: 100%;}
  .multi_layout h6.page_name{font-size: 14px;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .card .card-header h4 a{font-weight: 700 !important;}
  .multi_layout .card-primary{margin-top: 35px;margin-bottom: 15px;}
  .multi_layout .product-details .product-name{font-size: 12px;}
  .multi_layout .margin-top-50 {margin-top: 70px;}
  .multi_layout .waiting {height: 100%;width:100%;display: table;}
  .multi_layout .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
  .multi_layout .collef .bgimage{border-radius:5px;height: 250px;background-position: 50% 50%; background-size: cover;min-width: 140px;background-repeat:no-repeat;display: block;}
  .multi_layout .collef .subscriber_details{padding-right: 20px;}
  /*.multi_layout .colmid .section-title{padding-bottom: 10px;}*/
  .tab-content > .tab-pane{padding:0;}
   @media (max-width: 575.98px) {
      .multi_layout .collef{border-right: none !important;}
    }

  .multi_layout2{margin:0;background: #fff}
  .multi_layout2 .card{margin-bottom:0;border-radius: 0;}
  .multi_layout2 p, .multi_layout2 ul:not(.list-unstyled), .multi_layout2 ol{line-height: 15px;}
  .multi_layout2 .list-group li{padding: 15px 10px 12px 25px;}
  .multi_layout2{border:.5px solid #dee2e6;}
  .multi_layout2 .collef,.multi_layout2 .colmid{padding-left: 0px; padding-right: 0px;border-right: .5px solid #dee2e6;}
  .multi_layout2 .colmid .card-icon{border:.5px solid #dee2e6;}
  .multi_layout2 .colmid .card-icon i{font-size:30px !important;}
  .multi_layout2 .main_card{box-shadow: none;}
  .multi_layout2 .collef .makeScroll{max-height:550px;overflow:auto;}
  .multi_layout2 .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;cursor: pointer;z-index: 0;}
  .multi_layout2 .list-group .list-group-item:first-child{border-top:none;}
  .multi_layout2 .list-group .list-group-item:last-child{border-bottom:none;}
  .multi_layout2 .list-group .list-group-item.active{border:.5px solid #6777EF;}
  .multi_layout2 .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout2 .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;}
  .multi_layout2 h6.page_name{font-size: 14px;}
  .multi_layout2 .card .card-header input{max-width: 100% !important;}
  .multi_layout2 .card .card-header h4 a{font-weight: 700 !important;}
  .multi_layout2 .card-primary{margin-top: 35px;margin-bottom: 15px;}
  .multi_layout2 .product-details .product-name{font-size: 12px;}
  .multi_layout2 .margin-top-50 {margin-top: 70px;}
  .multi_layout2 .waiting {height: 100%;width:100%;display: table;}
  .multi_layout2 .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
  .subscriber_info_modal {cursor: pointer;}
</style>

<div class="modal fade" id="assign_group_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-user-tag"></i> <?php echo $this->lang->line("Assign Label");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>

            <div class="modal-body">    
                <div id="get_labels">              
                </div>
            </div>
            <div class="modal-footer">
              <a class="btn btn-primary float-left" href="" id="assign_group_submit"><i class="fas fa-user-tag"></i> <?php echo $this->lang->line("Assign Label") ?></a>
              <a class="btn btn-outline-secondary float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close") ?></a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assign_sqeuence_campaign_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style='min-width:40%;'>
        <div class="modal-content">
            <div class="modal-header bbw">
              <h5 class="modal-title"><i class="fas fa-sort-numeric-up"></i> <?php echo $this->lang->line("Assign sms/email Sequence");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            
            <div class="modal-body">   
              <div class="text-center" style="padding:20px;margin-bottom:20px;border:.5px solid #dee2e6; color:var(--blue);background: #fff;"><?php echo $this->lang->line("Bulk sequence assign is available for Email & SMS cmapaign. For Messenger, bulk campaign isn't available due to safety & avoiding breaking 24 Hours policy. "); ?></div>
              <div id="sequence_campaigns"></div>
            </div>
            <div class="modal-footer bg-whitesmoke">
              <a class="btn btn-lg btn-primary float-left" href="" id="assign_sequence_submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Assign Sequence") ?></a>
              <a class="btn btn-lg btn-light float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close") ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="subscriber_actions_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="padding:15px;">
              <h5 class="modal-title"><i class="fas fa-user-circle"></i> <?php echo $this->lang->line("Subscriber Actions");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>

            <div class="modal-body" id="subscriber_actions_modal_body" style="padding:0 15px 15px 15px;" data-backdrop="static" data-keyboard="false">
              
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="default-tab" data-toggle="tab" href="#default" role="tab" aria-controls="default" aria-selected="true"><?php echo $this->lang->line("Subscriber Data"); ?></a>
                </li>

                <?php if($user_input_flow_exist == 'yes') : ?>
                <li class="nav-item">
                  <a class="nav-link" id="flowanswers-tab" data-toggle="tab" href="#flowanswers" role="tab" aria-controls="flowanswers" aria-selected="false"><?php echo $this->lang->line("User Input Flow Answer"); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="customfields-tab" data-toggle="tab" href="#customfields" role="tab" aria-controls="customfields" aria-selected="false"><?php echo $this->lang->line("Custom Fields"); ?></a>
                </li>
                <?php endif; ?>

                <?php if($webview_access == 'yes') : ?>
                <li class="nav-item">
                  <a class="nav-link" id="formdata-tab" data-toggle="tab" href="#formdata" role="tab" aria-controls="formdata" aria-selected="false"><?php echo $this->lang->line("Custom Form Data"); ?></a>
                </li>
                <?php endif; ?>
                <?php if($ecommerce_exist == 'yes') : ?>
                <li class="nav-item">
                  <a class="nav-link" id="purchase-tab" data-toggle="tab" href="#purchase" role="tab" aria-controls="purchase" aria-selected="false"><?php echo $this->lang->line("Purchase History"); ?></a>
                </li>
                <?php endif; ?>

              </ul>

              <div class="tab-content" id="myTabContent">
                
                <div class="tab-pane fade active show" id="default" role="tabpanel" aria-labelledby="default-tab">
                  <div class="row multi_layout">
                  </div> 
                </div>

                <div class="tab-pane fade" id="formdata" role="tabpanel" aria-labelledby="formdata-tab">
                  <div class="card no_shadow" style="border:1px solid #dee2e6;border-top:none;border-radius:0">
                    <div class="card-body">
                      <div class="row formdata_div" style="padding-top:20px;"></div>                  
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="flowanswers" role="tabpanel" aria-labelledby="flowanswers-tab">
                  <div class="card no_shadow" style="border:1px solid #dee2e6;border-top:none;border-radius:0">
                    <div class="card-body">
                      <div class="row flowanswers_div" style="padding-top:20px;"></div>                  
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="customfields" role="tabpanel" aria-labelledby="customfields-tab">
                  <div class="card no_shadow" style="border:1px solid #dee2e6;border-top:none;border-radius:0">
                    <div class="card-body">
                      <div class="row customfields_div" style="padding-top:20px;"></div>                  
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
                  <div class="card no_shadow data-card" style="border:1px solid #dee2e6;border-top:none;border-radius:0">
                    <div class="card-body">
                      <div class="row purchase_div" style="padding-top:20px;"></div>
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <?php
                          $status_list[''] = $this->lang->line("Status");                
                          echo 
                          '<div class="input-group mb-3" id="searchbox">
                            <div class="input-group-prepend d-none">
                              <input type="text" value="" name="search_subscriber_id" id="search_subscriber_id">
                            </div>
                            <div class="input-group-prepend d-none">
                              '.form_dropdown('search_status',$status_list,'','class="form-control select2" id="search_status"').'
                            </div>
                            <input type="text" class="form-control" id="search_value2" autofocus name="search_value2" placeholder="'.$this->lang->line("Search...").'" style="max-width:25%;">
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                            </div>
                          </div>'; ?>                                          
                        </div>

                        <div class="col-12 col-md-3">

                        <?php
                          echo $drop_menu ='<a href="javascript:;" id="search_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="search_date_range_val">';
                        ?>

                                                   
                        </div>
                      </div>

                      <div class="table-responsive2">
                          <table class="table table-bordered" id="mytable2">
                            <thead>
                              <tr>
                                <th>#</th>      
                                <th style="vertical-align:middle;width:20px">
                                    <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                                </th>         
                                <th style="max-width: 130px"><?php echo $this->lang->line("Status")?></th>              
                                <th><?php echo $this->lang->line("Coupon")?></th>                   
                                <th><?php echo $this->lang->line("Amount")?></th>                   
                                <th><?php echo $this->lang->line("Currency")?></th>                   
                                <th><?php echo $this->lang->line("Method")?></th>                   
                                <th><?php echo $this->lang->line("Transaction ID")?></th>                   
                                <th><?php echo $this->lang->line("Invoice")?></th>                              
                                <th><?php echo $this->lang->line("Docs")?></th>                              
                                <th><?php echo $this->lang->line("Ordered at")?></th>                   
                                <th><?php echo $this->lang->line("Paid at")?></th>                  
                              </tr>
                            </thead>
                          </table>
                      </div>

                    </div>
                  </div>
                </div>

              </div>

                         
            </div>
   
        </div>
    </div>
</div>

<div class="modal fade" id="add_label" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Label") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="add_label_modal_body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                          <label><i class="fas fa-tags"></i> <?php echo $this->lang->line('Label Name'); ?></label>
                          <input type="text" name="group_name" id="group_name" class="form-control">
                          <span id="name_err" class="red"></span>
                        </div>
                    </div>
                </div>            
            </div>

            <div id="result_status"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-lg btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
              <button id="create_label_main" type="button" class="btn btn-lg btn-primary"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="import_lead_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-qrcode"></i> <?php echo $this->lang->line("Scan Page Inbox");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-12">
                      <div id="import_lead_body">
                        <div id="scan_load"></div><br>
                        <div class="row">
                          <div class="form-group col-12 col-lg-6">              
                              <label>
                                <?php echo $this->lang->line("Scan Latest Leads");?>
                                <a href="#" data-placement="right" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Scanning process scans your page conversation and import them as subscriber. We strongly recommend to use cron based scanning feature for first time, if your page conversation is huge. After importing all subscribers, the cron feature will not import any future new subscribers, you have to scan for latest subscribers manually occasionally using the scan limit feature. Although you can enable the cron based scanning again manually but be informed that it will rescan the full page conversation. If you are scanning for first time and your inbox conversation is moderate, then you can scan all of them at once. To get future new subscribers scan occasionally same as stated earlier.');?>" data-original-title="<?php echo $this->lang->line('Scan Latest Leads');?>"><i class="fa fa-info-circle"></i> </a>
                              </label>
                              <?php 
                              $scan_drop=
                              array
                              (
                                ''=>$this->lang->line("Scan all subscribers"),
                                "500"=>"500 ".$this->lang->line("Subscribers"),
                                "1000"=>"1000 ".$this->lang->line("Subscribers"),
                                "2000"=>"2000 ".$this->lang->line("Subscribers"),
                                "3000"=>"3000 ".$this->lang->line("Subscribers"),
                                "5000"=>"5000 ".$this->lang->line("Subscribers"),
                                "10000"=>"10000 ".$this->lang->line("Subscribers"),
                                "20000"=>"20000 ".$this->lang->line("Subscribers"),
                                "30000"=>"30000 ".$this->lang->line("Subscribers"),
                                "50000"=>"50000 ".$this->lang->line("Subscribers"),
                                "100000"=>"100000 ".$this->lang->line("Subscribers")
                              );
                              echo form_dropdown('lead_limit',$scan_drop, '','class="form-control select2" id="scan_limit" style="width:100%;"'); ?>
                          </div>

                          <div class="form-group col-12 col-lg-6" id="folder_con">              
                              <label>
                                <?php echo $this->lang->line("Folder");?>
                                <a href="#" data-placement="right" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('The target folder from which to retrieve conversations.');?>" data-original-title="<?php echo $this->lang->line('Folder');?>"><i class="fa fa-info-circle"></i> </a>
                              </label>
                              <?php 
                              $scan_drop=
                              array
                              (
                                "inbox"=>$this->lang->line("Inbox"),
                                "page_done"=>$this->lang->line("Done"),
                                "spam"=>$this->lang->line("Spam"),
                                "other"=>$this->lang->line("Other")
                              );
                              echo form_dropdown('folder',$scan_drop, '','class="form-control select2" id="folder" style="width:100%;"'); ?>
                          </div>
                        </div>
                      </div>                      
                    </div>
                </div>               
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default  btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
              <button type="button" class="btn btn-primary btn-lg"  id="start_scanning"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("Start Scanning"); ?></button>
          </div>

        </div>
    </div>
</div>

<div class="modal fade" id="subscriber_info_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-users"></i> <?php echo $this->lang->line("Subscribers");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>

            <div class="modal-body">    
              <div class="section">                
                <h2 class="section-title"><?php echo $this->lang->line('Conversation Subscribers'); ?></h2>
                <p><?php echo $this->lang->line("Conversation Subscribers are, who have conversation in your page inbox. These users may come from Messenger Bot, Comment Private Reply, Click to Messenger Ads or Send Message CTA Post.  These users are eligible to get Conversation Broadcast message. Even if after getting private reply, users doesn't reply back will be counted for Conversation Broadcast."); ?></p>
              </div>
              <div class="section">                
                <h2 class="section-title"><?php echo $this->lang->line('BOT Subscribers'); ?></h2>
                <p><?php echo $this->lang->line("BOT Subscribers are those users who have given message & get reply from Messenger BOT after enabling in our system. However you can also migrate Conversation Subscribers (Existing Subscribers) to BOT subscribers. In this case BOT subscribers are those who have given message to your page. BOT subscribers may less than Conversation subscribers for different reason like"); ?></p>
                <ol>
                  <li><?php echo $this->lang->line("The user deactivated their account."); ?></li>
                  <li><?php echo $this->lang->line("The user blocked your page."); ?></li>
                  <li><?php echo $this->lang->line("The user don't have activity for long days with your page."); ?></li>
                  <li><?php echo $this->lang->line("The user may in conversation subscriber list as got private reply of comment but never reply may not eligible for BOT Subscriber."); ?></li>
                </ol>
              </div>
              <div class="section">                
                <h2 class="section-title"><?php echo $this->lang->line('24H Subscribers'); ?></h2>
                <p><?php echo $this->lang->line("Those users who interacted with your messenger bot within 24 hours. This subscribers are eligible to get promotional message through Subscriber Broadcast."); ?></p>
              </div>
              <div class="section">                
                <h2 class="section-title"><?php echo $this->lang->line('Unavailable'); ?></h2>
                <p><?php echo $this->lang->line("You may find red color number as unavailable beside both Conversation Subscribers & BOT Subscribers means the number of users are unavailable for broadcast, because in last broadcasting campaign, Facebook responded with error during sending message to them. They will not be eligible for future broadcast campaign. However once that user send message to your page again, then user become available again."); ?></p>
              </div>
              <div class="section">                
                <h2 class="section-title"><?php echo $this->lang->line('Migrated Subscribers'); ?></h2>
                <p><?php echo $this->lang->line("Those subscribers are migrated as BOT subscribers from Conversation Subscribers. These are basically all old subscribers achieved before using our system for Messenger BOT."); ?></p>
              </div>
            </div>

            <div class="modal-footer">
              <a class="btn btn-outline-secondary float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close") ?></a>
            </div>
        </div>
    </div>
</div>

<style type="text/css">

    #page_id{width: 120px;}
    #label_id{width: 100px;}
    .bbw{border-bottom-width: thin !important;border-bottom:solid .5px #f9f9f9 !important;padding-bottom:20px;}
    @media (max-width: 575.98px) {
      #page_id{width: 80px;}
      #label_id{width: 80px;}
    }
    .flex-column .nav-item .nav-link.active
    {
      background: #fff !important;
      color: #3516df !important;
      border: 1px solid #988be1 !important;
    }

    .flex-column .nav-item .nav-link .form_id, .flex-column .nav-item .nav-link .insert_date
    {
      color: #608683 !important;
      font-size: 12px !important;
      padding: 0 !important;
      margin: 0 !important;
    }
    .swal-text{text-align: left !important;}
    @media (max-width: 575.98px) {
      #add_label { max-width: 100% !important; }
    }
</style>