<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();
while ( have_posts() ) :
	the_post(); ?>
	<article class="article_single" id="post-<?php the_ID(); ?>">
		<div class="container">
			<h1 class="title_page"><?php the_title() ?></h1>
			<section class="content_block">
				<?php the_content()?>
			</section>
		</div>
	</article>
<?php endwhile; ?>
<?php get_footer();
