<?php
$main_phone = WT::$obj->contacts->getValue('phone');
$whatToStrip = array("(",")"," ","-");
$hidden_phone = str_replace($whatToStrip, "", $main_phone);

$socials = get_field('socials', 'option');
if(is_post_type_archive( 'vacations' )) $main_class = 'page_archive_vacation';
if(is_post_type_archive( 'news' )) $main_class = 'page_archive_news';
if(is_post_type_archive( 'services' )) $main_class = 'page_archive_services';
if(is_singular('news')) $main_class = 'page_archive_news page_single_news';
if(is_singular('services')) $main_class = 'page_single_services';
if(is_page(8)) $main_class = 'page_tariffs';
if(is_page(10)) $main_class = 'page_about';
if(is_page(12)) $main_class = 'page_contacts';
if(is_tax( $taxonomy = 'category_serv', $term = '' )) $main_class = 'page_archive_services';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" href="<?php bloginfo('template_url') ?>/images/favicon.ico" type="image/x-icon" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>				   
    <div class="menu_mobile">
	    <header class="mobile_screen">
			<div class="container">
				<section class="header_block">
					<div class="head_block logo_block">
						<?php the_custom_logo(); ?>
						<span class="sub_logo">Твоя Врубная бухгалтерия</span>
					</div>
					<div class="menu_close">
						<a href="#" class="menu_close_btn">x</a>
					</div>
				</section>
			</div>
		</header>
		<section class="mobile_main">
			<div class="container">
			    <div class="head_block adr_block">
					<a class="listcity_button navig_menu"><?php echo do_shortcode('[wt_geotargeting get="city"][/wt_geotargeting]'); ?></a>
					<div id="hascity">
						<div class="hascity_content">
							<p>Ваш город <?php echo do_shortcode('[wt_geotargeting get="city"][/wt_geotargeting]'); ?>? </p>
							<button id="mycity" class="btn btn-default btn-xs">Верно</button>	
							<button id="nocity" class="btn btn-primary btn-xs">Нет, другой</button>
						</div>	
					</div>
				</div>
				<div id="dl-menu" class="dl-menuwrapper">
					<ul class="dl-menu dl-menuopen">
					    <?php wp_nav_menu( array(
							'theme_location' => 'mobile',
							'container'       => false,
							'items_wrap' => '%3$s',
							'walker' => new True_Walker_Nav_Menu
					    )); ?>
					</ul>
				</div>
				<div class="head_block info_block">
					<a href="tel:<?= $hidden_phone ?>" class="icon icon_phone"><?= WT::$obj->contacts->getValue('phone') ?></a>
					<a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_link">Заказать звонок</a>
					<a href="mailto:<?= WT::$obj->contacts->getValue('email') ?>" class="icon icon_mail"><?= WT::$obj->contacts->getValue('email') ?></a>
				</div>
				<div class="head_block messages_block">
					<span class="sub_text">Напишите нам в чат</span>
					<div class="mess_block">
					<?php foreach($socials as $item): if($item['icon'] != 'vk'):?> 
						<a href="<?= $item['info'] ?>" class="icon icon_mess main_icon_<?= $item['icon'] ?>"><?= $item['icon'] ?></a>
					<?php endif; endforeach; ?>	
					</div>
				</div>
			</div>
		</section>
	</div>
    <header class="desktop_screen">
	    <section class="header_block">
		    <div class="container">
				<div class="header_block_content">
					<div class="head_block logo_block">
					    <?php the_custom_logo(); ?>
						<span class="sub_logo">Твоя Врубная бухгалтерия</span>
					</div>
					<div class="head_block adr_block">
					    <span class="sub_text">Ваш город</span>
						<a class="listcity_button navig_menu"><?php echo do_shortcode('[wt_geotargeting get="city"][/wt_geotargeting]'); ?></a>
						<div id="hascity">
						<div class="hascity_content">
							<p>Ваш город <?php echo do_shortcode('[wt_geotargeting get="city"][/wt_geotargeting]'); ?>? </p>
							<button id="mycity" class="btn btn-default btn-xs">Верно</button>	
							<button id="nocity" class="btn btn-primary btn-xs">Нет, другой</button>
						</div>	
						</div>
					</div>
					<div class="head_block messages_block">
					    <span class="sub_text">Напишите нам в чат</span>
						<div class="mess_block">
						<?php foreach($socials as $item): if($item['icon'] != 'vk'):?> 
							<a href="<?= $item['info'] ?>" class="icon icon_mess main_icon_<?= $item['icon'] ?>"><?= $item['icon'] ?></a>
						<?php endif; endforeach; ?>	
						</div>
					</div>
					<div class="head_block info_block">
						<a href="tel:<?= $hidden_phone ?>" class="icon icon_phone"><?= WT::$obj->contacts->getValue('phone') ?></a>
						<a data-fancybox data-src="#callback" href="javascript:;" class="btn btn_link">Заказать звонок</a>
						<a href="mailto:<?= WT::$obj->contacts->getValue('email') ?>" class="icon icon_mail"><?= WT::$obj->contacts->getValue('email') ?></a>
					</div>
				</div>
			</div>
		</section>
		<section class="menu_block">
		    <div class="container">
				<div class="menu_content">
				    <nav class="header__nav">
						<ul>
						<?php wp_nav_menu( array(
								'theme_location' => 'main_menu',
								'container'       => false,
								'items_wrap' => '%3$s',
								'walker' => new True1_Walker_Nav_Menu
						)); ?>
						</ul>
					</nav>
				</div>
			</div>
		</section>
    </header>
	<header class="mobile_screen">
	    <div class="container">
			<section class="header_block">
			    <div class="head_block logo_block">
					    <?php the_custom_logo(); ?>
						<span class="sub_logo">Твоя Врубная бухгалтерия</span>
					</div>
				<div class="nav_menu">
					<a class="header__menu-btn" href="#">menu</a>
				</div>
		    </section>
		</div>
    </header>
	<?php if( is_front_page() ): ?>
	<main class="content home_main_page">
	<?php else: ?>
	<main class="content <?= $main_class ?>">	
		<div class="breadcrumb">
		   <div class="container"><div class="breadcrumb_content"><?php get_breadcrumb(); ?></div></div>
	    </div>
	<?php endif; ?>
