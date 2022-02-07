<?php $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : ''; ?>
<script type="text/javascript">
	var base_url = "<?php echo base_url(); ?>";
	var subscriber_id = '<?php echo $subscriberId;?>';
	var pickup = '<?php echo $pickup;?>';
	function load_data(start,reset,popmessage,comment_id) 
	{
	  var limit = $("#load_more").attr("data-limit");
	  $("#waiting").show();
	  if(reset) counter = 0;
	  $.ajax({
	    url: base_url+'ecommerce_review_comment/comment_list_data',
	    type: 'POST',
	    dataType : 'JSON',
	    data: {start:start,limit:limit,product_id:current_product_id,store_id:current_store_id,store_favicon:store_favicon,store_name:store_name,comment_id:comment_id,subscriber_id:subscriber_id,pickup:pickup},
	      success:function(response)
	      {
	        $("#waiting").hide();
	        $("#nodata").hide();     

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
	            iziToast.info({title: '',message: "<?php echo $this->lang->line('No more comment found.') ?>",position: 'bottomRight'});
	            $("#nodata").hide();
	          }
	          else $("#nodata").show();            
	        }
	      }
	  });
	}
	$(document).ready(function() {
		$(document).on('click','.leave_comment',function(e){
		  var new_comment = $(this).prevAll('.comment_reply').val();
		  var parent_product_comment_id = $(this).attr('parent-id');		  
		  if(new_comment==""){
		    swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please write a comment.'); ?>", 'error');
		    return false;
		  }
		  $(this).addClass('btn-progress');
		  $.ajax({
		    context: this,
		    type:'POST',
		    dataType:'JSON',
		    url:"<?php echo site_url();?>ecommerce_review_comment/new_comment",
		    data:{product_id:current_product_id,store_id:current_store_id,new_comment:new_comment,subscriber_id:subscriber_id,parent_product_comment_id:parent_product_comment_id,product_name:product_name,pickup:pickup},
		    success:function(response){
		      $(this).removeClass('btn-progress');
		      if(response.status=='0')
		      {
		        var span = document.createElement("span");
		        span.innerHTML = response.message;
		        if(response.login_popup)
		          swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {             
		           $("#login_form").trigger('click');
		          });
		        else swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'});
		      }
		      else
		      {
		        var data_start = $("#load_more").attr('data-start');
		        data_start  = parseInt(data_start);
		        data_start = data_start+1;
		        counter = counter+1;
		        $("#load_more").attr('data-start',data_start);
		        $("#nodata").hide();
		        if(parent_product_comment_id=='') $("#load_data").prepend(response.message);
		        else $(this).parent().parent().append(response.message);
		        if(parent_product_comment_id=='') $('html, body').animate({scrollTop: $("#comment_section").offset().top}, 1000);
		        $(this).prevAll('.comment_reply').val('');
		      }
		    }
		  });
		});

		$(document).on('click','.hide-comment',function(e){
		  e.preventDefault();

		  swal({
		    title: '<?php echo $this->lang->line("Hide comment?"); ?>',
		    text: '<?php echo $this->lang->line("Do you really really want to hide this comment?"); ?>',
		    icon: 'warning',
		    buttons: true,
		    dangerMode: true,
		    })
		    .then((willDelete) => {
		    if (willDelete)
		    {
		      var id = $(this).attr('data-id');
		      var subscriber_id = '<?php echo $subscriberId;?>';
		      $(this).addClass('btn-progress');
		      $.ajax({
		        context: this,
		        type:'POST',
		        dataType:'JSON',
		        url:"<?php echo site_url();?>ecommerce_review_comment/hide_comment",
		        data:{product_id:current_product_id,store_id:current_store_id,subscriber_id:subscriber_id,id:id},
		        success:function(response){
		          $(this).removeClass('btn-progress');

		          if(response.status=='0') swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'});
		          else $(this).parent().parent().hide();
		        }
		      });
		    }
		  });		  
		});

		$(document).on('click','#rate_now',function(e){
		  e.preventDefault();
		  var reason = $("#ReviewModal #reason").val();
		  var rating = $("#ReviewModal input[name=rating]:checked").val();
		  var review = $("#ReviewModal #review").val();
		  var cart_id = $("#ReviewModal #cart_id").val();
		  var insert_id = $("#ReviewModal #insert_id").val();
		  if(reason=='' || rating=='' || cart_id=='')
		  {
		  	swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please fill in the required fields"); ?> *','error');
		  	return false;
		  }
		  $(this).addClass('btn-progress');
		  $.ajax({
		    context: this,
		    type:'POST',
		    dataType:'JSON',
		    url:"<?php echo site_url();?>ecommerce_review_comment/new_review",
		    data:{product_id:current_product_id,store_id:current_store_id,reason:reason,subscriber_id:subscriber_id,rating:rating,review:review,cart_id:cart_id,product_name:product_name,insert_id:insert_id},
		    success:function(response){
		      $(this).removeClass('btn-progress');
		      if(response.status=='0')
		      {
   				var span = document.createElement("span");
   		        span.innerHTML = response.message;
   		        if(response.login_popup)
   		          swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {             
   		           $("#login_form").trigger('click');
   		          });
   		        else swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'});
		      }
		      else
		      {
		        swal('<?php echo $this->lang->line("Review Submitted"); ?>', response.message, 'success').then((value) => {location.reload();});
		      }
		    }
		  });
		});

		$(document).on('click','.hide-review',function(e){
		  e.preventDefault();

		  swal({
		    title: '<?php echo $this->lang->line("Hide review?"); ?>',
		    text: '<?php echo $this->lang->line("Do you really really want to hide this review?"); ?>',
		    icon: 'warning',
		    buttons: true,
		    dangerMode: true,
		    })
		    .then((willDelete) => {
		    if (willDelete)
		    {
		      var id = $(this).attr('data-id');
		      var subscriber_id = '<?php echo $subscriberId;?>';
		      $(this).addClass('btn-progress');
		      $.ajax({
		        context: this,
		        type:'POST',
		        dataType:'JSON',
		        url:"<?php echo site_url();?>ecommerce_review_comment/hide_review",
		        data:{product_id:current_product_id,store_id:current_store_id,subscriber_id:subscriber_id,id:id},
		        success:function(response){
		          $(this).removeClass('btn-progress');
		          if(response.status=='0') swal('<?php echo $this->lang->line("Error"); ?>',response.message,'error');
		          else swal('<?php echo $this->lang->line("Hidden Successfully"); ?>', response.message, 'success').then((value) => {location.reload();});
		        }
		      });
		    }
		  });		  
		});

		$(document).on('click','.leave_review_comment',function(e){
		  var review_reply = $(this).prevAll('.review_reply').val();
		  var parent_product_review_id = $(this).attr('parent-id');		  
		  if(review_reply==""){
		    swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please write a reply.'); ?>", 'error');
		    return false;
		  }
		  $(this).addClass('btn-progress');
		  $.ajax({
		    context: this,
		    type:'POST',
		    dataType:'JSON',
		    url:"<?php echo site_url();?>ecommerce_review_comment/new_review_comment",
		    data:{product_id:current_product_id,store_id:current_store_id,review_reply:review_reply,subscriber_id:subscriber_id,parent_product_review_id:parent_product_review_id},
		    success:function(response){
		      $(this).removeClass('btn-progress');
		      if(response.status=='0') swal('<?php echo $this->lang->line("Error"); ?>',response.message,'error');
		      else swal('<?php echo $this->lang->line("Hidden Successfully"); ?>', response.message, 'success').then((value) => {location.reload();});
		    }
		  });
		});
	});
</script>