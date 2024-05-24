<?php
/**
 * Функции шаблона (function.php)
 * @package WordPress
 * @subpackage 
 */ 
require_once( __DIR__.'/php/custom-types.php');
require_once( __DIR__.'/php/admin.php');
require_once( __DIR__.'/php/svg.php'); 

/* Общие настройки */
add_action('wp_enqueue_scripts', 'add_scripts');
if (!function_exists('add_scripts')) {
	function add_scripts() {
	    if(is_admin()) return false;
		wp_enqueue_script('jquery');
		//wp_enqueue_script('carousel-main', get_template_directory_uri().'/assets/owlcarousel/js/owl.carousel.js','','',true);
		wp_enqueue_script('carousel-navig', get_template_directory_uri().'/assets/owlcarousel/js/owl.navigation.js','','',true);
		wp_enqueue_script('js-ui', 'https://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js','','',true);
		wp_enqueue_script('moderniz', get_template_directory_uri().'/assets/js/modernizr.custom.js','','',true);
		wp_enqueue_script('dlmenu-navig', get_template_directory_uri().'/assets/js/jquery.dlmenu.js','','',true);
		wp_enqueue_script('fancybox1', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js','','',true);
	    wp_enqueue_script('libs', get_template_directory_uri().'/assets/js/libs.js','','',true);
	    wp_enqueue_script('main', get_template_directory_uri().'/assets/js/main.js','','',true);
		wp_enqueue_script('wpjs', get_template_directory_uri().'/assets/js/wp.js','','',true);
	}
}
add_action('wp_enqueue_scripts', 'add_styles');
if (!function_exists('add_styles')) {
	function add_styles() {
	    if(is_admin()) return false;
		wp_enqueue_style( 'carousel-style', get_template_directory_uri().'/assets/owlcarousel/css/owl.carousel.min.css' );
		wp_enqueue_style( 'carousel-theme', get_template_directory_uri().'/assets/owlcarousel/css/owl.theme.default.css' );
		wp_enqueue_style( 'ui-style', 'https://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/sunny/jquery-ui.css' );
		wp_enqueue_style( 'fancybox1-style', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css' );
		wp_enqueue_style( 'style', get_template_directory_uri().'/style.css' );
		wp_enqueue_style( 'main-style', get_template_directory_uri().'/assets/css/style.css' );
		wp_enqueue_style( 'mobile-style', get_template_directory_uri().'/assets/css/mobile.css' );	
	}
}

// Breadcrumbs
function get_breadcrumb() {
	// Settings
	$separator          = '<span class="bread_line">*</span>';
	$breadcrums_id      = 'breadcrumbs';
	$breadcrums_class   = 'breadcrumbs';
	$home_title         = 'Главная';
	$custom_taxonomy    = 'category_serv';
	global $post,$wp_query;
	if ( !is_front_page() ) {
		echo '<a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a>';
		echo $separator;
		if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
			echo '<span class="bread_elem">' . post_type_archive_title($prefix = '', false) . '</span>';
		} else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
			$post_type = get_post_type();
			if($post_type != 'post') {
				$post_type_object = get_post_type_object($post_type);
				$post_type_archive = get_post_type_archive_link($post_type);
				$term = get_queried_object();
				if($term->taxonomy == 'category_serv'){
					if($post_type_object && $post_type_object->name != 'services'){
						echo '<a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a>';
					}
				}
				echo $separator;
			}
			$custom_tax_name = get_queried_object()->name;
			echo '<span class="bread_elem">' . $custom_tax_name . '</span>';
		} else if ( is_single() ) {
			$post_type = get_post_type();
			if($post_type != 'post') {
				$post_type_object = get_post_type_object($post_type);
				$post_type_archive = get_post_type_archive_link($post_type);
				if($post_type_object->name != 'services'){
					echo '<a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a>';
				    echo $separator;
				}
			}
			$category = get_the_category();
			if(!empty($category) && !is_single()) {
				$last_category = end(array_values($category));
				$get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
				$cat_parents = explode(',',$get_cat_parents);
				$cat_display = '';
				foreach($cat_parents as $parents) {
					$cat_display .= '<span class="bread_elem">'.$parents.'</span>';
					$cat_display .= $separator;
				}
			}
			if( has_term('', $custom_taxonomy) ){
				$taxonomy_exists = taxonomy_exists($custom_taxonomy);
				if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
					$taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );				
					$cat_id         = $taxonomy_terms[0]->term_id;
					$cat_nicename   = $taxonomy_terms[0]->slug;
					$cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
					$cat_name       = $taxonomy_terms[0]->name;
				}
			}
			
			if(!empty($last_category)) {
				echo $cat_display;
				echo '<span class="bread_elem">' . get_the_title() . '</span>';
			} else if(!empty($cat_id)) {
				echo '<a class="bread-cat 222 bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a>';
				echo $separator;
				echo '<span class="bread_elem">' . get_the_title() . '</span>';
			} else {
				echo '<span class="bread_elem">' . get_the_title() . '</span>';
			}
		} else if ( is_category() ) {
			echo '<span class="bread_elem">' . single_cat_title('', false) . '</span>';
		} else if ( is_page() ) {
			if( $post->post_parent ){
				$anc = get_post_ancestors( $post->ID );
				$anc = array_reverse($anc);
				foreach ( $anc as $ancestor ) {
					$parents .= '<a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a>';
					$parents .= $separator;
				}
				echo $parents;
				echo '<span class="bread_elem">' . get_the_title() . '</span>';
			} else {
				echo '<span class="bread_elem">' . get_the_title() . '</span>';
			}
		} else if ( is_tag() ) {
			$term_id        = get_query_var('tag_id');
			$taxonomy       = 'post_tag';
			$args           = 'include=' . $term_id;
			$terms          = get_terms( $taxonomy, $args );
			$get_term_id    = $terms[0]->term_id;
			$get_term_slug  = $terms[0]->slug;
			$get_term_name  = $terms[0]->name;
			echo '<span class="bread_elem">' . $get_term_name . '</span>';
		} elseif ( is_day() ) {
			echo '<a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Архивы</a>';
			echo $separator;
			echo '<a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Архивы</a>';
			echo $separator;
			echo '<span class="bread_elem">' . get_the_time('jS') . ' ' . get_the_time('M') . ' Архивы</span>';
		} else if ( is_month() ) {
			echo '<a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Архивы</a>';
			echo $separator;
			echo '<span class="bread_elem">' . get_the_time('M') . ' Архивы</span>';
		} else if ( is_year() ) {
			echo '<span class="bread_elem">' . get_the_time('Y') . ' Архивы</span>';
		} else if ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			echo '<span class="bread_elem">' . $userdata->display_name . '</span>';
		} else if ( get_query_var('paged') ) {
			echo '<span class="bread_elem">'.__('Page') . ' ' . get_query_var('paged') . '</span>';
		} else if ( is_search() ) {           
			echo '<span class="bread_elem">Результат поиска: ' . get_search_query() . '</span>';
		} elseif ( is_404() ) {
			echo '<span class="bread_elem">' . 'Ошибка 404' . '</span>';
		}
	}
} 

