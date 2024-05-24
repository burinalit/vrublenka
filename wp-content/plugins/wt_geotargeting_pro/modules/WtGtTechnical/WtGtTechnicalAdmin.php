<?php

class WtGtTechnicalAdmin extends WtGtAdminBehavior
{
	function __construct(){
        add_action('admin_menu', array(&$this, 'menu'));
        add_action('admin_init', array(&$this, 'settingsRegister'));
        	}

    public function menu(){
        add_submenu_page(
            'wt_geotargeting',
            'WT GeoTargeting - Технические настройки',
            'Технические',
            'manage_options',
            'technical',
            array(&$this, 'optionsPageOutput')
        );
    }

    // ---------- НАСТРОЙКА ----------

    /**
     * Регистрируем настройки.
     * Настройки будут храниться в массиве, а не одна настройка = одна опция.
     */
    function settingsRegister(){
        register_setting('wt_geotargeting_technical_group', 'wt_geotargeting_technical_javascript', array(&$this, 'sanitizeCallback'));

        add_settings_section(
            'wt_geotargeting_technical_javascript',
            'Поддержка JavaScript',
            '',
            'wt_geotargeting_technical_javascript_page');

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'javascript_wt_location_disable',
            'option_name' => 'wt_geotargeting_technical_javascript',
            'label_for' => 'javascript_wt_location_disable',
            'desc'      => 'Отключить скрипт WtLocation',
        );

        add_settings_field(
            'javascript_wt_location_disable',
            '',
            array(&$this, 'displaySettings'),
            'wt_geotargeting_technical_javascript_page',
            'wt_geotargeting_technical_javascript',
            $field_params
        );

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'javascript_cookie_disable',
            'option_name' => 'wt_geotargeting_technical_javascript',
            'label_for' => 'javascript_cookie_disable',
            'desc'      => 'Отключить скрипт для чтения/сохранения Cookie',
        );

        add_settings_field(
            'javascript_cookie_disable',
            '',
            array(&$this, 'displaySettings'),
            'wt_geotargeting_technical_javascript_page',
            'wt_geotargeting_technical_javascript',
            $field_params
        );




        register_setting('wt_geotargeting_technical_group', 'wt_geotargeting_technical_yoast_seo', array(&$this, 'sanitizeCallback'));

        add_settings_section(
            'wt_geotargeting_technical_yoast_seo',
            'Совместимость с плагином Yoast SEO',
            '',
            'wt_geotargeting_technical_yoast_seo_page');

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'yoast_seo_canonical_disable',
            'option_name' => 'wt_geotargeting_technical_yoast_seo',
            'label_for' => 'yoast_seo_canonical_disable',
            'desc'      => 'Отключить каноническую ссылку Yoast SEO',
        );

        add_settings_field(
                'yoast_seo_canonical_disable',
                'Отключить canonical',
                array(&$this, 'displaySettings'),
                'wt_geotargeting_technical_yoast_seo_page',
                'wt_geotargeting_technical_yoast_seo',
                $field_params
        );

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'yoast_seo_canonical_rewrite',
            'option_name' => 'wt_geotargeting_technical_yoast_seo',
            'label_for' => 'yoast_seo_canonical_rewrite',
            'desc'      => 'Перезаписать каноническую ссылку Yoast SEO',
        );

        add_settings_field(
            'yoast_seo_canonical_rewrite',
            'Перезапись canonical',
            array(&$this, 'displaySettings'),
            'wt_geotargeting_technical_yoast_seo_page',
            'wt_geotargeting_technical_yoast_seo',
            $field_params
        );


        register_setting('wt_geotargeting_technical_group', 'wt_geotargeting_technical_locations', array(&$this, 'sanitizeCallback'));

        add_settings_section(
            'wt_geotargeting_technical_locations',
            'Справочник локаций',
            '',
            'wt_geotargeting_technical_locations_page');

        $field_params = array(
            'type' => 'select',
            'id' => 'locations_source',
            'option_name' => 'wt_geotargeting_technical_locations',
            'label_for' => 'locations_source',
            'vals' => array(
                'post_type_region' => 'Регионы - произвольная запись (region)',
                'table_location_dpd' => 'Таблица локаций DPD'
            ),
        );
        add_settings_field('locations_source', 'Источник', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_locations_page', 'wt_geotargeting_technical_locations', $field_params);


        register_setting('wt_geotargeting_technical_group', 'wt_geotargeting_technical_deactivation', array(&$this, 'sanitizeCallback'));

        add_settings_section(
            'wt_geotargeting_technical_deactivation',
            'Справочник локаций',
            '',
            'wt_geotargeting_technical_deactivation_page');

        $field_params = array(
            'type' => 'checkbox',
            'id' => 'deactivation_post_region_delete_enable',
            'option_name' => 'wt_geotargeting_technical_deactivation',
            'label_for' => 'deactivation_post_region_delete_enable',
            'desc'      => 'Удалить все регионы при деактивации плагина',
        );
        add_settings_field('deactivation_post_region_delete_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_technical_deactivation_page', 'wt_geotargeting_technical_deactivation', $field_params);

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
                settings_fields('wt_geotargeting_technical_group');     // скрытые защитные поля
                do_settings_sections('wt_geotargeting_technical_javascript_page'); // секции с настройками (опциями).
                do_settings_sections('wt_geotargeting_technical_yoast_seo_page');
                do_settings_sections('wt_geotargeting_technical_locations_page');
                do_settings_sections('wt_geotargeting_technical_deactivation_page');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}