<script>
  $(document).ready(function() {

    $('.singleAttributeName').on('change', function (e) {
      var value = $(this).val();
      if(value=='x'){
        $(this).parent().next().children('.form-control').val('');
        $(this).parent().next().children('.form-control').attr('readonly','');
      }
      else{
        $(this).parent().next().children('.form-control').removeAttr('readonly');
      }
    });

    // Uploads files
    var uploaded_file = $('#uploaded-file');
    Dropzone.autoDiscover = false;
    $("#thumb-dropzone").dropzone({ 
      url: '<?php echo base_url('ecommerce/upload_product_thumb'); ?>',
      maxFilesize:1,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".png,.jpg,.jpeg",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        // Shows error message
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(uploaded_file).val(data.filename);
          $("#tmb_preview").hide();
        }
      },
      removedfile: function(file) {
        var filename = $(uploaded_file).val();
        delete_uploaded_file(filename);
        $("#tmb_preview").show();
      },
    });

    function delete_uploaded_file(filename) {
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('ecommerce/delete_product_thumb'); ?>',
          success: function(data) {
            $('#uploaded-file').val('');
          }
        });
      }

      // Empties form values
      empty_form_values();     
    }

    // Empties form values
    function empty_form_values() {
      $('#thumb-dropzone .dz-preview').remove();
      $('#thumb-dropzone').removeClass('dz-started dz-max-files-reached');
      // Clears added file
      Dropzone.forElement('#thumb-dropzone').removeAllFiles(true);
    }


    // Uploads files
    var featured_images_array = []; 
    var featured_images_str = "";
    var featured_uploaded_file = $('#featured-uploaded-file');    
    Dropzone.autoDiscover = false;
    $("#feature-dropzone").dropzone({ 
      url: '<?php echo base_url('ecommerce/upload_featured_image'); ?>',
      maxFilesize:1,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".png,.jpg,.jpeg",
      maxFiles:3,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        // Shows error message
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          featured_images_array.push(data.filename);
          featured_images_str = featured_images_array.join(",");
          $(featured_uploaded_file).val(featured_images_str);
        }
      },
      removedfile: function(file) {
        if (typeof(file.xhr.response)==='undefined') return false;        
        var getfile = JSON.parse(file.xhr.response); 
        if (typeof(getfile['filename'])==='undefined') return false;      
        var filename = getfile['filename'];
        delete_uploaded_featured_file(filename);
        var _ref;
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;      
      },
    });

    function delete_uploaded_featured_file(filename) {   
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('ecommerce/delete_featured_image'); ?>',
          success: function(data) {
            featured_images_array.splice($.inArray(filename, featured_images_array), 1); // remove file
            featured_images_str = featured_images_array.join(",");
            $(featured_uploaded_file).val(featured_images_str);
          }
        });
        
      }  
    }


    // Uploads Product files
    var upload_product_file = $('#uploaded-product-file');    
    Dropzone.autoDiscover = false;
    $("#product-file-dropzone").dropzone({ 
      url: '<?php echo base_url('ecommerce/upload_product_file'); ?>',
      // maxFilesize:1,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".zip",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        // Shows error message
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(upload_product_file).val(data.filename);
        }
      },
      removedfile: function(file) {
        var filename = $(upload_product_file).val();
        delete_uploaded_product_file(filename);
        $("#tmb_preview").show();
      },
    });

    function delete_uploaded_product_file(filename) {   
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('ecommerce/delete_product_file'); ?>',
          success: function(data) {
            $('#uploaded-product-file').val('');
          }
        });
      }
      // Empties form values
      empty_product_form_values();     
    }

    // Empties form values
    function empty_product_form_values() {
      $('#product-file-dropzone .dz-preview').remove();
      $('#product-file-dropzone').removeClass('dz-started dz-max-files-reached');
      // Clears added file
      Dropzone.forElement('#product-file-dropzone').removeAllFiles(true);
    }




});
</script>