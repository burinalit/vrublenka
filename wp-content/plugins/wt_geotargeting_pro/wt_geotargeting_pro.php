<?php
/*
Plugin Name: WT Geotargeting Pro
Plugin URI: https://web-technology.biz/cms-wordpress/plugin-wt-geotargeting
Description: Набор инструментов для настройки геотаргетинга.
Version: 1.7.12
Author: Кусты Роман, АИТ "WebTechnology"
Author URI: https://web-technology.biz
*/

define('WT_GT_PRO_PLUGIN_FILE', __FILE__);
define('WT_GT_PRO_PLUGIN_DIR', dirname(WT_GT_PRO_PLUGIN_FILE));
define('WT_GT_PRO_PLUGIN_BASENAME', plugin_basename(WT_GT_PRO_PLUGIN_FILE));

require_once(WT_GT_PRO_PLUGIN_DIR . '/vendor/autoload.php');
include(WT_GT_PRO_PLUGIN_DIR . '/includes/IpGeoBase.php');	// Класс для работы с IpGeoBase
include(WT_GT_PRO_PLUGIN_DIR . '/includes/DaData.php');	// Класс для работы с DaData
include(WT_GT_PRO_PLUGIN_DIR . '/includes/SypexGeo.php');	// Класс для работы с SypexGeo

include(WT_GT_PRO_PLUGIN_DIR . '/includes/WtKit.php');      // Статический класс и набор инструментов
include(WT_GT_PRO_PLUGIN_DIR . '/includes/wt_data_files.php');

require(WT_GT_PRO_PLUGIN_DIR . '/includes/wt_gt_admin_behavior.php');
require(WT_GT_PRO_PLUGIN_DIR . '/includes/WtGtModuleBehavior.php');
require(WT_GT_PRO_PLUGIN_DIR . '/includes/WtGtLocationBehavior.php');

