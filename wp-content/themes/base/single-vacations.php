<?php 
$post_id = get_the_ID();
$post_type = get_post_type($post_id);

$socials = get_field('socials', 'option');
$salary = get_field('salary');
$experience = get_field('experience');
$employment = get_field('employment');
$schedule = get_field('schedule');
$request = get_field('request');
$conditions = get_field('conditions');
$contacts = get_field('contacts');

get_header(); ?>
<section class="vacation_single">
	<div class="container">
	    <div class="head_block page_block">
		<a href="<?= get_post_type_archive_link($post_type) ?>" class="nav-line__return-link"><span class="nav-line__return-link-text">Назад</span></a>
		<h1 class="title_page"><?= get_the_title() ?>, <?= $salary ?></h1>
		<div class="vacation_date">Вакансия опубликована <?= get_the_date('d.m.Y') ?></div>
		<div class="vacation_params">
		    <div class="param">Требуемый опыт: <?= $experience ?></div>
			<div class="param">Занятость: <?= $employment ?></div>
			<div class="param">График: <?= $schedule ?></div>
		</div>
		
		</div>
		<div class="list_block page_block">
		    <h2 class="subtitle">Требования</h2>
			<ul><?php foreach($request as $item): ?>
			    <li><span><?= $item['text'] ?></span></li>
			<?php endforeach; ?>
			</ul>
		</div>
		<div class="list_block page_block">
		    <h2 class="subtitle">Условия</h2>
			<ul><?php foreach($conditions as $item): ?>
			    <li><span><?= $item['text'] ?></span></li>
			<?php endforeach; ?>
			</ul>
		</div>
		<div class="contacts_block page_block">
		    <h2 class="subtitle">Контакты для связи</h2>
			<?php if($contacts == 'standart'): ?>
			    <p class="text"><?= WT::$obj->contacts->getValue('phone') ?></p>
				<p class="text">Режим работы <?= WT::$obj->contacts->getValue('working_hours_days') ?> <?= WT::$obj->contacts->getValue('working_hours_time') ?></p>
				<p class="text"><?= WT::$obj->contacts->getValue('address') ?></p>
				<p class="text"><?= WT::$obj->contacts->getValue('email') ?></p>
			<?php else: $conts = get_field('contacts_others');?>
			    <p class="text"><?= $conts['phone'] ?></p>
				<p class="text"><?= $conts['timework'] ?></p>
				<p class="text"><?= $conts['address'] ?></p>
				<p class="text"><?= $conts['email'] ?></p>
			<?php endif; ?>
			<div class="mess_block">
			<?php foreach($socials as $item): ?> 
				<a href="tel:<?= $item['info'] ?>" class="icon icon_mess main_icon_<?= $item['icon'] ?>"><?= $item['icon'] ?></a>
			<?php endforeach; ?>	
			</div>
		</div>
	</div>
</section>
<?php get_template_part('part/part_comand-block'); ?>
<?php get_footer();
