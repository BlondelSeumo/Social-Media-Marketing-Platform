<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-shopping-bag"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary"  href="<?php echo site_url('payment/add_package');?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Package"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
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
                    <th><?php echo $this->lang->line("Package ID"); ?></th>      
                    <th><?php echo $this->lang->line("Package Name"); ?></th>
                    <th><?php echo $this->lang->line("Price"); ?> - <?php echo isset($payment_config[0]['currency']) ? $payment_config[0]['currency'] : 'USD'; ?></th>
                    <th><?php echo $this->lang->line("Validity"); ?> - <?php echo $this->lang->line("days"); ?></th>
                    <th><?php echo $this->lang->line("Default Package"); ?></th>
                    <th style="min-width: 150px"><?php echo $this->lang->line("Actions"); ?></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>



<?php $csrf_token=$this->session->userdata('csrf_token_session'); ?>

<script>       
    var base_url="<?php echo site_url(); ?>";
    var csrf_token="<?php echo $csrf_token; ?>";
   
    $(document).ready(function() {

       var perscroll;

       var table = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: true,
          order: [[ 1, "desc" ]],
          pageLength: 10,
          ajax: 
          {
              "url": base_url+'payment/package_manager_data',
              "type": 'POST'
          },
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
                targets: [1],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,6],
                sortable: false
            },
            {
              targets: [3],
              "render": function ( data, type, row, meta ) 
              {
                 if(row[5]=="1" && row[3]=="0")
                 return "Free"; 
                 else return data;  
              }
            },
            {
              targets: [4],
              "render": function ( data, type, row, meta ) 
              {
                 if(row[5]=="1" && row[3]=="0")
                 return "Unlimited"; 
                 else return data; 
              }
            },
            {
              targets: [5],
              "render": function ( data, type, row, meta ) 
              {
                 if(data==1) return "<i class='fas fa-check-circle green'></i>";            
                 else return "<i class='fas fa-times-circle'></i>";
              }
            },
            {
              targets: [6],
              "render": function ( data, type, row, meta ) 
              {
                  var url=base_url+'payment/details_package/'+row[1];        
                  var edit_url=base_url+'payment/edit_package/'+row[1];
                  var delete_url=base_url+'payment/delete_package/'+row[1];
                  var more="<?php echo $this->lang->line('More Info');?>";
                  var edit_str="<?php echo $this->lang->line('Edit');?>";
                  var delete_str="<?php echo $this->lang->line('Delete');?>";
                  var str="";   
                  str="&nbsp;<a class='btn btn-circle btn-outline-primary' href='"+url+"'>"+'<i class="fas fa-eye"></i>'+"</a>";
                  str=str+"&nbsp;<a class='btn btn-circle btn-outline-warning' href='"+edit_url+"'>"+'<i class="fas fa-edit"></i>'+"</a>";
                 
                  if(row[5]=='0')
                  str=str+"&nbsp;<a href='"+delete_url+"' csrf_token='"+csrf_token+"' class='are_you_sure_datatable btn btn-circle btn-outline-danger'>"+'<i class="fa fa-trash"></i>'+"</a>";
                  else str=str+"&nbsp;<a class='btn btn-circle btn-outline-light' data-toggle='tooltip' title='<?php echo $this->lang->line("Default package can not be deleted.");?>'>"+'<i class="fa fa-trash"></i>'+"</a>";
                
                  return "<div style='min-weight:130px'>"+str+'</div>';
              }
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

