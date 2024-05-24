<?php

/**
 * Class WtGtUserAdmin
 */
class WtGtUserAdmin extends WtGtAdminBehavior
{
	function __construct(){
		add_action('admin_menu', array(&$this, 'menu'));
		add_action('admin_init', array(&$this, 'settingsRegister'));
	}

	public function menu(){
		add_submenu_page(
			'wt_geotargeting',
			'WT GeoTargeting - Пользователи',
			'Пользователи',
			'manage_options',
			'wt-gt-user',
			array(&$this, 'optionsPageOutput')
		);
	}

	// ---------- НАСТРОЙКА ----------

	/**
	 * Регистрируем настройки.
	 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
	 */
	function settingsRegister(){
		// $option_group, $option_name, $sanitize_callback
		register_setting('wt_geotargeting_user_group', 'wt_geotargeting_user', array(&$this, 'sanitizeCallback'));
		register_setting('wt_geotargeting_user_group', 'wt_geotargeting_user_not_exist', array(&$this, 'sanitizeCallback'));

		add_settings_section(
			'wt_geotargeting_user',
			'',
			'',
			'wt_geotargeting_user_page');

		$field_params = array(
			'type'      => 'checkbox', // тип
			'id'        => 'user_enable',
			'option_name' => 'wt_geotargeting_user',
			'label_for' => 'user_enable',
			'desc'      => 'Активировать поддержку пользователей',
		);
		add_settings_field('user_enable', '', array(&$this, 'displaySettings'), 'wt_geotargeting_user_page', 'wt_geotargeting_user', $field_params);

	}

	/**
	 * Создаем страницу настроек публикаций
	 */
	public function optionsPageOutput(){
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
				settings_fields('wt_geotargeting_user_group');     // скрытые защитные поля
				do_settings_sections('wt_geotargeting_user_page'); // секции с настройками (опциями).
				do_settings_sections('wt_geotargeting_user_not_exist_page'); // секции с настройками (опциями).
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
?>