include(WT_GT_PRO_PLUGIN_DIR . '/includes/WtInitialization.php'); // Настройка библиотек
include(WT_GT_PRO_PLUGIN_DIR . '/includes/WtGeolocation.php');		// Оболочка для работы с Web-сервисами
//include(WT_GT_PRO_PLUGIN_DIR . '/modules/wt_contacts/wt_contacts.php'); // Работа с контактами
include(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtLocation/WtGtLocation.php'); // Работа с локациями
include(WT_GT_PRO_PLUGIN_DIR . '/modules/wt_mail/wt_mail.php'); 	// Работа с email-рассылкой

class WtGeoTargetingPro
{
    public $locations = array(
        'post_type_region' => array(
            'name' => 'Регионы - произвольная запись (region)',
            'description' => 'Ручное добавление регионов и закрепление за ними контактной информации.',
            'init' => 'wt_contacts/wt_contacts',
            'class' => 'WtContacts'
        ),
        'table_location_dpd' => array(
            'name' => 'Таблица локаций DPD',
            'description' => 'Ручное добавление регионов и закрепление за ними контактной информации.',
            'init' => 'WtGtLocation/WtGtLocationDPD',
            'class' => 'WtGtLocationDPD'
        ),
    );

    public $modules = array(
//        'wt_contacts' => [
//            'name' => 'Регионы и Контакты',
//            'description' => 'Ручное добавление регионов и закрепление за ними контактной информации.'
//        ],
//        'wt_gt_nav_menu' => [
//            'name' => 'Меню',
//            'description' => 'Настройка геотаргетинга в меню.',
//            'class' => 'WtGtNavMenu'
//        ],
//        'WtGtLocation' => [
//            'name' => 'Локации',
//            'description' => ''
//        ],
//        'wt_mail' => [
//            'name' => 'Email',
//            'description' => 'Отправка email-писем на региональные почтовые ящики.'
//        ],
        'WtGtRegion' => array(
            'name' => 'Регионы',
            'class' => 'WtGtRegion'
        ),
        'WtGtPost' => array(
            'name' => 'Публикации и страницы',
            'description' => 'Создание связи между публикациями и локациями (регионами).',
            'class' => 'WtGtPost'
        ),
        'WtGtSubdomain' => array(
            'name' => 'Поддомены',
            'description' => 'Обработка обращений со входящих поддоменов.',
            'class' => 'WtGtSubdomain'
        ),
        'WtGtUser' => array(
            'name' => 'Пользователи',
            'description' => 'Взаимодействие с пользователями.',
            'class' => 'WtGtUser'
        ),
        'WtGtWooCommerce' => array(
            'name' => 'WooCommerce',
            'description' => 'Поддержка WooCommerce.',
            'class' => 'WtGtWooCommerce'
        ),
        'WtGtSeo' => array(
            'name' => 'SEO',
            'description' => 'Настройки SEO',
            'class' => 'WtGtSeo'
        ),
        'WtGtLocalisation' => array(
            'name' => 'Локализация',
            'description' => 'Настройки локализации',
            'class' => 'WtGtLocalisation'
        ),
        'WtGtTechnical' => array(
            'name' => 'Технические',
            'description' => 'Технические настройки',
            'class' => 'WtGtTechnical'
        ),
        'WtGtShortcode' => array(
            'name' => 'Шорткоды',
            'description' => 'Шорткоды',
            'class' => 'WtGtShortcode'
        )
    );

    function __construct(){
        add_action('plugins_loaded', array($this, 'pluginsLoaded'));
        add_action('init', array($this, 'initial'));
    }

    static function activation(){
        WtGtLocationBehavior::createTableLocationRelationships();
    }

    static function deactivation(){
        $post_region_delete_enable = Wt::$obj->technical->getSetting('deactivation_post_region_delete_enable');

        if (empty($post_region_delete_enable)) return;

        $locations = WtGtLocation::getObjects(array('fields' => 'ids'));

        foreach ($locations as $location_id){
            wp_delete_post($location_id, true);
        }
    }

    public function uninstall(){}

    function pluginsLoaded(){
        if (defined('ABSPATH') && is_admin()) $this->initialAdmin();

        $this->activationModules();
        $this->activationLocations();

        //new WtContacts();
        new WtMail();

        new WtInitialization();
    }

    function initial(){
       // new WtGtLocation();
    }

    public function initialAdmin(){
        require(WT_GT_PRO_PLUGIN_DIR . '/includes/wt_gt_admin.php');
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/wt_contacts/wt_contacts_admin.php');

        // Регистрация скриптов для админки
        add_action('admin_enqueue_scripts', array($this, 'registerAdminScripts'));

        $wt_gt_pro_admin = new WtGtAdmin();
        $wt_gt_pro_admin->geotargeting = $this;

        //new WtContactsAdmin();

        //require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtLocation/WtGtLocationAdmin.php');
        //new WtGtLocationAdmin();
    }

    public function activationModules(){
        foreach ($this->modules as $key => $module){

            if (file_exists(WT_GT_PRO_PLUGIN_DIR . '/modules/' . $key . '.php')) require(WT_GT_PRO_PLUGIN_DIR . '/modules/' . $key . '.php');
            elseif (file_exists(WT_GT_PRO_PLUGIN_DIR . '/modules/' . $key . '/' . $key . '.php')) require(WT_GT_PRO_PLUGIN_DIR . '/modules/' . $key . '/' . $key . '.php');
            else continue;

            new $key();
        }
    }

    public function activationLocations(){
        $setting_locations_source = Wt::$obj->technical->getSetting('locations_source');

        $module = $this->locations[$setting_locations_source];

        if (file_exists(WT_GT_PRO_PLUGIN_DIR . '/modules/' . $module['init'] . '.php')) require(WT_GT_PRO_PLUGIN_DIR . '/modules/' . $module['init'] . '.php');

        new $module['class']();
    }

    public function registerAdminScripts(){
        wp_register_script(
            'checkboxes',
            plugin_dir_url(WT_GT_PRO_PLUGIN_FILE) . '/libs/jquery.checkboxes-1.2.0.min.js',
            array('jquery'),
            '1.2.0'
        );
    }
}

new WtGeoTargetingPro();
register_activation_hook(WT_GT_PRO_PLUGIN_FILE, array('WtGeoTargetingPro', 'activation'));
register_deactivation_hook(WT_GT_PRO_PLUGIN_FILE, array('WtGeoTargetingPro', 'deactivation'));
register_uninstall_hook(WT_GT_PRO_PLUGIN_FILE, array('WtGeoTargetingPro', 'uninstall'));