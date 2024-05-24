<?php
$bank_details = get_field('bank_details', 'option');
$socials = get_field('socials', 'option');
$top_banner = get_field('top_banner');
/**
 * Template Name: Страница Спасибо
 */
get_header(); ?>
<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		    <h1 class="title_page">Благодарим за доверие</h1>
			<h2 class="subtitle_page">Вы только что оставили заявку. Наши специалисты свяжутся с Вами в течение 10 минут</h2>		
			<a href="<?php echo get_home_url( null, '/', 'https' ); ?>" class="btn btn_preim">На главную</a>
		</div>
	</div>
</section>
<?php get_footer();