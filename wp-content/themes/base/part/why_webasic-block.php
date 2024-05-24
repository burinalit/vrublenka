<?php $why_we_basic = get_field('why_we_basic', 'option');  ?>
<section class="page_block why_we_block">
    <div class="container">
	    <h2 class="title_block"><?= $why_we_basic['title'] ?></h2>
		<div class="subtitle_block"><?= $why_we_basic['subtitle'] ?></div>
		<div class="why_we_content">
		<?php foreach($why_we_basic['list'] as $item): ?>
			<div class="elem">
				<span class="icon"></span>
				<div class="title"><?= $item['name'] ?></div>
				<div class="text"><?= $item['text'] ?></div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</section>