<div class="tariffs_content">
	<?php foreach($args as $item): if($item['popular'] == 1) $dop_class="tar_best"; else $dop_class= ''; ?>
	<div class="tariff <?= $dop_class ?>">
		<?php if($item['popular'] == 1): ?><span class="best_var">Популярный</span><?php endif;?>
		<div class="name"><?= $item['title'] ?></div>
		<div class="img"><span style="background-image:url('<?= $item['img'] ?>');"></span></div>
		<div class="desc"><?= $item['desc'] ?></div>
		<?php if($item['price_block']): $price = $item['price_block']; ?>
		<div class="price_block"><?= $price['out'] ?> <span class="price"><?= $price['price'] ?> <span class="cur"> <?= $price['cur'] ?></span></span></div>
		<?php endif;?>
		<a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_preim">Подключиться</a>
		<ul>
		<?php foreach($item['servs'] as $serv):?>
			<li><span class="tap"><?= $serv["name"] ?></span>
			<?php if($serv["link"]): ?><a href="<?= $serv["link"] ?>" class="icon icon_tap">*</a><?php endif; ?>
			<?php if($serv["dop_servs"] == 1): ?>
				<?php foreach($serv['dop_list'] as $dop):?>
					<span class="plus_tap"><?= $dop['name'] ?></span>
				<?php endforeach; ?>
			<?php endif; ?>
			</li>
		<?php endforeach; ?>	
		</ul>
	</div>
	<?php endforeach; ?>
</div>