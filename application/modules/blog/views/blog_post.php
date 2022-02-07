<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-newspaper"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary"  href="<?php echo site_url('blog/add_post');?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Post"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

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
                    <th><?php echo $this->lang->line("ID"); ?></th>
                    <th><?php echo $this->lang->line("Title"); ?></th>
                    <th><?php echo $this->lang->line("Category"); ?></th>
                    <th><?php echo $this->lang->line("Author"); ?></th>
                    <th><?php echo $this->lang->line("Status"); ?></th>
                    <th><?php echo $this->lang->line("Created At"); ?></th>
                    <th><?php echo $this->lang->line("Published At"); ?></th>
                    <th><?php echo $this->lang->line("Actions"); ?></th>
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

<?php
$drop_menu = '<div class="btn-group dropleft float-right"><button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  '.$this->lang->line("Tag & Category").'  </button>  <div class="dropdown-menu dropleft"> <a class="dropdown-item has-icon pointer" href="'.base_url("blog/tag").'"><i class="fas fa-tag"></i> '.$this->lang->line("Tag Manager").'</a><a class="dropdown-item has-icon pointer" href="'.base_url("blog/category").'"><i class="fas fa-columns"></i> '.$this->lang->line("Category Manager").'</a>';
$drop_menu .= '</div> </div>';
?> 

<script type="text/javascript">
  var base_url="<?php echo site_url(); ?>";
  var perscroll;
  var table = $("#mytable").DataTable({
      serverSide: true,
      processing:true,
      bFilter: true,
      order: [[ 2, "desc" ]],
      pageLength: 10,
      ajax: {
          "url": base_url+'blog/post_data',
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
            targets: [0,3,4,5],
            className: 'text-center'
        },
        {
            targets: [0,5],
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
</script>

<script type="text/javascript"> 
    $(document).ready(function() {
      var drop_menu = '<?php echo $drop_menu;?>';
      setTimeout(function(){ 
        $("#mytable_filter").append(drop_menu); 
      }, 1000);
      // Post Delete
      $(document.body).on('click','.delete_post',function(e){
        e.preventDefault();
        var post_id = $(this).attr('data-id');
        swal({
          title: "<?php echo $this->lang->line("Are you sure?");?>",
          text: '<?php echo $this->lang->line("Do you really want to delete it?");?>',
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
              type:'POST',
              url: base_url+'blog/delete_post',
              data: {post_id:post_id},
              dataType : 'JSON',
              success:function(response)
              {
                if(response.status == "1") {
                  swal('<?php echo $this->lang->line("Success")?>',response.message, 'success');
                  table.draw();
                } 
                  
                if(response.status == "0"){
                  swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
                }
              }
            });
          }
        });
      });
    });
</script>