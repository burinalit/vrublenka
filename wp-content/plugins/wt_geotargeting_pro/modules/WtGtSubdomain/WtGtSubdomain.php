<?php

/**
 * Class WtGtSubdomain
 *
 * Обработка обращений со входящих поддоменов
 */
class WtGtSubdomain
{
    public $settings = array();

    // Источник имени субдомена
    public $subdomain_source = 'post_meta_subdomain';

    function __construct(){
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')){
            Wt::setObject('subdomain', $this);
        }

        $this->settings = get_option('wt_geotargeting_subdomain');
        if (defined('ABSPATH') && is_admin()){
            $this->initialAdmin();
            return;
        }

        // Проверка активации модуля субдомена
        if (Wt::$obj->subdomain->isDisable()) return;

        if (!empty($this->getSetting('subdomain_name_sourse'))) $this->subdomain_source = $this->getSetting('subdomain_name_sourse');

        ! is_admin() and add_action('init', array($this, 'initial'));

        add_action('wt_geotargeting_initialization_end', array($this, 'actionGeotargetingInitializationEnd'));
    }

    function initial(){
        // Проверка текущего Url и блокировка открытых Url на поддоменах
        $this->blockOpenUrlOnSubdomains();

        if ($this->checkOpenUrl()) return;

        // Проверка наличия открытых субдоменов
        if ($this->checkOpenSubdomain()) return;

        // Переадресация на региональный субдомен
        $this->actionRedirectToLocationSubdomain();

        // Проверка текущего субдомена и переадресация
        $this->actionRedirect();

        // Обработка канонической ссылки
        add_filter('get_canonical_url', array($this, 'filter_get_canonical_url'), 10, 2);
        add_filter('aioseop_canonical_url', array($this, 'filter_get_canonical_url'), 10, 1);   // Фильтр плагина All in One SEO Pack
        add_filter('wpseo_canonical', array($this, 'filter_get_canonical_url'), 10, 1 );        // Фильтр плагина SEO Yoast

        /* Хуки влияющие на каноническую ссылку: auth_post_meta__yoast_wpseo_canonical, redirect_canonical, sanitize_post_meta__yoast_wpseo_canonical */
    }

    public function initialAdmin(){
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtSubdomain/WtGtSubdomainAdmin.php');
        new WtGtSubdomainAdmin();

        if (!extension_loaded('intl')){
            add_action('admin_notices', function (){
                echo '<div class="error notice"><p>WT GeoTargeting Pro: Отсутствует PHP-модуль интернационализации <b>intl</b>. ';
                echo 'Для преобразование доменного имени из IDNA ASCII в Unicode необходимо установить PHP-модуль <b>intl</b>.</p></div>';
            });
        }
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
     * Проверка наличия текущего субдомена среди активных регионов
     * Устаревшая
     *
     * @return bool
     */
    public function checkSubdomain(){
        $url = $_SERVER['HTTP_HOST'];
        $subdomain = self::getSubdomain();

        if (empty($subdomain) || $url == self::getDomain()) return true;

        $location_args = array(
            'fields' => 'ids'
        );

        $subdomain_name_sourse = $this->getSetting('subdomain_name_sourse');

        if (!empty($subdomain_name_sourse) && $subdomain_name_sourse == 'post_name'){
            $location_args['slug'] = $subdomain;
        }else{
            $location_args['subdomain'] = $subdomain;
        }

        $location_id = WtGtLocation::getObject($location_args);

        if (empty($location_id)) return false;

        return $location_id;
    }

    function getRegionBasedSubdomain($location_args = array()){
        $url = $_SERVER['HTTP_HOST'];
        $subdomain = self::getSubdomain();

        // Преобразование доменного имени из IDNA ASCII в Unicode
        if (extension_loaded('intl')){
            $subdomain = idn_to_utf8($subdomain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        }

        if (empty($subdomain) || $url == self::getDomain()){
            $subdomain = 'NOT EXISTS';
            // $location_args['by_default'] = true;
        }

        $subdomain_name_sourse = $this->getSetting('subdomain_name_sourse');

        if (!empty($subdomain_name_sourse) && $subdomain_name_sourse == 'post_name'){
            $location_args['slug'] = $subdomain;
        }else{
            $location_args['subdomain'] = $subdomain;
        }

        $location_id = WtGtLocation::getObject($location_args);

        if (empty($location_id)) return false;

        return $location_id;
    }

    function getRegionIdBasedSubdomain(){
        $location_args = array(
            'fields' => 'ids'
        );

        $location_id = $this->getRegionBasedSubdomain($location_args);

        if (empty($location_id)) return false;

        return $location_id;
    }


    /**
     * Проверка закрытости субдомена
     *
     * @return bool
     */
    public function checkNotOpenSubdomain(){
        $url = $_SERVER['HTTP_HOST'];
        $subdomain = self::extractSubdomains($url);

        if (!empty($this->settings['open_subdomains']))
            $open_subdomains = explode(",", $this->settings['open_subdomains']);

        if (empty($open_subdomains)) return true;

        $open_key = array_search($subdomain, $open_subdomains);

        if ($open_key === FALSE)  return true;

        return false;
    }

    public function checkOpenSubdomain(){
        return !$this->checkNotOpenSubdomain();
    }

    public function checkOpenUrl(){
        $url = wp_parse_url($_SERVER['REQUEST_URI']);
        $open_urls = $this->getSetting('open_urls');

        foreach (explode(PHP_EOL, $open_urls) as $open_url){
            $matched = fnmatch(trim($open_url), $url["path"]);
            if ($matched) return $matched;
        }

        return false;
    }

    /**
     * Извлечь доменное имя
     *
     * @param $domain
     * @return mixed
     */
    static function extractDomain($domain)
    {
        if(preg_match("/(?P<domain>[a-z0-9\-][a-z0-9\-]{1,63}\.[a-z0-9\-]{2,12})$/i", $domain, $matches))
        {
            return $matches['domain'];
        } else {
            return $domain;
        }
    }

    /**
     * Извлечь субдомен
     *
     * @param $domain
     * @return string
     */
    static function extractSubdomains($domain)
    {
        $subdomains = $domain;
        $domain = self::extractDomain($subdomains);

        $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

        return $subdomains;
    }

    /**
     * @return mixed
     */
    static function getDomain(){
        $domain = $_SERVER['HTTP_HOST'];

        return self::extractDomain($domain);
    }

    /**
     * @return mixed
     */
    static function getSubdomain()
    {
        $url = $_SERVER['HTTP_HOST'];

        return self::extractSubdomains($url);
    }

    /**
     * @return string
     */
    static function getUrlPath()
    {
        $path = $_SERVER['REQUEST_URI'];

        // Удаляем слэш в начале пути
        $first_symbol = substr($path, 0, 1);
        if ($first_symbol == '/') $path = mb_substr($path, 1);

        return $path;
    }

    /**
     * Присвоение региона по окончанию определения геолокации пользователя
     *
     * @param $data
     */
    function actionGeotargetingInitializationEnd($data){

        $location_get_subdomain = $this->getSetting('location_get_subdomain');
        if (empty($location_get_subdomain)) return;

        $region_id = $this->getRegionIdBasedSubdomain();

//        if (!empty($region_id)) Wt::$obj->region->setActiveRegion($region_id);
        if (!empty($region_id)) Wt::$obj->contacts->setValueFromRegionId($region_id);
    }

    function getSubdomainFromActiveLocation()
    {

        if (!empty(WT::$obj->geo->data['city'])) {
            $active_location_city = WT::$obj->geo->data['city'];
            $location_subdomain = Wt::$obj->contacts->getRegionSubdomain(
                $active_location_city, array(
                'type' => 'city'
            ));
            if (!empty($location_subdomain)) return $location_subdomain;
        }

        if (!empty(WT::$obj->geo->data['region'])) {
            $active_location_region = WT::$obj->geo->data['region'];
            $location_subdomain = Wt::$obj->contacts->getRegionSubdomain($active_location_region, array(
                'type' => 'region'
            ));
            if (!empty($location_subdomain)) return $location_subdomain;
        }

        if (!empty(WT::$obj->geo->data['district'])) {
            $active_location_district = WT::$obj->geo->data['district'];
            $location_subdomain = Wt::$obj->contacts->getRegionSubdomain($active_location_district, array(
                'type' => 'district'
            ));
            if (!empty($location_subdomain)) return $location_subdomain;
        }

        if (!empty(WT::$obj->geo->data['country'])) {
            $active_location_country = WT::$obj->geo->data['country'];
            $location_subdomain = Wt::$obj->contacts->getRegionSubdomain(null, array(
                'type' => 'country',
                'iso' => $active_location_country
            ));
            if (!empty($location_subdomain)) return $location_subdomain;
        }

        return null;
    }


    public function actionRedirectToLocationSubdomain(){
        $mode = $this->getSetting('redirect_to_location_subdomain');
        if (empty($mode) || $mode == 0) return;

        // Если посетитель на поддомене, то переадресацию не делаем
        $subdomain = self::getSubdomain();
        if (!empty($subdomain)) return true;

        // Если режим переадресации "Один раз", проверяем наличие совершенной переадресации
        if ($mode == 1 && !empty($_COOKIE['wt_redirect_location'])) return;


        // Определяем текущий регион
        $location_subdomain = $this->getSubdomainFromActiveLocation();

        if (!empty($location_subdomain)){
            // Сохраняем факт переадресации в cookie - wt_redirect_location
            setcookie(
                "wt_redirect_location",
                1,
                time()+(3600*24*36),
                '/',
                self::getDomain()
            );


            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $http = "https";
            } else {
                $http = "http";
            }

            // Формирование URL
            $redirect_considering_url = $this->getSetting('redirect_considering_url');
            if (empty($redirect_considering_url)) $redirect_path = '';
            else $redirect_path = self::getUrlPath();

            // Переадресация
            $redirect_url = $http .'://' . $location_subdomain . '.' .self::getDomain() . '/' . $redirect_path;
            wp_redirect($redirect_url, 302);
            exit();
        }
    }

    /**
     * Проверка наличия текущего поддомена среди предустановленных регионов.
     * Переадресация или вывод ошибки в случае отсутствия поддомена.
     *
     * @return bool
     */
    public function actionRedirect()
    {
        $check_is_subdomain = $this->getSetting('check_is_subdomain');
        if (empty($check_is_subdomain)) return true;

        // Проверка наличия субдомена
        $region_id = $this->getRegionIdBasedSubdomain();

        if(empty($region_id))
        {
            $redirect_http_code = $this->getSetting('redirect_http_code');

            // Формирование URL
            $redirect_considering_url = $this->getSetting('redirect_considering_url');
            if (empty($redirect_considering_url)) $redirect_path = '';
            else $redirect_path = self::getUrlPath();

            $redirect_base_url = $this->getSetting('redirect_base_url');
            // Добавляем слэш в начале пути
            $last_symbol = substr($redirect_base_url, -1, 1);
            if ($last_symbol != '/') $redirect_base_url .= '/';

            $redirect_url = $redirect_base_url . $redirect_path;

            if (empty($redirect_url)) $redirect_url = 'http://' . self::getDomain() . $redirect_path;

            if (empty($redirect_http_code)) header("HTTP/1.0 404 Not Found", true, 404);
            else wp_redirect($redirect_url, $redirect_http_code);

            exit();
        }
    }

    // Блокировка открытых Url на субдоменах
    public function blockOpenUrlOnSubdomains()
    {
        $block_open_url_on_subdomains_enadle = $this->getSetting('block_open_url_on_subdomains');
        if (empty($block_open_url_on_subdomains_enadle)) return;

        // Проверяем наличие субдомена
        $subdomain = self::getSubdomain();

        // Проверяем совпадение с открытыми Url
        $check_open_url = $this->checkOpenUrl();

        // Если есть поддомен и Url совпадает то выводим ошибку
        if (!empty($subdomain) && $check_open_url){
            header("HTTP/1.0 404 Not Found", true, 404);
            exit();
        }
    }

    /**
     * Обработка канонической ссылки с целью удаления поддомена
     *
     * @param $canonical_url
     * @param null $post
     * @return mixed
     */
    public function filter_get_canonical_url($canonical_url, $post = null){

        if (isset($post->ID)) $post_id = $post->ID;
        else $post_id = get_the_ID();

        $post_meta_canonical_base_domain = get_post_meta($post_id, 'wt_canonical_base_domain', true);

        if ($post_meta_canonical_base_domain != 'on') return $canonical_url;

        $host = $_SERVER['HTTP_HOST'];
        $domain = self::getDomain();

        if ($host == $domain) return $canonical_url;

        $canonical_url_update = str_replace($host, $domain, $canonical_url);

        return $canonical_url_update;
    }

    /**
     * Проверка активации модуля
     *
     * @return bool
     */
    public function isEnable(){
        $enable = $this->getSetting('redirect_enable');
        if (empty($enable)) return false;
        return true;
    }

    /**
     * Проверка активации модуля
     *
     * @return bool
     */
    public function isDisable(){
        $enable = $this->getSetting('redirect_enable');
        if (empty($enable)) return true;
        return false;
    }
}