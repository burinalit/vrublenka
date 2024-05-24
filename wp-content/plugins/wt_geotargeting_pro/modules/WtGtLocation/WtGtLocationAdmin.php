<?php

/**
 * Class WtGtLocationAdmin
 */
class WtGtLocationAdmin extends WtGtAdminBehavior
{
    private $cache_city = array();
    private $cache_region = array();
    private $cache_district = array();
    private $cache_locations = array();

    function __construct()
    {
        add_action('admin_menu', array(&$this, 'menu'));
        add_action('admin_init', array(&$this, 'settingsRegister'));
    }

    public function menu()
    {
        add_submenu_page(
            'wt_geotargeting',
            'WT GeoTargeting - Импорт',
            'Импорт',
            'manage_options',
            'import',
            array(&$this, 'optionsPageOutput')
        );
    }

    // ---------- НАСТРОЙКА ----------

    /**
     * Регистрируем настройки.
     * Настройки будут храниться в массиве, а не одна настройка = одна опция.
     */
    function settingsRegister()
    {
        // $option_group, $option_name, $sanitize_callback
        register_setting('wt_geotargeting_import_group', 'wt_geotargeting_import', array(&$this, 'sanitizeCallback'));
        register_setting('wt_geotargeting_import_group', 'wt_geotargeting_import_not_exist', array(&$this, 'sanitizeCallback'));

        add_settings_section(
            'wt_geotargeting_import',
            '',
            '',
            'wt_geotargeting_import_page');

        $field_params = array(
            'type' => 'select',
            'id' => 'import_file_format',
            'option_name' => 'wt_geotargeting_import',
            'label_for' => 'import_file_format',
            'vals' => array(
                1 => 'Список городов IpGeoBase (cities.txt)',
                2 => 'Список городов (каждый с новой строки)',
                3 => 'Файл CSV, разделитель запятая (,)',
                4 => 'Импорт городов службы доставки DPD',
            ),
        );
        add_settings_field('import_file_format', 'Формат файла', array(&$this, 'displaySettings'), 'wt_geotargeting_import_page', 'wt_geotargeting_import', $field_params);

        $field_params = array(
            'type' => 'select',
            'id' => 'import_item_duplicate',
            'option_name' => 'wt_geotargeting_import',
            'label_for' => 'import_item_duplicate',
            'vals' => array(
                0 => 'Пропускать',
//				1 => 'Создавать дубликат',
                2 => 'Перезаписывать'
            ),
        );
        add_settings_field('import_item_duplicate', 'При совпадении городов', array(&$this, 'displaySettings'), 'wt_geotargeting_import_page', 'wt_geotargeting_import', $field_params);

        $field_params = array(
            'type' => 'select',
            'id' => 'import_action',
            'option_name' => 'wt_geotargeting_import',
            'label_for' => 'import_action',
            'vals' => array(
                3 => 'Загрузить файл и импортировать',
                4 => 'Импорт городов службы доставки DPD',
            ),
        );
        add_settings_field('import_action', 'Действие', array(&$this, 'displaySettings'), 'wt_geotargeting_import_page', 'wt_geotargeting_import', $field_params);

    }

