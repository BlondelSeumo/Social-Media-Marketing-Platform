<script>
	$(document).ready(function($) {
	
		/* button style extra toolbar in summernote */
		$('.visual_editor').summernote({
			height: 350,
			minHeight: 350,
			toolbar: [
			    ['style', ['style']],
			    ['font', ['bold', 'underline','italic','clear']],
			    ['fontname', ['fontname']],
			    ['color', ['color']],
			    ['para', ['ul', 'ol', 'paragraph']],
			    ['table', ['table']],
			    ['insert', ['link']],
			    ['view', ['codeview']]
			]
		});
	});
</script>