 "use strict";
 $(document).ready(function() {

	$(document).on('click', '#search_hashtag', function(event) {
		event.preventDefault();

		var account_name = $("#account_name").val();
		var hash_tag = $("#hash_tag").val();

		if(account_name == "") {
			swal(global_lang_warning, instagram_hash_tag_select_account, 'warning');
			return false;
		}

		if(hash_tag == "") {
			swal(global_lang_warning, instagram_hash_tag_provide_hash_tag, 'warning');
			return false;
		}

		$("#hashtag_search_result").html("");

		$(this).addClass('btn-progress disabled')

		$("#preloader").html('<img width="30%" class="center-block text-center" src="'+base_url+'assets/pre-loader/loading-animations.gif" alt="Processing...">');

		$.ajax({
			context:this,
			url: base_url+'instagram_reply/hashtag_search_result',
			type: 'POST',
			data: {account_name: account_name,hash_tag: hash_tag},
			success:function(response){
				$(this).removeClass('btn-progress disabled')
				$("#preloader").html("");
				$("#hashtag_search_result").html(response);
			}
		})
		


	});
});