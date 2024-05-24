<?php $forma1 = get_field('forma2', 'option'); ?>
<section class="page_block form_block">
    <div class="container">
	    <div class="cont">
	    <div class="form_content">
		    <div class="title_block"><?= $forma1['title'] ?></div>
			<div class="subtitle_block"><?= $forma1['subtitle'] ?></div>
			<div class="content_block">
				<?php echo do_shortcode('[contact-form-7 id="97" title="Записаться на консультацию"]')?>
			</div>
		</div>
		</div>
	</div>
</section>