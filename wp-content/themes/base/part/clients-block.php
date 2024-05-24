<section class="page_block clients_block">
    <div class="container">
	    <div class="clients_content">
		    <h2 class="title_block"><?= $args['title'] ?></h2>
			<div class="content_block">
			<?php foreach($args['category'] as $cl): ?>
			    <div class="faq_item">
				    <div class="quest_faq_item"><?= $cl['name'] ?></div>
					<div class="answer_faq_item">
					    <div class="elems_list">
						<?php foreach($cl['list'] as $elem): ?>
						    <div class="elem" style="background-image:url('<?= $elem['img'] ?>');"></div>
						<?php endforeach; ?>						
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		    </div>
		</div>
	</div>
</section>