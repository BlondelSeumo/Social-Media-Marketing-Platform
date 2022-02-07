<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-tags"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <button class="btn btn-primary" data-toggle="modal" data-target="#add_tag_modal">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Tag"); ?>
     </button>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo site_url().'blog/posts';?>"><?php echo $this->lang->line("Blog Manager"); ?></a></div>
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
                    <th><?php echo $this->lang->line("Name"); ?></th>
                    <th><?php echo $this->lang->line("Created At"); ?></th>
                    <th><?php echo $this->lang->line("Updated At"); ?></th>
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



<script type="text/javascript"> 
    var base_url="<?php echo site_url(); ?>";
   
    $(document).ready(function() {
      var perscroll;
      var table = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: true,
          order: [[ 2, "desc" ]],
          pageLength: 10,
          ajax: {
              "url": base_url+'blog/tag_data',
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

      // Tag Store
      $(document.body).on('submit', 'form[name="tag_store"]', function(e) {
        e.preventDefault();

        $("#save_tag").addClass("btn-progress");
        $('.form-control').removeClass('is-invalid');
        var action = $(this).attr('action');
        var formData = $(this).serialize();

        $.ajax({
          type:'POST' ,
          url:action,
          data: formData,
          dataType : 'JSON',
          success:function(response)
          {
            $("#save_tag").removeClass("btn-progress");

            if (response.status == '0'){
              $.each(response.errors, function(key, value){
                $("."+key).addClass('is-invalid');
                $('.'+key+'_error').html(value);
              });
            }

            if(response.status == "1") {
              swal('<?php echo $this->lang->line("Success")?>',response.message, 'success')
             .then((value) => {
                $("#add_tag_modal").modal('hide');
              });
              table.draw();
            } 
              
            if(response.status == "2"){
              swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
            }
          }
        });
      });

      // Tag Update Part
      $(document.body).on('click', '.edit_tag', function(e) {
        e.preventDefault();
        var tag_id = $(this).attr('data-id');
        var tag_name = $(this).attr('data-name');

        $("#tag_name").val(tag_name);
        $("input[name='tag_id']").val(tag_id);
        $("#update_tag_modal").modal();
      });

      $(document.body).on('submit', 'form[name="tag_update"]', function(e) {
        e.preventDefault();

        $("#update_tag").addClass("btn-progress");
        $('.form-control').removeClass('is-invalid');
        var action = $(this).attr('action');
        var formData = $(this).serialize();

        $.ajax({
          type:'POST' ,
          url:action,
          data: formData,
          dataType : 'JSON',
          success:function(response)
          {
            $("#update_tag").removeClass("btn-progress");

            if (response.status == '0'){
              $.each(response.errors, function(key, value){
                $("."+key).addClass('is-invalid');
                $('.'+key+'_error').html(value);
              });
            }

            if(response.status == "1") {
              swal('<?php echo $this->lang->line("Success")?>',response.message, 'success')
             .then((value) => {
                $("#update_tag_modal").modal('hide');
              });
              table.draw();
            } 
              
            if(response.status == "2"){
              swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
            }
          }
        });
      });

      // Tag Delete
      $(document.body).on('click','.delete_tag',function(e){
        e.preventDefault();
        var tag_id = $(this).attr('data-id');
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
              url: base_url+'blog/tag_delete',
              data: {tag_id:tag_id},
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

<div class="modal fade" tabindex="-1" role="dialog" id="add_tag_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add New Tag");?> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <form class="form-horizontal" name="tag_store" action="<?php echo site_url().'blog/tag_store';?>" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="name"><?php echo $this->lang->line("Tag Name"); ?> *  </label>
            <input type="text" id="name" class="form-control name" name="name" value="">
            <div class="invalid-feedback name_error"></div>
          </div>          
        </div>

        <div class="modal-footer bg-whitesmoke br">
          <button type="submit" id="save_tag" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save"); ?></button>
          <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="update_tag_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Tag");?> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <form class="form-horizontal" name="tag_update" action="<?php echo site_url().'blog/tag_update';?>" method="POST">
        <input type="hidden" name="tag_id" value="">
        <div class="modal-body">
          <div class="form-group">
            <label for="name"><?php echo $this->lang->line("Tag Name"); ?> *  </label>
            <input type="text" id="tag_name" class="form-control name" name="name">
            <div class="invalid-feedback name_error"></div>
          </div>          
        </div>

        <div class="modal-footer bg-whitesmoke br">
          <button type="submit" id="update_tag" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Update"); ?></button>
          <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>