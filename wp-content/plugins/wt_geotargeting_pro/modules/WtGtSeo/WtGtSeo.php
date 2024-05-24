<?php

/**
 * Class WtGtSeo
 */
class WtGtSeo
{
    public $settings = array();


    function __construct()
    {
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')) {
            Wt::setObject('seo', $this);
        }

        $this->settings = get_option('wt_geotargeting_technical');
        if (defined('ABSPATH') && is_admin()) {
            $this->initialAdmin();
            return;
        }

        // Проверка активации модуля субдомена
        $robots_txt_enable = $this->getSetting('robots_txt_enable');
        if (empty($robots_txt_enable)) return;

        !is_admin() and add_action('init', array($this, 'initial'));
    }

    function initial()
    {
        add_filter('robots_txt', array($this, 'filter_robots_txt'), 10, 2);
        add_action('wp_footer', array($this, 'action_wp_footer'), 99);
    }

    public function initialAdmin()
    {
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtSeo/WtGtSeoAdmin.php');
        new WtGtSeoAdmin();
    }

    /**
     * Получить настройку
     *
     * @param $attribute
     */
    public function getSetting($name)
    {
        if (empty($this->settings[$name])) return null;

        return $this->settings[$name];
    }

    function filter_robots_txt($output, $public)
    {

        $public = get_option('blog_public');
        if ('0' == $public) {
            echo 'User-agent: *' . PHP_EOL . 'Disallow: /';
            return;
        }

        $host = $_SERVER["HTTP_HOST"];
        $host = preg_replace("/\:\d+/is", "", $host);
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $http = "https";
        } else {
            $http = "http";
        }

        $subdomain = WtGtSubdomain::getSubdomain();

        $robots_txt_rewrite = $this->getSetting('robots_txt_rewrite');

        if (empty($robots_txt_rewrite)) echo $output;

        $output_all = $this->getSetting('robots_txt_all');
        if (!empty($output_all)) {
            echo $output_all;
        }

        if (empty($subdomain)) {
            $output_main = $this->getSetting('robots_txt_main');

            if (!empty($output_main)) {
                echo PHP_EOL . $output_main;
            }

        } else {
            $location_id = Wt::$obj->subdomain->getRegionIdBasedSubdomain();
            $subdomain_robots_txt = get_post_meta($location_id, 'robots_txt', true);

            if (!empty($subdomain_robots_txt)) {
                echo PHP_EOL . $subdomain_robots_txt;
            }
        }

        $robots_txt_sitemap_path = $this->getSetting('robots_txt_sitemap_path');
        if (!empty($robots_txt_sitemap_path)) {
            echo PHP_EOL . 'Sitemap: ' . $http . '://' . $host . $robots_txt_sitemap_path;
        }

        echo PHP_EOL . 'Host: ' . $http . '://' . $host . PHP_EOL;
    }

    function action_wp_footer(){
        $footer_code = Wt::$obj->contacts->getValue('footer_code');

        if (empty($footer_code)) return;

        echo $footer_code;
    }
}