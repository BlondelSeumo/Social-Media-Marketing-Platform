 "use strict";
 var imageEditor;
$(document).ready(function() {
  $(document).on('click', '.edit_media.image', function(e) {
    e.preventDefault();
    var source = $(this).attr('data-src');
    var image_type = source.split(".").pop();
    $("#image_type").val(image_type);
    imageEditor = new tui.ImageEditor('#tui-image-editor-container', {
      includeUI: {
        loadImage: {
          path: source,
          name: 'EditImage',
        },
          theme: blackTheme, // or whiteTheme
          // initMenu: 'filter',
          menuBarPosition: 'bottom',
        },

        cssMaxWidth: 700,
        cssMaxHeight: 500,
        usageStatistics: false,

      });
      $("#tuiModal").modal('show');
      window.onresize = function () {
        imageEditor.ui.resizeEditor();
      };

  });


  // Image editor   
  var supportingFileAPI = !!(window.File && window.FileList && window.FileReader);
  var rImageType = /data:(image\/.+);base64,/;
  var mask;

  $('#image_save').on('click', function() {

    // encode the file using the FileReader API
    var imageName = imageEditor.getImageName();
    var dataURL = imageEditor.toDataURL();
    var blob, type, w;

    var image_type = $("#image_type").val();
    image_type = image_type.toLowerCase();
    var filename = "image_"+user_id+"_"+makeid(20)+'.'+image_type;

    var formating = 'png';
    if(image_type=="jpg" || image_type=="jpeg") formating = "jpeg";

    $("#image_save").addClass("btn-progress");

    if (supportingFileAPI) {
      blob = base64ToBlob(imageEditor.toDataURL({format: formating}));
      var formData = new FormData();
      formData.append('croppedImage', blob, filename);
      // Make ajax call to upload image
      $.ajax({
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        url: base_url+'instagram_poster/image_editor',
        success: function(data) {
          if(data.name)
          {
            var data_modified = base_url+"upload_caster/image_video/"+user_id+"/"+data.name;
            var new_html = '<div class="col-4 col-md-4 col-lg-2 p-0 no-gutters"><img src="'+data_modified+'" width="100%" height="100" class="pr-1 pb-1 select_media image pointer"></div>';
            $("#image_block .upload_block").prepend(new_html);
            $('.select_media.image[src="'+data_modified+'"]').click();
          }
          else alert(data);

          $("#image_save").removeClass("btn-progress");
          $("#tuiModal").modal('hide');
        }
      });
      // Library: FileSaver - saveAs
      // saveAs(blob, imageName); // eslint-disable-line
    }
    else
    {
        alert('This browser needs a file-server');
        w = window.open();
        w.document.body.innerHTML = '<img src=' + dataURL + '>';
        $("#image_save").removeClass("btn-progress");
    }
  });

  function base64ToBlob(data) {
      var mimeString = '';
      var raw, uInt8Array, i, rawLength;

      raw = data.replace(rImageType, function(header, imageType) {
          mimeString = imageType;

          return '';
      });

      raw = atob(raw);
      rawLength = raw.length;
      uInt8Array = new Uint8Array(rawLength); // eslint-disable-line

      for (i = 0; i < rawLength; i += 1) {
          uInt8Array[i] = raw.charCodeAt(i);
      }

      return new Blob([uInt8Array], {type: mimeString});
  }

  function makeid(length) {
     var result           = '';
     var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
     var charactersLength = characters.length;
     for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
     }
     return result;
  }
});