$logo_width  = 318;
$logo_height = 53;

add_theme_support( 'custom-logo', array(
        'height'               => $logo_height,
		'width'                => $logo_width,
        'flex-height' => true,
        'flex-width'  => true,
		'header__logo-img' => true,
		'unlink-homepage-logo' => true,
    ) );
add_theme_support( 'post-thumbnails', array( 'news' ) ); 
add_image_size('news-thumb', 380, 314, true);
add_image_size('newshome-thumb', 580, 400, true);

add_filter( 'get_custom_logo', 'change_logo_class' );
function change_logo_class( $html ){ 
    $html = str_replace( 'custom-logo-link', 'main_logo', $html );
	return $html; 
}

add_filter( 'intermediate_image_sizes_advanced', 'true_remove_default_sizes' );
 
function true_remove_default_sizes( $sizes ) {
	unset( $sizes[ 'thumbnail' ] );
	unset( $sizes[ 'medium' ] );
	unset( $sizes[ 'large' ] );
	unset( $sizes[ 'medium_large' ] );
	unset( $sizes[ '1536x1536' ] );
	unset( $sizes[ '2048x2048' ] );
	return $sizes;
}

register_nav_menus(array(
	'main_menu' => 'Главное',
	'footer' => 'Нижнее 1',
	'footer1' => 'Нижнее 2',
	'mobile' => 'Мобильное'
));

class True1_Walker_Nav_Menu extends Walker_Nav_Menu {
	function start_lvl( &$output, $depth = 0, $args = NULL ){
		$output .= '<ul class="menu_sublist">';
	}
	function start_el( &$output, $item, $depth = 0, $args = NULL, $id = 0 ) {
		global $wp_query;           
		$indent = ( $depth ) ? str_repeat( "", $depth ) : '';
        
		$is_current_item = '';
        if(array_search('current-menu-item', $item->classes) != 0)
        {
            $is_current_item = ' active';
        }
		/*
		 * Генерируем строку с CSS-классами элемента меню
		 */
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		// функция join превращает массив в строку
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
 
		/*
		 * Генерируем ID элемента
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
 
		/*
		 * Генерируем элемент меню
		 */
		$output .= $indent . '<li class="menu-item'.$is_current_item.' '.$item->classes[0].'">';
 
		// атрибуты элемента, title="", rel="", target="" и 
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
 
		// ссылка и околоссылочный текст
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
 
 		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}


class Custom_Walker_Nav_Menu_top extends Walker_Nav_Menu
{
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $is_current_item = '';
        if(array_search('current-menu-item', $item->classes) != 0)
        {
            $is_current_item = ' active';
        }
        echo '<li class="menu-item'.$is_current_item.' '.$item->classes[0].'"><a href="'.$item->url.'">'.$item->title;
    }
    function end_el( &$output, $item, $depth = 0, $args = array() ) {
        echo '</a></li>';
    }
}

