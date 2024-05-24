<?php
$bank_details = get_field('bank_details', 'option');
$socials = get_field('socials', 'option');
$top_banner = get_field('top_banner');
/**
 * Template Name: Контакты
 */
get_header(); ?>
<?php get_template_part('part/banner3-block', null, $top_banner); ?>
<section class="page_block contdet_block">
	<div class="container">
		<div class="contdet_content">
			<div class="elem">
				<div class="title">Где мы находимся</div>
				<div class="text">
				    <p>Режим работы <?= WT::$obj->contacts->getValue('working_hours_days') ?> <?= WT::$obj->contacts->getValue('working_hours_time') ?></p>
					<p><?= WT::$obj->contacts->getValue('address') ?></p>
				</div>
			</div>
			<div class="elem">
				<div class="title">Реквизиты организации</div>
				<div class="text"><?= $bank_details ?></div>
			</div>
			<div class="elem">
				<div class="title">Связаться</div>
				<div class="text">
				    <p><?= WT::$obj->contacts->getValue('phone') ?></p>
					<p><?= WT::$obj->contacts->getValue('email') ?></p>
					<div class="mess_block">
				    <?php foreach($socials as $item): ?> 
						<a href="tel:<?= $item['info'] ?>" class="icon icon_mess main_icon_<?= $item['icon'] ?>"><?= $item['icon'] ?></a>
					<?php endforeach; ?>
				</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_template_part('part/form_consult2-block'); ?>
<?php get_footer();