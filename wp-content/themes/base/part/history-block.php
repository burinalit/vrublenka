<section class="page_block history_block">
    <div class="pred_history_block">
	    <div class="container">
	        <div class="pred_history_content">
	            <div class="title_block"><?= $args['title']?></div>
			    <div class="subtitle_block"><?= $args['subtitle']?></div>
				<?php foreach($args['list'] as $el): ?>
				    <p><?= $el['text'] ?></p>
				<?php endforeach; ?>
	        </div>
	    </div>
	</div>
	<div class="post_history_block">
	    <div class="container">
	        <div class="post_history_content">
			<?php foreach($args['vehi'] as $item): ?>
	            <div class="history_el">
				    <span class="year"><?= $item['year'] ?></span>
					<span class="title"><?= $item['name'] ?></span>
					<p class="text"><?= $item['text'] ?></p>
				</div>
			<?php endforeach; ?>	
	        </div>
	    </div>	
	</div>
</section>