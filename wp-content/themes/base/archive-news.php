<?php
/**
 * Страница архивов статей
 * @package WordPress
 */
 
global $post; 
get_header(); ?> 
<section class="page_block">
    <div class="container">
	    <h1 class="title_block">Новости</h1>
		<div class="news_list">
		    <?php 
			if (is_front_page()) {
				$currentPage = (get_query_var('page')) ? get_query_var('page') : 1;
			} else {
				$currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;
			}
			$posts_Query = new WP_Query(array(
				'post_type'      => 'news',
				'post_status'      => 'publish',
				'orderby'    => 'date',
	            'order'      => 'DESC',
				'posts_per_page' => 6,
				'paged'          => $currentPage,
			)); ?>
			<?php if ($posts_Query->have_posts()) : $post_arr = array(); ?>
			<?php while ( $posts_Query->have_posts() ) : $posts_Query->the_post(); $post_arr[] = $post->ID;?>
			    <div class="news_item">
				    <?php if( has_post_thumbnail() ):
					    echo get_the_post_thumbnail( $post->ID, 'news-thumb', array(
							'class' => 'post__img',
							'alt' => get_the_title()
						) );
					else: ?>
                    <div class="post__img"></div>
					<?php endif; ?>
					<p class="post__date"><?php the_time('j F Y'); ?></p>
					<a href="<?php the_permalink()?>" class="post__title"><?= get_the_title() ?></a>
					<div class="post__description">
					    <?php echo excerpt(get_the_excerpt(), 160); ?>
					</div>
				</div>
				<?php endwhile; 
            endif; wp_reset_query(); ?>
			<div class="pagination">
				<?php echo paginate_links([
					'base'      => str_replace(999999999, '%#%', get_pagenum_link(999999999)),
					'format'    => '',
					'current'   => max(1, $currentPage),
					'total'     => $posts_Query->max_num_pages,
					'prev_next' => true,
					'prev_text' => __( '<' ),
					'next_text' => __( '>' ),
				]); ?>	
			</div>
		</div>
	</div>
</section>
<section class="page_block other_news_block">
	<div class="container">
		<div class="title_block">Другие новости</div>
		<div class="news_list">
			<?php blog_item_other($post_arr); ?>
		</div>
	</div>
</section>		
<?php get_template_part('part/form_news-block'); ?>
<?php get_footer(); ?>