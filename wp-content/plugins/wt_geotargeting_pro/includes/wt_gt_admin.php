<?php


/**
 * Class WtGeoTargetingAdmin
 * Административная часть
 */
class WtGtAdmin extends WtGtAdminBehavior
{
	var $geobase_array = array(
		'ipgeobase_service' => 'IpGeoBase',
        'dadata_service' => 'DaData',
        'sypexgeo_service' => 'Sypex Geo',
		'maxmind_service' => 'MaxMind',
		'ipgeobase_and_maxmind_service' => 'Совместное использование IpGeoBase и MaxMind',
        'none' => 'Отключить'
	);

	var $debug_mode_array = array(
		0 => 'Отключен',
		'ip' => 'По заданному IP',
		'city' => 'По городу',
		'country' => 'По стране'
		);

	function __construct(){
		// Добавляем страницу настроек в панель администратора
	    add_action('admin_menu', array(&$this, 'adminMenu'));

	    //Добавляем в описание плагина ссылку на справку.
	    add_filter('plugin_row_meta', 'WtGtAdmin::pluginRowMeta', 10, 2);

	    add_action('admin_init', array(&$this, 'pluginSettings'));
	}

	/**
	 * Добавляем страницу настроек в панель администратора
     */
	function adminMenu()
	{
	    // Добавляем в сайдбар раздел геотаргетинга
	    add_menu_page(
	    	'WT GeoTargeting - Настройки', 
	    	'WT GeoTargeting', 
	    	'manage_options', 
	    	'wt_geotargeting', 
	    	'',
	    	'dashicons-location-alt'
	    );

	    // В первом пункте вложенного меню дублируем slug с главного пункта меню, дабы избежать дублей
	    add_submenu_page(
	    	'wt_geotargeting', 
	    	'WT GeoTargeting - Инструкция', 
	    	'Справка', 
	    	'manage_options', 
	    	'wt_geotargeting',
	        array(&$this, 'adminPageReference')
	    );

	    add_submenu_page(
	    	'wt_geotargeting', 
	    	'WT GeoTargeting - Настройки', 
	    	'Настройки', 
	    	'manage_options', 
	    	'wt_geotargeting/admin/setting.php',
	        array(&$this, 'optionsPageOutput')
	    );
	}

	/**
	 * Страница с инструкцией
     */
	function adminPageReference()
	{
		include(WT_GT_PRO_PLUGIN_DIR . '/templates/admin/reference.php');
	}

	/**
	 * Добавление ссылок к описанию плагина
	 *
	 * @param $meta
	 * @param $file
	 * @return array
     */
	public static function pluginRowMeta($meta, $file) {
        if ($file == WT_GT_PRO_PLUGIN_BASENAME) {
        	// Ссылка на страницу справки
            $meta[] = '<a href="options-general.php?page=wt_geotargeting">Как настроить геотаргетинг?</a>';
        }
        return $meta;
    }

    // ---------- НАСТРОЙКА ПЛАГИНА ----------

