<style>
	/*.dropdown-toggle::after{content:none !important;}*/
	#template_text{max-width: 60% !important;}
	#create_template_modal .modal-dialog,#update_template_modal .modal-dialog { min-width:60%; }
	.template_contents,.updated_template_contents { min-height: 200px !important;max-height:400px !important;}
	.note-toolbar{background:#eee !important;}
	.bbw{border-bottom-width: thin !important;border-bottom:solid .5px #f9f9f9 !important;padding-bottom:20px;}
	.note-editable{padding-top:20px !important;min-height: 200px !important;max-height:600px !important;border:none !important;padding-right:0 !important;}
	.note-editable::-webkit-scrollbar {
    	width: 0px !important; /* For Chrome, Safari, and Opera */
	}
	.card-header.note-toolbar>.btn-group { margin-right:0; }
	.button-outline
	{
	  background: #fff;
	  border: .5px dashed #ccc;
	}
	.button-outline:hover
	{
	  border: 1px dashed var(--blue) !important;
	  cursor: pointer;
	}
	@media (max-width: 575.98px) { 
		.note-editor.note-frame .note-editing-area .note-editable{margin-top: 60px!important;padding-top:20px;} 
	}
</style>