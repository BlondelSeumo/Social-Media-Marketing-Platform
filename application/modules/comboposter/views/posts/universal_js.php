<script>
	$(document).ready(function() {

		const base_url = '<?php echo base_url(); ?>';
		const post_type = '<?php echo $post_type; ?>';

		/* scroll */
		$(".makeScroll_1").mCustomScrollbar({
		  autoHideScrollbar:true,
		  theme:"dark-thick"
		});

		/* schedule type actions */
		let schedule_type = $("input[name=schedule_type]:checked").val();
		if (schedule_type == 'now') {
			$("#schedule_time").val("");
			$("#time_zone").val("");
		} else {
			$(".schedule_block_item").slideDown( 400 ).css('display', 'unset');
		}


		const today = new Date();
		$('.datepicker_x').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today,
			// maxDate: new Date(today.getFullYear(), today.getMonth() + 1, today.getDate())
		})
		
		$(document).on('click', '#schedule_type', function(event) {
			
			schedule_type = $("input[name=schedule_type]:checked").val();

			if (schedule_type == 'now') {

				$("#schedule_time").val("");
				$("#time_zone").val("");

				setTimeout(function () {$(".schedule_block_item").css('display', 'none');}, 401);
				$(".schedule_block_item").slideUp( 400 );
			} else {
				$(".schedule_block_item").slideDown( 400 ).css('display', 'unset');
			}
		});


		/* link meta info grab */
		$(document).on('blur', '#link', function(event) {
			
			let link = $("#link").val();

			$.ajax({
				url: base_url + 'comboposter/link_meta_info_grabber',
				type: 'POST',
				dataType: 'json',
				data: {link: link},
				success: function (response) {
					console.log(response);
					$("#link_caption").val(response.title);
					$("#link_description").val(response.description);
				}
			});
		});

		if (post_type == 'link') {
			$("#link").blur();
		}


		/**
		 * account page js
		 */
		$(document).on('click', '.facebook_accounts_list', function(event) {
			event.preventDefault();

			$(".search_page_list").val('').keyup();

			$(".facebook_accounts_list").removeClass('force_active');
			$(this).addClass('force_active');
			
			let facebook_id = $(this).attr('facebook_id');
			facebook_id = "#account_tab-" + facebook_id;

			$(".facebook_account_tab").removeClass('active');
			$(facebook_id).addClass('active'); 
		});

		$(document).on('click', '.select_all_facebook_page', function(event) {
			
			let page_id = $(this).attr('facebook_id');
			page_id = ".single_facebook_page-" + page_id;

			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(page_id).prop('checked', true);
			} else {
				$(page_id).prop('checked', false);
			}
		});

		$(document).on('click', '.twitter_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".twitter_single_user").prop('checked', true);
			} else {
				$(".twitter_single_user").prop('checked', false);
			}
		});

		$(document).on('click', '.tumblr_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".tumblr_single_user").prop('checked', true);
			} else {
				$(".tumblr_single_user").prop('checked', false);
			}
		});

		$(document).on('click', '.linkedin_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".linkedin_single_user").prop('checked', true);
			} else {
				$(".linkedin_single_user").prop('checked', false);
			}
		});	

		$(document).on('click', '.medium_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".medium_single_user").prop('checked', true);
			} else {
				$(".medium_single_user").prop('checked', false);
			}
		});	
		
		$(document).on('click', '.youtube_all_channel_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".youtube_single_channel").prop('checked', true);
			} else {
				$(".youtube_single_channel").prop('checked', false);
			}
		});	

		$(document).on('click', '.wordpress_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".wordpress_single_user").prop('checked', true);
			} else {
				$(".wordpress_single_user").prop('checked', false);
			}
		});		

		$(document).on('click', '.wordpress_self_hosted_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".wordpress_self_hosted_single_user").prop('checked', true);
			} else {
				$(".wordpress_self_hosted_single_user").prop('checked', false);
			}
		});

		$(document).on('click', '.reddit_all_account_select', function(event) {
			
			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(".reddit_single_user").prop('checked', true);
			} else {
				$(".reddit_single_user").prop('checked', false);
			}
		});

		$(document).on('click', '.pinterest_accounts_list', function(event) {
			event.preventDefault();

			$(".search_page_list").val('').keyup();

			$(".pinterest_accounts_list").removeClass('force_active');
			$(this).addClass('force_active');
			
			let pinterest_id = $(this).attr('pinterest_id');
			pinterest_id = "#p_account_tab-" + pinterest_id;

			$(".pinterest_account_tab").removeClass('active');
			$(pinterest_id).addClass('active'); 
		});

		$(document).on('click', '.select_all_pinterest_board', function(event) {
			
			let board_id = $(this).attr('pinterest_id');
			board_id = ".single_pinterest_board-" + board_id;

			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(board_id).prop('checked', true);
			} else {
				$(board_id).prop('checked', false);
			}
		});

		$(document).on('click', '.blogger_accounts_list', function(event) {
			event.preventDefault();

			$(".search_page_list").val('').keyup();

			$(".blogger_accounts_list").removeClass('force_active');
			$(this).addClass('force_active');
			
			let blogger_id = $(this).attr('blogger_id');
			blogger_id = "#b_account_tab-" + blogger_id;

			$(".blogger_account_tab").removeClass('active');
			$(blogger_id).addClass('active'); 
		});

		$(document).on('click', '.select_all_blogger_blog', function(event) {
			
			let blog_id = $(this).attr('blogger_id');
			blog_id = ".single_blogger_blog-" + blog_id;

			let is_all_checked = $(this).prop('checked');

			if (is_all_checked) {
				$(blog_id).prop('checked', true);
			} else {
				$(blog_id).prop('checked', false);
			}
		});


		/* click on load */
		$(".facebook_accounts_list").first().click();
		$(".pinterest_accounts_list").first().click();
		$(".blogger_accounts_list").first().click();


		/* rich content section */
		$('#rich_content').summernote({
			height: 300,	
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['fontname', ['fontname']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link']],
				['view', ['codeview']],
			],
		});

		$('#rich_content_html_section').summernote({ height: 300 });


		/* uploader section */
		if (post_type == 'image') {

			$("#upload_image").uploadFile({
				url: "<?php echo site_url();?>comboposter/upload_file_handler/image",
				fileName:"file",
				maxFileSize:<?php echo $this->config->item('comboposter_image_upload_limit'); ?>*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				acceptFiles:".png,.jpg,.jpeg,.bmp,.tiff",
				maxFileCount:1,
				deleteCallback: function (data, pd) {
					var delete_url = '<?php echo site_url();?>comboposter/delete_file_handler';
					for (var i = 0; i < data.length; i++) {
						$.post(delete_url, {op: "delete",name: data}, function (resp,textStatus, jqXHR) {
							$("#image_file").val('');
						});
					}
				},
				onSuccess:function(files,data,xhr,pd){
				   var data_modified = base_url+"upload/comboposter/<?php echo $this->user_id; ?>/"+data;
				   $("#image_file").val(data_modified);
				}
			});
		} else if (post_type == 'video') {

			$("#upload_video_thumbnail").uploadFile({
				url: "<?php echo site_url();?>comboposter/upload_file_handler/image",
				fileName:"file",
				maxFileSize:<?php echo $this->config->item('comboposter_image_upload_limit'); ?>*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				acceptFiles:".png,.jpg,.jpeg,.bmp,.tiff",
				maxFileCount:1,
				onSuccess:function(files,data,xhr,pd){
				   var data_modified = base_url+"upload/comboposter/<?php echo $this->user_id; ?>/"+data;
				   $("#video_url_thumbnail").val(data_modified);
				},
				deleteCallback: function (data, pd) {
					var delete_url = '<?php echo site_url();?>comboposter/delete_file_handler';
					for (var i = 0; i < data.length; i++) {
						$.post(delete_url, {op: "delete",name: data}, function (resp,textStatus, jqXHR) {
							$("#video_url_thumbnail").val('');
						});
					}
				}
			});

			$("#upload_video").uploadFile({
				url: "<?php echo site_url();?>comboposter/upload_file_handler/video",
				fileName:"file",
				maxFileSize:<?php echo $this->config->item('comboposter_video_upload_limit'); ?>*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				acceptFiles:".mp4,.mov,.avi,.wmv,.mpg,.flv",
				maxFileCount:1,
				onSuccess:function(files,data,xhr,pd){
				   var data_modified = base_url+"upload/comboposter/<?php echo $this->user_id; ?>/"+data;
				   $("#video_url").val(data_modified);
				},
				deleteCallback: function (data, pd) {
					var delete_url = '<?php echo site_url();?>comboposter/delete_file_handler';
					for (var i = 0; i < data.length; i++) {
						$.post(delete_url, {op: "delete",name: data}, function (resp,textStatus, jqXHR) {
							$("#video_url").val('');
						});
					}
				}
			});
		} else if (post_type == 'link') {

			$("#upload_link_thumbnail").uploadFile({
				url: "<?php echo site_url();?>comboposter/upload_file_handler/image",
				fileName:"file",
				maxFileSize:<?php echo $this->config->item('comboposter_image_upload_limit'); ?>*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				acceptFiles:".png,.jpg,.jpeg,.bmp,.tiff",
				maxFileCount:1,
				onSuccess:function(files,data,xhr,pd){
				   var data_modified = base_url+"upload/comboposter/<?php echo $this->user_id; ?>/"+data;
				   $("#thumbnail_url").val(data_modified);
				},
				deleteCallback: function (data, pd) {
					var delete_url = '<?php echo site_url();?>comboposter/delete_file_handler';
					for (var i = 0; i < data.length; i++) {
						$.post(delete_url, {op: "delete",name: data}, function (resp,textStatus, jqXHR) {
							$("#thumbnail_url").val('');
						});
					}
				}
			});
		}

		/* form submit section */
		$(document).on('click', '#submit_post', function(event) {
			event.preventDefault();
			
			const action_type = $(this).attr('action_type');
			const post_type = $(this).attr('post_type');

			let form_data = new FormData($("#comboposter_form")[0]);
			form_data.append('action_type', action_type);

			const form_action_url = base_url + 'comboposter/' + post_type + "_post/" + action_type;

			$(this).addClass('btn-progress');
			let that = $(this);

			$.ajax({
				url: form_action_url,
				type: 'POST',
				dataType: 'json',
				data: form_data,
				contentType: false,
				cache: false,
				processData: false,
				success: function (response) {

					// console.log(response);
					$(that).removeClass('btn-progress');
					
					if (response.status == 'error') {
						swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					} else if (response.status == 'success') {

						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success');
						setTimeout(function () {window.location.href = '<?php echo base_url('comboposter/'. $post_type. '_post/campaigns') ?>';}, 2000);
					}
				}
			});
							
		});





		/* account search on ul */
		// function search_in_ul(obj, search_text) {
		// 	// console.log(obj);
		// }

	});
</script>