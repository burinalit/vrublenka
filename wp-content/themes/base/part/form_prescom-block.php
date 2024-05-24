<?php $forma1 = get_field('forma5', 'option'); ?>
<section class="page_block perscom_block">
    <div class="container">
		<div class="perscom_content">
			<div class="title_block"><?= $forma1['title']?></div>
			<div class="subtitle_block"><?= $forma1['subtitle']?></div>
			<div class="btn_block"><a href="<?php echo get_home_url( null, 'vacation/', 'https' ); ?>" class="btn btn_preim"><?= $forma1['link']?></a></div>
		</div>
	</div>
</section>