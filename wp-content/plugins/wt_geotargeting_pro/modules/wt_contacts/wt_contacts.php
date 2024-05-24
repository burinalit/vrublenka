<?php

class WtContacts
{
	var $items;

	// Фиксирование отображения для нескольких шорткодов [wt_location]
	var $position = array();

	function __construct(){
		if (class_exists('Wt')) Wt::setObject('contacts', $this);

        new WtGtLocation();

		add_action('init', array($this, 'initial'));

		// Регистрируем шорткод [wt_contacts]
		add_shortcode('wt_contacts', array($this, 'shortcodeContacts'));

        // Регистрируем шорткод [wt_location]
        add_shortcode('wt_location', array($this, 'shortcodeLocation'));

		// Фильтрация запросов WP_Query по заголовку
		// https://wordpress.stackexchange.com/questions/22949/query-post-by-title
		add_filter('posts_where', array($this, 'title_like_posts_where'), 10, 2);
	}

	function initial(){
		$this->registerPostTypeRegion();

		if (empty($this->items['region_id'])){
            $this->setValuesBasedRegion();

            $deactivate_save_region_from_cookie = Wt::$obj->geo->getSetting('deactivate_save_region_from_cookie');
            if (empty($deactivate_save_region_from_cookie)) $this->updateCookie($this->items);
        }

        if (defined('ABSPATH') && is_admin()) $this->initialAdmin();
	}

	function initialAdmin(){
        new WtContactsAdmin();

        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtLocation/WtGtLocationAdmin.php');
        new WtGtLocationAdmin();
    }

	/**
	 * Добавление нового значения
	 *
	 * @param $name
	 * @param $values
	 */
	public function setValue($name, $value){
		$this->items[$name] = $value;
	}

	/**
	 * Получить текущее значение
	 *
	 * @param $attribute
     */
	public function getValue($attribute){
    	if (empty($this->items[$attribute])) return null;

    	return $this->items[$attribute];
    }

	/**
	 * Регистрация типа постов "Регион"
	 */
	function registerPostTypeRegion() {
		$labels = array(
			'name' => 'Регион',
			'singular_name' => 'Регион', // админ панель Добавить->Функцию
			'add_new' => 'Добавить регион',
			'add_new_item' => 'Добавить регион', // заголовок тега <title>
			'edit_item' => 'Редактировать регион',
			'new_item' => 'Новый регион',
			'all_items' => 'Все регионы',
			'view_item' => 'Просмотр региона на сайте',
			'search_items' => 'Искать регион',
			'not_found' =>  'Регионов не найдено.',
			'not_found_in_trash' => 'В корзине нет регионов.',
			'menu_name' => 'Регионы' // ссылка в меню в админке
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false, // Отображение на страницах сайта
			'exclude_from_search' => true, // Исключить из поиска на сайте
			'show_ui' => true, // показывать интерфейс в админке
			'has_archive' => true,
			'menu_position' => 22, // порядок в меню
			'hierarchical' => true,
			'rewrite' => false,
			'supports' => array('title', 'page-attributes', 'custom-fields'),
			'taxonomies' => array()
		);
		register_post_type('region', $args);
	}

	/**
	 * Установка значений на основе текущего региона и контактов из регионов

	 * @return bool
     */
	public function setValuesBasedRegion(){
		if (!class_exists('Wt')) return false;
		if (empty(Wt::$obj->geo)) return false;

		// Ищем среди контактов текущую локацию если автоматическая установка не отключена
		$deactivate_auto_set_region_from_cookie = Wt::$obj->region->getSetting('deactivate_auto_set_region_from_cookie');
		if (empty($deactivate_auto_set_region_from_cookie)){


            $region_id = Wt::$obj->geo->getRegion('location_id');
            if (empty($region_id)) $region_id = Wt::$obj->geo->getRegion('region_id'); // Устаревшая переменная

		    if (!empty($region_id)){
                $location = WtGtLocation::getObject(array(
                    'region_id' => $region_id
                ));
            }else
                foreach (WtGtLocation::$types as $key => $name){
                    $region_name = Wt::$obj->geo->getRegion($key);
                    $region_type = $key;

                    if (empty($region_name)) continue;

                    $location = WtGtLocation::getObject(array(
                        'region_name' => $region_name,
                        'region_type' => $region_type
                    ));
                    if (!empty($location)) break;
                }
        }

		// Если регион не присвоен и в базе подходящий отсутствует, тогда ищем значения по умолчанию
		if (empty($location)){
            $query = $this->getRegionsDefault();

            if (!empty($query->posts[0])) $location = $query->posts[0];
		}

		if (empty($location)) return false;

		$this->setValueFromRegion($location);

	}

