<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

if (!function_exists('bwp_init_warn_php')) :

function bwp_init_warn_php()
{
	echo '<div class="error"><p>' . __('BWP Plugins requires PHP <strong>5.1.0</strong> or higher. All BWP Plugins will not function until you update your software. Please deactivate BWP Plugins.') . '</p></div>';
	do_action('bwp_init_warn_php');
}

endif;

if (version_compare(PHP_VERSION, '5.1', '<') && !did_action('bwp_init_warn_php'))
{
	add_action('admin_notices', 'bwp_init_warn_php');
	add_action('network_admin_notices', 'bwp_init_warn_php');
}

else if (!class_exists('BWP_Init')) :

class BWP_Init {
	
	private static $_modules = array();

	public static $lib_path = '';

	public static $rev = 1;

	public static function init()
	{
		self::$_modules = array(
			'PluginCore',
			'PluginBase',
			'Options',
			'Tables'
		);

		self::$lib_path = dirname(__FILE__);
		
		self::load();
	}

	public static function load()
	{
		foreach (self::$_modules as $module)
			if (!class_exists('BWP_' . $module))
				include_once(self::$lib_path . '/' . $module . '.php');
	}
	
}

BWP_Init::init();

endif;

?>