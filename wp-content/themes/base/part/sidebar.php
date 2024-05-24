<?php
$terms = get_terms(
  array(
    'taxonomy'   => 'category_serv',
    'hide_empty' => false,
    'hierarchical' => false,
    'orderby' => 'term_id',
    'order' => 'ASC',
  )
);
$arg = array('orderby' => 'term_order', 'order' => 'ASC', 'fields' => 'all');
$category = wp_get_object_terms($args, 'category_serv', $arg); 
if($category)
    $cat_id = $category[0]->term_id;
else
    $cat_id = 0;
?>
<div class="vertical_menu_block">
    <ul class="vertical_menu">   
	<?php foreach ( $terms as $term ): 
	    $id = $term->term_id; 
		$menu_link = get_term_link($id, 'category_serv'); 
		$term_slug = $term->slug;
		$posts_Query = new WP_Query(array(
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
			'order'      => 'ASC',
		));
		if ($posts_Query->have_posts()) : $a_class = 'parent'; $menu_link = '#'; else: $a_class = 'no_parent'; endif; 
			if($term->term_id == $cat_id) $active_class = 'active_term'; 
			else $active_class = '';
		?>
		<li class="line_first <?= $active_class ?>">
		    <a class="<?= $a_class ?>" href="<?= $menu_link ?>"><?= $term->name ?></a>
			<?php 
			if ($posts_Query->have_posts()) : ?>
			  <ul class="child hidden">
				<?php while ( $posts_Query->have_posts() ) : $posts_Query->the_post(); $el_id = $post->ID; ?>
					<li class="line_second <?php if($args == $el_id): ?>active<?php endif; ?>"><a href="<?php the_permalink()?>"><?= get_the_title() ?></a></li>
				<?php endwhile; ?>
			  </ul>
		<?php endif; wp_reset_query(); ?> 
		</li>
	<?php endforeach; ?>
	</ul>
</div>