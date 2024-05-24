<?php 
$map = array();
$map['addr'] = WT::$obj->contacts->getValue('address');
?>
<?php $forma1 = get_field('forma3', 'option'); ?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=c735ea2c-8cc2-453a-8ee3-d35128a3be92" type="text/javascript"></script>
<script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
<script>
function init() {
		var myMap = new ymaps.Map('map', {
				center: [56.832034, 60.605209],
				zoom: 17.4,
				type: 'yandex#map',
				controls: []
			}),
			myCollection = new ymaps.GeoObjectCollection(),
			myPoints = [
				{ coords: [56.831327, 60.602746]},
			];
		// Заполняем коллекцию данными.
		for (var i = 0, l = myPoints.length; i < l; i++) {
			var point = myPoints[i];
			var image = "<?php bloginfo('template_directory') ?>/assets/images/map_icon.svg";
			myCollection.add(new ymaps.Placemark(
				point.coords, {
					balloonContentBody: point.text
				}, {
					preset: 'islands#violetCircleDotIcon',
					iconImageHref: image,
					iconLayout: 'default#image',
					iconImageSize: [50, 66],
				}
			));
		}

		// Добавляем коллекцию меток на карту.
		myMap.geoObjects.add(myCollection);
		myMap.behaviors.disable('scrollZoom');
		myMap.options.set('suppressMapOpenBlock', true);
		myMap.options.set('suppressObsoleteBrowserNotifier', true);
	}

	ymaps.ready(init);
</script>
<section class="page_block form_block formmap_block">
    <div class="container">
	    <div class="cont">
			<div class="form_content">
				<div class="title_block"><?= $forma1['title'] ?></div>
				<div class="subtitle_block"><?= $forma1['subtitle'] ?></div>
				<div class="content_block">
					<?php echo do_shortcode('[contact-form-7 id="97" title="Записаться на консультацию"]')?>
				</div>
			</div>
			<div class="map_content">
				<div class="infoblock">
				    <div class="elem">Врубленка</div>
					<div class="elem"><?= $map['addr'] ?></div>
				</div>
				<div id="map"></div>
			</div>
		</div>
	</div>
</section>