class True_Walker_Nav_Menu extends Walker_Nav_Menu {
	function start_lvl( &$output, $depth = 0, $args = NULL ){
		$output .= '<ul class="dl-submenu"><li class="dl-back"><a href="#">Назад</a></li>';
	}
	function start_el( &$output, $item, $depth = 0, $args = NULL, $id = 0 ) {
		global $wp_query;           
		$indent = ( $depth ) ? str_repeat( "", $depth ) : '';
 
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
 
		// функция join превращает массив в строку
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
 
		/*
		 * Генерируем ID элемента
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
 
		/*
		 * Генерируем элемент меню
		 */
		$output .= $indent . '<li' . $id . $value . $class_names .'>';
 
		// атрибуты элемента, title="", rel="", target="" и 
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
 
		// ссылка и околоссылочный текст
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
 
 		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

function excerpt($string,$length = 680){
	$string = strip_tags($string);
	if(strlen($string) < $length) return $string;
	$string = substr ($string, 0, $length);
	$string = rtrim($string, "!,.-");
	return substr($string, 0, strrpos($string, ' ')).'...';
}

function remove_page_from_query_string($query_string)
{ 
    if ($query_string['name'] == 'page' && isset($query_string['page'])) {
        unset($query_string['name']);
        $query_string['paged'] = $query_string['page'];
    }      
    return $query_string;
}
add_filter('request', 'remove_page_from_query_string');

/* другие новости в архиве */
function blog_item_other($array){
    $other_Query = new WP_Query(array(
		'post_type'      => 'news',
		'post_status'      => 'publish',
		'post__not_in' => $array, //[$id]				
		'posts_per_page' => 3,
		'orderby'    => 'rand',
	));
	if ($other_Query->have_posts()) : 
		while ( $other_Query->have_posts() ) : $other_Query->the_post(); ?>
			<div class="news_item">
				<?php if( has_post_thumbnail() ):
					echo get_the_post_thumbnail( $post->ID, 'news-thumb', array(
						'class' => 'post__img',
						'alt' => get_the_title()
					) );
				else: ?>
				<div class="post__img"></div>
				<?php endif; ?>
				<p class="post__date"><?php the_time('j F Y'); ?></p>
				<a href="<?php the_permalink()?>" class="post__title"><?= get_the_title() ?></a>
				<div class="post__description">
					<?php echo excerpt(get_the_excerpt(), 160); ?>
				</div>
			</div>
		<?php endwhile; endif; 
	wp_reset_query();
}

/* другие новости в записи */
function blog_items_other($id){
    $other_Query = new WP_Query(array(
		'post_type'      => 'news',
		'post_status'      => 'publish',
		'post__not_in' => [$id],		
		'posts_per_page' => 3,
		'orderby'    => 'rand',
	));
	if ($other_Query->have_posts()) : 
		while ( $other_Query->have_posts() ) : $other_Query->the_post(); ?>
			<div class="news_item">
				<?php if( has_post_thumbnail() ):
					echo get_the_post_thumbnail( $post->ID, 'news-thumb', array(
						'class' => 'post__img',
						'alt' => get_the_title()
					) );
				else: ?>
				<div class="post__img"></div>
				<?php endif; ?>
				<p class="post__date"><?php the_time('j F Y'); ?></p>
				<a href="<?php the_permalink()?>" class="post__title"><?= get_the_title() ?></a>
				<div class="post__description">
					<?php echo excerpt(get_the_excerpt(), 160); ?>
				</div>
			</div>
		<?php endwhile; endif; 
	wp_reset_query();
}

/* популярные услуги в записи */
function serv_items_other($id){
    $other_Query = new WP_Query(array(
		'post_type'      => 'services',
		'post_status'      => 'publish',
		'post__not_in' => [$id],		
		'posts_per_page' => 3,
		'orderby'    => 'rand',
	));
	if ($other_Query->have_posts()) : 
		while ( $other_Query->have_posts() ) : $other_Query->the_post(); 
		$price = get_field('small_price', $post->ID); 
		$cur = get_field('small_price_cur', $post->ID); ?>
			<div class="servs_item">
				<div class="icon"></div>
				<div class="title"><?= get_the_title() ?></div>
				<?php if($price): ?>
					<div class="price_block">от <span class="price"><?= $price ?> <?php if($cur): ?><span class="cur"> <?= $cur ?></span><?php endif; ?></span></div>
				<?php endif; ?>
				<a href="<?php the_permalink()?>" class="btn btn_preim" title="<?= get_the_title() ?>">Подробнее</a>
			</div>
		<?php endwhile; endif; 
	wp_reset_query();
}