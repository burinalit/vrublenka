<section class="page_block services_cat_block">
    <div class="container">
	    <div class="section_content">
			<h2 class="title_block"><?= $args['title'] ?></h2>
			<div class="subtitle_block"><?= $args['subtitle'] ?></div>
			<div class="serv_content">
			    <?php $lenth = count($args['list']); 
				foreach($args['list'] as $key => $item): 
				$start = '<div class="left_elems">';
				$finish = '</div>'; ?>
				<?php if($key != $lenth - 1): ?>
					<?php if($key == 0): echo $start; endif; ?>
					<div onClick="document.location='<?= $item['link'] ?>'" class="serv_elem <?php if($key == 0): ?> serv_elem_big <?php endif; ?>">
					   <?php if($key == 0): ?><div class="content"><?php endif; ?>
							<div class="title"><?= $item['name'] ?></div>
							<div class="text"><?= $item['text'] ?></div>
						<?php if($key == 0): ?></div>
						<div class="img" style="background-image:url('<?= $item['img'] ?>');"></div>
						<?php endif; ?>
					</div>
					<?php if($key == $lenth - 2): echo $finish; endif; ?>
				<?php else: ?>
				<div class="right_elems">
				    <div onClick="document.location='<?= $item['link'] ?>'" class="serv_elem">
						<div class="content">
							<div class="title"><?= $item['name'] ?></div>
							<div class="text"><?= $item['text'] ?></div>
						</div>		
						<div class="img" style="background-image:url('<?= $item['img'] ?>');"></div>
					</div>
				</div>
				<?php endif; ?>
			<?php endforeach; ?>	
			</div>
			<div class="tags_content">
			<?php foreach($args['tags'] as $el): ?>
				<a href="<?= get_permalink( $el ) ?>" class="tag_link"><?= get_the_title($el) ?></a>
			<?php endforeach; ?>	
			</div>
			<div class="all_serv"><a href="<?= $args['all_links'] ?>" class="btn btn_preim">Смотреть все услуги</a></div>
		</div>
	</div>
</section>