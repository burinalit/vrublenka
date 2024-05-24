<?php

/**
 * Class WtGtSeo
 */
class WtGtTechnical extends WtGtModuleBehavior
{
    public $settings_default = array(
        'locations_source' => 'post_type_region'
    );

    function __construct()
    {
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')) {
            Wt::setObject('technical', $this);
        }

        $this->loadSettings('wt_geotargeting_technical_javascript');
        $this->loadSettings('wt_geotargeting_technical_yoast_seo');
        $this->loadSettings('wt_geotargeting_technical_locations');
        $this->loadSettings('wt_geotargeting_technical_deactivation');

        if (defined('ABSPATH') && is_admin()) {
            $this->initialAdmin();
            return;
        }

        !is_admin() and add_action('init', array($this, 'initial'));
    }

    function initial()
    {
        // Регистрация скриптов для фронтенда
        if (!$this->checkSetting('javascript_cookie_disable') || !$this->checkSetting('javascript_wt_location_disable')){
            add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
        }

        $yoast_seo_canonical_disable = $this->getSetting('yoast_seo_canonical_disable');
        if (!empty($yoast_seo_canonical_disable)){
            add_filter('wpseo_canonical', '__return_false');
            add_action('wp_head', 'rel_canonical', 11);
        }

        $yoast_seo_canonical_rewrite = $this->getSetting('yoast_seo_canonical_rewrite');
        if (!empty($yoast_seo_canonical_rewrite)) add_filter('wpseo_canonical', array($this, 'filter_wpseo_canonical'), 9, 2);
    }

    public function initialAdmin()
    {
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtTechnical/WtGtTechnicalAdmin.php');
        new WtGtTechnicalAdmin();
    }

    public function registerScripts(){
        wp_register_script(
            'wt-cookie',
            plugin_dir_url(WT_GT_PRO_PLUGIN_FILE) . 'js/cookie.js',
            array('jquery'),
            '0.2.0');

        wp_register_script(
            'wt-location',
            plugin_dir_url(WT_GT_PRO_PLUGIN_FILE) . 'js/wt-location.js',
            array('jquery', 'wt-cookie'),
            '0.2.0');

        if (!$this->checkSetting('javascript_cookie_disable')) wp_enqueue_script('wt-cookie');
        if (!$this->checkSetting('javascript_wt_location_disable')) wp_enqueue_script('wt-location');
    }

    public function filter_wpseo_canonical($canonical, $presentation = null){
        if (!empty($presentation->model) && !empty($presentation->model->canonical)) $canonical = $presentation->model->canonical;
        else $canonical = $this->getCanonical();

        return $canonical;
    }

    /**
     * Изменённая WordPress-Функция rel_canonical()
     */
    public function getCanonical()
    {
        if (!is_singular()) return;

        $id = get_queried_object_id();

        if (0 === $id) return;

        $url = wp_get_canonical_url($id);

        if (empty($url)) return;

        return esc_url($url);
    }
}