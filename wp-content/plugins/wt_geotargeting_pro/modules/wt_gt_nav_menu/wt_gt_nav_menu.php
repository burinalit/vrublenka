<?php
/**
 * Изменение меню
 */
class WtGtNavMenu{
    function __construct()
    {
        // Изменение класса пункта меню
        add_filter('nav_menu_css_class', array($this, 'walker_nav_menu_start_el'), 10, 4);

        // Изменение ссылки пункта меню
        add_filter('nav_menu_link_attributes', array($this, 'navMenuLinkAttributesUpdate'), 10, 4);

        add_action('wp_head', array($this, 'wp_head'), 4);
    }


    function walker_nav_menu_start_el($classes, $item = null, $args = null, $depth = null)
    {
        if (is_null($item)) return $classes;

        $item_location_mode = get_post_meta($item->ID, 'view_location_mode', true);
        $item_locations = get_post_meta($item->ID, 'view_locations', true);

        if (!empty($item_location_mode)){
            $location_id = Wt::$obj->contacts->getValue('region_id');

            if ($item_location_mode == 1){
                if (FALSE === array_search($location_id, $item_locations)) $classes[] = 'display-none';
            }elseif ($item_location_mode == 2){
                if (FALSE !== array_search($location_id, $item_locations)) $classes[] = 'display-none';
            }
        }

        return $classes;
    }

    function wp_head(){
        echo '<style> .display-none{ display: none !important; } </style>';
    }

    /**
     * Изменение атрибутов генерируемого пункта меню на основе маски
     * Формат маски: [wt:example_text] - example_text произвольное мето-поле
     *
     * @param $atts
     * @param $item
     * @param $args
     * @param $depth
     */
    function navMenuLinkAttributesUpdate($atts, $item = null, $args = null, $depth = null){
        if (empty($atts['href'])) return $atts;

        $href = WT::$obj->contacts->contentMaskUpdate($atts['href']);

        if (!$href) return $atts;

        $atts['href'] = $href;

        return $atts;
    }
}