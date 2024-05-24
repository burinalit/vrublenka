<?php

/**
 * Class WtGtShortcode
 */
class WtGtShortcode extends WtGtModuleBehavior
{
    function __construct()
    {
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')) {
            Wt::setObject('content', $this);
        }

        if (defined('ABSPATH') && is_admin()) {
            $this->initialAdmin();
            return;
        }

        add_shortcode('wt_locations', array($this, 'shortcodeLocations'));

        !is_admin() and add_action('init', array($this, 'initial'));
    }

    function initial()
    {

    }

    public function initialAdmin()
    {
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtShortcode/WtGtShortcodeAdmin.php');
        new WtGtShortcodeAdmin();
    }

    /**
     * Вывод списка регионов
     *
     * Аргументы :
     * list_tag - Вид тега контейнера списка. Допустимы: ul, div. По умолчанию: ul.
     * container_id — Id контейнера. По умолчанию: null.
     * container_class — Класс контейнера. По умолчанию: null.
     * columns - Количество колонок.
     * column_class - Класс контейнера колонок.
     * protocol - Протокол сайта. По умолчанию: Определяется автоматически.
     * domain - Основной домен сайта. По умолчанию: Определяется автоматически.
     * filter_type — Фильтрация регионов по типу. Возможные варианты: administrative_district, city, region, district, country. По умолчанию: city.
     * filter_priority_view - Отображение приоритетных городов. По умолчанию: Отключена.
     * type_select_location - Тип выбора региона. Допустимы: java_script, link_subdomain. По умолчанию: java_script.
     * cookie_saved_data_type - Выбор вида данных сохраняемых в Cookie при выборе города методом JavaScript. Допустимы: location_name, location_id. По умолчанию: location_name.
     * url_path - Источник перехода. Допустимы: current_page - Текущая страница, null - Корень сайта. По умолчанию: current_page
     *
     * @param $atts
     * @param null $content
     * @return string|null
     */
    public function shortcodeLocations($atts, $content = null) {
        $atts = shortcode_atts( array(

            'container_id' => null,
            'container_class' => null,

            'columns' => 1,
            'column_class' => null,

            'list_tag' => 'ul',

            'protocol' => null,
            'domain' => null,
            'filter_type' => 'city',
            'filter_priority_view' => null,
            'type_select_location' => 'java_script',
            'cookie_saved_data_type' => 'location_name',
            'url_path' => 'current_page'
        ), $atts);

        $content_return = '';
        //if (!empty($content)) $content_return .= $content;

        $atts['default_location'] = Wt::$obj->contacts->getRegionDefault();


        $query_params = array(
            'item_type' => 'object',
            'filter' => array()
        );

        if (!empty($atts['filter_type'])) $query_params['filter']['type'] = $atts['filter_type'];
        if (!empty($atts['filter_priority_view'])) $query_params['filter']['priority_view'] = $atts['filter_priority_view'];
        if (!empty($atts['columns'])) $query_params['columns'] = $atts['columns'];

        $locations = Wt::$obj->contacts->getRegionsArray($query_params);

        if (empty($locations)) return null;

        if ($atts['type_select_location'] == 'link_subdomain'){
            if (empty($atts['protocol'])){
                $isHttps = !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']);

                if ($isHttps) $atts['protocol'] = 'https';
                else $atts['protocol'] = 'http';
            }

            if (empty($atts['domain'])){
                $atts['domain'] = WtGtSubdomain::getDomain();
            }
        }

        $content_return .= '<div';
        if (!empty($atts['container_id'])) $content_return .= ' id="' . $atts['container_id'] . '" ';
        if (!empty($atts['container_class'])) $content_return .= ' class="' . $atts['container_class'] . '" ';
        $content_return .=  '>';

        if ($atts['columns'] > 1){
            $content_return .= $this->viewColumns($locations, $atts);
        }else{
            $content_return .= $this->viewColumn($locations, $atts);
        }

        $content_return .= '</div>';

        // Переменные JavaScript для Ajax-поиска
        $content_return .= '<script>';
        $content_return .= 'var wt_gt_domain = "' . $atts['domain'] . '";';
        $content_return .= 'var list_tag = "' . $atts['list_tag'] . '"; ';
        $content_return .= 'var column_class = "' . $atts['column_class'] . '"; ';
        $content_return .= '</script>';

        return $content_return;
    }

    // Ссылка на поддомен локации
    public function viewLocationLinkSubdomain($location, $atts) {
        global $wp;

        $active_location_id = Wt::$obj->contacts->getValue('region_id');
        $default_location = $atts['default_location'];

        $subdomain_name_sourse = Wt::$obj->subdomain->getSetting('subdomain_name_sourse');

        if (!empty($subdomain_name_sourse) && $subdomain_name_sourse == 'post_name'){
            $location_subdomain = $location->post_name;
        }else{
            $location_subdomain = get_post_meta($location->ID, 'subdomain', true);
        }

        $url = $atts['protocol'] . '://';
        if (!empty($location_subdomain) && $default_location->ID != $location->ID) $url .= $location_subdomain . '.';
        $url .= $atts['domain'];

        if (!empty($atts['url_path']) && $atts['url_path'] == 'current_page' && !empty($wp->request)){
            $url_path = '/' . $wp->request;
        }else $url_path = '/';

        $url .= $url_path;

        $content_return = '<a href="' . $url . '"';

        if ($location->ID == $active_location_id) $content_return .= ' class="active"';

        $content_return .= '>';
        if (!empty($content)) $content_return .= $content;
        else $content_return .= $location->post_title;
        $content_return .= '</a>';

        return $content_return;
    }

    // Ссылка с выбором локации
    public function viewLocationLinkJavascript($location, $atts) {
        $content_return = '<a onclick="WtLocation.setValue(\'';
        if (isset($atts['cookie_saved_data_type']) && $atts['cookie_saved_data_type'] == 'location_id')
            $content_return .= $location->ID;
        else
            $content_return .= $location->post_title;
        $content_return .= '\', \'' . $atts['filter_type'] . '\',  \'reload\')"';
        $content_return .= '>';
        if (!empty($content)) $content_return .= $content;
        else $content_return .= $location->post_title;
        $content_return .= '</a>';

        return $content_return;
    }

    public function viewColumn($locations, $atts){
        $content_return = '';

        if ($atts['list_tag'] == 'div') $content_return .= '<div';
        elseif ($atts['list_tag'] == 'ul') $content_return .= '<ul';
        $content_return .= '>';

        foreach ($locations as $id => $location)
        {
            if ($atts['list_tag'] == 'ul') $content_return .= '<li>';

            if ($atts['type_select_location'] == 'java_script') $content_return .= $this->viewLocationLinkJavascript($location, $atts);
            elseif ($atts['type_select_location'] == 'link_subdomain') $content_return .= $this->viewLocationLinkSubdomain($location, $atts);

            if ($atts['list_tag'] == 'ul') $content_return .= '</li>';
        }

        if ($atts['list_tag'] == 'div') $content_return .= '</div>';
        elseif ($atts['list_tag'] == 'ul') $content_return .= '</ul>';

        return $content_return;
    }

    public function viewColumns($columns, $atts){
        $content_return = '';

        foreach ($columns as $column) {
            $content_return .= '<div';
            if (!empty($atts['column_class'])) $content_return .= ' class="' . $atts['column_class'] . '" ';
            $content_return .= '>';
            $content_return .= $this->viewColumn($column, $atts);
            $content_return .= '</div>';
        }

        return $content_return;
    }

    // Отобразить поле ввода поиска
    public function shortcodeSearchLocationInpit($atts = null, $content = null) {
        $atts = shortcode_atts( array(
            'class' => null,
            'placeholder' => 'Введите название города',
        ), $atts);

        $content_return = '';
        $content_return .= '<input type="text" id="search_location_name" name="search_location_name" ';
        if (!empty($atts['class'])) $content_return .= ' class="' . $atts['class'] . '" ';
        if (!empty($atts['placeholder'])) $content_return .= ' placeholder="' . $atts['placeholder'] . '" ';
        $content_return .= ' autocomplete="off">';

        return $content_return;
    }
}