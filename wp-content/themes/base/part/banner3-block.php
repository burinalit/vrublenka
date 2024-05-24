<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		<?php if($args['title']): ?>
		    <h1 class="title_page"><?= $args['title'] ?></h1>
		<?php else: ?>
		    <h1 class="title_page"><?php the_title() ?></h1>
		<?php endif; ?>	
			<h2 class="subtitle_page"><?= $args['subtitle'] ?></h2>
		<?php if($args['link']): ?>	
			<a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_preim"><?= $args['link']?></a>
		<?php endif; ?>
		</div>
	</div>
</section>