	function setValueFromRegion($region){
		$this->setValue('region_id', $region->ID);
        $this->setValue('slug', $region->post_name);

		$meta = get_metadata('post', $region->ID);

		foreach ($meta as $key => $value){
            // Если meta является переводом, то пропускаем
            if (Wt::$obj->localisation->isEnable() && Wt::$obj->localisation->checkMatchPolylangLanguagesList($key)) continue;

			$this->setValue($key, $value[0]);
		}

        if (Wt::$obj->localisation->isEnable()){
            // Узнать текущий язык
            $active_locale = determine_locale();
            if (!empty($meta[$active_locale][0])){
                $localisation = maybe_unserialize($meta[$active_locale][0]);
                foreach ($localisation as $key => $value){
                    $this->setValue($key, $value);
                }
            }
        }

		/* Составное название локации */
        if ($meta['region_type'][0] == 'administrative_district' && !empty($region->post_parent)){
            $parent_title = get_the_title($region->post_parent);
            $this->setValue('region', $parent_title . ', ' . $region->post_title);
        }else{
            if (!empty($this->getValue('region_name'))) $this->setValue('region', $this->getValue('region_name'));
            else{
                $this->setValue('region', $region->post_title);
                $this->setValue('region_name', $region->post_title);
            }
        }
	}

	function setValueFromRegionId($region_id){
		$region = get_post($region_id);
		if (empty($region)) return false;
		$this->setValueFromRegion($region);
	}


	/**
	 * Получить все регионы
	 *
	 * @param array $params
	 * @return WP_Query
     */
	function getRegions($params = array()){
		$args = array(
			'post_type' => 'region',
			'post_status' => 'publish',

			'orderby' => 'post_title',
			'order' => 'ASC',
			'posts_per_page' => -1
		);

		$query = new WP_Query($args);

		return $query;
	}

	/**
	 * Получить регионы по умолчанию
	 * Параметры:
	 * 		filter / parent - Фильтрация по родителю
     *
	 * @param array $params
	 * @return WP_Query
     */
	function getRegionsDefault($params = array()){

		$args = array(
			'post_type' => 'region',
			'meta_query' => array(
				array(
					'key'     => 'by_default',
					'value'   => 'true',
				),
			),
		);

		if (!empty($params['filter']) && !empty($params['filter']['parent'])){
			$args['post_parent'] = $params['filter']['parent'];
		}

		$query = new WP_Query($args);

		return $query;
	}

	/**
	 * Получить количество значений по умолчанию
	 *
	 * @param array $params
	 * @return int
     */
	function getRegionsDefaultCount($params = array()){
		$query = $this->getRegionsDefault($params);

		if (empty($query->posts[0])) return 0;
		else return count($query->posts);
	}

	function getRegionDefault($params = array()){
        $query = $this->getRegionsDefault($params);

        if (empty($query->posts[0])) return false;

        return $query->posts[0];
    }

	/**
	 * @param array $params
	 * @return bool
     */
	function getRegionDefaultName($params = array()){
		$query = $this->getRegionsDefault($params);

		if (empty($query->posts[0])) return false;

		$region = $query->posts[0];

		return $region->post_title;
	}

	/**
	 * Проверка активного региона соответствию региону установленного по умолчанию
	 *
	 * @return bool
     */
	function checkRegionDefault(){
		$region_default_name = $this->getRegionDefaultName();
		$region_active = $this->getValue('region');

		if ($region_active === $region_default_name) return true;

		return false;
	}

	/**
	 * Установка значений на основе названия страны
	 *
	 * @param $name
	 * @return bool
     */
	function setValuesOfCountry($name){
		$query = new WP_Query(array(
			'post_type' => 'region',
			'title' => $name,
			'meta_query' => array(
				array(
					'key'     => 'region_type',
					'value'   => 'country',
				),
			),
		));

		if (empty($query->posts[0])) return false;

		$region = $query->posts[0];

		$this->setValue('region_id', $region->ID);
        $this->setValue('region', $region->post_title);
        $this->setValue('slug', $region->post_name);

		$meta = get_metadata('post', $region->ID);
		foreach ($meta as $key => $value){
			$this->setValue($key, $value[0]);
		}
	}

