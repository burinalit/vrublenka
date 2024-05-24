<?php
/**
 * Кастомный класс для отображения панели управления пользовательскими меню
 */
class WtGtNavMenuEdit extends Walker_Nav_Menu_Edit
{
    /**
     * Start the element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

        $output_new = '';
        // First, make item with standard class
        parent::start_el( $output_new, $item, $depth, $args, $id );

        $output_explode = explode('<div class="menu-item-actions description-wide submitbox">', $output_new, 2);
        $output .= $output_explode[0];

        $meta_view_location_mode = get_post_meta($item->ID, 'view_location_mode', true);

        $output .= '<p class="description description-wide">';


        $output .= '<label for="edit-view_location_mode-' . $item->ID .'">Режим отображения пункта меню';
        $output .= '<select id="edit-view_location_mode-' . $item->ID .'" name="view_location_mode[' . $item->ID .']" type="text" class="widefat">';
        $output .= '<option value="0" ';
        if ($meta_view_location_mode == 0) $output .= 'selected="selected"';
        $output .= '>Для всех локаций</option>';
        $output .= '<option value="1"';
        if ($meta_view_location_mode == 1) $output .= 'selected="selected"';
        $output .= '>Для выбранных локаций</option>';
        $output .= '<option value="2"';
        if ($meta_view_location_mode == 2) $output .= 'selected="selected"';
        $output .= '>Для всех, за исключением выбранных</option>';
        $output .= '</select>';
        $output .= '</label>';

        /* Поле выбора локаций */
        $locations = Wt::$obj->contacts->getRegionsArray();
        $meta_view_locations = get_post_meta($item->ID, 'view_locations', true);

        $output .= '<label for="edit-view_location_mode-' . $item->ID .'">Локации (регионы)';
        $output .= '<select id="edit-view_location_mode-' . $item->ID .'" name="view_locations[' . $item->ID .'][]" type="text" class="widefat" multiple="multiple">';

        foreach ($locations as $location_id => $location_name){
            $output .= '<option value="' . $location_id . '"';
            if (!empty($meta_view_locations) && FALSE !== array_search($location_id, $meta_view_locations)) {
                $output .= 'selected="selected"';
            }
            $output .= '>' . $location_name . '</option>';
        }

        $output .= '</select>';
        $output .= '<span class="description">Можно выбрать несколько регионов используя зажатые клавиши Shift и Ctrl</span>';
        $output .= '</label>';


        $output .= '</p>';

        $output .= '<div class="menu-item-actions description-wide submitbox">';
        $output .= $output_explode[1];
    }
}