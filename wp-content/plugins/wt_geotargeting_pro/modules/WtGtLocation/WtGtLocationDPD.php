<?php

/**
 * Локации DPD
 * User: Roman Kusty
 * Date: 26.02.2017
 */
class WtGtLocationDPD extends WtGtLocationBehavior
{
    function __construct()
    {
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')){
            Wt::setObject('location', $this);
            Wt::setObject('contacts', $this);
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!is_plugin_active('woo-dpd/woo-dpd.php')) return false;

        add_action('init', array($this, 'initial'));

        add_action('wp_ajax_search_location', array($this, 'ajax_search_locations'));           // Привязка ajax-события search_location
        add_action('wp_ajax_nopriv_search_location', array($this, 'ajax_search_locations'));    // Привязка ajax-события search_location
    }

    function initial(){
        $this->refreshParametersFromActiveLocation();
    }

    /**
     * Запрос локаций
     *
     * Параметры:
     * id - ID
     *
     * @param array $options
     * @return mixed
     */
    static function getObjects($options = array()){
        global $DPDconfig;

        $where = '';

        // Устаревшие значения "region_name"
        if (!empty($options['region_name'])) $options['name'] = $options['region_name'];

        // Построение запроса
        if (!empty($options['location_id'])) $where .= ' CITY_ID = ' . $options['location_id'] . '';

        if (!empty($options['region_type']) && $options['region_type'] == 'city'){
            if (!empty($options['name'])) $where .= ' CITY_NAME = "' . $options['name'] . '"';
            $where .= ' AND CITY_ABBR = "г"';
        }elseif (!empty($options['region_type']) && $options['region_type'] == 'region'){
            if (!empty($options['name'])) $where .= ' REGION_NAME = "' . $options['name'] . '"';
        }

        if (!empty($options['search'])) $where .= ' CITY_NAME LIKE "%' . $options['search'] . '%"';

        $db = \Ipol\DPD\DB\Connection::getInstance(new \Ipol\DPD\Config\Config($DPDconfig));
        $locationsTable = $db->getTable('location')->find(['where' => $where])->fetchAll();

        return $locationsTable;
    }


    static function getObject($options = array()){
        $location = self::getObjects($options);

        return $location[0];
    }

    function refreshParametersFromActiveLocation(){


        if (!class_exists('Wt')) return false;
        if (empty(Wt::$obj->geo)) return false;

        // Ищем среди контактов текущую локацию если автоматическая установка не отключена
        $deactivate_auto_set_region_from_cookie = Wt::$obj->region->getSetting('deactivate_auto_set_region_from_cookie');

        if (empty($deactivate_auto_set_region_from_cookie)){

            $location_id = Wt::$obj->geo->getRegion('location_id');

            if (!empty($location_id)){
                $location = self::getObject(array(
                    'location_id' => $location_id
                ));
            }else
                foreach (self::$types as $key => $name){
                    $location_name = Wt::$obj->geo->getRegion($key);
                    $location_type = $key;

                    if (empty($location_name)) continue;

                    $location = self::getObject(array(
                        'name' => $location_name,
                        'region_type' => $location_type
                    ));

                    if (!empty($location)) break;
                }
        }

        // Если регион не присвоен и в базе подходящий отсутствует, тогда ищем значения по умолчанию
//        if (empty($location)){
//            $query = $this->getRegionsDefault();
//            $query->posts[0];
//            if (!empty($query->posts[0])) $location = $query->posts[0];
//        }

        if (empty($location)) return false;

        $this->refreshParametersFromLocation($location);
    }

    public function refreshParametersFromLocation($location){
        foreach ($location as $key => $value){
            $this->setValue(mb_strtolower($key), $value);
        }

        if ($location['CITY_NAME']) $this->setValue('region', $location['CITY_NAME']);
    }

    public function getNamesParents($location_id = null){
        return $this->parameter;
    }

    public function ajax_search_locations(){
        // Очищаем входящие данные
        $value = sanitize_text_field($_POST['value']);

        $cities = $this->getObjects(
            array(
                'search' => $value
            )
        );

        echo json_encode($cities);

        die;
    }
}