<?php

/**
 * Class WtGtSeoAdmin
 */
class WtGtSeoAdmin extends WtGtAdminBehavior
{
	function __construct(){
        add_action('admin_menu', array(&$this, 'menu'));
        add_action('admin_init', array(&$this, 'settingsRegister'));

        // Проверка активации модуля субдомена
        $robots_txt_enable = Wt::$obj->seo->getSetting('robots_txt_enable');
        if (empty($robots_txt_enable)) return;

		add_filter('wt_gt_region_meta_fields_scheme', array($this, 'filterRegionMetaFieldsScheme'));
	}

    public function menu(){
        add_submenu_page(
            'wt_geotargeting',
            'WT GeoTargeting - Настройки robots.txt',
            'robots.txt',
            'manage_options',
            'robots_txt',
            array(&$this, 'optionsPageOutput')
        );
    }

    // ---------- НАСТРОЙКА ----------

    /**
     * Регистрируем настройки.
     * Настройки будут храниться в массиве, а не одна настройка = одна опция.
     */
    function settingsRegister(){
        // $option_group, $option_name, $sanitize_callback
        register_setting('wt_geotargeting_technical_robots_group', 'wt_geotargeting_technical', array(&$this, 'sanitizeCallback'));
        register_setting('wt_geotargeting_technical_robots_group', 'wt_geotargeting_technical_not_exist', array(&$this, 'sanitizeCallback'));

        add_settings_section(
            'wt_geotargeting_technical',
            '',
            '',
            'wt_geotargeting_technical_page');

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'robots_txt_enable',
            'option_name' => 'wt_geotargeting_technical',
            'label_for' => 'robots_txt_enable',
            'desc'      => 'Активировать генерацию robots.txt',
        );
        add_settings_field('robots_txt_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_page', 'wt_geotargeting_technical', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'robots_txt_rewrite',
            'option_name' => 'wt_geotargeting_technical',
            'label_for' => 'robots_txt_rewrite',
            'desc'      => 'Перезаписать robots.txt',
        );
        add_settings_field('robots_txt_rewrite', '', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_page', 'wt_geotargeting_technical', $field_params);

        $field_params = array(
            'type'      => 'textarea', // тип
            'id'        => 'robots_txt_all',
            'option_name' => 'wt_geotargeting_technical',
            'label_for' => 'robots_txt_all',
            'desc'      => 'Напишите код, который необходимо отобразить во всех файлах robots.txt',
        );
        add_settings_field('robots_txt_all', 'Код для всех файлов', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_page', 'wt_geotargeting_technical', $field_params);

        $field_params = array(
            'type'      => 'textarea', // тип
            'id'        => 'robots_txt_main',
            'option_name' => 'wt_geotargeting_technical',
            'label_for' => 'robots_txt_main',
            'desc'      => 'Напишите код, который необходимо отобразить только в файле robots.txt на сайте основного доменного имени',
        );

        add_settings_field('robots_txt_main', 'Код для основного домена', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_page', 'wt_geotargeting_technical', $field_params);

        $field_params = array(
            'type'      => 'text', // тип
            'id'        => 'robots_txt_sitemap_path',
            'option_name' => 'wt_geotargeting_technical',
            'label_for' => 'robots_txt_sitemap_path',
            'desc'      => 'Укажите относительный путь к файлу Sitemap.',
            'placeholder' => '/sitemap.xml'
        );
        add_settings_field('robots_txt_sitemap_path', 'Путь к файлу Sitemap', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_page', 'wt_geotargeting_technical', $field_params);
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
                settings_fields('wt_geotargeting_technical_robots_group');     // скрытые защитные поля
                do_settings_sections('wt_geotargeting_technical_page'); // секции с настройками (опциями).
                do_settings_sections('wt_geotargeting_technical_not_exist_page'); // секции с настройками (опциями).
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

	function filterRegionMetaFieldsScheme($meta_fields_scheme){
		$meta_fields_scheme_subdomain = array(
			'technical' => array(
                'label' => 'Технические',
                'icon'  => 'dashicons-editor-code',
				'fields' => array(
					'robots_txt' => array(
						'type'    => 'textarea',
						'label'   => 'robots.txt',
						'description' => 'Дополнительные настройки файла robots.txt'
					),
                    'footer_code' => array(
                        'type'    => 'textarea',
                        'label'   => 'Footer code',
                        'description' => 'Код, добавляемый в конце страницы сайта'
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