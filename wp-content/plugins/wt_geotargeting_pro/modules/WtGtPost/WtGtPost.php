<?php

/**
 * Class WtGtPost
 *
 * ��������� ����������
 */
class WtGtPost
{
    public $settings = array();

    function __construct(){
        if (class_exists('Wt')) Wt::setObject('post', $this);

        add_action('init', array($this, 'initial'));

        add_filter('wt_geotargeting_initialization_method', array($this, 'filterInitializationMethod'));
        add_filter('wt_geotargeting_initialization_data', array($this, 'filterInitializationData'),10, 2);
    }

    function initial(){
        $this->settings = get_option('wt_geotargeting_post');

        // Поддержка маски в H1, title и description
        add_filter('document_title_parts', array($this, 'filterDocumentTitleParts'), 21, 1);// wp_get_document_title()
        add_filter('the_title', array($this, 'filterTheTitle'), 21, 2);                     // wp_title()
        add_filter('wp_title', array($this, 'filterTheTitle'), 21, 2);              // wp_title()
        add_filter('aioseop_title', array($this, 'filterTheTitle'), 21, 2);
        add_filter('aioseop_description', array($this, 'filterTheTitle'), 21, 2);
        add_filter('wp_title_parts', array($this, 'filterWpTitleParts'), 21, 2);

        // Yoast SEO
        add_filter('wpseo_title', array($this, 'filterWpSeoTitle'), 21, 2);
        add_filter('wpseo_opengraph_title', array($this, 'filterWpSeoTitle'), 21, 2);
        add_filter('wpseo_metadesc', array($this, 'filterWpSeoMetadesk'), 21, 2);

        // Поддержка шорткодов в H1, title и description
        if (!empty($this->settings['activate_shortcode_title_description']) && $this->settings['activate_shortcode_title_description'] == 'on'){
            add_filter('document_title_parts', array($this, 'filterShortcodeDocumentTitleParts'), 21, 1);
            add_filter('wp_title_parts', array($this, 'filterShortcodeWpTitleParts'), 21, 2);

            add_filter('the_title', 'do_shortcode', 21, 1);
            add_filter('wp_title', 'do_shortcode', 21, 1);
            add_filter('aioseop_title', 'do_shortcode', 21, 1);
            add_filter('aioseop_description', 'do_shortcode', 21, 1);

            // Yoast SEO
            add_filter('wpseo_title', array($this, 'filterShortcodeWpSeoTitle'), 21, 2);
            add_filter('wpseo_opengraph_title', array($this, 'filterShortcodeWpSeoTitle'), 21, 2);
            add_filter('wpseo_metadesc', 'do_shortcode', 21, 1);

            // Rank Math
            add_filter('rank_math/frontend/title', 'do_shortcode', 21, 1);
            add_filter('rank_math/frontend/description', 'do_shortcode', 21, 1);
        }

        // Поддержка шорткодов в H1, title и description
        if (!empty($this->settings['activate_menu_setting']) && $this->settings['activate_menu_setting'] == 'on'){
            require(WT_GT_PRO_PLUGIN_DIR . '/modules/wt_gt_nav_menu/wt_gt_nav_menu.php');
            new WtGtNavMenu();
        }

        if (defined('ABSPATH') && is_admin()){
            $this->initialAdmin();
            return;
        }

        // ?????????? ?????? ?? ???????? ???????
        add_filter('pre_get_posts', array($this, 'filterPostsByActiveLocation'), 10, 1);
    }

    public function initialAdmin(){
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtPost/WtGtPostAdmin.php');
        new WtGtPostAdmin();

        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtPost/WtGtPostVisibilityMetaBox.php');
        new WtGtPostVisibilityMetaBox();

        // Настройка Меню
        if (!empty($this->settings['activate_menu_setting']) && $this->settings['activate_menu_setting'] == 'on') {
            require(WT_GT_PRO_PLUGIN_DIR . '/modules/wt_gt_nav_menu/wt_gt_nav_menu_admin.php');
            new WtGtNavMenuAdmin();
        }
    }

    /**
     * ???????? ?????????
     *
     * @param $attribute
     */
    public function getSetting($name){
        if (empty($this->settings[$name])) return null;

        return $this->settings[$name];
    }

    public function getSettingArray($name){
        if (empty($this->settings[$name])) return array();

        $return = explode(',', $this->settings[$name]);

        return $return;
    }

    /**
     * ????????? ???? document_title_parts ?? ??????? wp_get_document_title()
     *
     * @param $title
     * @return string
     */
    function filterDocumentTitleParts($title){
        $title['title'] = $this->updateTitleByUniversalMaskHtmlTitle($title['title']);
        $title['title'] = $this->updateTitleByMask($title['title']);
        return $title;
    }

    /**
     * ????????? ???? wp_title_parts ?????????? ??????? wp_title()
     *
     * @param $title_array
     * @param $title
     * @return mixed
     */
    function filterWpTitleParts($title_array, $title = ''){
        $title_array[0] = $this->updateTitleByUniversalMaskH1($title_array[0]);
        $title_array[0] = $this->updateTitleByMask($title_array[0]);
        return $title_array;
    }

    /**
     * @param $title
     * @param $id
     * @return string
     */
    function filterTheTitle($title = null, $id = null){
        if (!is_singular()) return $title;

        // Отключить обработку для пунктов меню
        $post_type = get_post_type($id);
        if ($post_type == 'nav_menu_item') return $title;

        $title = $this->updateTitleByUniversalMaskH1($title);
        $title = $this->updateTitleByMask($title);

        // Поддержка шорткодов в H1, title и description
        if (!empty($this->settings['activate_shortcode_title_description']) && $this->settings['activate_shortcode_title_description'] == 'on'){
            $title = do_shortcode($title);
        }

        return $title;
    }

