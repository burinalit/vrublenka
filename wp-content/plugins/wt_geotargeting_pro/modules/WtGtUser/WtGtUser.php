<?php

/**
 * Class WtGtUser
 *
 * Взаимодействие с пользователями
 */
class WtGtUser
{
    public $id = 0;

    public $settings = array();

    function __construct(){

        $this->settings = get_option('wt_geotargeting_user');

        if (defined('ABSPATH') && is_admin())
        {
            // Вкладываем ajax-запросы в админку, так как они обрабатываются через скрипты wp-admin
            add_action('wp_ajax_user_location_save', array($this, 'ajaxUserLocationSave'));           // Привязка ajax-события user_location_save
            add_action('wp_ajax_nopriv_user_location_save', array($this, 'ajaxUserLocationSave'));    // Привязка ajax-события user_location_save

            $this->initialAdmin();
            return;
        }

        // Проверка активации модуля
        $user_enadle = $this->getSetting('user_enable');
        if (empty($user_enadle)) return;

        add_filter('wt_geotargeting_initialization_method', array($this, 'filterInitializationMethod'));
        add_filter('wt_geotargeting_initialization_data', array($this, 'filterInitializationData'),10, 2);

        ! is_admin() and add_action('init', array($this, 'initial'));

        }

    function initial(){

    }

    public function initialAdmin(){
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtUser/WtGtUserAdmin.php');
        new WtGtUserAdmin();
    }

    /**
     * Получить настройку
     *
     * @param $attribute
     */
    public function getSetting($name){
        if (empty($this->settings[$name])) return null;

        return $this->settings[$name];
    }


    /**
     * ID авторизованного пользователя
     *
     * @return int
     */
    public function getCurrentUserId()
    {
        if ($this->id != 0) return $this->id;

        // Получаем текущего пользователя
        $current_user = wp_get_current_user();

        if ($current_user->ID != 0) $this->id = $current_user->ID;

        return $this->id;
    }

    public function getMetaData()
    {
        $return = array();

        if ($this->getCurrentUserId() == 0) return false;

        $location_id = get_user_meta($this->getCurrentUserId(), 'location_id', true);
        if (!empty($location_id)) $return['location_id'] = $location_id;

        $city = get_user_meta($this->getCurrentUserId(), 'city', true);
        if (!empty($city)) $return['city'] = $city;

        return $return;
    }

    public function filterInitializationMethod($method)
    {
        if ($method != false) return $method;

        $user_meta_data = $this->getMetaData();

        if (empty($user_meta_data)) return $method;

        if (isset($user_meta_data['city']))
        {
            $location_id = Wt::$obj->region->getCityId($user_meta_data['city']);

            if (empty($user_meta_data['location_id']) || $user_meta_data['location_id'] != $location_id){
                update_user_meta($this->getCurrentUserId(), 'location_id', $location_id);
            }
        }elseif(isset($user_meta_data['location_id'])) $location_id = $user_meta_data['location_id'];

        if ($location_id > 0) $method = 'user';

        return $method;
    }

    public function filterInitializationData($data, $method)
    {
        if ($method != 'user') return $data;

        $user_meta_data = $this->getMetaData();

        if (empty($user_meta_data['location_id'])) return $data;

        $location = Wt::$obj->region->getRegion(array('id' => $user_meta_data['location_id']));

        if (isset($location['city'])) $data['city'] = $location['city'];

        return $data;
    }

    /**
     * Создание/обновление мета-данных пользователя
     */
    public function ajaxUserLocationSave(){
        $return = array();

        $user_id = $this->getCurrentUserId();
        $return['user_id'] = $user_id;

        // Очищаем входящие данные
        $city = sanitize_text_field($_POST['city']);

        if ($user_id != 0)
        {
            $location = WT::$obj->contacts->getCity($city);

            if (!empty($location->ID)){
                $return['location_id'] = $location->ID;

                self::userLocationSave($user_id, $location->ID, $location->post_title);
            }
        }

        echo json_encode($return);

        die;
    }

    static function userLocationSave($user_id = null, $location_id, $location_name){
        if (is_null($user_id)){
            $current_user = wp_get_current_user();
            if ($current_user->ID != 0) $user_id = $current_user->ID;
            else return false;
        }

        update_user_meta($user_id, 'location_id', $location_id);
        update_user_meta($user_id, 'city', $location_name);
    }
}