	/**
	 * Поведение шорткода [wt_contacts]Текст: {return}[/wt_contacts]
	 *
	 * @param $atts
	 * @param null $content
	 * @return null|string
     */
	public function shortcodeContacts($atts, $content=null) {
		// Attributes
		$atts = shortcode_atts(
			array(
				'get' => '',
			),
			$atts
		);

		if (empty($atts['get'])) return false;

		$value = $this->getValue($atts['get']);

		if (empty($value)) return null;

		if (!empty($content)){
			$content = strtr($content, array('{return}' => $value));
			return $content;
		}

		return $value;
	}

    /**
     * Поведение шорткода [wt_location]Текст: {get}[/wt_contacts]
     *
     * @param $atts
     * @param null $content
     * @return null|string
     */
    public function shortcodeLocation($atts, $content=null) {
        // Attributes
        $atts = shortcode_atts(
            array(
                'shortcode' => false,
                'position' => false,
                'show_for' => false,
                'not_show_for' => false,
                'default' => false,
                'get' => '',
            ),
            $atts
        );

        if ($atts['position'] !== false &&
            isset($this->position[$atts['position']]) &&
            $this->position[$atts['position']] > 0)
            return null;

        // Проверка совпадения города в параметрах show_for и not_show_for
        if ($atts['show_for']){
            $show_for = explode(",", $atts['show_for']);

            $show_for_check = array_search($this->getValue('region') , $show_for);
        }

        if ($atts['not_show_for']){
            $not_show_for = explode(",", $atts['not_show_for']);
            $not_show_for_check = array_search($this->getValue('region') , $not_show_for);
        }

        if (isset($show_for_check) && $show_for_check === false) return null;
        elseif (isset($not_show_for_check) && $not_show_for_check !== false) return null;

        if (!empty($atts['get'])) $value = $this->getValue($atts['get']);

        if (!empty($content) && !empty($value)){
            $content = strtr($content, array('{get}' => $value));
            return $content;
        }elseif(!empty($value)){
            $content = $value;
        }elseif(!empty($atts['get']) && empty($value)){
            $content = null;
        }

        if (empty($content)) return null;

        if (isset($atts['shortcode']) && $atts['shortcode'] == true){
            $content = do_shortcode($content);
        }

        // Фиксируем показ
        if ($atts['position'] !== false){
            if (empty($this->position[$atts['position']])) $this->position[$atts['position']] = 0;
            $this->position[$atts['position']]++;
        }

        return $content;
    }

	/**
	 * Формирование массива регионов
	 * Параметры:
	 * 		filter / type - тип локации
	 * 		filter / parent - родительская локация
	 * 		pack - упаковка данных по иерархии
	 * 		columns - количество колонок
     *      orderby - сортировка. Структура аналогична одноименному параметру WP_Query.
	 *
	 * @param array $params
	 * @return array
     */
	public function getRegionsArray($params = array())
    {
		$args = array(
			'post_type' => 'region',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => -1,	// Количество выводимых объектов = Все

            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
		);

		if (!empty($params['title'])){
			$args['post_title_like'] = $params['title'];
		}

		$meta_query = array();

		if (!empty($params['filter']) && !empty($params['filter']['type'])) {
			$meta_query[] = array(
				'key' => 'region_type',
				'value' => $params['filter']['type'],
			);
		}

		if (!empty($params['filter']) && !empty($params['filter']['priority_view'])) {
			$meta_query[] = array(
				'key' => 'priority_view',
				'value' => $params['filter']['priority_view']
			);
		}

		if (!empty($meta_query)) $args['meta_query'] = $meta_query;

		if (!empty($params['filter']) && isset($params['filter']['parent'])){
			$args['post_parent'] = $params['filter']['parent'];
		}

        if (!empty($params['orderby'])){
            $args['orderby'] = $params['orderby'];
        }

		$query = new WP_Query($args);

		$regions_arr = array();

		foreach ($query->posts as $region){
			if (isset($params['item_type']) && $params['item_type'] == 'object' ) $regions_arr[$region->ID] = $region;
			else{
                $regions_arr[$region->ID] = $region->post_title;

                if (Wt::$obj->localisation->isEnable()){
                    $active_locale = determine_locale(); // Узнать текущий язык
                    $localisation = get_metadata('post', $region->ID, $active_locale, true);
                    if (!empty($localisation['region_name'])) $regions_arr[$region->ID] = $localisation['region_name'];
                }
            }
		}

		/* Делим на колонки в случае необходимости */
		if (!empty($params['columns']) && $params['columns'] > 1) $regions_arr = $this->dividedToColumns($regions_arr, $params['columns']);

		return $regions_arr;
	}