    /**
     * ????????? ???? wpseo_title ??????? Yoast SEO
     *
     * @param $title
     * @param $item
     * @return string
     */
    function filterWpSeoTitle($title, $item = null){
        $title = $this->updateTitleByUniversalMaskH1($title);
        $title = $this->updateTitleByMask($title);
        return $title;
    }

    /**
     * ????????? ???? wpseo_metadesc ??????? Yoast SEO
     *
     * @param $title
     * @param $item
     * @return string
     */
    function filterWpSeoMetadesk($title){
        $title = $this->updateTitleByUniversalMaskH1($title);
        $title = $this->updateTitleByMask($title);
        return $title;
    }

    /**
     * ????????? ????????? ? ???????????? ? ??????
     * {example_text} - ???????? ????????? ????????????? ?? ????????
     *
     * @param $title
     * @return mixed
     */
    function updateTitleByMask($title){
        preg_match_all("/{([_a-z]+)}/", $title, $matches);
        $variables = $matches[1];

        foreach ($variables as $key => $value){
            $title = preg_replace(
                '/{' . $value . '}/',
                WT::$obj->contacts->getValue($value),
                $title);
        }

        return $title;
    }

    public function updateTitleByUniversalMaskHtmlTitle($title){
        if (empty($this->settings['mask_html_title'])) return $title;

        $title = preg_replace(
            '/{title}/',
            $title,
            $this->settings['mask_html_title']);

        return $title;
    }

    public function updateTitleByUniversalMaskH1($title){
        if (empty($this->settings['mask_h1'])) return $title;

        $title = preg_replace(
            '/{title}/',
            $title,
            $this->settings['mask_h1']);

        return $title;
    }

    public function filterPostsByActiveLocation($query){

        // Проверка главного цикла
        if(!$query->is_main_query()) return;

        // Проверяем что посетитель находится не в панели администратора
        if(is_admin()) return;

        $support_select_region = $this->getSettingArray('support_select_region');

        if(empty($query->query['post_type'])) $query_post_type = null;
        else $query_post_type = $query->query['post_type'];

        $is_return = true;

        // Если текущая страница является страницей записей
        if (in_array('post', $support_select_region, true) && $query->is_posts_page) $is_return = false;
        // Или активный тип записи активирован
        elseif (in_array($query_post_type, $support_select_region, true)) $is_return = false;


        // ???? ??????????? ????? ???????? ?? ????????? ??????????, ?? ???????? ??????????
        $support_filter_taxonomy = $this->getSettingArray('support_filter_taxonomy');
        foreach ($support_filter_taxonomy as $item){
            if (!empty($query->query[$item])) $is_return = false;
        }

        // ???? ??????????? ????????, ?? ????????? ??????????
        if(!empty($query->query['page']) || !empty($query->query['name']) ) $is_return = true;

        if ($is_return) return;



        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        $args = array(); // ?????????? ??????
        $args['meta_query'] = array('relation' => 'AND'); // ????????? ????? ?????????, ? ??? ??? "? ?? ? ???", ????? ???(OR)

        if ($this->getSetting('filter_neighbors_view') == 'on'){
            $active_locations_including_neighbors = WtGtLocation::getLocationsIncludingNeighborsId($active_location_id);

            $args['meta_query'][] = array(
                'key' => 'visibility_location_id', // ???????? ????????????? ????
                'value' => $active_locations_including_neighbors, // ?????????? ???????? ????????????? ????
                'type' => 'numeric', // ??? ????, ????? ????????? ????? ??????? ????????, ? ??? ????? ?????
                'compare' => 'IN'
            );

        }else{
            $args['meta_query'][] = array(
                'key' => 'visibility_location_id', // ???????? ????????????? ????
                'value' => $active_location_id, // ?????????? ???????? ????????????? ????
                'type' => 'numeric' // ??? ????, ????? ????????? ????? ??????? ????????, ? ??? ????? ?????
            );
        }

        query_posts(array_merge($args, $query->query)); // ??????? ??????? ??????? ??????? ???????????? ????? wp ? ????? ???????? ?????????? ?? ????? ? ?????????
    }

    public function filterInitializationMethod($method)
    {
         if ($method != false) return $method;

         $url_path = WtGtSubdomain::getUrlPath();
         $url_path_explode = explode("/", $url_path);

         if (empty($url_path_explode[0])) return $method;

         $location = WtGtLocation::getObject(array(
             'slug' => $url_path_explode[0],
             'args' => array(
                 'fields' => 'ids'
             )
         ));

        if (empty($location)) return $method;

        $method = 'page';

        return $method;
    }

    public function filterInitializationData($data, $method)
    {
        if ($method != 'page') return $data;

        $url_path = WtGtSubdomain::getUrlPath();
        $url_path_explode = explode("/", $url_path);

        if (empty($url_path_explode[0])) return $method;

        $location = WtGtLocation::getObject(array('slug' => $url_path_explode[0]));

        if (empty($location)) return $method;

        if (!empty($location->post_title)) $data['city'] = $location->post_title;

        WtGtUser::userLocationSave(null, $location->ID, $location->post_title);

        return $data;
    }

    function filterShortcodeDocumentTitleParts($title){
        $title['title'] = do_shortcode($title['title']);
        return $title;
    }

    function filterShortcodeWpTitleParts($title_array, $title = ''){
        $title_array[0] = do_shortcode($title_array[0]);
        return $title_array;
    }

    function filterShortcodeWpSeoTitle($title, $item = null){
        $title = do_shortcode($title);
        return $title;
    }
}