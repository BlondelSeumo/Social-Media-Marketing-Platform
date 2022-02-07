<div class="widget-wrap">
	<div class="widget-content">
		<form action="<?php echo base_url('blog/posts_filter'); ?>" class="widget-search-form" method="GET">
			<button type="submit"><i class="fa fa-search"></i></button>
			<input type="hidden" name="type" value="search">
			<input type="text" class="form-control" name="keywords" placeholder="<?php echo $this->lang->line('Search'); ?>">
		</form>
	</div>
</div><!--/.widget-wrap-->
<div class="widget-wrap">
	<div class="widget-heading">
		<h3><?php echo $this->lang->line('Category'); ?></h3>
	</div><!--/.widget-heading-->
	<div class="widget-content">
		<ul class="categories">
			<?php foreach($sidebar['categories'] as $category){
				echo "<li><a href=".base_url('blog/posts_filter?type=category&slug='.$category['slug'].'&id='.$category['id']).">{$category['name']}<span>({$category['total_posts']})</span></a></li>";
			} ?>
		</ul>
	</div>
</div><!--/.widget-wrap-->
<div class="widget-wrap">
	<div class="widget-heading">
		<h3><?php echo $this->lang->line('Tags'); ?></h3>
	</div><!--/.widget-heading-->
	<div class="widget-content">
		<ul class="tags">
			<?php foreach($sidebar['tags'] as $tag){
				echo "<li><a href=".base_url('blog/posts_filter?type=tags&slug='.mb_strtolower($tag['name'])).">{$tag['name']}</a></li>";
			} ?>
		</ul>
	</div>
</div><!--/.widget-wrap-->