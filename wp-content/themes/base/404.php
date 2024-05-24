<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header(); ?>
<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		    <div class="maintitle_page">404</div>
		    <h1 class="title_page">Такой страницы не существует</h1>
			<h2 class="subtitle_page">Перейдите на Главную. Там много интересного</h2>		
			<a href="<?php echo get_home_url( null, '/', 'https' ); ?>" class="btn btn_preim">На главную</a>
		</div>
	</div>
</section>
<?php get_footer();
