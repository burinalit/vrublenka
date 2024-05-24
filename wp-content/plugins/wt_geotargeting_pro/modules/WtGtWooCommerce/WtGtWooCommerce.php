<?php

/**
 * Class WtGtWooCommerce
 */
class WtGtWooCommerce
{
    public $settings = array();

    function __construct()
    {
        // Открываем доступ к коду через статический класс WT плагина WT KIT
        if (class_exists('Wt')) {
            Wt::setObject('woocommerce', $this);
        }

        $this->settings = get_option('wt_geotargeting_woocommerce');

        // Обновление видимости товара в регионах
        if (!empty($this->getSetting('product_visibility_location_terms_enable'))){
            add_action('woocommerce_product_set_visibility', array($this, 'action_product_set_visibility'), 10, 2);
            add_filter( 'woocommerce_product_query_tax_query', array($this, 'filter_product_query_tax_query'), 10, 2 );
        }


        if (defined('ABSPATH') && is_admin()) {
            $this->initialAdmin();
            return;
        }

        // Проверка активации модуля
        $woocommerce_enable = $this->getSetting('woocommerce_enable');
        if (empty($woocommerce_enable)) return;

        add_filter('woocommerce_product_get_sale_price', array($this, 'filter_product_get_sale_price'), 99, 2);
        add_filter('woocommerce_product_get_regular_price', array($this, 'filter_product_get_regular_price'), 10, 2);
        add_filter('woocommerce_product_get_price', array($this, 'filter_product_get_price'), 10, 2);
        add_filter('woocommerce_product_is_on_sale', array($this, 'filter_product_is_on_sale'), 10, 2);


        add_filter('woocommerce_product_get_stock_quantity', array($this, 'filter_product_get_stock_quantity'), 10, 2);
        add_filter('woocommerce_product_is_in_stock', array($this, 'filter_product_is_in_stock'), 10, 2);


        // Оформление заказа: Автозаполнение полей Страна/Регионг/Город
        $default_address_fields_enable = $this->getSetting('default_address_fields_enable');
        if (!empty($default_address_fields_enable)){
            add_filter('default_checkout_billing_country', array($this, 'filter_default_checkout_billing_country'), 10, 2);
            add_filter('woocommerce_default_address_fields', array($this, 'filter_default_address_fields'));
            add_filter('woocommerce_billing_fields', array($this, 'filter_billing_fields'), 1001, 2);
            add_filter('woocommerce_shipping_fields', array($this, 'filter_shipping_fields'), 1001, 2);
        }

        !is_admin() and add_action('init', array($this, 'initial'));

    }

    function initial()
    {

    }

    public function initialAdmin()
    {
        require(WT_GT_PRO_PLUGIN_DIR . '/modules/WtGtWooCommerce/WtGtWooCommerceAdmin.php');
        new WtGtWooCommerceAdmin();
    }

    public function getSetting($name)
    {
        if (empty($this->settings[$name])) return null;

        return $this->settings[$name];
    }

    public function priceUpdateCoefficient($price){
        $regular_price_coefficient = Wt::$obj->contacts->getValue('regular_price_coefficient');
        if (!empty($regular_price_coefficient)){
            $regular_price_coefficient = str_replace(",", ".", $regular_price_coefficient);
            $regular_price_coefficient = $regular_price_coefficient + 0;
            $price = $price * $regular_price_coefficient;
        }

        return $price;
    }

    function filter_product_get_regular_price($price, $product = null){
        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        if (empty($active_location_id)) return $price;

        $active_location_price = get_post_meta($product->get_id(), '_regular_price_location_' . $active_location_id, true);

        if (!empty($active_location_price)) return $active_location_price;

        return $this->priceUpdateCoefficient($price);
    }

    function filter_product_get_sale_price($price, $product = null){
        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        if (empty($active_location_id)) return $price;

        $active_location_regular_price = get_post_meta($product->get_id(), '_regular_price_location_' . $active_location_id, true);
        $active_location_sale_price = get_post_meta($product->get_id(), '_sale_price_location_' . $active_location_id, true);

        if (!empty($active_location_sale_price)) return $active_location_sale_price;
        elseif (!empty($active_location_regular_price)) return null;

        if (empty($price)) return $price;

        return $this->priceUpdateCoefficient($price);
    }

    function filter_product_get_price($price, $product = null){
        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        if (empty($active_location_id)) return $price;

        $active_location_regular_price = get_post_meta($product->get_id(), '_regular_price_location_' . $active_location_id, true);
        $active_location_sale_price = get_post_meta($product->get_id(), '_sale_price_location_' . $active_location_id, true);

        if (!empty($active_location_sale_price)) return $active_location_sale_price;
        elseif (!empty($active_location_regular_price)) return $active_location_regular_price;

        return $this->priceUpdateCoefficient($price);
    }

    function filter_product_is_on_sale($on_sale, $product){
        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        if (empty($active_location_id)) return $on_sale;

        $active_location_regular_price = get_post_meta($product->get_id(), '_regular_price_location_' . $active_location_id, true);
        $active_location_sale_price = get_post_meta($product->get_id(), '_sale_price_location_' . $active_location_id, true);

        return $on_sale;
    }

