<?php

class WtGtPostAdmin extends WtGtAdminBehavior
{
	function __construct(){
		// Добавляем страницу настроек в панель администратора
		add_action('admin_menu', array(&$this, 'menu'));
		add_action('admin_init', array(&$this, 'settingsRegister'));

		add_filter('wt_gt_region_meta_fields_scheme', array($this, 'filterRegionMetaFieldsSchemeAddSpelling'));
	}

	public function menu(){
		add_submenu_page(
			'wt_geotargeting',
			'WT GeoTargeting - Настройка страниц',
			'Страницы',
			'manage_options',
			'posts',
			array(&$this, 'optionsPageOutput')
		);
	}

	// ---------- НАСТРОЙКА ПУБЛИКАЦИЙ ----------

	/**
	 * Регистрируем настройки.
	 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
	 */
	function settingsRegister(){

		register_setting('wt_geotargeting_post_group', 'wt_geotargeting_post', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_post',
			'',
			'',
			'wt_geotargeting_posts_page');

		$field_params = array(
			'type'      => 'text',
			'id'        => 'support_select_region',
			'option_name' => 'wt_geotargeting_post',
			'label_for' => 'support_select_region',
			'desc'      => 'Перечислите типы публикаций, для которых необходимо включить настройки видимости.'
		);
		add_settings_field('support_select_region', 'Поддержка настроек видимости', array(&$this, 'displaySettings'), 'wt_geotargeting_posts_page', 'wt_geotargeting_post', $field_params);

		$field_params = array(
			'type'      => 'text',
			'id'        => 'support_filter_taxonomy',
			'option_name' => 'wt_geotargeting_post',
			'label_for' => 'support_filter_taxonomy',
			'desc'      => 'Перечислите таксономии, участвующии в фильтрации.'
		);
		add_settings_field('support_filter_taxonomy', 'Фильтрация по таксономии и локации', array(&$this, 'displaySettings'), 'wt_geotargeting_posts_page', 'wt_geotargeting_post', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'filter_neighbors_view',
            'option_name' => 'wt_geotargeting_post',
            'label_for' => 'filter_neighbors_view',
            'desc'      => 'Отображение соседних локаций (по области) в том числе',
        );
        add_settings_field('filter_neighbors_view', '', array(&$this, 'displaySettings'), 'wt_geotargeting_posts_page', 'wt_geotargeting_post', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'activate_shortcode_title_description',
            'option_name' => 'wt_geotargeting_post',
            'label_for' => 'activate_shortcode_title_description',
            'desc'      => 'Поддержка шорткодов в H1, title и description',
        );
        add_settings_field('activate_shortcode_title_description', '', array(&$this, 'displaySettings'), 'wt_geotargeting_posts_page', 'wt_geotargeting_post', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'activate_menu_setting',
            'option_name' => 'wt_geotargeting_post',
            'label_for' => 'activate_menu_setting',
            'desc'      => 'Настройка пунктов меню',
        );
        add_settings_field('activate_menu_setting', '', array(&$this, 'displaySettings'), 'wt_geotargeting_posts_page', 'wt_geotargeting_post', $field_params);


        register_setting('wt_geotargeting_post_group', 'wt_geotargeting_autocorrect', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_post',
			'Автогенерация заголовков',
			'',
			'wt_geotargeting_autocorrect_page');

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mask_html_title',
			'option_name' => 'wt_geotargeting_post',
			'label_for' => 'mask_html_title'
		);
		add_settings_field('mask_html_title', 'Маска тега &#060;title&#062;', array(&$this, 'displaySettings'), 'wt_geotargeting_autocorrect_page', 'wt_geotargeting_post', $field_params);

		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mask_h1',
			'option_name' => 'wt_geotargeting_post',
			'label_for' => 'mask_h1'
		);
		add_settings_field('mask_h1', 'Маска заголовка &#060;h1&#062;', array(&$this, 'displaySettings'), 'wt_geotargeting_autocorrect_page', 'wt_geotargeting_post', $field_params);
	}

	/**
	 * Создаем страницу настроек публикаций
	 */
	function optionsPageOutput(){
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
				settings_fields('wt_geotargeting_post_group');     // скрытые защитные поля
				do_settings_sections('wt_geotargeting_posts_page');
				do_settings_sections('wt_geotargeting_autocorrect_page');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	function filterRegionMetaFieldsSchemeAddSpelling($meta_fields_scheme){
		$meta_fields_spelling = array(
			'spelling' => array(
				'label' => 'Написание',
				'icon'  => 'dashicons-editor-spellcheck',
				'fields' => array(
					'region_name_nominative' => array(
						'type'    => 'text',
						'label'   => 'Именительный падеж',
						'description' => 'Кто? Что? (есть)'
					),
					'region_name_genitive' => array(
						'type'    => 'text',
						'label'   => 'Родительный падеж',
						'description' => 'Кого? Чего? (нет)'
					),
					'region_name_dative' => array(
						'type'    => 'text',
						'label'   => 'Дательный падеж',
						'description' => 'Кому? Чему? (дам)'
					),
					'region_name_accusative' => array(
						'type'    => 'text',
						'label'   => 'Винительный падеж',
						'description' => 'Кого? Что? (вижу)'
					),
					'region_name_instrumental' => array(
						'type'    => 'text',
						'label'   => 'Творительный падеж',
						'description' => 'Кем? Чем? (горжусь)'
					),
					'region_name_prepositional' => array(
						'type'    => 'text',
						'label'   => 'Предложный падеж',
						'description' => 'О ком? О чем? (думаю)'
					),
				)
			)
		);

		$meta_fields_scheme = array_merge_recursive(
			$meta_fields_scheme,
			$meta_fields_spelling
		);

		return $meta_fields_scheme;
	}
}
?>