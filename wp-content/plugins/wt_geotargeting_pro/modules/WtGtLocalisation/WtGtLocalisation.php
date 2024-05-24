<?php

/**
 * Class WtGtLocalisation
 *
 * !!! Удалить переводы вручную после отключения языка в плагине Polylang
 */
class WtGtLocalisation
{
    public $settings = array();

    public $languages_list = null;

    function __construct()
    {
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')) {
            Wt::setObject('localisation', $this);
        }

        $this->settings = get_option('wt_geotargeting_localisation');

        if (defined('ABSPATH') && is_admin()) {
            $this->initialAdmin();
            return;
        }

        // Проверка активации модуля
        $localisation_enable = $this->getSetting('localisation_enable');
        if (empty($localisation_enable)) return;

        !is_admin() and add_action('init', array($this, 'initial'));

    }

    function initial()
    {

    }

    public function initialAdmin()
    {
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtLocalisation/WtGtLocalisationAdmin.php');
        new WtGtLocalisationAdmin();
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

    // Проверка активации модуля
    public function isEnable(){
        $enable = $this->getSetting('localisation_enable');
        if (empty($enable)) return false;
        return true;
    }

    // Проверка активации модуля
    public function isPolylangEnable(){
        $enable = $this->getSetting('polylang_enable');
        if (empty($enable)) return false;
        return true;
    }

    public function refreshPolylangLanguagesList(){
        $languages_list = get_site_option('_transient_pll_languages_list');
        // $languages = get_terms( 'language', array( 'hide_empty' => false, 'orderby' => 'term_group' ) );

        foreach ($languages_list as $id => $language){
            $this->languages_list[$language['locale']] = $language['name'];
        }
    }

    public function refreshWordPressApiLanguagesList(){
        require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
        $languages_list = wp_get_available_translations();

        foreach ($languages_list as $id => $language){
            $this->languages_list[$id] = $id . ' - ' . $language['native_name'];
        }
    }

    public function getLanguagesList(){

        if (!empty($this->languages_list)) return $this->languages_list;

        if ($this->isPolylangEnable()) $this->refreshPolylangLanguagesList();
        else $this->refreshWordPressApiLanguagesList();

        return $this->languages_list;
    }

    public function checkMatchPolylangLanguagesList($key){
        if ($this->languages_list == null) $this->refreshPolylangLanguagesList();

        return !empty($this->languages_list[$key]);
    }
}