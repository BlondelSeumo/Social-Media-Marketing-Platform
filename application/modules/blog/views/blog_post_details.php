<?php 
	include("application/modules/blog/views/blog_style.php"); 
?>

<div class="container">
	<div class="row">
		<div class="col-sm-8">
			<div class="post-details">
                <?php if($post[0]['thumbnail'] !=''): ?>
                    <img class="img-thumbnail post-thumbnail" src="<?php echo base_url('upload/blog/'.$post[0]['thumbnail']); ?>">
                <?php endif; ?>
                <h1 class="title text-left"><?php echo $post[0]['title']; ?></h1>
                <div class="blog-meta-wrap">
					<p class="blog-meta">
						<span><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo date('M d, Y', strtotime($post[0]["published_at"])); ?></span>
						<span><i class="fa fa-folder-open" aria-hidden="true"></i> <a href="<?php echo base_url('blog/posts_filter?type=category&slug='.$post[0]['category_slug'].'&id='.$post[0]['category_id']);?>"><?php echo $post[0]["category_name"]; ?></a></span>
						<span><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $post[0]["views"]; ?></span>
						<span><i class="fa fa-comments" aria-hidden="true"></i> <?php echo $post[0]["total_comments"]; ?> <?php echo $this->lang->line("Comments"); ?></span>
						<span class="tags"><i class="fa fa-tags"></i> 
                            <?php
                                foreach (explode(',', $post[0]["tags"]) as $tag) {
                                    echo "<a href=".base_url('blog/posts_filter?type=tags&slug='.mb_strtolower($tag)).">$tag</a> ";
                                }
                            ?>
						</span>
					</p>
				</div>

                <div class="post-content">
                    <?php echo $post[0]["body"]; ?>
                </div><!--/.post-content-->
            </div><!--/.single-post-->

            <div class="comments-wrapper">
                <h2><?php echo $post[0]["total_comments"]; ?> <?php echo $this->lang->line("Comments"); ?></h2>
                <div class="comments_area" id="display_comments" data-post-id="<?php echo $post[0]["id"]; ?>">
                	
                </div><!--/.comments_area-->
                <div class="comment-form">
                    <h1><?php echo $this->lang->line("Leave a Comment");?></h1>
                    <?php if ($this->session->userdata('logged_in') == 1): ?>
                        <form name="comment-action" action="<?php echo base_url('blog/comment_action'); ?>" method="post">
                        	<input type="hidden" name="post_id" value="<?php echo $post[0]["id"]; ?>">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default">
                                      <div class="panel-heading">
                                        <i class="fa fa-edit"></i>
                                        <?php echo $this->lang->line("Your Comment");?>
                                      </div>
                                      <textarea class="form-control" rows="7" name="comment" placeholder=' <?php echo $this->lang->line("Your Comment");?>'></textarea>
                                    </div>
                                    <strong id="comment-response" class=""></strong>
                                    <button type="submit" class="comment-submit-btn"><i class="fas fa-comment-medical"></i> <?php echo $this->lang->line("Post Comment");?></button>
                                </div><!--/.col-sm-12-->
                            </div><!--/.row-->
                        </form>
                    <?php else: ?>
                        
                        <a href="<?php echo base_url('home/login') ?>" class="btn btn-default btn-lg"><i class="fa fa-sign-in"></i> <?php echo $this->lang->line("Login to Comment");?></a>
                    <?php endif; ?>
                </div><!--/.comment-form-->
            </div><!--/.comments-wrapper-->
		</div><!--/.col-sm-8-->
		<div class="col-sm-4">
			<aside id="sidebar">
                <?php 
                    include("application/modules/blog/views/blog_sidebar.php"); 
                ?>
            </aside><!--/#sidebar-->
		</div><!--/.col-sm-4-->
	</div><!--/.row-->
</div><!--/.container-->

<?php if ($this->session->userdata('logged_in') == 1): ?>
<!-- reply modal -->
<div class="modal fade" id="reply-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
  	<form name="comment-action" action="<?php echo base_url('blog/comment_action'); ?>" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line("Leave a Reply");?></h4>
	      </div>
	      <div class="modal-body">
	        <input type="hidden" name="post_id" value="<?php echo $post[0]["id"]; ?>">
	        <input type="hidden" name="parent_id" value="">
            <div class="row">
                <div class="col-sm-12">
                    <textarea class="form-control" rows="7" name="comment" placeholder="<?php echo $this->lang->line('Your Comment');?>"></textarea>
                    <strong id="comment-response" class=""></strong>
                </div><!--/.col-sm-12-->
            </div><!--/.row-->
	      </div><!--/.modal-body-->
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("Close");?></button>
	        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line("Post Reply");?></button>
	      </div>
	    </div><!--/.modal-content-->
    </form>
  </div>
</div>

<!-- comment edit modal -->
<div class="modal fade" id="comment-edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
  	<form name="comment-action" action="<?php echo base_url('blog/comment_action?type=edit'); ?>" method="post">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line("Edit");?></h4>
	      </div>
	      <div class="modal-body">
	        <input type="hidden" name="comment_id" value="">
            <div class="row">
                <div class="col-sm-12">
                    <textarea class="form-control" rows="7" name="comment" placeholder="<?php echo $this->lang->line('Your Comment');?>"></textarea>
                    <strong id="comment-response" class=""></strong>
                </div><!--/.col-sm-12-->
            </div><!--/.row-->
	      </div><!--/.modal-body-->
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("Close");?></button>
	        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line("Update");?></button>
	      </div>
	    </div><!--/.modal-content-->
    </form>
  </div>
</div>
<?php endif; ?>