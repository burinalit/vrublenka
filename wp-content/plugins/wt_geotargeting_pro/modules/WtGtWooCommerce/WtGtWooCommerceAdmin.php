<?php

/**
 * Class WtGtWooCommerceAdmin
 */
class WtGtWooCommerceAdmin extends WtGtAdminBehavior
{
	function __construct(){
		add_action('admin_menu', array(&$this, 'menu'));
		add_action('admin_init', array(&$this, 'settingsRegister'));

        // Проверка активации модуля
        $woocommerce_enable = Wt::$obj->woocommerce->getSetting('woocommerce_enable');
        if (empty($woocommerce_enable)) return;

        // Регистрация скриптов для админки
        add_action('admin_enqueue_scripts', array($this, 'registerAdminScripts'));

        add_filter('woocommerce_product_options_pricing', array($this, 'action_product_options_pricing')); // Добавляем поля на страницу редактирования товара
        add_filter('woocommerce_product_options_stock_fields', array($this, 'action_product_options_stock_fields')); // Добавляем поля на страницу редактирования товара
        add_filter('woocommerce_process_product_meta', array($this, 'action_process_product_meta'), 10, 2); // Сохраняем добавленные поля

        add_filter('wt_gt_region_meta_fields_scheme', array($this, 'filterRegionMetaFieldsScheme'));

        //add_action('add_meta_boxes', array($this, 'add_meta_boxes')); // Метабокс для отладки
	}

	function registerAdminScripts(){
        wp_register_script(
            'wt-woocommerce-admin',
            plugin_dir_url(WT_GT_PRO_PLUGIN_FILE) . '/modules/WtGtWooCommerce/wt-woocommerce-admin.js',
            array('jquery'),
            '1.0');
        wp_enqueue_script('wt-woocommerce-admin');
    }

	public function menu(){
		add_submenu_page(
			'wt_geotargeting',
			'WT GeoTargeting - WooCommerce',
			'WooCommerce',
			'manage_options',
			'wt-gt-woocommerce',
			array(&$this, 'optionsPageOutput')
		);
	}

    function add_meta_boxes(){
        $post_types = array('product');

        foreach ($post_types as $screen) {
            add_meta_box(
                'debugging-metabox',
                'Debugging',
                array($this, 'add_meta_box_callback'),
                $screen,
                'advanced', // normal, advanced, side.
                'core'
            );
        }
    }

    public function add_meta_box_callback($post) {

        $terms = get_the_terms($post->ID, 'product_visibility');

        var_dump($terms);

        $meta_values = get_post_meta($post->ID);

        var_dump($meta_values);

    }

	// ---------- НАСТРОЙКА ----------

