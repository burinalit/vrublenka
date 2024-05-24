<div class="single_content">
<?php foreach($args as $item): ?>
    <div class="single_item">
	    <?php if($item['title']): ?><h2 class="title_single"><?= $item['title'] ?></h2><?php endif; ?>
		<?php if($item['text']): ?><div class="content_single"><?= $item['text'] ?></div><?php endif; ?>
	</div>
<?php endforeach; ?>
</div>