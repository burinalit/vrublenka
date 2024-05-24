<?php

class WtContactsAdmin extends WtGtAdminBehavior
{
	/**
	 * @var array Типы региона
     */
	public $region_types = array(
		'administrative_district' => 'Административный округ',
		'city' => 'Город',
		'region' => 'Область',
		'district' => 'Округ',
		'country' => 'Страна'
	);

	function __construct(){

		// Регистрируем фреймворк ButterBean
		//add_action( 'plugins_loaded', array(&$this, 'thLoad'));

		add_action( 'butterbean_register', array(&$this, 'thRegister'), 10, 2 );
		$this->thLoad();

		// Сообщения при публикации или изменении типа записи Region
		add_filter('post_updated_messages', array(&$this, 'regionUpdatedMessages'));

		// РЕГИОНЫ
		// Создаем в таблице новые колонки "Тип", "Телефон", "Email"
		add_filter('manage_edit-region_columns', array(&$this, 'regionAddViewsColumn'), 4);
		// Заполняем колонки таблицы данными
		add_filter('manage_region_posts_custom_column', array(&$this, 'regionFillViewsColumn'), 5, 2);

		add_action('add_meta_boxes', array( $this, 'add_relation_meta_boxes'));
	}

	public function add_relation_meta_boxes() {
		add_meta_box(
			'visibility-metabox',
			__('Отображаемые публикации', 'visibility-metabox'),
			array($this, 'add_meta_box_callback'),
			'region',
			'side',
			'core'
		);
	}

	public function add_meta_box_callback( $post ) {
		$location = new WtGtLocation(get_the_ID());
		$relation_posts = $location->getPostsId();
		echo '<ul>';
		foreach ($relation_posts as $value){
			$post_status = get_post_status($value);

			if ($post_status != 'publish'){
				$location->deletePost($value);
				continue;
			}

			echo '<li><a href="' . get_permalink($value) . '">' . get_the_title($value) . '</a></li>';
		}
		echo '</ul>';
	}


	## Очистка данных
	function sanitize_callback( $options ){ 
		// очищаем
		foreach( $options as $name => & $val ){
			if( $name == 'input' )
				$val = strip_tags( $val );

			if( $name == 'checkbox' )
				$val = intval( $val );
		}

		//die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )

		return $options;
	}

	/**
	 * Инициируем фреймворк ButterBean
	 *
	 * @version 0.1.2
	 */
	function thLoad() {
		require_once(WT_GT_PRO_PLUGIN_DIR . '/butterbean/butterbean.php');
	}

	/**
	 * Выводим произвольные поля с помощью фреймворка ButterBean
	 *
	 * @version 0.1.2
	 * @param $butterbean
	 * @param $post_type
	 */
	function thRegister( $butterbean, $post_type ) {

		// Bail if not our post type.
		if ( 'region' !== $post_type )
			return;

		$this->dataFilesInit(); // Подключаем справочники

		$meta_fields_scheme = array(
			'main' => array(
				'label' => 'Основные',
				'icon'  => 'dashicons-admin-generic',
				'fields' => array(
					'region_type' => array(
						'type'    => 'select',
						'label'   => 'Тип региона',
						'choices'   => $this->region_types,
                        'choice_default' => 'city'
					),
					'country_iso' => array(
						'type'    => 'select',
						'label'   => 'Страна',
						'choices'   => $this->data->getCountriesForSelect(),
                        'choice_default' => 'RU'
					),
					'iso' => array(
						'type'    => 'text',
						'label'   => 'Код ISO (Альфа-2)',
					),
					'by_default' => array(
						'type'    => 'checkbox',
						'label'   => 'По умолчанию',
						'description' => 'Использовать данный регион по умолчанию'
					),
					'priority_view' => array(
						'type'    => 'checkbox',
						'label'   => 'Приоритет',
						'description' => 'Приоритет в отображении'
					),
					'admin_email' => array(
						'type'    => 'text',
						'label'   => 'Email администратора',
						'description' => 'Электронный адрес (или несколько адресов через запятую) для отправки уведомлений'
					),
					'note' => array(
						'type'    => 'textarea',
						'label'   => 'Заметка',
					)
				)
			),
			'contacts' => array(
				'label' => 'Контакты',
				'icon'  => 'dashicons-book-alt',
				'fields' => array(
					'address' => array(
						'type'    => 'textarea',
						'label'   => 'Адрес'
					),
					'working_hours_time' => array(
						'type'    => 'text',
						'label'   => 'Режим работы - время'
					),
					'working_hours_days' => array(
						'type'    => 'text',
						'label'   => 'Режим работы - дни'
					),
					'phone' => array(
						'type'    => 'text',
						'label'   => 'Телефон'
					),
					'email' => array(
						'type'    => 'email',
						'label'   => 'Email'
					),
                    'latitude' => array(
                        'type'    => 'text',
                        'label'   => 'Широта (Latitude, lat)'
                    ),
                    'longitude' => array(
                        'type'    => 'text',
                        'label'   => 'Долгота (Longitude, lng)'
                    ),
				)
			)
		);

		$meta_fields_scheme = apply_filters('wt_gt_region_meta_fields_scheme', $meta_fields_scheme);

		$butterbean->register_manager(
			'region-settings',
			array(
				'label'     => esc_html__( 'Настройки', 'your-textdomain' ),
				'post_type' => 'region',
				'context'   => 'normal',
				'priority'  => 'high'
			)
		);

		$manager = $butterbean->get_manager('region-settings');

		foreach ($meta_fields_scheme as $section_id => $section){
			// Регистрируем секции
			$manager->register_section(
				$section_id,
				array(
					'label' => $section['label'],
					'icon'  => $section['icon']
				)
			);

			foreach ($section['fields'] as $field_id => $field){
				// Регистрируем мета-поля
				$register_control_params = array(
					'type' => $field['type'],
					'section' => $section_id,
					'label'   => $field['label'],
					'attr'    => array('class' => 'widefat')
				);

				if (!empty($field['choices'])) $register_control_params['choices'] = $field['choices'];
                if (!empty($field['choice_default'])) $register_control_params['choice_default'] = $field['choice_default'];
				if (!empty($field['description'])) $register_control_params['description'] = $field['description'];

				$manager->register_control($field_id, $register_control_params);
				$manager->register_setting($field_id, array('sanitize_callback' => array(&$this, 'thDataValidation')));
			}
		}
	}

