<?php
/**
 * Кастомный класс для отображения панели управления пользовательскими меню
 */
class WtGtNavMenuEdit extends Walker_Nav_Menu_Edit
{
    /**
     * Start the element output.
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        parent::start_el($output, $item, $depth, $args, $id);

        // Отключение ошибок libxml и передача полномочий по выборке и обработке информации об ошибках пользователю
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        // Prevent using LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD as support deppends on Libxml version.

        // Загрузка HTML из строки
        $dom->loadHTML(mb_convert_encoding('<div>' . $output . '</div>', 'HTML-ENTITIES', 'UTF-8'));

        // Remove this container from the document, DOMElement of it still exists.
        $container = $dom->getElementsByTagName('div')->item(0);
        $container = $container->parentNode->removeChild($container);
        // Remove all  direct children from the document ( <html>,<head>,<body> ).
        while ($dom->firstChild) {
            $dom->removeChild($dom->firstChild);
        }
        // Document clean. Add direct children of the container to the document again.
        while ($container->firstChild) {
            $dom->appendChild($container->firstChild);
        }

        $xpath = new \DOMXpath($dom);
        // Clear the errors so they are not kept in memory.
        libxml_clear_errors();
        $classname = 'menu-item';
        // Get last li element as output will contain all menu elements before the current element.
        $li = $xpath->query("(//li[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')])[last()]");
        $menu_element_id = (int)str_replace('menu-item-', '', $li->item(0)->getAttribute('id'));
        // Safety check.
        if ((int)$menu_element_id !== (int)$item->ID) {
            return;
        }
        // Get the fieldset in the list element.
        // @todo need to make sure is the correct fieldset by class. No risk now as there is only one.
        $fieldset = $li->item(0)->getElementsByTagName('fieldset');
        // Get the firs element of the fieldset, in this case it's a span.
        // @todo get first element independently of the tag.
        $fieldset_item = $fieldset->item(0);
        if (isset($fieldset_item)) $in_fieldset = $fieldset_item->getElementsByTagName('span');
        // Create an element as a wrapper for the fields.
        $custom_fields_wrapper = $dom->createElement('div');
        $custom_fields_wrapper->setAttribute('class', 'menu_custom_fields');


        /* Поле выбора режима отображения */

        $field = array(
            'name' => 'view_location_mode',
            'label' => 'Режим отображения пункта меню',
            'element' => 'select',
            'attrs' => array(
                'type' => 'text',
                'class' => 'widefat'
            ),
            'options' => array(
                '0' => 'Для всех локаций',
                '1' => 'Для выбранных локаций',
                '2' => 'Для всех, за исключением выбранных'
            ),
        );

        $label = false;
        $input = false;

        $field_wrapper = $dom->createElement('p');                        // Создает новый узел-элемент
        $field_wrapper->setAttribute('class', 'description description-wide');

        // Create the label and input elements.
        $label = $dom->createElement('label', esc_html($field['label']));
        $label->setAttribute('for', "edit-" . $field['name'] . "-{$item->ID}");
        $input = $dom->createElement($field['element']);
        $input->setAttribute('id', "edit-" . $field['name'] . "-{$item->ID}");
        $input->setAttribute('name', $field['name'] . "[{$item->ID}]");

        // Установка атрибутов
        if (isset($field['attrs'])) {
            foreach ($field['attrs'] as $attr_key => $attr_value) {
                $input->setAttribute($attr_key, $attr_value);
            }
        }
        // If the element has options then create the options.
        if (isset($field['options'])) {
            if (method_exists($this, 'create_options_for_' . $field['element'])) {
                $input = call_user_func(array(
                    $this,
                    'create_options_for_' . $field['element']
                ), $dom, $field['name'], $item->ID, $input, $field);
            }
        } else {
            // Set the value.
            $input->setAttribute('value', get_post_meta($item->ID, $field['name'], true));
        }
        // Append the elements.
        $label->appendChild($input);
        $field_wrapper->appendChild($label);
        $custom_fields_wrapper->appendChild($field_wrapper);


        /* Поле выбора локаций */
        $locations = Wt::$obj->contacts->getRegionsArray();

        $field = array(
            'name' => 'view_locations',
            'label' => 'Локации (регионы)',
            'element' => 'select',
            'attrs' => array(
                'type' => 'text',
                'class' => 'widefat',
                'multiple' => 'multiple'
            ),
            'options' => $locations,
            'description' => 'Можно выбрать несколько регионов используя зажатые клавиши Shift и Ctrl'
        );

        $label = false;
        $input = false;
        $span = false;

        $field_wrapper = $dom->createElement('p');                        // Создает новый узел-элемент
        $field_wrapper->setAttribute('class', 'description description-wide');

        // Create the label and input elements.
        $label = $dom->createElement('label', esc_html($field['label']));
        $label->setAttribute('for', "edit-" . $field['name'] . "-{$item->ID}");
        $input = $dom->createElement($field['element']);
        $input->setAttribute('id', "edit-" . $field['name'] . "-{$item->ID}");
        $input->setAttribute('name', $field['name'] . "[{$item->ID}][]");

        // Установка атрибутов
        if (isset($field['attrs'])) {
            foreach ($field['attrs'] as $attr_key => $attr_value) {
                $input->setAttribute($attr_key, $attr_value);
            }
        }
        // If the element has options then create the options.
        if (isset($field['options'])) {
            if (method_exists($this, 'create_options_for_' . $field['element'])) {
                $input = call_user_func(array(
                    $this,
                    'create_options_for_' . $field['element']
                ), $dom, $field['name'], $item->ID, $input, $field);
            }
        } else {
            // Set the value.
            $input->setAttribute('value', get_post_meta($item->ID, $field['name'], true));
        }

        if (!empty($field['description'])){
            $span = $dom->createElement('span', esc_html($field['description']));
            $span->setAttribute('class', 'description');
        }

        // Append the elements.
        $label->appendChild($input);

        if (isset($span)) $label->appendChild($span);

        $field_wrapper->appendChild($label);
        $custom_fields_wrapper->appendChild($field_wrapper);

        // Intert it at the beginng of the fieldset.
        if (isset($in_fieldset)) $in_fieldset->item(0)->parentNode->insertBefore($custom_fields_wrapper, $in_fieldset->item(0));
        $output = $dom->saveHTML();
    }

    public function create_options_for_select($dom, $field_key, $menu_item_id, $input, $field)
    {
        $meta_values = get_post_meta($menu_item_id, $field_key, true);

        if (!is_array($meta_values)) $meta_values = explode(",", $meta_values);

        foreach ($field['options'] as $key => $name) {
            $option = $dom->createElement('option', esc_html($name));
            $option->setAttribute('value', $key);
            if (FALSE !== array_search($key, $meta_values)) {
                $option->setAttribute('selected', 'selected');
            }
            $input->appendChild($option);
        }
        return $input;
    }
}