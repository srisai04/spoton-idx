<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_PluginBase extends BWP_PluginCore {

	/**
	 * Various data, non-wp and wp related.
	 */
	private $_plugin_data = array(
		'title' 	=> 'Better WordPress Plugins',
		'url'		=> 'http://betterwp.net',
		'version'	=> '1.0.0',
		'db'		=> '1',
		'req_wp'	=> '3',
		'req_php'	=> '5.1'
	);
	
	private $_plugin_wp_data = array(
		'key'		=> 'bwp',
		'ckey' 		=> 'BWP', // Capitalized Key
		'domain'	=> 'bwp-domain',
		'file'		=> '',
		'cap'		=> 'manage_options' // Capability
	);

	/**
	 * All options we have for a plugin
	 */	
	protected $o_objects = array();

	/**
	 * Options & Default options
	 */
	public $options = array(), $default_options = array();

	/**
	 * Network-wide options
	 */
	protected $site_options = array();

	/**
	 * Hold DB option keys
	 */	
	private $_option_keys = array();

	/**
	 * Hold all option pages / tabs
	 */	
	private $_option_pages = array();

	/**
	 * Need additional tables?
	 */	
	private $_tables = array();

	/**
	 * Message shown to user (Warning, Notes, etc.)
	 */	
	private $_notices = array(), $_notice_shown = false;

	/**
	 * Error messages shown to user
	 */
	private $_error_mess = array(), $_error_shown = false;


	/**
	 * Stats for debugging purposes
	 */
	private $_exe_time_limit = 30, $_time_start = 0, $_mem_start = 0;

	/**
	 * Other private things
	 */
	private $_dbver = 1, $_pkey = '';

	/**
	 * Other protected things
	 */
	protected $cap = '', $admin_page = '', $form_tabs = array();

	/**
	 * Plugin text-domain
	 */
	public $domain;

	/**
	 * Default constructor, call this when init any plugin to check requirements
	 */	
	protected function __construct($plugin_data, $plugin_wp_data)
	{
		// Build basic plugin data
		$this->_build_plugin_data($plugin_data, $plugin_wp_data);
		// Time execution limit, needed by some plugins
		$this->_exe_time_limit = @ini_get('max_execution_time');
		$this->_exe_time_limit = (empty($this->_exe_time_limit)) ? 30 : $this->_exe_time_limit;
		// Check for requirements
		if (!$this->check_required_versions())
			return false;

		return true;
	}

	protected function init($delayed_properties = false)
	{
		/* Start the stats counter */
		$this->start_counter();
		/* Support installation and uninstallation */
		$this->_build_tables();
		register_activation_hook($this->get_pwpd('file'), array($this, 'install'));
		register_deactivation_hook($this->get_pwpd('file'), array($this, 'uninstall'));
		/* Update table if db_ver has been changed */
		$this->update_db();
		/* Build options */
		$this->build_options();
		/* Build constants */
		$this->_build_constants();
		/* Add global actions and filters */
		$this->add_hooks();
		/* Enqueue needed media */
		add_action('init', array($this, 'enqueue_media'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_media'));
		/* Load other properties, delay if needed */
		if ($delayed_properties)
			add_action('init', array($this, 'init_properties'));
		else
			$this->init_properties();
		/* Loaded everything for this plugin, now you can add other things to it, such as stylesheet, etc. */
		do_action($this->get_pwpd('key') . '_loaded');
		/* Admin Init */
		if (is_admin())
			add_action('wp_loaded', array($this, 'init_admin'), 99999);
	}

	private function start_counter()
	{
		// Memory Usage
		$this->_mem_start = memory_get_usage();
		// Time
		$mtime = explode(' ', microtime());
		$this->_time_start = $mtime[1] + $mtime[0];
		return true;
	}

	protected function stop_counter()
	{
		$mem 		= memory_get_usage() - $this->_mem_start;
		$mtime      = explode(' ', microtime());
		$time_end   = $mtime[1] + $mtime[0];
		$time_total = $time_end - $this->_time_start;
		return array('time' => $time_total, 'mem' => $mem);
	}

	protected function add_hooks()
	{
		/* intentionally left blank */
	}

	protected function add_admin_hooks()
	{
		/* intentionally left blank */
	}

	public function enqueue_media()
	{
		/* intentionally left blank */
	}

	public function admin_enqueue_media()
	{
		/* intentionally left blank */
	}

	protected function init_properties()
	{
		/* intentionally left blank */
	}

	protected function add_option_key($key, $option, $title)
	{
		$this->_option_keys[$key] = $option;
		$this->_option_pages[$key] = $title;
		define(strtoupper($key), $option);
	}

	private function _build_constants()
	{
		// Framework constants
		if (!defined('BWP_FRW_ADM_CSS'))
		{
			define('BWP_FRW_ADM_CSS', plugin_dir_url(BWP_Init::$lib_path) . 'BWP/Admin/css');
			define('BWP_FRW_ADM_JS', plugin_dir_url(BWP_Init::$lib_path) . 'BWP/Admin/js');
			define('BWP_FRW_FORM_RESET_L10N', 'bwp_form_reset_l10n');
		}
		// Plugin-specific constants
		define($this->get_pwpd('ckey') . '_IMAGES', plugin_dir_url($this->get_pwpd('file')) . 'images');
		define($this->get_pwpd('ckey') . '_CSS', plugin_dir_url($this->get_pwpd('file')) . 'css');
		define($this->get_pwpd('ckey') . '_JS', plugin_dir_url($this->get_pwpd('file')) . 'js');
		define($this->get_pwpd('ckey') . '_LIB_PATH', plugin_dir_path($this->get_pwpd('file')) . 'lib');
	}

	protected function get_pd($key)
	{
		if (!empty($this->_plugin_data[$key]))
			return $this->_plugin_data[$key];
		return '';
	}
	
	protected function get_pwpd($key)
	{
		if (!empty($this->_plugin_wp_data[$key]))
			return $this->_plugin_wp_data[$key];
		return '';
	}

	private function _localized()
	{
		load_plugin_textdomain($this->domain, false, basename(dirname($this->get_pwpd('file'))) . '/languages');
	}
	
	/**
	 * Build base properties
	 */
	private function _build_plugin_data(array $plugin_data, array $plugin_wp_data)
	{		
		$this->_plugin_data 			= wp_parse_args($plugin_data, $this->_plugin_data);
		$this->_plugin_wp_data 			= wp_parse_args($plugin_wp_data, $this->_plugin_wp_data);
		$this->_dbver					= $this->_plugin_data['db'];
		$this->_pkey					= $this->_plugin_wp_data['key'];
		$this->_plugin_wp_data['ckey'] 	= strtoupper($this->_plugin_wp_data['key']);
		$this->domain 					= $this->_plugin_wp_data['domain'];
		$this->cap 						= $this->_plugin_wp_data['cap'];
		// Load locale
		$this->_localized();
	}

	protected function get_o($field)
	{
		if (isset($this->options[$field]))
			return $this->options[$field];
		else
			return 'undefined';
	}

	protected function get_objo($option_key, $db = false)
	{
		$options = $this->o_objects;
		foreach ($options as $option)
			if ($option instanceof BWP_Options && $option_key == $option->get_option_key())
				return ($db) ? $option->get_options() : $this->core_get($option, 'options');
		return array();
	}

	protected function get_oobj($option_key)
	{
		$options = $this->o_objects;
		foreach ($options as $option)
			if ($option instanceof BWP_Options && $option_key == $option->get_option_key())
				return $option;
		return NULL;
	}

	public function update_options($option_key, array $values)
	{
		$options = $this->o_objects;
		foreach ($options as $option)
		{
			if ($option instanceof BWP_Options && $option_key == $option->get_option_key())
			{
				$option->update_options($values);
				$this->options = array_merge($this->options, $this->core_get($option, 'options'));
				break;
			}
		}
	}

	protected function build_options()
	{
		$options = $this->o_objects;
		foreach ($options as $option)
			if ($option instanceof BWP_Options)
			{
				$this->default_options = array_merge($this->default_options, $this->core_get($option, 'defaults'));
				$this->options = array_merge($this->options, $this->core_get($option, 'options'));
			}
	}

	protected function add_option(BWP_Options $oobj)
	{
		$this->o_objects[] = $oobj;
	}

	private function _build_tables()
	{
		foreach ($this->_tables as &$table)
		{
			if ('both' == $table->get_install_method())
			{
				register_activation_hook($this->get_pwpd('file'), array($table, 'install'));
				register_deactivation_hook($this->get_pwpd('file'), array($table, 'uninstall'));
			}
			else
				register_activation_hook($this->get_pwpd('file'), array($table, 'install'));
		}
	}

	protected function add_table($name, $columns, $args = array('engine' => '', 'install_method' => 'both'), $upgrade_method = 'dbDelta')
	{
		if (empty($args['install_method']))
			wp_die($this->get_pd['title'] . ' ' . __('error: No install_method (\'both\' or \'install\') is set for table, table installation aborted.', $this->domain));
		$this->_tables[$name] = new BWP_Tables($name, $columns, $args, $upgrade_method);
	}

	protected function install_table($name)
	{
		if (!empty($this->_tables[$name]))
			$this->_tables[$name]->install();
	}

	public function install()
	{
		/* intentionally left blank */
	}

	public function uninstall()
	{
		/* intentionally left blank */
	}

	protected function update_plugin_db($old, $new)
	{
		/* intentionally left blank */
	}

	protected function update_db()
	{
		// If DB is currently at rev 1, or we're not in admin, no need to do anything
		if (1 == $this->_dbver || empty($this->_dbver) || !is_admin())
			return;
		// Check the current DB version
		$current_db_ver = get_option($this->_pkey . '_db_ver');
		if (!$current_db_ver || $current_db_ver < $this->_dbver)
		{
			$this->update_plugin_db($current_db_ver, $this->_dbver);
			update_option($this->_pkey . '_db_ver', $this->_dbver);
		}
	}

	public function add_icon()
	{
		return '<div class="icon32" id="icon-bwp-plugin" style=\'background-image: url("' . constant($this->get_pwpd('ckey') . '_IMAGES') . '/icon_menu_32.png");\'><br></div>'  . "\n";
	}

	public function show_version()
	{
		$plugin_version = $this->get_pd('version');
		if (empty($plugin_version)) return '';
		return '<a class="nav-tab version" title="' . sprintf(esc_attr(__('You are using version %s!', $this->domain)), $this->get_pd('version')) . '">' . $this->get_pd('version') . '</a>';
	}

	private function _build_tabs()
	{
		foreach ($this->_option_pages as $key => $page)
		{
			$pagelink = (!empty($this->_option_keys[$key])) ? $this->_option_keys[$key] : '';
			$network = ('manage_network' == $this->cap) ? 'network/' : '';
			$this->form_tabs[$page] = get_option('siteurl') . '/wp-admin/' . $network . 'admin.php?page=' . $pagelink;
		}
	}

	public function warn_required_versions()
	{
		echo '<div class="error"><p>' . sprintf(__('%s requires WordPress <strong>%s</strong> or higher and PHP <strong>%s</strong> or higher. The plugin will not function until you update your software. Please deactivate this plugin.', $this->domain), $this->get_pd('title'), $this->get_pd('req_wp'), $this->get_pd('req_php')) . '</p></div>';
	}

	private function check_required_versions()
	{
		if (version_compare(PHP_VERSION, $this->get_pd('req_php'), '<') || version_compare(get_bloginfo('version'), $this->get_pd('req_wp'), '<'))
		{
			add_action('admin_notices', array($this, 'warn_required_versions'));
			add_action('network_admin_notices', array($this, 'warn_required_versions'));
			return false;
		}
		else
			return true;
	}

	protected function admin_page_url($admin_page = '')
	{
		$admin_page = (!empty($admin_page)) ? $admin_page : $this->admin_page;
		return admin_url() . 'admin.php?page=' . $admin_page;
	}

	protected function is_admin_page()
	{
		if (is_admin() && !empty($_REQUEST['page']) && in_array($_REQUEST['page'], $this->_option_keys))
		{
			$this->admin_page = $_REQUEST['page'];
			return true;
		}
	}

	public function plugin_action_links($links, $file) 
	{
		$option_keys = array_values($this->_option_keys);
		$setting_page = (empty($option_keys[0])) ? '' : $option_keys[0];
		if ($file == plugin_basename($this->get_pwpd('file')) && !empty($setting_page))
			$links[] = '<a href="admin.php?page=' . $setting_page . '">' . __('Settings') . '</a>';

		return $links;
	}

	public function admin_ajax_print_js()
	{
		/* intentionally left blank */
	}

	public function admin_ajax_js()
	{
		/* intentionally left blank */
	}

	public function admin_ajax_callback()
	{
		/* intentionally left blank */
	}

	public function build_menus()
	{
		/* intentionally left blank */
	}

	protected function build_utility_menu($top)
	{
		if (!defined('BWP_UTILITIES'))
		{
			define('BWP_UTILITIES', $top);
			add_menu_page(__('BWPP Utilities', $this->domain), __('BWPP Utilities', $this->domain), $this->cap, BWP_UTILITIES, array($this, 'build_admin_pages'), constant($this->get_pwpd('ckey') . '_IMAGES') . '/icon_menu.png');
		}
	}

	public function init_admin()
	{
		if (!current_user_can($this->cap))
			return;

		if ($this->is_admin_page())
		{
			// Load option page builder
			if (!class_exists('BWP_Admin_OptionPages'))
			{
				require_once(BWP_Init::$lib_path . '/Admin/Forms.php');
				require_once(BWP_Init::$lib_path . '/Admin/OptionPages.php');
			}
			// Admin hooks
			$this->add_admin_hooks();
			// Build tabs
			$this->_build_tabs();
			// Build forms
			$this->build_forms();
		}
		// Build admin menus
		add_action(($this->is_multisite() && 'manage_network' == $this->get_pwpd('cap')) ? 'network_admin_menu' : 'admin_menu', array($this, 'build_admin_menus'), 1);
	}

	protected function build_forms()
	{
		/* intentionally left blank */
	}

	public function build_admin_menus()
	{
		add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);

		$this->build_menus();

		if ($this->is_admin_page())
		{
			if (version_compare(get_bloginfo('version'), '3.3', '>='))
				// Since WordPress 3.3, we will need to enqueue a different CSS file
				wp_enqueue_style('bwp-option-page-33',  BWP_FRW_ADM_CSS . '/op33.css', array(), BWP_Init::$rev);
			else if (version_compare(get_bloginfo('version'), '3.2', '>='))
				// Since WordPress 3.2, we will need to enqueue a different CSS file
				wp_enqueue_style('bwp-option-page-32',  BWP_FRW_ADM_CSS . '/op32.css', array(), BWP_Init::$rev);
			else
				// Enqueue the old style for the option page
				wp_enqueue_style('bwp-option-page',  BWP_FRW_ADM_CSS . '/op.css', array(), BWP_Init::$rev);
			// Ajax
			add_action('admin_head', array($this, 'admin_ajax_print_js'));
			add_action('admin_init', array($this, 'admin_ajax_js'), 9);
			// Icons
			add_filter('bwp_admin_form_icon', array($this, 'add_icon'));
			add_filter('bwp_admin_plugin_version', array($this, 'show_version'));
		}
	}

	protected function add_notice($notice, $form_name)
	{
		if (!in_array($notice, $this->_notices))
		{
			$this->_notices[] = $notice;
			add_action('bwp_opa_before_form_' . $form_name, array($this, 'show_notices'));
		}
	}
	
	public function show_notices()
	{
		if (false == $this->_notice_shown)
		{
			foreach ($this->_notices as $notice)
				echo '<div class="bwp-notices"><p>' . $notice . '</p></div>';
			$this->_notice_shown = true;
		}
	}

	protected function add_error_mess($error, $form_name)
	{
		if (!in_array($error, $this->_error_mess))
		{
			$this->_error_mess[] = $error;
			add_action('bwp_opa_before_form_' . $form_name, array($this, 'show_error_mess'));
		}
	}
	
	public function show_error_mess()
	{
		if (false == $this->_error_shown)
		{
			foreach ($this->_error_mess as $error)
				echo '<div class="bwp-notices bwp-errors"><p>' . $error . '</p></div>';
			$this->_error_shown = true;
		}
	}

	public static function is_multisite()
	{
		if (function_exists('is_multisite') && is_multisite())
			return true;
		return false;
	}

	public static function is_normal_admin()
	{
		if (self::is_multisite() && !is_super_admin())
			return true;
		return false;
	}

	public static function date($time, $type = 'read')
	{
		switch ($type)
		{
			case 'full': return date('c', $time); break;
			case 'read': return date('F j, Y, g:i a', $time); break;
		}
	}

}
?>