<?php 
require_once( __DIR__.'/phpQuery/phpQuery.php');
define('HOST','https://ekaterinburg.flamp.ru/firm/vrubljonka_bukhgalterskaya_kompaniya-70000001029991969');
$data_site = file_get_contents(HOST);
$document = phpQuery::newDocument($data_site);
$content_info = $document->find('.pages-filial__rating .filial-rating');
foreach ($content_info as $var) {
	$pq = pq($var);
	$count = $pq->find('.filial-rating__value')->text();
	$all_rev = $pq->find('.filial-rating__reviews')->text();
}
$content_prev = $document->find('#reviews .js-cat-entities-ugc-item');
$reviews = array();
foreach ($content_prev as $key => $el) {
	$pq = pq($el);
	$avatar = $pq->find('cat-brand-avatar')->attr('image');
	$name = $pq->find('.author__content a.name')->attr('title');
	$date = $pq->find('.author__content a.ugc-date')->attr('title');
	$grade = $pq->find('cat-brand-review-estimation')->attr('estimation');
	$text = $pq->find('.ugc-item__content .t-rich-text p')->text();

	if (strpos($text, 'Показать целиком') !== false)
		$text = strstr($text, 'Показать целиком', true);

	$reviews[$key]['avatar'] = $avatar;
	$reviews[$key]['name'] = $name;
	$reviews[$key]['date'] = $date;
	$reviews[$key]['text'] = $text;
	$reviews[$key]['grade'] = $grade;
} ?>
<section class="page_block reviews_block">
    <div class="container">
	    <h2 class="title_block">Что говорят клиенты о нас</h2>
		<div class="reviews_content">
		    <div class="rev_el review_info desktop_screen">
			    <div class="logo_system"></div>
				<div class="company_info">
				    <img src="<?php bloginfo('template_directory') ?>/assets/images/little_logo.svg" alt="Врубленка лого"/>
					<div class="name">Врублёнка</div>
					<div class="subname">Бухгалтерская компания</div>
				</div>
				<div class="rev_info">
				    <span class="count"><?= $count ?></span>
					<span class="alls"><?= $all_rev ?></span>
				</div>
			</div>
			<div class="rev_el review_info mobile_screen">
			    <div class="top_block">
				    <div class="logo_system"></div>
					<div class="rev_info">
						<span class="alls"><?= $all_rev ?></span>
					</div>
				</div>
				<div class="bottom_block">
				    <img src="<?php bloginfo('template_directory') ?>/assets/images/little_logo.svg" alt="Врубленка лого"/>
					<div class="company_info">
						<div class="name">Врублёнка</div>
						<div class="subname">Бухгалтерская компания</div>
					</div>
					<div class="rev_info">
						<span class="count"><?= $count ?></span>
					</div>
				</div>
			</div>
			<?php foreach($reviews as $item): ?>
			<div class="rev_el">
			    <div class="avatar desktop_screen" style="background-image:url('<?= $item['avatar'] ?>');"></div>
				<div class="name desktop_screen"><?= $item['name'] ?></div>
				<div class="date desktop_screen"><?= $item['date'] ?></div>
				<div class="top_rev mobile_screen">
				    <div class="avatar" style="background-image:url('<?= $item['avatar'] ?>');"></div>
					<div class="inf_autor">
					    <div class="name"><?= $item['name'] ?></div>
				        <div class="date"><?= $item['date'] ?></div>
					</div>
				</div>
				<div class="text"><?= $item['text'] ?></div>
				<a href="#" class="read_rev">Читать полностью на Flamp</a>
				<ul class="grade_count">
				<?php for($i = 1; $i <= $item['grade']; $i++): ?>
				    <li class="grade"><?= $i ?></li>
				<?php endfor; ?>
				</ul>
			</div>
		    <?php endforeach; ?>
		</div>
	</div>
</section>