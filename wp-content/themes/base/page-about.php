<?php
$top_banner = get_field('top_banner');
$sposobn = get_field('sposobn');
$history = get_field('history');
$command = get_field('command');
$sotr_list = get_field('sotr_list');
$in_numbers = get_field('in_numbers', 'option'); 
/**
 * Template Name: О компании
 */
get_header();?>
<?php get_template_part('part/banner2-block', null, $top_banner); ?>
<?php get_template_part('part/sposobn-block', null, $sposobn); ?>
<?php get_template_part('part/history-block', null, $history); ?>
<?php get_template_part('part/command-block', null, $command); ?>
<?php get_template_part('part/in_number-block', null, $in_numbers); ?>
<?php get_template_part('part/sotrs-block', null, $sotr_list); ?>
<?php get_template_part('part/form_prescom-block'); ?>
<?php get_template_part('part/reviews-block'); ?>
<?php get_template_part('part/form_consult-block'); ?>
<?php get_footer();?>