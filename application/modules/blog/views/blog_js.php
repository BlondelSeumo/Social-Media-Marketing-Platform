<script type="text/javascript">
$(document).ready(function(){
  $(document.body).on('submit', 'form[name="comment-action"]', function(e) {
    e.preventDefault();
    var form = $(this);
    form.find('#comment-response').html('');
    var action = form.attr('action');
    var formData = form.serialize();

    $.ajax({
      type:'POST' ,
      url:action,
      data: formData,
      dataType : 'JSON',
      success:function(response)
      {

        if (response.status == '0'){
          form.find('#comment-response').attr('class', 'text-danger');
          form.find('#comment-response').html(response.errors);
        }

        if(response.status == "1") {
          form.find('textarea[name="comment"]').val('');
          form.find('#comment-response').attr('class', 'text-success');
          form.find('#comment-response').html(response.message);
          setTimeout(function(){
            $('.modal').modal('hide');
          }, 1000);
          setTimeout(function(){
            location.reload();
          }, 2000);
        }
          
        if(response.status == "2"){
          form.find('#comment-response').attr('class', 'text-danger');
          form.find('#comment-response').html(response.message);
        }
      }
    });
  });

  $(document.body).on('click', '.comment-replay', function(){
    var id = $(this).attr('data-comment-id');
    $('#reply-modal').find('input[name="parent_id"]').val(id);
    $('#reply-modal').modal();
  });

  $(document.body).on('click', '.comment-edit', function(){
    var id = $(this).attr('data-comment-id');
    var comment = $('#comment-text-'+id).html();
    $('#comment-edit-modal').find('input[name="comment_id"]').val(id);
    $('#comment-edit-modal').find('textarea[name="comment"]').html(comment);
    $('#comment-edit-modal').modal();
  });

  $(document.body).on('click', '.comment-delete', function(){
    var ans = confirm("<?php echo $this->lang->line('Do you really want to delete this comment?');?>");
    if(!ans) return false;
    var id = $(this).attr('data-comment-id');
    $.ajax({
      url:"<?php echo base_url().'blog/comment_delete/';?>"+id,
      method:"POST",
      data:{id:id},
      dataType : 'JSON',
      success:function(response)
      {
        location.reload();
      }
    });
  });
});

$(document).ready(function(){
  // Comment loads area
  $(document.body).on('click', '.load-more-comments', function(){
    var start = $(this).attr('data-start');
    $(this).html('<i class="fa fa-refresh fa-spin"></i> <?php echo $this->lang->line("Load older comments");?>...');
    load_comments(start);
  });


  load_comments(null);

  function load_comments(start = null)
  {
    var post_id = $('#display_comments').attr('data-post-id');
    $.ajax({
      url:"<?php echo base_url().'blog/load_comments/';?>"+post_id,
      method:"POST",
      data:{start:start},
      success:function(data)
      {
        $('#display_comments').find('.load-more-comments').remove();
        $('#display_comments').append(data);
      }
    });
  }
});
</script>