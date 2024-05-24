<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		    <h1 class="title_page"><?= $args['title'] ?></h1>
			<h2 class="subtitle_page"><?= $args['subtitle'] ?></h2>
			<ul><?php foreach($args['list'] as $elem): ?>		    
			    <li><span><?= $elem['text'] ?></span></li>
			<?php endforeach; ?></ul>		
			<a href="<?= $args['button']['link'] ?>" class="btn btn_preim"><?= $args['button']['text'] ?></a>
		</div>
	</div>
</section>