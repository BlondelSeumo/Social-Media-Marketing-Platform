"use strict";
$("#auto_share_this_post_by_pages").select2();
$("#auto_like_this_post_by_pages").select2({
    maximumSelectionLength: 2
});

$(".auto_share_post_block_item,.auto_like_post_block_item").hide();
$(document).on("click","#modal_close",function(){
  location.reload();
});

$(document).on("change","input[name=auto_share_post]",function(){    
  if($("input[name=auto_share_post]:checked").val()=="1")
  $(".auto_share_post_block_item").show();
  else $(".auto_share_post_block_item").hide();
}); 


$(document).on("change","input[name=auto_like_post]",function(){    
  if($("input[name=auto_like_post]:checked").val()=="1")
  $(".auto_like_post_block_item").show();
  else $(".auto_like_post_block_item").hide();
}); 