    /**
	 * Создаем страницу настроек плагина
	 */
	function optionsPageOutput(){
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
					settings_fields( 'wt_geotargeting_group' );     // скрытые защитные поля
					do_settings_sections( 'wt_geotargeting_geobase_page' ); // секции с настройками (опциями).
					do_settings_sections( 'wt_geotargeting_default_page' ); // секции с настройками (опциями).
					do_settings_sections( 'wt_geotargeting_debug_page' ); // секции с настройками (опциями).
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Регистрируем настройки.
	 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
	 */
	function pluginSettings(){
		
		$this->dataFilesInit(); // Подключаем справочники

        $country_alpha2_items = $this->data->getCountriesForSelect();
        $country_alpha2_items = array_merge(array('' => 'Неизвестно'), $country_alpha2_items);

        $city_items = $this->data->getCities();
        $city_items = array_merge(array('' => 'Неизвестно'), $city_items);

		// $option_group, $option_name, $sanitize_callback
		register_setting('wt_geotargeting_group', 'wt_geotargeting_geobase', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_group', 'wt_geotargeting_region', array(&$this, 'sanitizeCallback'));
        register_setting('wt_geotargeting_group', 'wt_geotargeting_sistem', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_group', 'wt_geotargeting_debug', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_group', 'wt_geotargeting_default', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_geobase',
			'',
			'',
			'wt_geotargeting_geobase_page');

		$field_params = array(
			'type'      => 'select',
			'id'        => 'base_name',
			'option_name' => 'wt_geotargeting_geobase',
			'label_for' => 'base_name',
			'vals'		=> $this->geobase_array,
			'desc'      => 'Выберите сервис для определения месторасположения посетителя сайта'
		);
		add_settings_field('name', 'Сервис геолокации', array(&$this, 'displaySettings'), 'wt_geotargeting_geobase_page', 'wt_geotargeting_geobase', $field_params);

        $field_params = array(
            'type'      => 'text', // тип
            'id'        => 'dadata_api_key',
            'option_name' => 'wt_geotargeting_geobase',
            'label_for' => 'dadata_api_key'
        );
        add_settings_field('dadata_api_key', 'DaData: API key', array(&$this, 'displaySettings'), 'wt_geotargeting_geobase_page', 'wt_geotargeting_geobase', $field_params);

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'maxmind_user_id',
			'option_name' => 'wt_geotargeting_geobase',
			'label_for' => 'maxmind_user_id'
		);
		add_settings_field('maxmind_user_id', 'MaxMind: User ID', array(&$this, 'displaySettings'), 'wt_geotargeting_geobase_page', 'wt_geotargeting_geobase', $field_params);

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'maxmind_license_key',
			'option_name' => 'wt_geotargeting_geobase',
			'label_for' => 'maxmind_license_key'
		);
		add_settings_field('maxmind_license_key', 'MaxMind: License key', array(&$this, 'displaySettings'), 'wt_geotargeting_geobase_page', 'wt_geotargeting_geobase', $field_params);

		$field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'deactivate_auto_set_region_from_cookie',
			'option_name' => 'wt_geotargeting_region',
			'label_for' => 'deactivate_auto_set_region_from_cookie',
			'desc'      => 'Отключить автоматическую установку региона на основе данных из cookie',
		);
		add_settings_field('deactivate_auto_set_region_from_cookie', '', array(&$this, 'displaySettings'), 'wt_geotargeting_geobase_page', 'wt_geotargeting_geobase', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'deactivate_save_region_from_cookie',
            'option_name' => 'wt_geotargeting_sistem',
            'label_for' => 'deactivate_save_region_from_cookie',
            'desc'      => 'Отключить сохранение в cookie текущего региона и всех характеристик (meta-переменных)',
        );
        add_settings_field('deactivate_save_region_from_cookie', '', array(&$this, 'displaySettings'), 'wt_geotargeting_geobase_page', 'wt_geotargeting_geobase', $field_params);