	/**
	 * Регистрируем настройки.
	 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
	 */
	function settingsRegister(){
		// $option_group, $option_name, $sanitize_callback
		register_setting('wt_geotargeting_woocommerce_group', 'wt_geotargeting_woocommerce', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_woocommerce_group', 'wt_geotargeting_woocommerce_not_exist', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_woocommerce',
			'',
			'',
			'wt_geotargeting_woocommerce_page');

		$field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'woocommerce_enable',
			'option_name' => 'wt_geotargeting_woocommerce',
			'label_for' => 'woocommerce_enable',
			'desc'      => 'Активировать поддержку WooCommerce',
		);
		add_settings_field('woocommerce_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_woocommerce_page', 'wt_geotargeting_woocommerce', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'default_address_fields_enable',
            'option_name' => 'wt_geotargeting_woocommerce',
            'label_for' => 'default_address_fields_enable',
            'desc'      => 'Активировать заполнение полей Страна/Регион/Город значениями активного региона',
        );
        add_settings_field('default_address_fields_enable', 'Оформление заказа', array(&$this, 'displaySettings'), 'wt_geotargeting_woocommerce_page', 'wt_geotargeting_woocommerce', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'product_visibility_location_terms_enable',
            'option_name' => 'wt_geotargeting_woocommerce',
            'label_for' => 'product_visibility_location_terms_enable',
            'desc'      => 'Активировать взаимосвязь отображения товара с региональными запасами',
        );
        add_settings_field('product_visibility_location_terms_enable', 'Фильтрация товара', array(&$this, 'displaySettings'), 'wt_geotargeting_woocommerce_page', 'wt_geotargeting_woocommerce', $field_params);

    }

	/**
	 * Создаем страницу настроек публикаций
	 */
	public function optionsPageOutput(){
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
				settings_fields('wt_geotargeting_woocommerce_group');     // скрытые защитные поля
				do_settings_sections('wt_geotargeting_woocommerce_page'); // секции с настройками (опциями).
				do_settings_sections('wt_geotargeting_woocommerce_not_exist_page'); // секции с настройками (опциями).
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

    function sanitizeCallback($sanitize_value){

        if ($sanitize_value['product_visibility_location_terms_enable'] == 'on') {
            // Создаём термины таксономии
            $locations = Wt::$obj->location->getObjects(array('fields' => 'ids'));

            foreach ($locations as $location_id){
                $outofstock_location_term = get_term_by('slug', 'outofstock_location_' . $location_id, 'product_visibility');

                if (empty($outofstock_location_term)) $outofstock_location_term = wp_insert_term('outofstock_location_' . $location_id, 'product_visibility');
            }
        }else{
            // Удаляем термины таксономии
            $product_visibility_terms = get_terms(
                array(
                    'taxonomy'   => 'product_visibility',
                    'hide_empty' => false,
                )
            );

            foreach ($product_visibility_terms as $term){
                $parse = explode("outofstock_location_", $term->slug);
                if (empty($parse[1])) continue;

                wp_delete_term($term->term_id, 'product_visibility');
            }
        }

        return $sanitize_value;
    }

	public function action_product_options_pricing(){
        global $product_object;

        //$price_locations = array();

        $product_meta = get_post_meta($product_object->get_id());

        foreach ($product_meta as $key => $values){
            $price_type = null;
            $label = null;

            $parse = explode("_regular_price_location_", $key);
            if (!empty($parse[1])) {
                $price_type = 'regular';
                $label = 'Базовая цена';
            }
            else $parse = explode("_sale_price_location_", $key);

            if (empty($price_type) && !empty($parse[1])) {
                $price_type = 'sale';
                $label = 'Цена распродажи';
            }elseif (empty($parse[1])) continue;

            //$price_locations[$parse[1]] = $values[0];

            $location_name = WtGtLocation::getNameById($parse[1]);

            woocommerce_wp_text_input(
                array(
                    'id'        => $key,
                    'value'     => $values[0],
                    'label'     => $label . ' / '  . $location_name,
                    'data_type' => 'price',
                    //'description' => '<a href="#" class="">Удалить</a>',
                )
            );
        }

        $locations = WtGtLocation::getObjects(array('orderby' => 'post_title'));

        echo '<div id="product_price_locations"></div>';

        echo '<p class="form-field">';
        echo '<label for="product_add_price_location"><b>Добавить цену</b></label>';
        echo '<select id="product_add_price_type_location" style="margin-right: 10px;">';
        echo '<option value="regular">Базовая</option>';
        echo '<option value="sale">Распродажа</option>';
        echo '</select>';
        echo '<select id="product_add_price_location" style="margin-right: 10px;">';
        foreach ($locations as $location){
            //if (array_key_exists($location->ID, $price_locations)) continue;
            echo '<option value="' . $location->ID . '">' . $location->post_title . '</option>';
        }
        echo '</select>';
        echo '<button type="button" class="button" onclick="product_add_location_price()">Добавить</button>';
        echo '</p>';
    }

    public function action_product_options_stock_fields(){
        global $product_object;

        $stock_locations = array();

        $product_meta = get_post_meta($product_object->get_id());

        foreach ($product_meta as $key => $values){
            $parse = explode("_stock_location_", $key);
            if (empty($parse[1])) continue;

            $stock_locations[$parse[1]] = $values[0];

            $location_name = WtGtLocation::getNameById($parse[1]);

            woocommerce_wp_text_input(
                array(
                    'id'        => $key,
                    'value'     => $values[0],
                    'label'     => 'Запасы: '  . $location_name,
                    'type'      => 'number',
                    'data_type' => 'stock',
                    //'description' => '<a href="#" class="">Удалить</a>',
                )
            );
        }

        $locations = WtGtLocation::getObjects(array('orderby' => 'post_title'));

        echo '<div id="product_stock_locations"></div>';

        echo '<p class="form-field">';
        echo '<label for="product_add_stock_location"><b>Добавить запасы в</b></label>';
        echo '<select id="product_add_stock_location" style="margin-right: 10px;">';
        foreach ($locations as $location){
            if (array_key_exists($location->ID, $stock_locations)) continue;

            echo '<option value="' . $location->ID . '">' . $location->post_title . '</option>';
        }
        echo '</select>';
        echo '<button type="button" class="button" onclick="product_add_location_stock()">Добавить</button>';
        echo '</p>';
    }

    public function action_process_product_meta($post_id){

        foreach ($_POST as $key => $values){
            $parse = explode("_regular_price_location_", $key);

            if (empty($parse[1])) $parse = explode("_sale_price_location_", $key);
            if (empty($parse[1])) $parse = explode("_stock_location_", $key);

            if (empty($parse[1])) continue;

            if (isset($values) && $values != ''){
                update_post_meta($post_id, $key, $values);
                continue;
            }

            delete_post_meta($post_id, $key);
        }
    }

    function filterRegionMetaFieldsScheme($meta_fields_scheme){
        $meta_fields_scheme_subdomain = array(
            'woocommerce' => array(
                'label' => 'WooCommerce',
                'icon'  => 'dashicons-cart',
                'fields' => array(
                    'regular_price_coefficient' => array(
                        'type'    => 'text',
                        'label'   => 'Коэффициент базовой цены',
                        'description' => 'Коэффициент применяемый ко всем базовым ценам',
                        'placeholder' => '1'
                    ),
                )
            )
        );

        $meta_fields_scheme = array_merge_recursive(
            $meta_fields_scheme,
            $meta_fields_scheme_subdomain
        );

        return $meta_fields_scheme;
    }
}
