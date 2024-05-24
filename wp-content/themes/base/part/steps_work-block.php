<?php $steps = get_field('steps_work', 'option');  ?>
<section class="page_block how_we_block">
    <div class="how_we_cont">
		<div class="container">
			<div class="title_block"><?= $steps['title'] ?></div>
			<div class="how_we_content">
			<?php foreach($steps['list'] as $key => $item): $key++; ?>
			    <div class="elem how_<?= $key ?>">
					<span class="num"><?= $key ?></span>
					<div class="content">
						<span class="icon" style="background-image:url('<?= $item['img'] ?>');"></span>
						<span class="text"><?= $item['text'] ?></span>
					</div>  
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>