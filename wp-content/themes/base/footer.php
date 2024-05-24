<?php
$socials = get_field('socials', 'option');
$bank_details = get_field('bank_details', 'option');
$contractor = get_field('contractor', 'option');
$contractor_link = get_field('contractor_link', 'option');
$copyright = get_field('copyright', 'option');
?>
</main>
<footer class="desktop_screen">
    <div class="container">
	    <section class="footer_top">
		    <div class="footer_block">
			    <div class="logo_block">
					<?php the_custom_logo(); ?>
					<span class="sub_logo">Твоя Врубная бухгалтерия</span>
				</div>
				<p class="addrr"><?= WT::$obj->contacts->getValue('address') ?></p>
				<p class="timework">Режим работы <?= WT::$obj->contacts->getValue('working_hours_time') ?></p>
				<p class="email"><?= WT::$obj->contacts->getValue('email') ?></p>
				<p class="phone"><?= WT::$obj->contacts->getValue('phone') ?></p>
			</div>
			<div class="footer_block footer_menu">
			    <div class="title_menu">Услуги</div>
				<nav class="footer__nav">
					<ul>
					<?php wp_nav_menu( array(
							'theme_location' => 'footer',
							'walker' => new Custom_Walker_Nav_Menu_Top
					)); ?>
					</ul>
				</nav>
			</div>
			<div class="footer_block footer_menu">
			    <div class="title_menu">О нас</div>
				<nav class="footer__nav">
					<ul>
					<?php wp_nav_menu( array(
							'theme_location' => 'footer1',
							'walker' => new Custom_Walker_Nav_Menu_Top
					)); ?>
					</ul>
				</nav>
			</div>
			<div class="footer_block footer_info">
			    <div class="title_menu">Информация</div>
				<div class="bank_det"><?= $bank_details ?></div>
				<a href="#" class="text_link">Политика конфиденциальности</a>
				<a href="#" class="text_link">Пользовательское соглашение</a>
			</div>
		</section>
		<section class="footer_bottom">
		    <div class="footer_block footer_podr">
			    <a href="<?= $contractor_link ?>" class="text"><?= $contractor ?></a>
			</div>
			<div class="footer_block footer_social">
				<div class="mess_block">
				    <?php foreach($socials as $item): ?> 
						<a href="<?= $item['info'] ?>" class="icon icon_mess main_icon_<?= $item['icon'] ?>"><?= $item['icon'] ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="footer_block footer_copy">
				<p class="text"><?= $copyright ?></p>
			</div>
		</section>
	</div>
</footer>
<footer class="mobile_screen">
    <div class="accordion footer_menu">
	    <div class="container">
			<div class="accordion__toggle"><span>Услуги</span></div>
			<ul class="accordion__content navbar-nav">
				<?php wp_nav_menu( array(
						'theme_location' => 'footer',
						'walker' => new Custom_Walker_Nav_Menu_Top
				)); ?>
			</ul>
		</div>
	</div>
	<div class="accordion footer_menu">
	    <div class="container">
			<div class="accordion__toggle"><span>О нас</span></div>
			<ul class="accordion__content navbar-nav">
				<?php wp_nav_menu( array(
						'theme_location' => 'footer1',
						'walker' => new Custom_Walker_Nav_Menu_Top
				)); ?>
			</ul>
		</div>
	</div>
	<div class="footer_infoblock">
	    <div class="container">
			<div class="footer_block">
			    <div class="logo_block">
					<?php the_custom_logo(); ?>
				</div>
				<p class="addrr"><?= WT::$obj->contacts->getValue('address') ?></p>
				<p class="timework">Режим работы: <?= WT::$obj->contacts->getValue('working_hours_days') ?> <?= WT::$obj->contacts->getValue('working_hours_time') ?></p>
				<p class="phone"><?= WT::$obj->contacts->getValue('phone') ?></p>
				<p class="email"><?= WT::$obj->contacts->getValue('email') ?></p>
			</div>
			<div class="footer_social">
				<div class="mess_block">
				    <?php foreach($socials as $item): ?> 
						<a href="<?= $item['info'] ?>" class="icon icon_mess main_icon_<?= $item['icon'] ?>"><?= $item['icon'] ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="bank_det"><?= $bank_details ?></div>
			<div class="footer_podr">
			    <a href="<?= $contractor_link ?>" class="text"><?= $contractor ?></a>
			</div>
			<div class="footer_copy">
				<p class="text"><?= $copyright ?></p>
			</div>
		</div>
	</div>
</footer>
<div style="display: none">
    <div id="callback" class="popup">
        <div class="popup-form">
		    <div class="title_block">Запишитесь на консультацию</div>
			<div class="subtitle_block">Подберем индивидуальные условия исходя из задачи</div>
			<div class="order_form">
				<?php echo do_shortcode('[contact-form-7 id="97" title="Бесплатный звонок"]')?>
			</div>	
        </div>
    </div>

</div>
<div style="display: none; width: 300px;" id="listcity">
	<div class="popup-form">
		<div class="title_block">Выберите город</div>
		<input type="text" class="text-field form-control search-control" name="search_location_name" id="search_location_name" placeholder="Введите название города">
		<?php $active_city = Wt::$geolocation->getValue('city'); ?>
		<div class="row-city" id="search_location_result">
		<?php 
		$columns = Wt::$obj->contacts->getRegionsArray(
			array(
				'columns' => 2,
				'pack' => 'country',
				'orderby' => 'menu_order title',
				'container_class'=>'row',
				'column_class'=>'col-12 col-md-6',
				'filter' => array(
					'type' => 'city'),
			)
		); ?>
			<?php foreach($columns as $column): ?>
			    <div class="col-city">
				    <?php foreach($column as $item): if($active_city == $item) $class="active"; else $class="";?>
				        <div class="item_city">
						<a class="<?= $class ?>" onclick="WtLocation.setCity('<?= $item ?>', 'reload');">
							<?php if($item == 'Москва' || $item == 'Санкт-Петербург' || $item == 'Екатеринбург') echo '<b>'; ?>
							<?= $item ?>
							<?php if($item == 'Москва' || $item == 'Санкт-Петербург' || $item == 'Екатеринбург') echo '</b>'; ?>
						</a></div>
				    <?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<script type="application/javascript">
    jQuery(function() {
        jQuery('#search_location_name').keypress(function () {
                searchLocation();
            }
        );

        jQuery('#search_location_button').click(function () {
                searchLocation();
            }
        );
    });
    // Поиск города на форме выбора региона
    function searchLocation() {
        searchLocationName = jQuery("#search_location_name").val();

        jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'search_location',
                    value: searchLocationName,
                    data_type: 'object'
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    search_location_result_html = '';

                    cities = JSON.parse(data);

                    for (key in cities) {
						var reload = 'reload';
						var result = "WtLocation.setCity('" + cities[key].post_title + "', '"+reload+"');";
                        search_location_result_html += '<div class="item_city">';
						search_location_result_html += '<a onclick="'+result+'">';
                        search_location_result_html += cities[key].post_title + '</a>';
						search_location_result_html += '</div>';
                    }

                    jQuery("#search_location_result").html(search_location_result_html);
                }
            }
        );
    }
</script>
<?php wp_footer(); ?>
</body>
</html>
