<?php
/**
 * Template Name: Шаблон тарифов
 */
$small_tariffs = get_field('small_tariffs', 'option'); 
$main_tariffs = get_field('main_tariffs', 'option'); 
$top_banner = get_field('top_banner'); 
$predtar_title = get_field('predtar_title'); 
$predtar_subtitle = get_field('predtar_subtitle'); 

get_header();?>
<?php get_template_part('part/banner4-block', null, $top_banner); ?>
<section class="page_block tariffs_block">
    <div class="container">
	    <div class="title_block"><?= $predtar_title ?></div>
		<div class="subtitle_block"><?= $predtar_subtitle ?></div>
		<?php get_template_part('part/tariffs-block', null, $small_tariffs); ?>
		<div class="all_tariffs"><a href="#" class="btn btn_preim">Смотреть все</a></div>
	</div>
</section>
<section class="page_block tariffs_details_block">
    <div class="container">
	    <div class="tar_details_content">
		    <div class="tabs_wrap">
			    <div class="tabs_row tabs_title">
				    <div class="tabs_item"><span class="text">ЧТО входит в тариф</span></div>
					<div class="tabs_item"><span class="tariff">Стартап</span></div>
					<div class="tabs_item"><span class="tariff">Стандартный</span></div>
					<div class="tabs_item"><span class="tariff">Бизнес</span></div>
					<div class="tabs_item"><span class="tariff">Премиум</span></div>
				</div>
				<?php foreach($main_tariffs as $item): ?>
				    <div class="faq_item">
						<div class="tabs_row cat quest_faq_item">
							<div class="tabs_item">
								<span class="name"><?= $item['category'] ?></span>
								<?php if($item['info']): ?>
								    <span class="cat_info">*</span>
									<span class="info"><?= $item['info'] ?></span>
								<?php endif; ?>
								
							</div>
							<?php foreach($item['tariffs'] as $tariff): ?>
							<div class="tabs_item res">
								<?php if($tariff['res'] == 0): ?>
									<span class="val_not"></span>
								<?php elseif($tariff['res'] == 1): ?>
									<span class="val_true"></span>
								<?php elseif($tariff['res'] == 2): ?>
									<span class="val_text"><?= $tariff['res_text'] ?></span>
								<?php endif; ?>
							</div>
							<?php endforeach; ?>
						</div>
					    <?php if($item['list']): ?>
						<div class="answer_faq_item tabs_content">
						<?php foreach($item['list'] as $elem): ?>
							<div class="tabs_row list_elem">
								<div class="tabs_item">
									<span class="name"><?= $elem['name'] ?></span>
								</div>
								<?php foreach($elem['tariffs'] as $tariff): ?>
								<div class="tabs_item res">
									<?php if($tariff['res'] == 0): ?>
										<span class="val_not"></span>
									<?php elseif($tariff['res'] == 1): ?>
										<span class="val_true"></span>
									<?php elseif($tariff['res'] == 2): ?>
										<span class="val_text"><?= $tariff['res_text'] ?></span>
									<?php endif; ?>
								</div>
								<?php endforeach; ?>
							</div>				
						<?php endforeach; ?> 
						</div>
						<?php endif; ?>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	</div>
</section>
<?php get_template_part('part/form_consult1-block'); ?>
<?php get_footer();?>