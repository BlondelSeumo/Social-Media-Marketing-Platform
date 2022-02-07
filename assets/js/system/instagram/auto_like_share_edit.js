"use strict";
$("#edit_auto_share_this_post_by_pages").select2();
$("#edit_auto_like_this_post_by_pages").select2({
    maximumSelectionLength: 2
});

var edit_auto_share_post = $("input[name=edit_auto_share_post]:checked").val();
var edit_auto_like_post = $("input[name=edit_auto_like_post]:checked").val();
if(typeof(edit_auto_share_post) == "undefined")
{
  $(".edit_auto_share_post_block_item").hide();
}

if(typeof(edit_auto_like_post) == "undefined")
{
  $(".edit_auto_like_post_block_item").hide();
}

$(document).on("change","input[name=edit_auto_share_post]",function(){    
  if($("input[name=edit_auto_share_post]:checked").val()=="1")
  $(".edit_auto_share_post_block_item").show();
  else $(".edit_auto_share_post_block_item").hide();
}); 
$(document).on("change","input[name=edit_auto_like_post]",function(){    
  if($("input[name=edit_auto_like_post]:checked").val()=="1")
  $(".edit_auto_like_post_block_item").show();
  else $(".edit_auto_like_post_block_item").hide();
}); 