	/**
	 * Деление массива на колонки
	 *
	 * @param array $data Массив значений
	 * @param int $col Количество колонок
	 * @return array
     */
	private function dividedToColumns($data, $col = 1){
		$data_count = count($data);

		$column_max = round($data_count/$col, 0, PHP_ROUND_HALF_UP);

		$data_columns = array();

		$number = 1;
		foreach ($data as $id => $value){
			$data_columns[$number][$id] = $value;
			if (count($data_columns[$number]) == $column_max) $number ++;
		}
		return $data_columns;
	}

	/**
	 * Добавление в WP_Query нового параметра фильтрации post_title_like
	 *
	 * @param $where
	 * @param $wp_query
	 * @return string
     */
	function title_like_posts_where($where, $wp_query = null) {
		global $wpdb;

		if (is_null($wp_query)) return $where;

		if ($post_title_like = $wp_query->get('post_title_like')){
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql($wpdb->esc_like($post_title_like)) . '%\'';
		}
		return $where;
	}

	/**
     *
	 * @param $name
	 * @param null $type
	 * @return null
     */
	public function getRegion($name, $params = array()){

		if (empty($params['type'])) $params['type'] = 'region';

		$args = array(
			'post_type' => 'region',
			'meta_query' => array(
				array(
					'key'     => 'region_type',
					'value'   => $params['type'],
				)
			)
		);

		if (!empty($name)) {
			$args['post_title_like'] = $name;
		}

		if (!empty($params['iso'])){
			$args['meta_query'] = array(
				array(
					'key'     => 'iso',
					'value'   => $params['iso'],
				)
			);
		}

		$query = new WP_Query($args);

		if (empty($query->posts[0])) return null;
		else return $query->posts[0];
	}

	/**
	 * Проверить наличие региона
	 *
	 * @param $name
	 * @param array $params
	 * @return bool
     */
	public function checkRegion($name, $params = array()){
		$region = $this->getRegion($name, $params);
		if (isset($region)) return true;
		else false;
	}

	/**
	 * @param $name
	 * @param array $params
	 * @return null
     */
	public function getCountry($name, $params = array()){
		$params['type'] = 'country';
		return $this->getRegion($name, $params);
	}

	/**
	 * Получить округ
	 *
	 * @param $name
	 * @param array $params
	 * @return null
     */
	public function getDistrict($name, $params = array()){
		$params['type'] = 'district';
		return $this->getRegion($name, $params);
	}

	/**
	 * Получить город
     *
     * Подлежит удалению. Оригинал в классе WtGtLocation()
	 *
	 * @param $name
	 * @param array $params
	 * @return null
     */
	public function getCity($name, $params = array()){
		$params['type'] = 'city';
		return $this->getRegion($name, $params);
	}

	/**
	 * Получить административный округ
	 *
	 * @param $name
	 * @param array $params
	 * @return null
     */
	public function getAdministrativeDistrict($name, $params = array()){
		$params['type'] = 'administrative_district';
		return $this->getRegion($name, $params);
	}

	public function getRegionSubdomain($name, $params = array()){
		$region = $this->getRegion($name, $params);
		if (empty($region)) return false;

        $subdomain_name_sourse = Wt::$obj->subdomain->getSetting('subdomain_name_sourse');

        if (!empty($subdomain_name_sourse) && $subdomain_name_sourse == 'post_name'){
            return $region->post_name;
        }else{
            return get_post_meta($region->ID, 'subdomain', true);
        }
	}

	/**
	 * Обработка масок строки
	 * Формат маски: [wt:example_text] - example_text произвольное мето-поле
	 *
	 * @param $content
	 * @return bool|mixed
     */
	function contentMaskUpdate($content){
		preg_match_all("/%5Bwt:([_a-z]+)%5D/", $content, $matches);
		$variables = $matches[1];

		if (empty($variables)) return false;

		foreach ($variables as $key => $value){
			$content = preg_replace(
				'/%5Bwt:' . $value . '%5D/',
				$this->getValue($value),
				$content);
		}

		return $content;
	}

	/**
	 * Обновление значений активного региона в cookie
	 *
	 * @param array $data Новые значения
	 * @return array|bool|mixed
	 */
	function updateCookie(array $data = null) {
		if (is_null($data)) return false;

		$data_cookie = array();

		// Обновляем данные
		foreach ($data as $key => $value) {
			$data_cookie[$key] = $value;
		}

		if (!empty($data_cookie)) {
			//setcookie('wt_active_region', json_encode($data_cookie), time() + 3600 * 24 * 7, '/'); // устанавливаем куки для JS на неделю
			return $data_cookie;
		}else return false;
	}
}