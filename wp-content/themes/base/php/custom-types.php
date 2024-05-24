<?php
add_action( 'init', 'register_faq_post_type' );
function register_faq_post_type() {
	register_post_type( 'news', [
		'label'               => 'Новости',
		'labels'              => array(
			'name'          => 'Новости',
			'singular_name' => 'Новость',
			'menu_name'     => 'Новости',
			'all_items'     => 'Все новости',
			'add_new'       => 'Добавить новость',
			'add_new_item'  => 'Добавить новую запись',
			'edit'          => 'Редактировать',
			'edit_item'     => 'Редактировать новость',
			'new_item'      => 'Новая запись',
			'featured_image'     => 'Изображение',
            'set_featured_image' => 'Добавить изображение'
		),
		'description'         => '',
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'rest_base'           => '',
		'show_in_menu'        => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => array( 'slug'=>'news', 'with_front'=>false, 'pages'=>true, 'feeds'=>false, 'feed'=>false ),
		'has_archive'         => 'news',
		'query_var'           => true,
		'supports'            => [ 'title','editor','thumbnail','excerpt' ],
	] );

}

add_action( 'init', 'lc_custom_post_vacation' );
function lc_custom_post_vacation() {

    $labels = array(
        'name'               => 'Вакансии', // основное название для типа записи
        'singular_name'      => 'Вакансия', // название для одной записи этого типа
        'add_new'            => 'Добавить вакансию', // для добавления новой записи
        'add_new_item'       => 'Добавление вакансии', // заголовка у вновь создаваемой записи в админ-панели.
        'edit_item'          => 'Редактирование вакансии', // для редактирования типа записи
        'new_item'           => 'Новая вакансия', // текст новой записи
        'view_item'          => 'Смотреть вакансию', // для просмотра записи этого типа.
        'search_items'       => 'Искать', // для поиска по этим типам записи
        'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
        'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
        'all_items'          => 'Все вакансии',
        'featured_image'     => 'Изображение',
        'set_featured_image' => 'Добавить изображение'
    );

    $args = array(
        'labels'            => $labels,
        'description'       => 'Список вакансий',
        'public'            => true,
        'menu_position'     => 9,
        'supports'          => array( 'title', 'editor', 'thumbnail'),
        'has_archive'       => true,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'show_in_menu'       => true,
        'menu_icon' => 'dashicons-megaphone',
        'hierarchical'       => true,
		'rewrite'             => array( 'slug'=>'vacations', 'with_front'=>false, 'pages'=>true, 'feeds'=>false, 'feed'=>false ),
		'has_archive'         => 'vacation',
		'query_var'           => true,
		'supports'            => array( 'title', 'editor', 'thumbnail'),   
    );

    register_post_type( 'vacations', $args);
}

add_action( 'init', 'lc_custom_post_services' );
function lc_custom_post_services() {
	register_taxonomy( 'category_serv', [ 'services' ], [
		'label'                 => 'Разделы услуг',
		'labels'                => array(
			'name'              => 'Разделы услуг',
			'singular_name'     => 'Раздел',
			'search_items'      => 'Искать Раздел',
			'all_items'         => 'Все Разделы',
			'parent_item'       => 'Родит. раздел',
			'parent_item_colon' => 'Родит. раздел:',
			'edit_item'         => 'Ред. Раздел',
			'update_item'       => 'Обновить Раздел',
			'add_new_item'      => 'Добавить Раздел',
			'new_item_name'     => 'Новый Раздел',
			'menu_name'         => 'Раздел',
		),
		'public'                => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
		'show_ui'               => true,
		'show_tagcloud'         => false,
		'hierarchical'          => true,
		'sort' => true,
		'rewrite'               => array('slug'=>'services'),
		'show_admin_column'     => true,
		'show_in_rest' => true
	] );
	register_post_type( 'services', [
		'label'               => 'Услуги',
		'labels'              => array(
			'name'          => 'Услуги',
			'singular_name' => 'Запись',
			'menu_name'     => 'Услуги',
			'all_items'     => 'Все записи',
			'add_new'       => 'Добавить запись',
			'add_new_item'  => 'Добавить новую запись',
			'edit'          => 'Редактировать',
			'edit_item'     => 'Редактировать запись',
			'new_item'      => 'Новая запись',
		),
		'description'         => '',
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'rest_base'           => '',
		'show_in_menu'        => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => array( 'slug'=>'services/%category_serv%', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
		'has_archive'         => 'services',
		'query_var'           => true,
		'supports'            => array( 'title', 'editor', 'thumbnail'),
		'taxonomies'          => array( 'category_serv' ),
	] );
}

## Отфильтруем ЧПУ произвольного типа
add_filter( 'post_type_link', 'faq_permalink', 1, 2 );
function faq_permalink( $permalink, $post ){

	if( strpos( $permalink, '%category_serv%' ) === false )
		return $permalink;

	$terms = get_the_terms( $post, 'category_serv' );
	if( ! is_wp_error( $terms ) && !empty( $terms ) && is_object( $terms[0] ) )
		$term_slug = array_pop( $terms )->slug;
	else
		$term_slug = 'no-category_serv';

	return str_replace( '%category_serv%', $term_slug, $permalink );
}

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title' 	=> 'Настройки сайта',
        'menu_title'	=> 'Шаблон',
        'menu_slug' 	=> 'theme-general-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));

    acf_add_options_sub_page(array(
        'page_title' 	=> 'Блоки',
        'menu_title'	=> 'Блоки',
		'menu_slug' 	=> 'theme-blocks',
        'parent_slug'	=> 'theme-general-settings',
    ));
	
	acf_add_options_sub_page(array(
        'page_title' 	=> 'Тарифы',
        'menu_title'	=> 'Тарифы',
		'menu_slug' 	=> 'theme-tariffs',
        'parent_slug'	=> 'theme-general-settings',
    ));
	acf_add_options_sub_page(array(
        'page_title' 	=> 'Клиенты',
        'menu_title'	=> 'Клиенты',
		'menu_slug' 	=> 'theme-clients',
        'parent_slug'	=> 'theme-general-settings',
    ));
}


