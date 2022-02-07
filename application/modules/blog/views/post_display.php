<?php if(count($posts) > 0): foreach ($posts as $post): ?>
<?php
	$post_details = base_url('blog/post_details/'.$post['slug'].'/'.$post['id']);
?>
<article class="blog-entry">
	<?php if($post['thumbnail'] ==''): ?>
		<a href="<?php echo $post_details; ?>" class="blog-thumbnail hidden-xs" style="background-image: url(<?php echo base_url('assets/img/news/img01.jpg'); ?>);"></a>
	<?php else: ?>
		<a href="<?php echo $post_details; ?>" class="blog-thumbnail hidden-xs" style="background-image: url(<?php echo base_url('upload/blog/'.$post['thumbnail']); ?>);"></a>
	<?php endif; ?>
	<div class="blog-content-wrap">
		<h3 class="blog-title text-left"><a href="<?php echo $post_details; ?>"><?php echo mb_substr($post["title"], 0, 80); ?>...</a></h3>
		<div class="blog-meta-wrap">
			<p class="blog-meta">
				<span><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo date('M d, Y', strtotime($post["published_at"])); ?></span>
				<span><i class="fa fa-folder-open" aria-hidden="true"></i> <a href="<?php echo base_url('blog/posts_filter?type=category&slug='.$post['category_slug'].'&id='.$post['category_id']);?>"><?php echo $post["category_name"]; ?></a></span>
				<span><i class="fa fa-comments" aria-hidden="true"></i> <?php echo $post["total_comments"]; ?> <?php echo $this->lang->line("Comments");?></span>
			</p>
		</div>
		<p class="blog-excerpt"><?php echo mb_substr(strip_tags($post["body"]), 0,200); ?>...</p>
		<!-- <p><a href="<?php echo $post_details; ?>" class="readmore-btn"><?php echo $this->lang->line("Read More");?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a></p> -->
	</div>
</article>
<?php endforeach; endif;?>
<?php echo $paiging_links; ?>