	/**
	 * Валидация и очистка данных при сохранении произвольных полей
	 *
	 * @version 0.1.2
	 * @param $options
	 * @return mixed
	 */
	public function thDataValidation($options){
		return $options;
	}

	/**
	 * Сообщения при публикации или изменении типа записи Region
	 *
	 * @version 0.1.2
	 * @param $messages
	 * @return mixed
	 */
	public function regionUpdatedMessages( $messages ) {
		global $post;

		$messages['region'] = array(
			0 => '', // Не используется. Сообщения используются с индекса 1.
			1 => sprintf( 'Регион обновлен. <a href="%s">Посмотреть регион</a>', esc_url( get_permalink($post->ID) ) ),
			2 => 'Произвольное поле обновлено.',
			3 => 'Произвольное поле удалено.',
			4 => 'Регион обновлен.',
			/* %s: дата и время ревизии */
			5 => isset($_GET['revision']) ? sprintf( 'Регион восстановлен из ревизии %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( 'Регион опубликован. <a href="%s">Перейти к региону</a>', esc_url( get_permalink($post->ID) ) ),
			7 => 'Регион сохранен.',
			8 => sprintf( 'Регион сохранен. <a target="_blank" href="%s">Предпросмотр региона</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
			9 => sprintf( 'Регион запланирован на: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Предпросмотр региона</a>',
				// Как форматировать даты в PHP можно посмотреть тут: http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
			10 => sprintf( 'Черновик региона обновлен. <a target="_blank" href="%s">Предпросмотр регион</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
		);

		return $messages;
	}

	/**
	 * Просмотр регионов: Добавление колонок
	 * 09.12.2016
	 *
	 * @version 0.2.4
	 * @param $columns
	 * @return array
     */
	function regionAddViewsColumn( $columns ){
		$num = 2; // после какой по счету колонки вставлять новые

		$new_columns = array(
			'type' => 'Тип',
			'phone' => 'Телефон',
			'email' => 'Email',
			'check_address' => 'Адрес'

		);

		return array_slice($columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
	}


	/**
	 * Просмотр регионов: Заполнение колонок
	 * 09.12.2016
	 *
	 * @version 0.2.4
	 * @param $colname
	 * @param $post_id
     */
	function regionFillViewsColumn($colname, $post_id ){

		if($colname === 'type'){
			$region_type_key = get_post_meta($post_id, 'region_type', 1);
			echo $this->getRegionType($region_type_key);
		}

		if($colname === 'phone'){
			echo get_post_meta($post_id, 'phone', 1);
		}

		if($colname === 'email'){
			echo get_post_meta($post_id, 'email', 1);
		}

		if($colname === 'check_address'){
			$address = get_post_meta($post_id, 'address', 1);
			if (empty($address)) return null;
			echo '<span class="dashicons dashicons-yes"></span>';
		}
	}

	/**
	 * Получить тип региона
	 * 09.12.2016
	 *
	 * @version 0.2.4
	 * @param $key
	 * @return null
     */
	function getRegionType($key){
		if (empty($this->region_types[$key])) return null;

		return $this->region_types[$key];
	}
}
?>