		add_settings_section(
			'wt_geotargeting_default',
			'Региональные значения по умолчанию',
			array(&$this, 'displaySettingSectionDefaultInfo'),
			'wt_geotargeting_default_page');

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'city',
			'option_name' => 'wt_geotargeting_default',
			'label_for' => 'city'
		);
		add_settings_field('city', 'Город', array(&$this, 'displaySettings'), 'wt_geotargeting_default_page', 'wt_geotargeting_default', $field_params);

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'region',
			'option_name' => 'wt_geotargeting_default',
			'label_for' => 'region'
		);
		add_settings_field( 'region', 'Регион', array(&$this, 'displaySettings'), 'wt_geotargeting_default_page', 'wt_geotargeting_default', $field_params );

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'district',
			'option_name' => 'wt_geotargeting_default',
			'label_for' => 'district'
		);
		add_settings_field( 'district', 'Округ', array(&$this, 'displaySettings'), 'wt_geotargeting_default_page', 'wt_geotargeting_default', $field_params );

		$field_params = array(
			'type'      => 'select',
			'id'        => 'country',
			'option_name' => 'wt_geotargeting_default',
			'vals'		=> $country_alpha2_items
		);
		add_settings_field( 'country', 'Страна посетителя', array(&$this, 'displaySettings'), 'wt_geotargeting_default_page', 'wt_geotargeting_default', $field_params );



		// $id, $title, $callback, $page
		add_settings_section(
			'wt_geotargeting_debug',
			'Тестирование и отладка',
			array(&$this, 'displaySettingSectionDebugInfo'),
			'wt_geotargeting_debug_page');

		$field_params = array(
			'type'      => 'select', // тип
			'id'        => 'mode',
			'option_name' => 'wt_geotargeting_debug',
			'desc'      => 'Выберите режим.', // описание
			'vals' => $this->debug_mode_array
			);
		add_settings_field( 'mode', 'Режим тестирования', array(&$this, 'displaySettings'), 'wt_geotargeting_debug_page', 'wt_geotargeting_debug', $field_params );
	 

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'ip',
			'option_name' => 'wt_geotargeting_debug',
			'desc'      => 'Введите IP-адрес посетителя.', // описание
			'label_for' => 'ip' // позволяет сделать название настройки лейблом (если не понимаете, что это, можете не использовать), по идее должно быть одинаковым с параметром id
		);
		add_settings_field( 'ip', 'IP-адрес посетителя', array(&$this, 'displaySettings'), 'wt_geotargeting_debug_page', 'wt_geotargeting_debug', $field_params );

		$field_params = array(
			'type'      => 'select',
			'id'        => 'city_id',
			'option_name' => 'wt_geotargeting_debug',
			'desc'      => 'Выберите город посетителя.',
			'vals'		=> $city_items
		);
		add_settings_field( 'city_id', 'Город посетителя', array(&$this, 'displaySettings'), 'wt_geotargeting_debug_page', 'wt_geotargeting_debug', $field_params );

		$field_params = array(
			'type'      => 'select',
			'id'        => 'country_alpha2',
			'option_name' => 'wt_geotargeting_debug',
			'desc'      => 'Выберите страну посетителя.',
			'vals'		=> $country_alpha2_items
		);
		add_settings_field( 'country_alpha2', 'Страна посетителя', array(&$this, 'displaySettings'), 'wt_geotargeting_debug_page', 'wt_geotargeting_debug', $field_params );

		// Обновление файла с контактной информацией с мультисайта
		add_settings_field(
			'multisite_contacts_update',
			'Мультисайт',
			array(&$this, 'displaySettingButtonUpdateContactsMultisite'),
			'wt_geotargeting_debug_page',
			'wt_geotargeting_debug',
			array() );

		add_action(	 // Добавление скрипта для обработки нажатия кнопки
			'admin_print_footer_scripts',
			array(&$this, 'javascriptUpdateContactsMultisite'),
			99);

		add_action( // Регистрируем функцию для обработки ajax запроса
			'wp_ajax_update_contacts_multisite',
			array(&$this, 'callbackUpdateContactsMultisite'));
	}

	/*
	 * Функция отображения полей ввода
	 * Здесь задаётся HTML и PHP, выводящий поля
	 */

	/**
	 * Поясняющее сообщение для секции "Значения по умолчанию"
     */
	function displaySettingSectionDefaultInfo(){
		echo '<p>Указанные вами значения "По умолчанию" будут использоваться в случае отсутствия в базе "IpGeoBase" данных о местоположении посетителя.</p>';
	}

	/**
	 * Поясняющее сообщение для секции тестирования и отладки
     */
	function displaySettingSectionDebugInfo(){
		echo '<p>Воспользовавшись нижепредставленными полями вы можете протестировать работу сайта от лица пользователей из других регионов.<br>Тестирование возможно только администратором сайта.</p>';
	}

	/**
	 * Кнопка "Обновить контактную информацию с сайтов"
     */
	function displaySettingButtonUpdateContactsMultisite(){
		echo '<button type="button" onclick="click_update_contacts_multisite();">Обновить контактную информацию с сайтов</button>';
	}

	function javascriptUpdateContactsMultisite() {
		?>
		<script type="text/javascript" >
			function click_update_contacts_multisite() {
				var data = {
					action: 'update_contacts_multisite',
					whatever: 1234
				};
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post( ajaxurl, data, function(response) {
					alert(response);
				});
			}
		</script>
		<?php
	}

	/**
	 * Выборка контактных данных с мультисайтов
     */
	function callbackUpdateContactsMultisite() {
		$contacts = array();

		$sites = wp_get_sites();

		$site_count = 0;
		foreach ($sites as $site){
			switch_to_blog($site['blog_id']);

			$site_contacts = (array) get_option('wt_contacts', array());

			if (empty($site_contacts['region'])) continue;

			$site_contacts['blog_id'] = $site['blog_id'];
			$contacts[$site_contacts['region']] = $site_contacts;

			$site_count++;
		}

		$file_contacts = json_encode($contacts);

		// Определяем каталог и создаем в нем файл
		$new_file = WP_CONTENT_DIR.'/uploads/multisite_geo_info.txt';
		wp_mkdir_p(dirname($new_file));

		// Открываем созданный файл для записи и сохраняем в него данные
		$ifp = @ fopen( $new_file, 'wb' );
		@fwrite( $ifp, $file_contacts);
		fclose( $ifp );
		clearstatcache();

		echo 'Информация успешно сохранена. '.$site_count.' мультисайт.';

		wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
	}
}
?>