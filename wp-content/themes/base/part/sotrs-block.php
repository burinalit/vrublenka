<section class="page_block collegs_block">
	<div class="container">
	    <div class="title_block">Сотрудники</div>
		<div class="collegs_content">
		<?php foreach($args as $item): ?>
			<div class="collegs_el">
			    <div class="img" style="background-image:url('<?= $item['img'] ?>');"></div>
				<div class="title"><?= $item['name'] ?></div>
				<div class="post"><?= $item['post'] ?></div>
			</div>
		<?php endforeach; ?>	
		</div>
	</div>	
</section>