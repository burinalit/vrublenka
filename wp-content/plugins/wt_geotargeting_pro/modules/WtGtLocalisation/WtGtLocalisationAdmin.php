<?php

/**
 * Class WtGtLocalisationAdmin
 */
class WtGtLocalisationAdmin extends WtGtAdminBehavior
{
	function __construct(){
		add_action('admin_menu', array(&$this, 'menu'));
		add_action('admin_init', array(&$this, 'settingsRegister'));

        // Регистрация скриптов для админки
        add_action('admin_enqueue_scripts', array($this, 'registerAdminScripts'));

        // Проверка активации модуля субдомена
        if (!Wt::$obj->localisation->isEnable()) return;

        // Добавление мета-бокса
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'), 10, 2);
	}

	function registerAdminScripts(){
        wp_register_script(
            'wt-localisation-admin',
            plugin_dir_url(WT_GT_PRO_PLUGIN_FILE) . '/modules/WtGtLocalisation/wt-localisation-admin.js',
            array('jquery'),
            '1.0');
        wp_enqueue_script('wt-localisation-admin');
    }

	public function menu(){
		add_submenu_page(
			'wt_geotargeting',
			'WT GeoTargeting - Локализация',
			'Локализация',
			'manage_options',
			'wt-gt-localisation',
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
		register_setting('wt_geotargeting_localisation_group', 'wt_geotargeting_localisation', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_localisation_group', 'wt_geotargeting_localisation_not_exist', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_localisation',
			'',
			'',
			'wt_geotargeting_localisation_page');

		$field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'localisation_enable',
			'option_name' => 'wt_geotargeting_localisation',
			'label_for' => 'localisation_enable',
			'desc'      => 'Активировать локализацию',
		);
		add_settings_field('localisation_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_localisation_page', 'wt_geotargeting_localisation', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'polylang_enable',
            'option_name' => 'wt_geotargeting_localisation',
            'label_for' => 'polylang_enable',
            'desc'      => 'Активировать поддержку плагина <a href="https://ru.wordpress.org/plugins/polylang/" target="_blank">Polylang</a>',
        );
        add_settings_field('polylang_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_localisation_page', 'wt_geotargeting_localisation', $field_params);

	}

	/**
	 * Создаем страницу настроек
	 */
	public function optionsPageOutput(){
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>
			<form action="options.php" method="POST">
				<?php
				settings_fields('wt_geotargeting_localisation_group');     // скрытые защитные поля
				do_settings_sections('wt_geotargeting_localisation_page'); // секции с настройками (опциями).
				do_settings_sections('wt_geotargeting_localisation_not_exist_page'); // секции с настройками (опциями).
                ?>

                <?php $site_language = get_bloginfo('language'); ?>
                <p>Язык сайта (<?php echo $site_language; ?>) является основным языком регионов. </p>

                <?php
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

    function add_meta_boxes(){
        $post_types = array('region');

        foreach ($post_types as $screen) {
            add_meta_box(
                'localisation-metabox',
                'Локализация',
                array($this, 'add_meta_box_callback'),
                $screen,
                'normal', // advanced, normal, side
                'core'
            );
        }
    }

    public function add_meta_box_callback($post) {
        wp_nonce_field( 'wt_data_localisation', 'wt_nonce_localisation' );

        $languages_list = Wt::$obj->localisation->getLanguagesList();

        $active_locale = determine_locale();

        unset($languages_list[$active_locale]);

        echo '<h3>Текущий язык: ' . $active_locale . '</h3>';


        echo '<h3>Переводы:</h3>';

        $localisations = array();

        foreach ($languages_list as $key => $value){
            $localisations[$key] = get_post_meta($post->ID, $key, true);
        }

        echo '<table class="widefat importers striped">';
        foreach ($localisations as $language => $localisation){
            if (empty($localisation)) continue;



            foreach ($localisation as $key => $value){
                echo '<tr>';
                echo '<td><b>' . $language . '</b></td><td>' . $value . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';

        echo '<h3>Новый перевод названия региона:</h3>';

        echo '<select name="wt_localisation_language">';
        echo '<option value="">Выбрерите язык</option>';
        foreach ($languages_list as $key => $value){
            echo '<option value="' . $key . '">' . $value . '</option>';
        }
        echo '<select>';

        echo '<input type="hidden" name="wt_localisation_meta_name" value="region_name">';
        echo '<input type="text" name="wt_localisation_meta_value">';

        echo '<p>Для сохранения нового значения нажмите кнопку "Обновить"</p>';
//        echo '<button>Добавить</button>';
    }

    public function save_post($post_id, $post) {
        if (!isset( $_POST['wt_nonce_localisation'])) return $post_id;

        $nonce = $_POST['wt_nonce_localisation'];
        if ( !wp_verify_nonce( $nonce, 'wt_data_localisation' ) )
            return $post_id;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        $localisations = array();

        $localisation_language = $_POST['wt_localisation_language'];
        $localisation_meta_name = $_POST['wt_localisation_meta_name'];
        $localisation_meta_value = $_POST['wt_localisation_meta_value'];

        if (!empty($localisation_language) && !empty($localisation_language)&& !empty($localisation_language)){
            $localisations[$localisation_language][$localisation_meta_name] = $localisation_meta_value;
        }

        foreach ($localisations as $key => $value){
            update_post_meta($post_id, $key, $value);
        }
    }
}
?>
