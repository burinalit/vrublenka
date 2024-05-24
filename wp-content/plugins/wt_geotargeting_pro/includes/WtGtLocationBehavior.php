<?php

/**
 * Поведение для локацие
 */
class WtGtLocationBehavior
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

    static $parameter_type_list = array();

    static $table_relation_post = 'location_relationships';

    public $cache_objects = array();

    public function initial(){}

    public function initialAdmin(){}

    public function refreshParameters($id){}

    /**
     * Установка значений на основе текущего региона и контактов из регионов

     * @return bool
     */
    public function refreshParametersFromActiveLocation(){}
    public function setValuesBasedRegion(){ $this->refreshParametersFromActiveLocation(); }

    public function refreshParametersFromLocation($location){}
    public function setValueFromRegion($location){ $this->refreshParametersFromLocation($location);}

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
    static function getObjects($options = array()){}


    static function getObject($options = array()){}

    /**
     * Получить локацию по умолчанию
     * Параметры:
     * 		filter / parent - Фильтрация по родителю
     *
     * @param array $params
     * @return WP_Query
     */
    function getObjectDefault($params = array()){}

    /**
     * Получить город
     *
     * @param $name
     * @param array $params
     * @return null
     */
    public function getCity($name, $options = array()){}

    public function getCityId($name, $options = array()){}

    static function getNameById($id){}

    function getNamesParents($location_id = null){}

    /**
     * Формирование массива регионов
     * Параметры:
     * 		filter / type - тип локации
     * 		filter / parent - родительская локация
     * 		pack - упаковка данных по иерархии
     * 		columns - количество колонок
     *      orderby - сортировка. Структура аналогична одноименному параметру WP_Query.
     *
     * @param array $params
     * @return array
     */
    public function getRegionsArray($params = array()){ return $regions_arr = array();}

    /**
     * Добавить связь со страницей
     *
     * @param $post_id
     */
    function insertPost($post_id){}

    /**
     * Удалить связь со страницей
     *
     * @param $post_id
     */
    function deletePost($post_id){}

    /**
     * Получить массив ID страниц, прикрепленных к текущему региону
     *
     * @return mixed
     */
    function getPostsId(){}

    static function getLocationsIncludingNeighborsId($id){}

    /**
     * Создание таблицы "Связь локации и поста"
     * Поле object_type может принимать значения: post, user
     */
    static function createTableLocationRelationships(){
        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . self::$table_relation_post;

        $charset_collate = 'DEFAULT CHARACTER SET ' . $wpdb->charset .' COLLATE ' . $wpdb->collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = 'CREATE TABLE ' . $table_name . ' (
            location_id bigint(20) NOT NULL,
            object_id bigint(20) NOT NULL,
            object_type varchar(10) NOT NULL,
            PRIMARY KEY  (location_id, object_id, object_type)
        ) ' . $charset_collate .';';

        dbDelta($sql);
    }

    /**
     * Добавить связь между страницей и локацией
     *
     * @param $location_id
     * @param $post_id
     */
    function insertItemLocationPost($location_id, $post_id){}

    static function insertLocationRelationships($location_id, $object_id, $object_type = 'post'){}

    /**
     * Удалить связь между страницей и локацией
     *
     * @param $location_id
     * @param $post_id
     */
    function deleteItemLocationPost($location_id, $post_id){}

    public function ajax_search_locations(){
        $cities = array();

        echo json_encode($cities);

        die;
    }
}