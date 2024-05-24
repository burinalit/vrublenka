<?php

/**
 * Class WtGtRegion
 *
 * Настройка регионов
 */
class WtGtRegion
{
    public $settings = array();

    static $types = array(
        'administrative_district' => 'Административный округ',
        'city' => 'Город',
        'region' => 'Регион',
        'district' => 'Округ',
        'country' => 'Страна'
    );

    function __construct(){
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')){
            Wt::setObject('region', $this);
        }

        add_action('init', array($this, 'initial'));

        add_action('wp_ajax_regions_json', array($this, 'ajaxGetRegionsJson'));
        add_action('wp_ajax_nopriv_regions_json', array($this, 'ajaxGetRegionsJson'));
    }

    function initial(){
        $this->settings = get_option('wt_geotargeting_region');
    }

    function setActiveRegion($value){
        $data_default = array(
            'administrative_district' => null,
            'country' => null,
            'district' => null,
            'region' => null,
            'city' => null
        );

        $params = array();
        if (is_int($value)) $params['id'] = $value;
        else $params['name'] = $value;
        $data_new = $this->getRegion($params);

        if (!is_array($data_new)) return false;

        $data = array_merge($data_default, $data_new);

        // Сохраняем текущий регион
        Wt::$gt->data = $data;

        $deactivate_save_region_from_cookie = Wt::$obj->geo->getSetting('deactivate_save_region_from_cookie');
        if (empty($deactivate_save_region_from_cookie)) Wt::$gt->geolocation->updateCookie($data);
    }


    function getRegion($params = array()){
        $return_values = array();

        $args = array(
            'post_type' => 'region',
            'nopaging' => true,         // Отключаем пагинацию
        );

        if (!empty($params['id'])) $args['page_id'] = $params['id'];
        if (!empty($params['name'])) $args['title'] = $params['name'];

        $query = new WP_Query($args);

        if (empty($query->posts[0])) return null;

        $region = $query->posts[0];

        $region_type = get_post_meta($region->ID, 'region_type', true);
        $return_values[$region_type . '_id'] = $region->ID;
        $return_values[$region_type] = $region->post_title;

        if (!empty($region->post_parent)){
            $parent_values = $this->getRegion(array('id' => $region->post_parent));
            $return_values = array_merge_recursive($return_values, $parent_values);
        }

        return $return_values;
    }


    // Скопировал функцию в WtGtLocation
    public function getCityId($name)
    {
        $region = WT::$obj->contacts->getCity($name);

        if (empty($region->ID)) return null;

        return $region->ID;
    }

    // Скопировал в WtGtLocation в функцию getObject()
    static function getObject($options = array()){
        $args = array();
        $args['post_type'] = 'region';
        $args['post_status'] = 'publish';

        if (!empty($options['region_id'])){
            $args['p'] = $options['region_id'];
            return new WP_Query($args);
        }

        $args['meta_query'] = array();

        if (!empty($options['fields'])) $args['fields'] = $options['fields'];
        if (!empty($options['region_name'])) $args['title'] = $options['region_name'];

        if (!empty($options['region_type'])) $args['meta_query'][] = array(
                'key'     => 'region_type',
                'value'   => $options['region_type']
            );

        if (!empty($options['subdomain'])) $args['meta_query'][] = array(
            'key'     => 'subdomain',
            'value'   => $options['subdomain']
        );

        return new WP_Query($args);
    }

    public function getSetting($name){
        if (empty($this->settings[$name])) return null;

        return $this->settings[$name];
    }

    public function ajaxGetRegionsJson(){
        $regions = array();
        $regions_obj = Wt::$obj->contacts->getRegions();

        foreach ($regions_obj->posts as $region_obj){
            $regions[$region_obj->ID] = array(
                'region_id' => $region_obj->ID,
                'region_name' => $region_obj->post_title,
            );

            $meta = get_metadata('post', $region_obj->ID);

            foreach ($meta as $key => $value){
                $regions[$region_obj->ID][$key] = $value[0];
            }
        }

        echo json_encode($regions);
        exit();
    }
}