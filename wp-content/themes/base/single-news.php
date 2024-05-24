<?php 
$post_id = get_the_ID();
get_header(); ?>
<section class="article_single">
	<div class="container">
	    <div class="return"><a href="/news/" class="return_link">Вернуться назад</a></div>
	    <h1 class="title_page"><?php the_title() ?></h1>
		<p class="date"><?= get_the_time('d.m.Y'); ?></p>
		<?php if( has_post_thumbnail() ):
			echo get_the_post_thumbnail( $post_id, 'news-full', array(
				'class' => 'post__img',
				'alt' => get_the_title()
			) );
		endif; ?>
		<section class="content_block">
			<?php the_content()?>
		</section>
	</div>
</section>
<section class="page_block other_news_block">
	<div class="container">
		<div class="title_block">Также может быть интересно</div>
		<div class="news_list">
			<?php blog_items_other($post_id); ?>
		</div>
	</div>
</section>	
<?php get_template_part('part/form_consult-block'); ?>
<?php get_footer()?>