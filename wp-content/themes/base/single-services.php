<?php 
$small_tariffs = get_field('small_tariffs', 'option'); 
$forma1 = get_field('form_person_card', 'option');
$small_price = get_field('small_price'); 
$cur = get_field('small_price_cur'); 
$subtitle = get_field('subtitle'); 
$dates = get_field('cout_dates'); 
$details = get_field('detail_serv'); 
$dops = get_field('dop_text'); 
$link_price = get_field('link_price'); 

$post_id = get_the_ID();
get_header()?>
<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		    <h1 class="title_page"><?php the_title() ?></h1>
			<?php if($subtitle): ?><h2 class="subtitle_page"><?= $subtitle ?></h2><?php endif; ?>
			<div class="price_block">от <span class="price"><?= $small_price ?> <?php if($cur): ?><span class="cur"> <?= $cur ?></span><?php endif; ?></span></div>
			<div class="buttons_block">
			    <a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_preim">Оставить заявку</a>
				<a href="<?= $link_price ?>" class="btn_price">Скачать весь прайс</a></div>
		</div>
	</div>
</section>
<?php get_template_part('part/preims_vac-block'); ?>
<section class="single_serv_block page_block">
    <div class="container">
	    <div class="single_serv_content">
		    <?php get_template_part('part/sidebar', null, $post_id); ?>
			<?php get_template_part('part/single_content', null, $details); ?>
		</div>
	</div>
</section>
<section class="page_block in_number_block">
    <div class="container">
		<div class="in_number_content">
			<div class="elem">
				<div class="count"><span><?= $small_price ?> ₽</span></div>
				<span class="text">стоимость выполнения услуги</span>
			</div>
			<div class="elem">
				<div class="count"><span><?= $dates ?></span></div>
				<span class="text">срок выполнения услуги</span>
			</div>
			<div class="elem">
				<div class="count"><span>100%</span></div>
				<span class="text">гарантия выполнения услуги</span>
			</div>
		</div>
	</div>
</section>
<?php if($dops): ?>
<section class="single_dop_block page_block">
    <div class="container">
	    <div class="single_dop_content">
			<?php get_template_part('part/single_content', null, $dops); ?>
		</div>
	</div>
</section>
<?php endif; ?>
<section class="page_block tariffs_block">
    <div class="container">
	    <div class="title_block">Тарифы и пакеты</div>
		<div class="subtitle_block">Занимайтесь бизнесом, всю бухгалтерию берем на себя</div>
		<?php get_template_part('part/tariffs-block', null, $small_tariffs); ?>
		<div class="all_tariffs"><a href="<?php echo get_home_url( null, 'tariffs/', 'https' ); ?>" class="btn btn_preim">Смотреть все</a></div>
	</div>
</section>
<?php get_template_part('part/form_personcard-block', null, $forma1); ?>
<?php get_template_part('part/steps_work-block'); ?>
<?php get_template_part('part/reviews-block'); ?>
<section class="page_block services_list_block">
    <div class="container">
	    <div class="title_block">Популярные услуги</div>
		<div class="services_list_content">
		    <?php serv_items_other($post_id); ?>
		</div>
	</div>
</section>
<?php get_template_part('part/form_consult-block'); ?>
<?php get_footer()?>