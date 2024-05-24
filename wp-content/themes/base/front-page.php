<?php 
$map = array();
$news = array();
$map['phone'] = WT::$obj->contacts->getValue('phone');
$map['mail'] = WT::$obj->contacts->getValue('email');
$map['addr'] = WT::$obj->contacts->getValue('address');

$small_tariffs = get_field('small_tariffs', 'option'); 
$in_numbers = get_field('in_numbers', 'option'); 

$clients = get_field('clients', 'option'); 
$top_banner = get_field('top_banner');
$small_servs = get_field('small_servs'); 
$pers_block = get_field('pers_block'); 
$about = get_field('about'); 
$news_block = get_field('news_block');
 
$news['title'] = $news_block['title'];
$news['subtitle'] = $news_block['subtitle'];
$news['link'] = $news_block['all_link'];
			
/**
 * Template Name: Шаблон главной
 */
get_header()?>
<?php get_template_part('part/banner1-block', null, $top_banner); ?>
<?php get_template_part('part/preims_vac-block'); ?>
<?php get_template_part('part/servshome-block', null, $small_servs); ?>
<?php get_template_part('part/why_webasic-block'); ?>
<section class="page_block help_block">
    <div class="container">
		<div class="help_content">
			<div class="img"></div>
			<div class="content">
				<h2 class="title_block"><?= $pers_block['title'] ?></h2>
				<div class="subtitle_block"><?= $pers_block['subtitle'] ?></div>
				<ul><?php foreach($pers_block['list'] as $els): ?>
					<li><?= $els['text'] ?></li>
				<?php endforeach; ?></ul>
				<a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_preim">Оставить заявку</a>
			</div>
		</div>
	</div>
</section>
<?php get_template_part('part/about-block', null, $about); ?>
<?php get_template_part('part/in_number-block', null, $in_numbers); ?>
<section class="page_block tariffs_block">
    <div class="container">
	    <h2 class="title_block">Тарифы и пакеты</h2>
		<div class="subtitle_block">Занимайтесь бизнесом, всю бухгалтерию берем на себя</div>
		<?php get_template_part('part/tariffs-block', null, $small_tariffs); ?>
		<div class="all_tariffs"><a href="<?php echo get_home_url( null, 'tariffs/', 'https' ); ?>" class="btn btn_preim">Смотреть все</a></div>
	</div>
</section>
<?php get_template_part('part/clients-block', null, $clients); ?>
<?php get_template_part('part/reviews-block'); ?>
<?php get_template_part('part/news-block', null, $news); ?>
<?php get_template_part('part/form_consult-block'); ?>
<?php get_template_part('part/map-block', null, $map); ?>
<?php get_footer();?>