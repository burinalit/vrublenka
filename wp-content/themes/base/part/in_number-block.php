<section class="page_block in_number_block">
    <div class="container">
	    <h2 class="title_block"><span><?= $args['title'] ?></span></h2>
		<div class="in_number_content">
		<?php foreach($args['list'] as $elem): ?>
			<div class="elem">
				<div class="count"><span><?= $elem['number'] ?></span></div>
				<span class="text"><?= $elem['text'] ?></span>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</section>