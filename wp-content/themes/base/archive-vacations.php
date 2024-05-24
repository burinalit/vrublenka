<?php
$vac_Query = new WP_Query(array(
	'post_type'      => 'vacations',
	'post_status'      => 'publish',
	'orderby'    => 'date',
	'order'      => 'DESC',
	'posts_per_page' => 10,
));
$top_banner = get_field('banner_vacations', 'option');
get_header(); ?>
<?php get_template_part('part/banner3-block', null, $top_banner); ?>
<?php get_template_part('part/preims_vac-block'); ?>
<section class="vacations_block page_block">
    <div class="container">
	    <div class="title_block">Вакансии</div>
		<div class="vacations_list">
			<?php if ($vac_Query->have_posts()) : 
	            while ( $vac_Query->have_posts() ) : 
				$vac_Query->the_post(); $id = $post->ID;
				$salary = get_field('salary', $id);
				$experience = get_field('experience', $id);
				$employment = get_field('employment', $id);
				$schedule = get_field('schedule', $id); ?>
				<div class="vac_item vac-item-<?= $id ?>">
				    <div class="vac_title_block"><a href="<?php the_permalink()?>" title="<?= get_the_title() ?>" class="vac_title"><?= get_the_title() ?></a><div class="vac_price"><?= $salary ?></div></div>
					<div class="vacation_date">Вакансия опубликована <?= get_the_date('d.m.Y') ?></div>
					<div class="vacation_params">
						<div class="param">Требуемый опыт: <?= $experience ?></div>
						<div class="param">Занятость: <?= $employment ?></div>
						<div class="param">График: <?= $schedule ?></div>
					</div>
                </div>
				<?php endwhile; 
            endif; wp_reset_query(); ?>
		</div>
	</div>
</section>
<?php get_template_part('part/part_comand-block'); ?>
<?php get_footer();
