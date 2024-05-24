<?php $why_we_basic = get_field('why_we_dr', 'option');  ?>
<section class="page_block why_we_block">
    <div class="title_content">
	    <div class="container">
		    <div class="t_cont_item">
				<div class="title_block"><?= $why_we_basic['title'] ?></div>
				<div class="subtitle_block"><?= $why_we_basic['subtitle'] ?></div>
			</div>
		</div>
	</div>
    <div class="container">
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