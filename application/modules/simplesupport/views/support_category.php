<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-layer-group"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary"  href="<?php echo site_url('simplesupport/add_category');?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Category"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('simplesupport/tickets'); ?>"><?php echo $this->lang->line("Support Desk"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="table-responsive">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th><?php echo $this->lang->line("ID"); ?></th>      
                    <th><?php echo $this->lang->line("Category Name"); ?></th>                    
                    <th style="min-width: 150px"><?php echo $this->lang->line("Actions"); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sl=0;
                    foreach ($category_data as $key => $value) 
                    {
                       $sl++;
                       $id=$value['id'];
                       $action = "<a href='".base_url()."simplesupport/edit_category/".$value['id']."' class='btn btn-circle btn-outline-warning' title='".$this->lang->line("Edit")."' '><i class='fa  fa-edit'></i></a>&nbsp;<a href='".base_url()."simplesupport/delete_category/".$value['id']."' class='btn btn-circle btn-outline-danger are_you_sure_datatable non_ajax' title='".$this->lang->line("Delete")."'><i class='fa fa-trash'></i></a>";
                       echo "<tr>";
                         echo "<td>".$sl."</td>";
                         echo "<td>".$id."</td>";
                         echo "<td>".$value['category_name']."</td>";
                         echo "<td>".$action."</td>";
                       echo "</tr>";
                    }
                  ?>
                </tbody>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>





<script>       
    var base_url="<?php echo site_url(); ?>";
   
    $(document).ready(function() {

      var table = $("#mytable").DataTable({          
          processing:true,
          bFilter: true,
          pageLength: 10,
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [            
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0],
                sortable: false
            }
          ]
      });
    });

   
 
</script>

