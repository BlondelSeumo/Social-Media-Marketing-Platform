"use strict";
$(document).ready(function() {
    $(document.body).on('click', '.acceptcookies', function(event) {
        event.preventDefault();
        $('.cookiealert').hide();
        $.ajax({
            url: base_url+'home/allow_cookie',
            type: 'POST',
        })
    });
});