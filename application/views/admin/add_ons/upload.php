<?php $this->load->view("include/upload_js"); ?>
<?php $is_demo=$this->is_demo;?>
<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-cloud-upload-alt"></i> <?php echo $page_title; ?></h1>    
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('addons/lists');?>"><?php echo $this->lang->line("Add-on Manager"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>
  <?php if($this->session->flashdata('addon_uplod_success')!="") echo "<div class='alert alert-success text-center'><i class='fa fa-check'></i> ".$this->session->flashdata('addon_uplod_success')."</div>";?>

   <div class="section-body">
      <div class="row">

        <div class="col-12 col-md-6">
          <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line("Upload New Add-on"); ?></h4>
            </div>
            <div class="card-body">
              <div class="form-group">    
                <div id="addon_url_upload"><?php echo $this->lang->line('Upload');?></div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-justify">
              <h6><?php echo $this->lang->line('After you upload add-on file you will be taken to add-on manager page, you need to active the add-on there.');?> <?php echo $this->lang->line('If you are having trouble uploading file using our uploader then you can simply upload add-on zip file in application/modules folder, unzip it and then activate it from add-on manager.');?></h6>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="card" id="server-status">
            <div class="card-header">
              <h4><i class="fas fa-server"></i> <?php echo $this->lang->line("Server Status"); ?></h4>
            </div>
            <div class="card-body">
              <?php
                $list1=$list2="";
                if(function_exists('ini_get'))
                {
                  $make_dir = (!function_exists('mkdir')) ? $this->lang->line("Not Enabled"):$this->lang->line("Enabled");
                  $zip_archive = (!class_exists('ZipArchive')) ? $this->lang->line("Not Enabled"):$this->lang->line("Enabled");
                  $list1 .= "<li class='list-group-item'><b>mkdir</b> : ".$make_dir."</li>"; 
                    $list1 .= "<li class='list-group-item'><b>upload_max_filesize</b> : ".ini_get('upload_max_filesize')."</li>";   
                  $list1 .= "<li class='list-group-item'><b>max_input_time</b> : ".ini_get('max_input_time')."</li>";
                  $list2 .= "<li class='list-group-item'><b>ZipArchive</b> : ".$zip_archive."</li>";  
                    $list2 .= "<li class='list-group-item'><b>post_max_size</b> : ".ini_get('post_max_size')."</li>"; 
                  $list2 .= "<li class='list-group-item'><b>max_execution_time</b> : ".ini_get('max_execution_time')."</li>";
                 }
                ?>
                <div class="row">
                  <div class="col-12 col-md-6">                     
                  <ul class="list-group">
                    <?php echo $list1; ?>
                  </ul>
                  </div>
                  <div class="col-12 col-md-6">
                    <ul class="list-group">
                      <?php echo $list2; ?>
                  </ul>
                  </div>
                </div>
            </div>
          </div>  
        </div>

      </div>
      

      

   </div>
</section>



<script>
  var base_url = "<?php echo base_url(); ?>";
  $("document").ready(function(){

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;}); 

    $("#addon_url_upload").uploadFile({
        url:base_url+"addons/upload_addon_zip",
        fileName:"myfile",
        maxFileSize:100*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        showDelete:false,
        acceptFiles:".zip",
        deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('addons/delete_uploaded_zip');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) {                         
                  });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = data;
               window.location.assign(base_url+'addons/lists'); 
           }
    });
  });
</script>

