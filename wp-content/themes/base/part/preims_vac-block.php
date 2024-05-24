<?php $preims_vacat = get_field('preims_vacat', 'option');  ?>
<section class="page_block preim_vac_block">
    <div class="container">
	    <div class="block_content">
		<?php foreach($preims_vacat['list'] as $item): ?>
			<div class="elem">
				<span class="icon"></span>
				<span class="text"><?= $item['text'] ?></span>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</section>