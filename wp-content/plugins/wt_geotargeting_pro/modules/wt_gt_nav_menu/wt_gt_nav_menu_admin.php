<?php
/**
 * Изменение панели управления меню
 */
class WtGtNavMenuAdmin {

    function __construct()
    {
        add_filter('wp_edit_nav_menu_walker', array($this, 'edit_nav_menu_walker'), 10, 2);
        add_action('wp_update_nav_menu_item', array($this, 'update_nav_menu_item'), 10, 3);
    }

    /**
     * Переопределение класса для формирования панели управления меню
     *
     * @return void.
     */
    function edit_nav_menu_walker($walker)
    {
        $walker = 'WtGtNavMenuEdit';
        require_once(WT_GT_PRO_PLUGIN_DIR . '/modules/wt_gt_nav_menu/wt_gt_nav_menu_edit.php');
        return $walker;
    }


    /**
     * Редактирование кастомных метаданных элементов меню
     *
     * @return  void
     */
    function update_nav_menu_item($menu_id, $menu_item_id, $args)
    {
        $request = stripslashes_deep($_POST);     // Удаляет экранирование символов

        // Сохраняем режим отображения
        if (isset($request['view_location_mode']) && isset($request['view_location_mode'][$menu_item_id])) {
            update_post_meta($menu_item_id, 'view_location_mode', sanitize_text_field($request['view_location_mode'][$menu_item_id]));
        } else {
            delete_post_meta($menu_item_id, 'view_location_mode');
        }

        // Сохраняем локации
        if (isset($request['view_locations']) && isset($request['view_locations'][$menu_item_id])) {
            $menu_item_view_locations = array_unique($request['view_locations'][$menu_item_id]);
            update_post_meta($menu_item_id, 'view_locations', $menu_item_view_locations);
        } else {
            delete_post_meta($menu_item_id, 'view_locations');
        }
    }
}