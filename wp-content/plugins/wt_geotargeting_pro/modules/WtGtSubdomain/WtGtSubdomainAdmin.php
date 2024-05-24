<?php

/**
 * Class WtGtSubdomainAdmin
 */
class WtGtSubdomainAdmin extends WtGtAdminBehavior
{
	/**
	 * @var array HTTP Коды перенаправления
     */
	private $http_codes_redirect = array(
		0 => 'Перенаправление отключено',
		301 => '301 - Ресурс перемещен навсегда',
		302 => '302 - Ресурс временно перемещен',
	);

	private $redirect_to_location_subdomain_mode = array(
		0 => 'Отключено',
		1 => 'Один раз',
		2 => 'Постоянное'
	);

	function __construct(){
		add_action('admin_menu', array(&$this, 'menu'));
		add_action('admin_init', array(&$this, 'settingsRegister'));

		add_filter('wt_gt_region_meta_fields_scheme', array($this, 'filterRegionMetaFieldsSchemeAddSubdomain'));

        // Проверка активации модуля субдомена
        if (Wt::$obj->subdomain->isDisable()) return;

		// Добавление мета-бокса настройки домена в канонической ссылке
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'), 10, 2);
	}

	public function menu(){
		add_submenu_page(
			'wt_geotargeting',
			'WT GeoTargeting - Настройки субдоменов',
			'Субдомены',
			'manage_options',
			'subdomain',
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
		register_setting('wt_geotargeting_subdomain_group', 'wt_geotargeting_subdomain', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_subdomain_group', 'wt_geotargeting_subdomain_not_exist', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_subdomain',
			'',
			'',
			'wt_geotargeting_subdomain_page');

		$field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'redirect_enable',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'redirect_enable',
			'desc'      => 'Активировать поддержку субдоменов',
		);
		add_settings_field('redirect_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);

        $field_params = array(
            'type'      => 'select', // тип
            'id'        => 'subdomain_name_sourse',
            'option_name' => 'wt_geotargeting_subdomain',
            'label_for' => 'subdomain_name_sourse',
            'vals'		=> array(
                'post_meta_subdomain' => 'Meта-переменная - subdomain',
                'post_name' => 'Ярлык - post_name',
            ),
        );
        add_settings_field('subdomain_name_sourse', 'Источник имени субдомена', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);


        $field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'location_get_subdomain',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'location_get_subdomain',
			'desc'      => 'Устанавливать местоположение на основе субдомена',
		);
		add_settings_field('location_get_subdomain', '', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);

//        $field_params = array(
//            'type'      => 'checkbox', // тип
//            'id'        => 'main_domain_location_by_default',
//            'option_name' => 'wt_geotargeting_subdomain',
//            'label_for' => 'main_domain_location_by_default',
//            'desc'      => 'Устанавливать местоположение корневого домена на основе региона "По умолчанию"',
//        );
//        add_settings_field('main_domain_location_by_default', '', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);


        $field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'check_is_subdomain',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'location_get_subdomain',
			'desc'      => 'Проверять наличие субдомена среди регионов',
		);
		add_settings_field('check_is_subdomain', '', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);



		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'open_subdomains',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'open_subdomains',
			'desc'      => 'Перечислите через запятую субдомены неучитываемые в переадресации и установке региона.',
			'placeholder' => 'www, landing, sale'
		);
		add_settings_field('open_subdomains', 'Открытые субдомены', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);

        $field_params = array(
            'type'      => 'textarea', // тип
            'id'        => 'open_urls',
            'option_name' => 'wt_geotargeting_subdomain',
            'label_for' => 'open_urls',
            'desc'      => 'Перечислите (разделяя новой строкой) Url неучитываемые в переадресации и установке региона.',
            'placeholder' => 'www, landing, sale'
        );
        add_settings_field('open_urls', 'Открытые Url', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);

        $field_params = array(
            'type'      => 'checkbox', // тип
            'id'        => 'block_open_url_on_subdomains',
            'option_name' => 'wt_geotargeting_subdomain',
            'label_for' => 'block_open_url_on_subdomains',
            'desc'      => 'Блокировка открытых Url на субдоменах',
        );
        add_settings_field('block_open_url_on_subdomains', '', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);


        $field_params = array(
			'type'      => 'select', // тип
			'id'        => 'redirect_to_location_subdomain',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'redirect_to_location_subdomain',
			'vals'		=> $this->redirect_to_location_subdomain_mode,
		);
		add_settings_field('redirect_to_location_subdomain', 'Перенаправление на региональный поддомен', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);

		$field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'redirect_considering_url',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'redirect_considering_url',
			'desc'      => 'Учитывать полный путь URL при перенаправлении',
		);
		add_settings_field('redirect_considering_url', '', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_page', 'wt_geotargeting_subdomain', $field_params);


		add_settings_section(
			'wt_geotargeting_subdomain_not_exist',
			'Поведение при отсутствии субдомена',
			'',
			'wt_geotargeting_subdomain_not_exist_page');

		$field_params = array(
			'type'      => 'select', // тип
			'id'        => 'redirect_http_code',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'redirect_http_code',
			'vals'		=> $this->http_codes_redirect
		);
		add_settings_field('redirect_http_code', 'HTTP код перенаправления', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_not_exist_page', 'wt_geotargeting_subdomain_not_exist', $field_params);


		$field_params = array(
			'type'      => 'text', // тип
			'id'        => 'redirect_base_url',
			'option_name' => 'wt_geotargeting_subdomain',
			'label_for' => 'redirect_base_url',
			'desc'      => 'Укажите адрес, на который необходимо перенаправлять посетителей в случае отсутствия субдомена.'
		);
		add_settings_field('redirect_base_url', 'Адрес перенаправления', array(&$this, 'displaySettings'), 'wt_geotargeting_subdomain_not_exist_page', 'wt_geotargeting_subdomain_not_exist', $field_params);


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
				settings_fields('wt_geotargeting_subdomain_group');     // скрытые защитные поля
				do_settings_sections('wt_geotargeting_subdomain_page'); // секции с настройками (опциями).
				do_settings_sections('wt_geotargeting_subdomain_not_exist_page'); // секции с настройками (опциями).
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Добавление на странице редактирования Региона поля ввода Субдомена
	 *
	 * @param $meta_fields_scheme
	 * @return array
     */
	function filterRegionMetaFieldsSchemeAddSubdomain($meta_fields_scheme){
		$meta_fields_scheme_subdomain = array(
			'main' => array(
				'fields' => array(
					'subdomain' => array(
						'type'    => 'text',
						'label'   => 'Субдомен',
						'description' => 'Введите доменное имя третьего уровня без основного домена',
						'placeholder' => 'moscow'
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

	function add_meta_boxes(){
	    $post_types = array('page', 'post');

        foreach ($post_types as $screen) {
            add_meta_box(
                'canonical-metabox',
                __('Canonical', 'canonical-metabox'),
                array($this, 'add_meta_box_callback'),
                $screen,
                'side',
                'core'
            );
        }
    }

    public function add_meta_box_callback($post) {
        wp_nonce_field( 'wt_data_canonical', 'wt_nonce_canonical' );

        $post_meta_canonical_base_domain = get_post_meta( $post->ID, 'wt_canonical_base_domain', true);

        echo '<input type="checkbox" name="wt_canonical_base_domain" ';

        if ($post_meta_canonical_base_domain == 'on') echo 'checked="checked" ';

        echo '/> Каноническая ссылка ведёт на основной домен';
    }

    public function save_post($post_id, $post) {
	    if (!isset( $_POST['wt_nonce_canonical'])) return $post_id;

        $nonce = $_POST['wt_nonce_canonical'];
        if ( !wp_verify_nonce( $nonce, 'wt_data_canonical' ) )
            return $post_id;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        $canonical_base_domain = $_POST['wt_canonical_base_domain'];

        if (empty($canonical_base_domain)){
            delete_post_meta($post->ID, "wt_canonical_base_domain");
        }else{
            update_post_meta($post->ID, "wt_canonical_base_domain", $canonical_base_domain);
        }
    }
}