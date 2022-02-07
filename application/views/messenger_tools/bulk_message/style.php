<style type="text/css" media="screen">
	.badge-status{font-weight: 700;}
	.chat_box
	{
		width: 260px;
		background: #fff;
		margin: 0 auto;
	}
	.chat_header,.chat_body,.chat_footer
	{
		width: 100%;
	}
	.chat_header
	{
		min-height: 30px;
		background: #3E7cF7;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
		color: #fff;
		padding: 5px 10px;
	}
	.chat_body
	{
		height: 300px;
		overflow-y: auto;
		border: 1px solid #ccc;
		padding: 10px 5px 10px 10px;
	}
	.chat_footer
	{
		border: 1px solid #ccc;
		border-top-width: 0;
		padding: 0 4px 0 7px;
	}
	#page_name
	{
		font-weight: 600;
	}
	#page_name:hover
	{
		text-decoration: underline;
		cursor: pointer;
	}
	#page_thumb
	{
		height: 28px;
		width: 28px;
		cursor: pointer;
	}
	#preview_message
	{
		background: #f1f0f0;
		padding: 8px 5px;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		border-radius: 10px;
		margin-left: 5px;
		max-width: 185px;
		font-size: 12px;
		font-family: Arial, sans-serif;
		color: #4b4f56;
		white-space:pre-wrap;
		word-wrap:break-word;
		line-height:15.36px;
		/* margin-right: 15px; */
	}
	#video_thumb
	{
		margin-top: 3px;
		margin-left: 33px;
		width: 185px;
		border: 1px solid #ccc;
		-webkit-border-radius:  0 12px 12px 12px;
		-moz-border-radius:  0 12px 12px 12px;
		border-radius:  0 12px 12px 12px;
	}
	#video_thumb iframe
	{
		-webkit-border-radius:  0 12px 0 0;
		-moz-border-radius:  0 12px 0 0;
		border-radius:  0 12px 0 0;
	}
	#video_embed
	{
		margin: 0;
		padding: 0;
	}
	#video_thumb iframe:active,#video_thumb iframe:active #video_thumb
	{
		-webkit-border-radius:  0;
		-moz-border-radius:  0;
		border-radius:  0;
	}
	#video_info 
	{
		padding: 3px 4px;		
	    font-family: Arial, sans-serif;
	}
	#video_info_title
	{
		font-size: 12px;
	    line-height: 16px;
	    max-height: 32px;
	    color: #1d2129;
	    font-weight: bold;
	    overflow: hidden;
	}
	#video_info_description
	{
		color: #1d2129;		
	    overflow: hidden;
	    line-height:16px;
	    max-height: 16px;
	    overflow-x:hidden;
	    overflow-y:hidden;
	    font-size: 11.5px;
	}
	#video_info_youtube
	{
		color: #90949c;
		font-size: 10px;
	}

	#link_thumb
	{
		margin-top: 3px;
		margin-left: 33px;
		width: 185px;
		height: 75px;
		border: 1px solid #ccc;
		-webkit-border-radius:  0 12px 12px 12px;
		-moz-border-radius:  0 12px 12px 12px;
		border-radius:  0 12px 12px 12px;
	}
	#link_thumb img
	{
		-webkit-border-radius:  0 0 0 12px;
		-moz-border-radius:  0 0 0 12px;
		border-radius:  0 0 0 12px;
		border: none;
		height: 73px;
		width: 100%;
	}
	#link_embed
	{
		margin: 0;
		padding: 0;
	}
	
	#link_info 
	{
		padding: 12px 4px 4px 4px;		
	    font-family: Arial, sans-serif;
	}
	#link_info_title
	{
		font-size: 12px;
	    line-height: 16px;
	    max-height: 16px;
	    color: #1d2129;
	    font-weight: bold;
	    overflow: hidden;
	}
	#link_info_description
	{
		color: #1d2129;		
	    overflow: hidden;
	    line-height:16px;
	    max-height: 16px;
	    overflow-x:hidden;
	    overflow-y:hidden;
	    font-size: 11.5px;
	}
	#link_info_website
	{
		color: #90949c;
		font-size: 10px;
		overflow: hidden;
		line-height: 13px;
		max-height: 13px;
	}
	
	#emotion_container
	{
		border:1px solid #bbb;
		border-top: none;
		padding: 10px 0;
		background: #fff;
	}
	.popover
	{
	    min-width: 300px !important;
	}

</style>


<style type="text/css" media="screen">
	.box-header{border-bottom:1px solid #eee !important;margin-bottom:15px;}
	/* .box-primary{border:1px solid #ccc !important;} */
	.box-body{padding:10px 10px !important;}
	.preview{padding:10px 0 !important;}
	.box-footer{border-top:1px solid #ccc !important;padding:10px 0;}
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
	.box-header{color:#3C8DBC;}
	.box-body
	{
		padding: 20px !important;
		background: #fff;
	}
	#test_msg_box_body
	{
		background: #fff !important;
	}
	.box-footer 
	{		
		background: #fff;
	}

	.ms-choice span
	{
		padding-top: 2px !important;
	}
	.hidden
	{
		display: none;
	}
	.box-primary
	{
		-webkit-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
	}
</style>