    /**
     * Создаем страницу настроек публикаций
     */
    public function optionsPageOutput()
    {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>
            <?php do_action('admin_notices'); ?>
            <form action="options.php" enctype="multipart/form-data" method="POST">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="import_file_upload">Файл импорта</label></th>
                        <td><input name="wt_geotargeting_import_file_upload" type="file"><br><span
                                    class="description">Загрузите файл для импорта регионов</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
                settings_fields('wt_geotargeting_import_group');     // скрытые защитные поля
                do_settings_sections('wt_geotargeting_import_page'); // секции с настройками (опциями).
                do_settings_sections('wt_geotargeting_import_not_exist_page'); // секции с настройками (опциями).
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function sanitizeCallback($sanitize_value)
    {
        if ($sanitize_value['import_action'] == '4'){
            $this->actionDpdDbImport();
        }
        else {
            if (!empty($_FILES['wt_geotargeting_import_file_upload'])) {
                $file_upload = $_FILES['wt_geotargeting_import_file_upload'];

                if (($handle = fopen($file_upload["tmp_name"], "r")) !== FALSE) {

                    if ($sanitize_value['import_file_format'] == '1') {
                        $row = 1;
                        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                            // 0 - ID IpGeoBase
                            // 1 - Город
                            // 2 - Регион
                            // 3 - Округ
                            // 4 - Координата
                            // 5 - Координата

                            $ipgeobase_id = $data[0];
                            $city_name = iconv('CP1251', 'UTF-8', $data[1]);
                            $region_name = iconv('CP1251', 'UTF-8', $data[2]);
                            $district_name = iconv('CP1251', 'UTF-8', $data[3]);

                            // Проверяем наличие округа в кэше для сокращения количества обращений к БД
                            $post_district_id = array_search($district_name, $this->cache_district);

                            if (empty($post_district_id)) {
                                $post_district = WT::$obj->contacts->getRegion($district_name, array('type' => 'district'));

                                if (empty($post_district)) {
                                    $post_district_values = array(
                                        'post_type' => 'region',
                                        'post_title' => $district_name,
                                        'post_status' => 'publish',
                                        'meta_input' => array(
                                            'region_type' => 'district'),
                                        'post_author' => 1
                                    );

                                    $post_district_id = wp_insert_post($post_district_values);
                                } else {
                                    $post_district_id = $post_district->ID;
                                }

                                // Сохраняем значение округа в кэш
                                $this->cache_district[$post_district_id] = $district_name;
                            }

                            // Проверяем наличие региона в кэше для сокращения количества обращений к БД
                            $post_region_id = array_search($region_name, $this->cache_region);

                            if (empty($post_region_id)) {
                                $post_region = WT::$obj->contacts->getRegion($region_name, array('type' => 'region'));

                                if (empty($post_region)) {
                                    $post_region_values = array(
                                        'post_type' => 'region',
                                        'post_title' => $region_name,
                                        'post_status' => 'publish',
                                        'post_parent' => $post_district_id,
                                        'meta_input' => array(
                                            'region_type' => 'region'),
                                        'post_author' => 1
                                    );

                                    $post_region_id = wp_insert_post($post_region_values);
                                } else {
                                    $post_region_id = $post_region->ID;
                                }

                                // Сохраняем значение региона в кэш
                                $this->cache_region[$post_region_id] = $region_name;
                            }

                            // Проверяем наличие города в кэше для сокращения количества обращений к БД
                            $post_city_id = array_search($city_name, $this->cache_city);

                            if (empty($post_city_id)) {
                                $post_city = WT::$obj->contacts->getRegion($city_name, array('type' => 'city'));

                                if (empty($post_city)) {
                                    $post_city_values = array(
                                        'post_type' => 'region',
                                        'post_title' => $city_name,
                                        'post_status' => 'publish',
                                        'post_parent' => $post_region_id,
                                        'meta_input' => array(
                                            'region_type' => 'city',
                                            'ipgeobase_id' => $ipgeobase_id),
                                        'post_author' => 1
                                    );

                                    $post_city_id = wp_insert_post($post_city_values);
                                } else {
                                    $post_city_id = $post_city->ID;
                                }

                                // Сохраняем значение города в кэш
                                $this->cache_city[$post_city_id] = $city_name;
                            }
                        }
                        fclose($handle);

                    } elseif (($sanitize_value['import_file_format'] == '2')) {
                        $row = 1;
                        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                            $city_name = trim($data[0]);

                            if (empty($city_name)) continue;

                            // Проверяем наличие города в кэше для сокращения количества обращений к БД
                            $post_city_id = array_search($city_name, $this->cache_city);

                            if (empty($post_city_id)) {
                                $post_city = WT::$obj->contacts->getRegion($city_name, array('type' => 'city'));

                                if (empty($post_city)) {
                                    $post_city_values = array(
                                        'post_type' => 'region',
                                        'post_title' => $city_name,
                                        'post_status' => 'publish',
                                        'meta_input' => array(
                                            'region_type' => 'city'
                                        ),
                                        'post_author' => 1
                                    );

                                    $post_city_id = wp_insert_post($post_city_values);
                                } else {
                                    $post_city_id = $post_city->ID;
                                }

                                // Сохраняем значение города в кэш
                                $this->cache_city[$post_city_id] = $city_name;
                            }
                        }
                        fclose($handle);
                    } elseif (($sanitize_value['import_file_format'] == '3')) {

                        if (Wt::$obj->localisation->isEnable()) {
                            $columns_language_list = array();
                            $columns_language = array();

                            $languages_list = Wt::$obj->localisation->getLanguagesList();
                            foreach ($languages_list as $key => $value) {
                                $columns_language_list[] = $key;
                            }
                        }

                        $row = 0;
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $row++;

                            // Сохранить названия столбцов
                            if ($row == 1) {
                                $columns = $data;
                                foreach ($data as $key => $value) {
                                    if (Wt::$obj->localisation->isEnable() && array_search($value, $columns_language_list)) $columns_language[$key] = $value;
                                }
                                continue;
                            }

                            // Получить название и тип региона, получить родительские название и тип

                            $parent_name_column_key = array_search('parent_name', $columns);
                            $parent_type_column_key = array_search('parent_type', $columns);
                            $region_name_column_key = array_search('region_name', $columns);
                            $region_type_column_key = array_search('region_type', $columns);

                            $region_parent_id = null;
                            $parent_name = $data[$parent_name_column_key];
                            $parent_type = $data[$parent_type_column_key];
                            $region_name = $data[$region_name_column_key];
                            $region_type = $data[$region_type_column_key];

                            // Сохранение данных в базу данных

                            if (!empty($parent_name) && !empty($parent_type)) {
                                $region_parent_id = null;
                                $region_parent = WT::$obj->location->getObject(array('name' => $parent_name, 'type' => $parent_type));

                                if (empty($region_parent)) {
                                    $region_parent_options = array(
                                        'post_type' => 'region',
                                        'post_title' => $parent_name,
                                        'post_status' => 'publish',
                                        'meta_input' => array(
                                            'type' => $parent_type),
                                        'post_author' => 1,
                                        'post_parent' => 0
                                    );

                                    $region_parent_id = wp_insert_post($region_parent_options);
                                } else {
                                    $region_parent_id = $region_parent->ID;
                                }
                            }

                            $region_id = null;
                            $region = WT::$obj->location->getObject(array('name' => $region_name, 'type' => $region_type));

                            if (empty($region)) {
                                $region_options = array(
                                    'post_type' => 'region',
                                    'post_title' => $region_name,
                                    'post_status' => 'publish',
                                    'meta_input' => array(
                                        'region_type' => $region_type),
                                    'post_author' => 1
                                );

                                if (!empty($region_parent_id)) $region_options['post_parent'] = $region_parent_id;

                                $region_id = wp_insert_post($region_options);
                            } else {
                                $region_id = $region->ID;
                            }

                            // Если регион является дубликатом и перезапись отключена - то завершаем итерацию
                            if ((empty($region) && empty($region_id)) ||
                                (!empty($region) && $sanitize_value['import_item_duplicate'] != '2' && !empty($region_id))
                            ) continue;

                            // Сохранить мета-данные
                            foreach ($data as $key => $value) {
                                if (empty($key) || empty($value)) continue;

                                if ($key == $parent_name_column_key ||
                                    $key == $parent_type_column_key ||
                                    $key == $region_name_column_key) continue;

                                if (Wt::$obj->localisation->isEnable() && isset($columns_language[$key])) continue;

                                update_post_meta($region_id, $columns[$key], $value);

                                //echo 'Сохранение meta-переменной: ' . $value;
                            }

                            if (!empty($region_parent_id)){
                                wp_update_post(array('ID' => $region_id, 'post_parent' => $region_parent_id));
                            }


                            // Сохранить перевод названия
                            if (Wt::$obj->localisation->isEnable()) {
                                foreach ($columns_language as $key => $value) {
                                    if (empty($data[$key])) continue;

                                    $localisations = array();
                                    $localisations['region_name'] = $data[$key];
                                    update_post_meta($region_id, $value, $localisations);

                                    //echo 'Сохранение перевода ' . $key . ' в meta-переменной: ' . $value;
                                }
                            }
                        }
                        fclose($handle);
                    }
                    add_action('admin_notices', function () {
                        echo '<div class="notice notice-success"><p>Импорт выполнен успешно.</p></div>';
                    });
                } else {
                    add_action('admin_notices', function () {
                        echo '<div class="notice notice-error"><p>Файл импорта отсутствует.</p></div>';
                    });
                }
            }
        }
        return $sanitize_value;
    }

    function actionDpdDbImport(){
        global $DPDconfig;

//        $dpd_action = new \DPD\Actions(new \Ipol\DPD\Config\Config($DPDconfig));

        $db = \Ipol\DPD\DB\Connection::getInstance(new \Ipol\DPD\Config\Config($DPDconfig));
        $locationsTable = $db->getTable('location')->find()->fetchAll();

        $wt_gt_location_import_from_dbd_last_id = get_option('wt_gt_location_import_from_dbd_last_id');

        $row_nmb = 0;
        foreach ($locationsTable as $location){
            $row_nmb++;

            if (!empty($wt_gt_location_import_from_dbd_last_id) && $wt_gt_location_import_from_dbd_last_id > $location['ID']) continue;

            $dbd_table_id = $location['ID'];
            $country_code = $location['COUNTRY_CODE'];
            $country_name = $location['COUNTRY_NAME'];
            $region_name = $location['REGION_NAME'];
            $dbd_city_id = $location['CITY_ID'];
            $city_name = $location['CITY_NAME'];
            $is_city = $location['IS_CITY'];

            $post_country_id = null;
            $post_country =  null;
            $post_region_id = null;
            $post_region =  null;
            $post_city_id = null;
            $post_city =  null;


            // Проверяем наличие страны в кэше для сокращения количества обращений к БД
            if (isset($this->cache_locations[$country_code])) $post_country_id = $this->cache_locations[$country_code]['post_id'];

            if (empty($post_country_id)) {
                $post_country = WtGtLocation::getObject(array('name' => $country_name, 'type' => 'country'));

                if (!empty($post_country)) $post_country_id = $post_country->ID;
                else {
                    $post_country_values = array(
                        'post_type' => 'region',
                        'post_title' => $country_name,
                        'post_status' => 'publish',
                        'meta_input' => array(
                            'region_type' => 'country',
                            'iso' => $country_code,
                            'country_iso' => $country_code,
                            ),
                        'post_author' => 1
                    );

                    $post_country_id = wp_insert_post($post_country_values);
                }

                // Сохраняем значение страны в кэш
                $this->cache_locations[$country_code]['country_name'] = $country_name;
                $this->cache_locations[$country_code]['post_id'] = $post_country_id;
            }

            // Проверяем наличие региона в кэше для сокращения количества обращений к БД
            if (isset($this->cache_locations[$country_code]['regions'][$region_name]))
                $post_region_id = $this->cache_locations[$country_code]['regions'][$region_name]['post_id'];

            if (empty($post_region_id)) {
                $post_region = WtGtLocation::getObject(array('name' => $region_name, 'type' => 'region', 'post_parent' => $post_country_id));

                if (!empty($post_region)) $post_region_id = $post_region->ID;
                else{
                    $post_region_values = array(
                        'post_type' => 'region',
                        'post_title' => $region_name,
                        'post_status' => 'publish',
                        'post_parent' => $post_country_id,
                        'meta_input' => array(
                            'region_type' => 'region',
                            'country_iso' => $country_code
                        ),
                        'post_author' => 1
                    );

                    $post_region_id = wp_insert_post($post_region_values);
                }

                // Сохраняем значение региона в кэш
                $this->cache_locations[$country_code]['regions'][$region_name]['post_id'] = $post_region_id;
            }

            // Проверяем наличие города в кэше для сокращения количества обращений к БД
            if (isset($this->cache_locations[$country_code]['regions'][$region_name]['cities'][$city_name]))
                $post_city_id = $this->cache_locations[$country_code]['regions'][$region_name]['cities'][$city_name]['post_id'];

            if (empty($post_city_id)) {
                $post_city = WtGtLocation::getObject(array('name' => $city_name, 'type' => 'city', 'post_parent' => $post_region_id));

                if (empty($post_city)) {
                    $post_city_values = array(
                        'post_type' => 'region',
                        'post_title' => $city_name,
                        'post_status' => 'publish',
                        'post_parent' => $post_region_id,
                        'meta_input' => array(
                            'region_type' => 'city',
                            'dbd_table_id' => $dbd_table_id,
                            'dbd_city_id' => $dbd_city_id,
                            'country_iso' => $country_code),
                        'post_author' => 1
                    );

                    $post_city_id = wp_insert_post($post_city_values);
                }

                // Сохраняем значение города в кэш
                // $this->cache_locations[$country_code]['regions'][$region_name]['cities'][$city_name]['post_id'] = $post_city_id;
            }

            update_option('wt_gt_location_import_from_dbd_last_id', $dbd_table_id);
        }
        delete_option('wt_gt_location_import_from_dbd_last_id');
    }
}