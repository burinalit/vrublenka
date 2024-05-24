<?php
/**
 * Template Name: Шаблон категории услуг
 */
$small_tariffs = get_field('small_tariffs', 'option'); 
$term = get_queried_object();
$term_id = $term->term_id;
$term_slug = $term->slug;

$terms = get_terms(
  array(
    'taxonomy'   => 'category_serv',
    'hide_empty' => false,
    'hierarchical' => false,
    'orderby' => 'term_id',
    'order' => 'ASC',
  )
); 



get_header(); 
?>
<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		    <h1 class="title_page">Услуги для бизнеса</h1>
			<h2 class="subtitle_page">Мы позаботимся о документах, чтобы Ваш бизнес работал эффективно</h2>
			<a href="#" class="btn btn_preim">Оставить заявку</a>
		</div>
	</div>
</section>
<section class="page_block services_list_block">
    <div class="container">
	    <div class="title_block">Мы предлагаем</div>
		<div class="subtitle_block">Широкий ассортимент услуг </div>
		<div class="services_list_content">
		    <div id="tabs_order" class="order_tabs send js-styled-scroll" data-target-scroll="textarea">   
				<div class="order_tabs_content">
					<div class="tab_content tab_content_active">
						<?php $posts_Query = new WP_Query(array(
							'post_type'      => 'services',
							'post_status'      => 'publish',
							'tax_query' => array(
								array(
									'taxonomy' => 'category_serv',
									'field' => 'slug',
									'terms' => $term_slug
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
				</div>
			</div>
		</div>
	</div>
</section>
<section class="page_block why_we_block">
    <div class="title_content">
	    <div class="container">
		    <div class="t_cont_item">
				<div class="title_block">Почему выбирают нас</div>
				<div class="subtitle_block">Клиенты выбирают спокойствие и уверенность</div>
			</div>
		</div>
	</div>
    <div class="container">
		<div class="why_we_content">
			<div class="elem">
				<span class="icon"></span>
				<div class="title">Преимущество или выделяющаяся черта бизнеса в цифрах</div>
				<div class="text">По своей сути рыбатекст является альтернативой традиционному lorem ipsum, который вызывает у некторых людей недоумение при попытках прочитать рыбу текст</div>
			</div>
			<div class="elem">
				<span class="icon"></span>
				<div class="title">Преимущество или выделяющаяся черта бизнеса в цифрах</div>
				<div class="text">По своей сути рыбатекст является альтернативой традиционному lorem ipsum, который вызывает у некторых людей недоумение при попытках прочитать рыбу текст</div>
			</div>
			<div class="elem">
				<span class="icon"></span>
				<div class="title">Преимущество или выделяющаяся черта бизнеса в цифрах</div>
				<div class="text">По своей сути рыбатекст является альтернативой традиционному lorem ipsum, который вызывает у некторых людей недоумение при попытках прочитать рыбу текст</div>
			</div>
		</div>
	</div>
</section>
<section class="page_block pers_block">
    <div class="container">
		<div class="pers_content">
			<div class="title_block">Для Вас персональное предложение</div>
			<div class="subtitle_block">Оставьте заявку и узнайте все, что мы приготовили для Вас</div>
			<a href="#" class="btn btn_preim">Оставить заявку</a>
		</div>
	</div>
</section>
<?php get_template_part('part/reviews-block'); ?>
<section class="page_block tariffs_block">
    <div class="container">
	    <div class="title_block">Тарифы и пакеты</div>
		<div class="subtitle_block">Занимайтесь бизнесом, всю бухгалтерию берем на себя</div>
		<?php get_template_part('part/tariffs-block', null, $small_tariffs); ?>
		<div class="all_tariffs"><a href="#" class="btn btn_preim">Смотреть все</a></div>
	</div>
</section>
<?php get_template_part('part/form_consult-block'); ?>
<?php get_footer();?>