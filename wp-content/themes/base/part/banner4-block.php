<section class="banner_block page_block">
    <div class="container">
	    <div class="banner_content">
		    <h1 class="title_page"><?= $args['title'] ?></h1>
			<h2 class="subtitle_page"><?= $args['subtitle'] ?></h2>
			<div class="price_block">от <span class="price"><?= $args['price'] ?><span class="cur"> ₽/месяц</span></span></div>
			<div class="buttons_block">
			    <a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_preim"><?= $args['button_ord'] ?></a>
				<a href="<?= $args['button_price']['link'] ?>" class="btn_price"><?= $args['button_price']['text'] ?></a>
			</div>
		</div>
	</div>
</section>
