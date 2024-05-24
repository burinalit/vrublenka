<?php

/**
 * Локации
 * User: Roman Kusty
 * Date: 26.02.2017
 */
class WtGtLocation
{
    public $id;

    static $types = array(
        'administrative_district' => 'Административный округ',
        'city' => 'Город',
        'region' => 'Регион',
        'district' => 'Округ',
        'country' => 'Страна'
    );

    public $parameter = array();

    static $parameter_type_list = array(

    );

    static $table_relation_post = 'location_relationships';

    public $cache_objects = array();

    function __construct($id = null)
    {
        if (!empty($id)){
            $this->id = $id;
            $this->refreshParameters($id);
        }
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')){
            Wt::setObject('location', $this);
        }


        add_action('wp_ajax_search_location', array($this, 'ajax_search_locations'));           // Привязка ajax-события search_location
        add_action('wp_ajax_nopriv_search_location', array($this, 'ajax_search_locations'));    // Привязка ajax-события search_location
    }

    public function initialAdmin(){

    }

    public function refreshParameters($id){
        $this->setValue('region_id', $id);

        $meta = get_metadata('post', $id);

        foreach ($meta as $key => $value){
            $this->setValue($key, $value[0]);
        }
    }



    public function setValue($name, $value){
        $this->parameter[$name] = $value;
    }

    public function getValue($attribute){
        if (empty($this->parameter[$attribute])) return null;

        return $this->parameter[$attribute];
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
        // Нормализуем устаревшие переменные
        if (!empty($options['region_id'])) $options['id'] = $options['region_id'];


        $args = array();
        $args['post_type'] = 'region';
        $args['post_status'] = 'publish';

        if (!empty($options['id'])){
            $args['p'] = $options['id'];
            $query = new WP_Query($args);

            return $query->posts;
        }

        $args['meta_query'] = array();
        $args['posts_per_page'] = -1;
        $args['order'] = 'ASC';
        $args['update_post_meta_cache'] = false;
        $args['update_post_term_cache'] = false;

        if (!empty($options['fields'])) $args['fields'] = $options['fields'];

        if (!empty($options['name'])) $args['title'] = $options['name'];
        if (!empty($options['region_name'])) $args['title'] = $options['region_name'];

        if (!empty($options['region_type'])) $args['meta_query'][] = array(
            'key'     => 'region_type',
            'value'   => $options['region_type']
        );

        if (!empty($options['meta_query'])) {
            $args['meta_query'] = $options['meta_query'];
        }

        if (!empty($options['subdomain']) && $options['subdomain'] == 'NOT EXISTS'){
            $args['meta_query'][] = array(
                'key'     => 'subdomain',
                'compare' => 'NOT EXISTS'
            );
        }elseif (!empty($options['subdomain'])){
            $args['meta_query'][] = array(
                'key'     => 'subdomain',
                'value'   => $options['subdomain']
            );
        }

        // Условие для поиска региона по субдомену
        if (!empty($options['slug']) && $options['slug'] == 'NOT EXISTS'){
            // Субдомен отсутствует
            $args['meta_query'][] = array(
                array(
                    'key'     => 'by_default',
                    'value'   => 'true',
                ),
            );
        }
        elseif (!empty($options['slug'])) $args['name'] = $options['slug'];

        if (!empty($options['post_parent'])) $args['post_parent'] = $options['post_parent'];

        if (!empty($options['country_iso'])) $args['meta_query'][] = array(
            'key'     => 'country_iso',
            'value'   => $options['country_iso']
        );

        if (!empty($options['order'])) $args['order'] = $options['order'];
        if (!empty($options['orderby'])) $args['orderby'] = $options['orderby'];

        if (!empty($options['args']) && is_array($options['args'])) $args = array_merge ($args, $options['args']);

        $query = new WP_Query($args);

        return $query->posts;
    }


    static function getObject($options = array())
    {
        $posts = self::getObjects($options);

        if (empty($posts[0])) return null;

        return $posts[0];
    }

    /**
     * Получить локацию по умолчанию
     * Параметры:
     * 		filter / parent - Фильтрация по родителю
     *
     * @param array $params
     * @return WP_Query
     */
    function getObjectDefault($params = array()){

        $args = array(
            'post_type' => 'region',
            'meta_query' => array(
                array(
                    'key'     => 'by_default',
                    'value'   => 'true',
                ),
            ),
        );

        if (!empty($params['filter']) && !empty($params['filter']['parent'])){
            $args['post_parent'] = $params['filter']['parent'];
        }

        $query = new WP_Query($args);

        if (empty($query->posts[0])) return false;

        $region = $query->posts[0];

        return $region;
    }

    /**
     * Получить город
     *
     * @param $name
     * @param array $params
     * @return null
     */
    public function getCity($name, $options = array()){
        $options['type'] = 'city';
        $options['name'] = $name;

        return $this->getObject($options);
    }

    public function getCityId($name, $options = array())
    {
        $name = trim($name);

        $region = $this->getCity($name, $options);

        if (empty($region->ID)) return null;

        return $region->ID;
    }

    static function getNameById($id)
    {
        if (!empty(Wt::$obj->location->cache_objects[$id]['name'])) return Wt::$obj->location->cache_objects[$id]['name'];


        $location_object = self::getObject(array('id' => $id));

        if (empty($location_object->post_title)) return false;

        Wt::$obj->location->cache_objects[$id]['name'] = $location_object->post_title;

        return $location_object->post_title;
    }

    function getNamesParents($location_id = null){
        $names = array();

        if (empty($location_id)) $location_id = Wt::$obj->contacts->getValue('region_id');

        $location = self::getObject(['id' => $location_id]);

        if (empty($location)) return false;

        $location_type = get_post_meta($location->ID, 'region_type', true);

        if (empty($location_type)) return false;

        $country_iso = get_post_meta($location->ID, 'country_iso', true);

        if (!empty($country_iso)) $country_name = Wt::$obj->data_files->getCountryName($country_iso);
        if (!empty($country_iso)) $names['country_code'] = $country_iso;
        if (!empty($country_name)) $names['country_name'] = $country_name;


        $names[$location_type . '_name'] = $location->post_title;

        if (!empty($location->post_parent)) {
            $names_parents = $this->getNamesParents($location->post_parent);
            $names = array_merge($names, $names_parents);
        }

        return $names;
    }




    /**
     * Добавить связь со страницей
     *
     * @param $post_id
     */
    function insertPost($post_id){
        $this->insertItemLocationPost($this->id, $post_id);
    }

    /**
     * Удалить связь со страницей
     *
     * @param $post_id
     */
    function deletePost($post_id){
        $this->deleteItemLocationPost($this->id, $post_id);
    }

    /**
     * Получить массив ID страниц, прикрепленных к текущему региону
     *
     * @return mixed
     */
    function getPostsId(){
        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . self::$table_relation_post;

        $posts = $wpdb->get_col(
            $wpdb->prepare('SELECT object_id FROM ' . $table_name . ' WHERE `location_id` = %d AND `object_type` = "post";', $this->id)
        );

        return $posts;
    }

    static function getLocationsIncludingNeighborsId($id){
        // Получаем родителя локации
        $location = self::getObject(array('id' => $id));

        if (empty($location->post_parent)) return array($id);

        // Получаем все вложенные локации родителя
        $locations = self::getObjects(array(
            'post_parent' => $location->post_parent,
            'fields' => 'ids'
        ));
        return $locations;
    }

    /**
     * Добавить связь между страницей и локацией
     *
     * @param $location_id
     * @param $post_id
     */
    function insertItemLocationPost($location_id, $post_id){
        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . self::$table_relation_post;

        $wpdb->insert(
            $table_name,
            array(
                'location_id' => $location_id,
                'object_id' => $post_id,
                'object_type' => 'post'
            ),
            array('%d', '%d')
        );
    }

    static function insertLocationRelationships($location_id, $object_id, $object_type = 'post'){
        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . self::$table_relation_post;

        $result = $wpdb->insert(
            $table_name,
            array(
                'location_id' => $location_id,
                'object_id' => $object_id,
                'object_type' => $object_type
            ),
            array('%d', '%d', '%s')
        );

        return $result;
    }

    /**
     * Удалить связь между страницей и локацией
     *
     * @param $location_id
     * @param $post_id
     */
    function deleteItemLocationPost($location_id, $post_id){
        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . self::$table_relation_post;

        $wpdb->delete(
            $table_name,
            array(
                'location_id' => $location_id,
                'object_id' => $post_id,
                'object_type' => 'post'
            ),
            array('%d', '%d')
        );
    }

    function ajax_search_locations(){
        // Очищаем входящие данные
        $value = sanitize_text_field($_POST['value']);

        if (isset($_POST['data_type'])) $data_type = sanitize_text_field($_POST['data_type']);
        else $data_type = 'array';

        // Поиск подходящих городов
        if ($data_type == 'array'){
            $cities = Wt::$obj->contacts->getRegionsArray(
                array(
                    'filter' => array(
                        'type' => 'city'
                    ),
                    'title' => $value
                )
            );
        }elseif ($data_type == 'object'){
            $cities = Wt::$obj->contacts->getRegionsArray(
                array(
                    'item_type' => 'object',
                    'title' => $value,
                    'filter' => array(
                        'type' => 'city')
                )
            );
        }


        echo json_encode($cities);

        die;
    }
}