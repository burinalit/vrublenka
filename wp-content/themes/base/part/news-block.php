<?php 
$news_Query = new WP_Query(array(
	'post_type'      => 'news',
	'post_status'      => 'publish',			
	'posts_per_page' => 4,
	'orderby'    => 'date',
	'order'      => 'DESC',
)); ?>
<section class="page_block news_block">
    <div class="container">
	    <div class="news_content">
            <h2 class="title_block"><?= $args['title'] ?></h2>
			<div class="subtitle_block"><?= $args['subtitle'] ?></div>
			<div class="news_list desktop_screen">
			<?php if ($news_Query->have_posts()) : $key = 0;
		    while ( $news_Query->have_posts() ) : $news_Query->the_post();?>
			<?php if($key == 0) : ?>
			    <div class="left">
				    <div class="news_elem">
					    <div class="image">
					    <?php if( has_post_thumbnail() ):
							echo get_the_post_thumbnail( $post->ID, 'newshome-thumb', array(
								'class' => 'post__img',
								'alt' => get_the_title()
							) );
						else: ?>
						<div class="post__img"></div>
						<?php endif; ?>
					    </div>
						<div class="content">
						    <div class="date"><?php the_time('j F Y'); ?></div>
							<a href="<?php the_permalink()?>" class="title"><?= get_the_title() ?></a>
							<div class="text"><?php echo excerpt(get_the_excerpt(), 160); ?></div>
						</div>
					</div>
				</div>	
			<?php else: ?>	
				<?php if($key == 1) : ?><div class="right"><?php endif; ?>	
						<div class="news_elem">
							<div class="image">
							<?php if( has_post_thumbnail() ):
								echo get_the_post_thumbnail( $post->ID, 'news-thumb', array(
									'class' => 'post__img',
									'alt' => get_the_title()
								) );
							else: ?>
							<div class="post__img"></div>
							<?php endif; ?>
							</div>
							<div class="content">
								<div class="date"><?php the_time('j F Y'); ?></div>
								<a href="<?php the_permalink()?>" class="title"><?= get_the_title() ?></a>
								<div class="text"><?php echo excerpt(get_the_excerpt(), 160); ?></div>
							</div>
						</div>
				<?php if($key == 3) : ?></div><?php endif; ?>	
            <?php endif; ?>				
			<?php $key++; endwhile; 
            endif; wp_reset_query(); ?>	
			</div>
			<div class="news_list mobile_screen">
			<?php if ($news_Query->have_posts()) :
		    while ( $news_Query->have_posts() ) : $news_Query->the_post();?>
				<div class="news_elem">
				    <div class="image">
					<?php if( has_post_thumbnail() ):
						echo get_the_post_thumbnail( $post->ID, 'news-thumb', array(
							'class' => 'post__img',
							'alt' => get_the_title()
						) );
					else: ?>
					<div class="post__img"></div>
					<?php endif; ?>
					</div>
					<div class="content">
					    <a href="<?php the_permalink()?>" class="title"><?= get_the_title() ?></a>
						<div class="text"><?php echo excerpt(get_the_excerpt(), 160); ?></div>
						<div class="date"><?php the_time('j F Y'); ?></div>
					</div>
				</div>
			<?php endwhile; 
            endif; wp_reset_query(); ?>		
			</div>
			<div class="all_news"><a href="<?php echo get_home_url( null, 'news/', 'https' ); ?>" class="btn btn_preim">Все новости</a></div>
        </div>
	</div>
</section>