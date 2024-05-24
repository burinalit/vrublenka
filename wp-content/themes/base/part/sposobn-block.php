<section class="page_block our_res_block">
	<div class="container">
		<div class="title_block"><?= $args['title'] ?></div>
		<div class="our_res_content">
		<?php foreach($args['list'] as $elem): ?>
			<div class="elem">
				<span class="icon" style="background-image:url('<?= $elem['img'] ?>');"></span>
				<span class="text"><?= $elem['name'] ?></span>
			</div>
		<?php endforeach; ?>	
		</div>
	</div>
</section>