<?php
/* Отключить импорт-экспор и комментарии */
function remove_menus(){
    global $menu;
    global $user_ID;
    $restricted = array(__('Tools'), __('Comments'));
    end ($menu);
    while (prev($menu)){
        $value = explode(' ', $menu[key($menu)][0]);
        if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
    }
}
add_action('admin_menu', 'remove_menus');

add_action('widgets_init', 'unregister_basic_widgets' );
function unregister_basic_widgets() {
    unregister_widget('WP_Widget_Calendar');         // Календарь
    unregister_widget('WP_Widget_Archives');         // Архивы
    unregister_widget('WP_Widget_Links');            // Ссылки
    unregister_widget('WP_Widget_Meta');             // Мета виджет
    unregister_widget('WP_Widget_Recent_Comments');  // Последние комментарии
    unregister_widget('WP_Widget_RSS');              // RSS
    unregister_widget('WP_Widget_Tag_Cloud');        // Облако меток
    unregister_widget('WP_Widget_Media_Audio');      // Audio
    unregister_widget('WP_Widget_Media_Video');      // Video
    unregister_widget('WP_Widget_Media_Gallery');    // Gallery
}

## Удаление метабоксов на странице редактирования записи
add_action('admin_menu','remove_default_post_screen_metaboxes');
function remove_default_post_screen_metaboxes() {
    // для постов
    remove_meta_box( 'postcustom','post','normal' ); // произвольные поля
    remove_meta_box( 'postexcerpt','post','normal' ); // цитата
    remove_meta_box( 'commentstatusdiv','post','normal' ); // комменты
    remove_meta_box( 'trackbacksdiv','post','normal' ); // блок уведомлений
    remove_meta_box( 'slugdiv','post','normal' ); // блок альтернативного названия статьи
    remove_meta_box( 'authordiv','post','normal' ); // автор

    // для страниц
    remove_meta_box( 'postcustom','page','normal' ); // произвольные поля
    remove_meta_box( 'postexcerpt','page','normal' ); // цитата
    remove_meta_box( 'commentstatusdiv','page','normal' ); // комменты
    remove_meta_box( 'trackbacksdiv','page','normal' ); // блок уведомлений
    remove_meta_box( 'slugdiv','page','normal' ); // блок альтернативного названия статьи
    remove_meta_box( 'authordiv','page','normal' ); // автор
}

## Удаление табов "Все рубрики" и "Часто используемые" из метабоксов рубрик (таксономий) на странице редактирования записи.
add_action('admin_print_footer_scripts', 'hide_tax_metabox_tabs_admin_styles', 99);
function hide_tax_metabox_tabs_admin_styles(){
    $cs = get_current_screen();
    if( $cs->base !== 'post' || empty($cs->post_type) ) return; // не страница редактирования записи
    ?>
    <style>
        .postbox div.tabs-panel{ max-height:1200px; border:0; }
        .category-tabs{ display:none; }
    </style>
    <?php
}

## Произвольный порядок пунктов в главном меню админки
if( is_admin() ){
    add_filter('custom_menu_order', '__return_true'); // включаем ручную сортировку
    add_filter('menu_order', 'custom_menu_order'); // ручная сортировка
    function custom_menu_order( $menu_order ){
        if( ! $menu_order ) return true;

        return array(
            'index.php', // консоль
            'separator-last',
            'edit.php?post_type=services', 
            'edit.php?post_type=vacations',
			'edit.php?post_type=news', 
            'edit.php?post_type=region',
			'separator1', // записи типа events
			'theme-general-settings',
            'edit.php?post_type=page', // страницы
            'edit.php', // посты
            'upload.php', // медиа
			'wpcf7', //contact form 7
			'wt_geotargeting',
            'separator2', // записи типа events
            'themes.php',
            'edit.php?post_type=acf-field-group',
			'options-general.php',
            'plugins.php',
            'users.php',
        );
    }
}

add_filter('show_admin_bar', '__return_false');

/* удалить следы wp */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', 'disable_wp_emojis_in_tinymce' );
function disable_wp_emojis_in_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

/* удаление писем об автоматическом обновлении */
add_filter( 'auto_core_update_send_email', '__return_false' );
add_filter( 'auto_core_update_send_email', 'wpb_stop_auto_update_emails', 10, 4 );

function wpb_stop_update_emails( $send, $type, $core_update, $result ) {
if ( ! empty( $type ) && $type == 'success' ) {
return false;
}
return true;
}

// Скрываем уведомления о новой версии WordPress
if ( 1 ) {

	// Общий счётчик обновлений в админ-баре
	add_action( 'admin_bar_menu', function ( $wp_adminbar ) {
		$wp_adminbar->remove_node( 'updates' );
	}, 999 );

	add_action( 'admin_menu', function () {

		// "Доступен WordPress X.X" в Консоле - Для Single установки
		remove_action( 'admin_notices', 'update_nag', 3 );

		// "Доступен WordPress X.X" в Консоле - Для Multisite установки
		remove_action( 'network_admin_notices', 'update_nag', 3 );

		// "Скачать версию X.X" в футере
		remove_action( 'update_footer', 'core_update_footer' );

		// Общий счётчик обновлений в админ-меню
		remove_submenu_page( 'index.php', 'update-core.php' );

		// Счётчик плагинов для обновления в админ-меню
		$GLOBALS['menu'][65][0] = __( 'Plugins' );

	}, 999 );

	// "Обновление до X.X" в виджете "На виду" в Консоле
	add_action( 'admin_head-index.php', function () {
		?>
		<style>
			#wp-version-message .button {
				display: none;
			}
		</style>
		<?php
	} );

}