    function filter_product_is_in_stock($on_stock, $product){
        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        if (empty($active_location_id)) return $on_stock;

        $active_location_stock = get_post_meta($product->get_id(), '_stock_location_' . $active_location_id, true);

        if (!isset($active_location_stock)) return $on_stock;

        if ($active_location_stock == '') return $on_stock;

        if ($active_location_stock == '0') return false;

        return true;
    }

    function filter_product_get_stock_quantity($stock, $product = null){

        $active_location_id = Wt::$obj->contacts->getValue('region_id');

        if (empty($active_location_id)) return $stock;

        $active_location_stock = get_post_meta($product->get_id(), '_stock_location_' . $active_location_id, true);

        if (!isset($active_location_stock)) return $stock;

        if ($active_location_stock == '') return $stock;

        return $active_location_stock;
    }

    /**
     * Оформление заказа: Заполнение полей Страна/Регион/Город значениями активного региона
     *
     * @param $address_fields
     * @return mixed
     */
    function filter_default_address_fields($address_fields){
        $location_names = Wt::$obj->location->getNamesParents();

        if (!empty($location_names['city_name'])) $address_fields['city']['default'] = $location_names['city_name'];
        if (!empty($location_names['region_name'])) $address_fields['state']['default'] = $location_names['region_name'];

        if (!empty($location_names['country_code'])) $address_fields['country']['default'] = $location_names['country_code'];
        elseif(!empty($location_names['country_name'])) $address_fields['country']['default'] = $location_names['country_name'];

        return $address_fields;
    }

    function filter_billing_fields($fields, $country = null){
        $location_names = Wt::$obj->location->getNamesParents();

        if (!empty($location_names['city_name'])) $fields['billing_city']['default'] = $location_names['city_name'];
        if (!empty($location_names['region_name'])) $fields['billing_state']['default'] = $location_names['region_name'];

        if (!empty($location_names['country_code'])) $fields['billing_country']['default'] = $location_names['country_code'];
        elseif(!empty($location_names['country_name'])) $fields['billing_country']['default'] = $location_names['country_name'];

        return $fields;
    }

    function filter_shipping_fields($fields, $country = null){
        $location_names = Wt::$obj->location->getNamesParents();

        if (!empty($location_names['city_name'])) $fields['shipping_city']['default'] = $location_names['city_name'];
        if (!empty($location_names['region_name'])) $fields['shipping_state']['default'] = $location_names['region_name'];

        if (!empty($location_names['country_code'])) $fields['shipping_country']['default'] = $location_names['country_code'];
        elseif(!empty($location_names['country_name'])) $fields['shipping_country']['default'] = $location_names['country_name'];

        return $fields;
    }

    public function filter_default_checkout_billing_country($value, $input){
        $location_names = Wt::$obj->location->getNamesParents();

        if (!empty($location_names['country_code'])) return $location_names['country_code'];

        return $value;
    }

    static function productSetPriceLocation($post_id, $price, $price_type, $location_id){
        $key = '_' . $price_type . '_price_location_' . $location_id;

        if (empty($price)){
            delete_post_meta($post_id, $key);
            return;
        }

        update_post_meta($post_id, $key, $price);
    }

    static function productSetStockLocation($post_id, $stock, $location_id){
        $key = '_stock_location_' . $location_id;

        if (empty($stock)){
            delete_post_meta($post_id, $key);
            return;
        }

        update_post_meta($post_id, $key, $stock);
    }

    /**
     * Обновление таксономии product_visibility отвечающей за фильтрацию товара
     * Добавляем фильтрацию отсутствующего товара в регионах
     *
     * @param $product_id
     * @param $product_catalog_visibility
     */
    function action_product_set_visibility($product_id, $product_catalog_visibility){
        $product = wc_get_product($product_id);
        $terms = array();

        $locations = Wt::$obj->location->getObjects(array('fields' => 'ids'));

        foreach ($locations as $location_id){
            $location_stock = $product->get_meta('_stock_location_' . $location_id);

            if ($location_stock === '0'){
                $terms[] = 'outofstock_location_' . $location_id;
            }else{
                wp_remove_object_terms($product_id, '_stock_location_' . $location_id, 'product_visibility');
            }
        }

        if (!empty($terms)) wp_set_post_terms($product_id, $terms, 'product_visibility', true);
    }

    /**
     * Корректировка запроса фильтрации товара. Исключение отсутствующего товара в регионах
     *
     * @param $tax_query
     * @param $that
     * @return mixed
     */
    function filter_product_query_tax_query($tax_query, $that){
        if ('yes' !== get_option('woocommerce_hide_out_of_stock_items')) return $tax_query;

        if (empty($tax_query)) return $tax_query;

        foreach ($tax_query as $key => $item){
            if (empty($item['taxonomy'])) continue;
            if ($item['taxonomy'] != 'product_visibility') continue;
            if ($item['field'] != 'term_taxonomy_id') continue;
            if (empty($item['terms'])) continue;

            $outofstock_term = get_term_by('slug', 'outofstock', 'product_visibility');
            $active_location_id = Wt::$obj->contacts->getValue('region_id');
            $outofstock_location_term = get_term_by('slug', 'outofstock_location_' . $active_location_id, 'product_visibility');
            if (empty($outofstock_location_term)) continue;


            foreach ($item['terms'] as $term_key => $term){
                if ($term == $outofstock_term->term_id) $tax_query[$key]['terms'][$term_key] = $outofstock_location_term->term_id;
            }
        }
        return $tax_query;
    }
}