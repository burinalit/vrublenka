<?php
$small_tariffs = get_field('small_tariffs', 'option'); 
$terms = get_terms(
  array(
    'taxonomy'   => 'category_serv',
    'hide_empty' => false,
    'hierarchical' => false,
    'orderby' => 'term_id',
    'order' => 'ASC',
  )
); 
$top_banner = get_field('banner_services', 'option');
$forma1 = get_field('form_person', 'option');
get_header();?> 
<?php get_template_part('part/banner3-block', null, $top_banner); ?>
<section class="page_block services_list_block">
    <div class="container">
	    <div class="title_block">Мы предлагаем</div>
		<div class="subtitle_block">Широкий ассортимент услуг </div>
		<div class="services_list_content">
		    <div id="tabs_order" class="order_tabs send js-styled-scroll" data-target-scroll="textarea">   
				<div class="order_tabs_list">
				<?php foreach ( $terms as $key => $cat ): 
				    if($key == 0) $class="order_tab_active"; else $class=""; 
					$name = get_field('small_title', 'category_serv_'.$cat->term_id);
					if($name): ?>
					    <span class="order_tab <?= $class ?>" data-id="<?= $key ?>"><?= $name ?></span>
					<?php else: ?>
					    <span class="order_tab <?= $class ?>" data-id="<?= $key ?>"><?= $cat->name ?></span>
					<?php endif; ?>
				<?php endforeach; ?>	
				</div>
				<div class="order_tabs_content">
				<?php foreach ( $terms as $key => $cat ): 
				    if($key == 0) $class="tab_content_active"; else $class=""; 
					$category = $cat->slug; ?>
					<div class="tab_content <?= $class ?>" data-id="<?= $key ?>">
						<?php $posts_Query = new WP_Query(array(
							'post_type'      => 'services',
							'post_status'      => 'publish',
							'tax_query' => array(
								array(
									'taxonomy' => 'category_serv',
									'field' => 'slug',
									'terms' => $category
								)
							),				
							'posts_per_page' => 100,
							'orderby'    => 'date',
							'order'      => 'DESC',
						));
						if ($posts_Query->have_posts()) : 
							while ( $posts_Query->have_posts() ) : $posts_Query->the_post();
								$price = get_field('small_price', $post->ID);
								$cur = get_field('small_price_cur', $post->ID); ?>
								<div class="servs_item">
								    <div class="icon"></div>
									<div class="title"><?= get_the_title() ?></div>
									<?php if($price): ?>
									    <div class="price_block">от <span class="price"><?= $price ?> <?php if($cur): ?><span class="cur"> <?= $cur ?></span><?php endif; ?></span></div>
									<?php endif; ?>
									<a href="<?php the_permalink()?>" class="btn btn_preim" title="<?= get_the_title() ?>">Подробнее</a>
								</div>
						<?php endwhile; endif; 
						wp_reset_query(); ?>
                    </div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_template_part('part/why_we-block'); ?>
<?php get_template_part('part/form_person-block', null, $forma1); ?>
<?php get_template_part('part/reviews-block'); ?>
<section class="page_block tariffs_block">
    <div class="container">
	    <div class="title_block">Тарифы и пакеты</div>
		<div class="subtitle_block">Занимайтесь бизнесом, всю бухгалтерию берем на себя</div>
		<?php get_template_part('part/tariffs-block', null, $small_tariffs); ?>
		<div class="all_tariffs"><a href="<?php echo get_home_url( null, 'tariffs/', 'https' ); ?>" class="btn btn_preim">Смотреть все</a></div>
	</div>
</section>
<?php get_template_part('part/form_consult-block'); ?>
<?php get_footer();?>