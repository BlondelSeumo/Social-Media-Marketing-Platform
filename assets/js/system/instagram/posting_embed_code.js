"use strict";
Prism.highlightAll();
$(".toolbar-item").find("a").addClass("copy");

$(document).on("click", ".copy", function(event) {
    event.preventDefault();
    $(this).html(global_lang_url_copied_clipbloard);
    var that = $(this);
    
    var text = $(this).prev("code").text();
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
    setTimeout(function(){
      $(that).html(global_lang_copy);
    }, 2000); 

});