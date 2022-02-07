<style type="text/css">
	.blog-entry{
		height: auto;
		width: 100%;
		margin-bottom: 30px;
		overflow: hidden;
	}
	.blog-thumbnail{
		display: inline-block;
	    height: 180px;
	    width: 250px;
	    margin-top: 10px;
	    border: 1px solid #f1f1f1;
	    box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
	    -webkit-box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
	    -moz-box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
	    -ms-box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
	    -o-box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
	    float: left;
	    margin-right: 15px;
	    background-size: cover;
    	background-repeat: no-repeat;
    	background-position: center center;
	}
	.blog-content-wrap{
		width: auto;
		height: auto;
		overflow: hidden;
		padding-top: 5px;
	}
	.blog-title, .blog-title a{
		display: block;
		font-weight: 300;
    	font-size: 24px;
    	line-height: 1.3;
	    color: #000000cc;
	}
	.blog-meta-wrap{
		width: 100%;
		font-weight: 500;
    	display: block;
	}
	.blog-meta-wrap .blog-meta span {
	    font-size: 14px;
	    margin: 0 7px 0 0;
	    color: #bfbfbf;
	}
	.blog-meta-wrap .blog-meta span a{
	    color: #000000;
	    margin: 0;
	}
	.readmore-btn{
		text-decoration: none;
	    color: #1eafed;
	    outline: none !important;
	    font-weight: 600;
	}

	#sidebar{
		width: 100%;
		height: 100%;
		padding: 0 15px;
		overflow: hidden;
	}

	.widget-wrap{
		height: 100%;
		width: 100%;
		overflow: hidden;
		margin-bottom: 30px;
		padding-top: 10px;
	}

	.widget-search-form{
		position: relative;
    	margin-bottom: 15px;
	}

	.widget-search-form input {
	    padding-right: 50px;
	    font-size: 14px;
	    height: 52px !important;
	    background: #fff !important;
	    color: #000000 !important;
	    font-size: 18px;
	    border-radius: 0px;
	    -webkit-box-shadow: none !important;
	    box-shadow: none !important;
	    border: 1px solid #111;
	}

	.widget-search-form button {
	    position: absolute;
	    background: transparent;
	    border: none;
	    height: 50px;
	    width: 50px;
	    font-size: 16px;
    	color: #000;
	    top: 50%;
	    right: 0;
	    -webkit-transform: translateY(-50%);
	    -ms-transform: translateY(-50%);
	    transform: translateY(-50%);
	}

	.widget-wrap .widget-heading h1{
	    font-size: 26px;
    	font-style: italic;
    	margin-bottom: 20px;
    	font-weight: 600;
	}

	.widget-content .categories{
		margin: 0;
		padding: 0;
		display: block;
	}
	.widget-content .categories li{
	    position: relative;
	    margin-bottom: 10px;
	    padding-bottom: 10px;
	    border-bottom: 1px solid #dee2e6;
	    list-style: none;
	}
	.widget-content .categories li a{
	    display: block;
	    color: #000000;
	}
	.widget-content .categories li:last-child{
	    margin-bottom: 0;
	    border-bottom: none;
	    padding-bottom: 0;
	}
	.widget-content .categories li a span{
	    position: absolute;
	    right: 0;
	    top: 0;
	    color: #ccc;
	}

	.widget-content .tags{
		margin: 0;
		padding: 0;
		display: block;
	}

	.widget-content .tags li{
		display: inline-block;
		float: left;
		margin: 0;
		padding: 0;
	}

	.widget-content .tags li a {
	    text-transform: uppercase;
	    display: inline-block;
	    padding: 4px 10px;
	    margin-bottom: 7px;
	    margin-right: 4px;
	    border-radius: 4px;
	    color: #000000;
	    border: 1px solid #ccc;
	    font-size: 11px;
	}
	.widget-content .tags li a:hover {
	    border: 1px solid #000;
	}

	/*Single post page*/
	.post-details{
		height: 100%;
		width: 100%;
		padding-bottom: 50px;
		margin-bottom: 20px;
		overflow: hidden;
	}
	.post-details .post-thumbnail{
		display: flex;
	    margin: 0 auto;
	    margin-bottom: 30px;
	    max-height: 400px;
	}
	.post-details .title{
		margin-bottom: 20px;
	    font-weight: 300;
	    font-size: 27px;
	    text-transform: capitalize;
	}
	.post-details .blog-meta .tags a{
		display: inline-block;
	    text-transform: capitalize;
	    padding: 0px 5px;
	    border-radius: 4px;
	    border: 1px solid #ccc;
	    font-size: 11px;
	}
	.post-details .blog-meta .tags a:hover{
		border-color: #111;
	}
	.post-details .post-content p{
		font-size: 16px;
	}
	.post-details .post-content img{
		max-width: 100%;
	    height: auto;
	    border-radius: 2px;
	    display: flex;
	    margin: 15px auto;
	    overflow: hidden;
	}

	/*Comments*/
	.comments-wrapper{
	    height: 100%;
	    width: 100%;
	    margin-bottom: 100px;
	}
	.comments-wrapper h2{
	    color: #464646;
	    font-style: normal;
	    font-weight: 600;
	    text-transform: uppercase;
	    font-size: 24px;
	    overflow: hidden;
	}
	.comments_area{
	    height: 100%;
	    width: 100%;
	    padding-bottom: 30px;
	    margin-top: 40px;
	    margin-bottom: 65px;
	    border-bottom: 1px solid #dddddd;
	}
	.comment{
	    height: 100%;
	    width: 100%;
	    margin-top: 10px;
	    margin-bottom: 0;
	}
	.comment-box{
	    height: 100%;
	    width: 100%;
	    padding: 15px;
	    border-radius: 3px;
	    overflow: hidden;
	}
	.comment-author{
	    width: 55px;
	    height: auto;
	    display: inline-block;
	    float: left;
	    margin: 0 10px 15px 0;
	}
	.comment-author img{
	    height: 50px;
	    width: 50px;
	    border-radius: 50px;
	    -webkit-border-radius: 50px;
	    -moz-border-radius: 50px;
	    -o-border-radius: 50px;
	    -ms-border-radius: 50px;
	    overflow: hidden;
	}
	.comment > .comment{
	    margin-left: 20px;
	    width: auto;
	}
	.comment-heading{
	    padding-bottom: 7px;
	    overflow: hidden;
	}
	.comment-heading h4{
	    display: inline-block;
	    float: left;
	    font-size: 18px;
	    padding-right: 8px;
	    margin: 0;
	}
	.comment-content{
	    color: #6f6f6f;
	    display: flow-root;
	    overflow: hidden;
	}
	.comment-text{
	    color: #6f6f6f;
	    display: inline;
	    overflow: hidden;
	}
	.comment-action{
	    display: inline-block;
	}
	.load-more-comments{
	    /*display: block;*/
	    /*width: 100%;*/
	    border: none;
	    text-align: center;
	    margin: 15px 0;
	    padding: 11px 14px;
	    font-size: 14px;
	    color: #000;
	    font-weight: bold;
	    /*background-color: #1eafed;*/
	    transition: 0.20s ease-in-out;
	    -moz-transition: 0.20s ease-in-out;
	    -o-transition: 0.20s ease-in-out;
	    -webkit-transition: 0.20s ease-in-out;
	    -ms-transition: 0.20s ease-in-out;
	    border: 1px solid black;
	}
	.load-more-comments.disabled{
	    cursor: not-allowed;
	    color: #ffffff;
	    background-color: #62c3ec;
	}
	.load-more-comments:not(.disabled):focus,
	.load-more-comments:not(.disabled):active,
	.load-more-comments:not(.disabled):hover{
	    color: #ffffff;
	    background-color: transparent;
	    border: 1px solid #1eafed;
	    color: #111;
	}
	.comment-form h1{
	    font-size: 30px;
	    color: #464646;
	    font-style: normal;
	    text-transform: capitalize;
	    font-weight: 600;
	    padding-bottom: 20px;
	}
	.comment-form .form-group .input-group-addon label{
	    font-weight: 300;
	    line-height: 0;
	}
	.comment-form .panel{
	    border-radius: 3px;
	}
	.comment-form .panel textarea{
	    border: none;
	    resize: vertical !important;
	}
	.comment-submit-btn{
	    color: #ffffff;
	    background: #1eafed;
	    border-radius: 2px;
	    color: #fff;
	    display: inline-block;
	    font-size: 14px;
	    height: 42px;
	    letter-spacing: .5px;
	    line-height: 42px;
	    padding: 0 25px;
	    border:none;
	    text-transform: uppercase;
	    white-space: nowrap;
	    float: right;
	    transition: all 0.40s ease-in-out;
	    -webkit-transition: all 0.40s ease-in-out;
	    -moz-transition: all 0.40s ease-in-out;
	    -ms-transition: all 0.40s ease-in-out;
	    -o-transition: all 0.40s ease-in-out;
	}
	.comment-submit-btn:hover, .comment-submit-btn:focus, .comment-submit-btn:active {
	    background-color: transparent;
	    border: 1px solid #1eafed;
	    color: #111;
	    outline: 0px;
	}
</style>