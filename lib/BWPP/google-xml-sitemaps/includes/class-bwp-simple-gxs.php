<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!class_exists('BWP_FRAMEWORK'))
	require_once(dirname(__FILE__) . '/class-bwp-framework.php');

class BWP_SIMPLE_GXS extends BWP_FRAMEWORK {

	/**
	 * Debugging the plugin
	 */
	var $debug = true, $logs = array('log' => array(), 'sitemap' => array());

	/**
	 * Modules to load when generating sitemapindex
	 */
	var $allowed_modules = array(), $requested_modules = array();
	
	/**
	 * Directory to load modules from
	 */
	var $module_directory = '';

	/**
	 * The permalink structure for sitemap files
	 */
	var $module_url = array();
	
	/**
	 * Frequency & priority
	 */
	var $frequency = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
	var $priority = array('0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3, '0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6, '0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1.0' => 1.0);
	
	/**
	 * Other properties
	 */
	var $post_types, $taxonomies, $terms, $cache_time, $module_data, $output, $output_num = 0, $num_log = 25;
	var $templates = array(), $module_map = array();
	var $sitemap_alias = array(), $use_permalink = true, $query_var_non_perma = '';
	var $ping_per_day = 100, $timeout = 3;
	var $xslt = '', $xslt_index = '';
	// @since 1.0.1
	var $build_data = array('time', 'mem', 'query');

	/**
	 * Constructor
	 */
	function __construct($version = '1.1.5')
	{
		// Plugin's title
		$this->plugin_title = 'BWP Google XML Sitemaps';
		// Plugin's version
		$this->set_version($version);
		$this->set_version('3.0', 'wp');
		// Basic version checking
		if (!$this->check_required_versions())
			return;

		// Default options
		$options = array(
			'enable_cache' => 'yes',
			'enable_cache_auto_gen'  => 'yes',
			'enable_gzip' => '',
			'enable_php_clean' => 'yes',
			'enable_xslt' => '',
			'enable_sitemap_date' => '',
			'enable_sitemap_taxonomy' => '',
			'enable_sitemap_external' => '',
			'enable_sitemap_split_post' => 'yes',
			'enable_sitemap_author' => '',
			'enable_sitemap_site' => 'yes',
			'enable_stats' => 'yes',
			'enable_credit' => 'yes',
			'enable_ping' => 'yes',
			'enable_ping_google' => 'yes',
			'enable_ping_bing' => 'yes',
			'enable_ping_ask' => '',
			'enable_log' => 'yes',
			'enable_debug' => '',
			'enable_robots' => 'yes',
			'enable_global_robots' => '',
			'enable_gmt' => 'yes',
			'input_exclude_post_type' => '',
			'input_exclude_taxonomy' => 'post_tag',
			'input_cache_age' => 1,
			'input_item_limit' => 5000,
			'input_split_limit_post' => 5000,
			'input_alt_module_dir' => $this->uni_path_sep(ABSPATH),
			'input_oldest' => 7,
			'input_sql_limit' => 500,
			'input_custom_xslt' => '',
			'select_output_type' => 'concise',
			'select_time_type' => 3600,
			'select_oldest_type' => 16400,
			'select_default_freq' => 'daily',
			'select_default_pri' => 1.0,
			'select_min_pri' => 0.1,
			'input_cache_dir' => '',
			'input_ping' => array(
				'google' => 'http://www.google.com/webmasters/sitemaps/ping?sitemap=%s', 
				'bing' => 'http://www.bing.com/webmaster/ping.aspx?siteMap=%s', 
				'ask' => 'http://submissions.ask.com/ping?sitemap=%s'),
			'input_sitemap_url' => '',
			'input_sitemap_struct' => ''
		);
		// Super admin only options
		$this->site_options = array('enable_robots', 'enable_global_robots', 'enable_log', 'enable_debug', 'enable_ping', 'enable_ping_google', 'enable_ping_bing', 'enable_ping_ask', 'enable_gzip', 'enable_php_clean', 'enable_cache', 'enable_cache_auto_gen', 'input_cache_age', 'input_alt_module_dir', 'input_sql_limit', 'input_cache_dir', 'select_time_type');

		$this->build_properties('BWP_GXS', 'bwp-simple-gxs', $options, 'Google XML Sitemaps', dirname(dirname(__FILE__)) . '/bwp-simple-gxs.php', 'http://betterwp.net/wordpress-plugins/google-xml-sitemaps/', false);

		$this->add_option_key('BWP_GXS_GENERAL', 'bwp_gxs_general', __('XML Sitemaps', 'bwp-simple-gxs'));

		define('BWP_GXS_LOG', 'bwp_gxs_log');
		define('BWP_GXS_PING', 'bwp_gxs_ping_data');

		$this->init();
	}

	function init_properties()
	{
		$this->module_directory = plugin_dir_path($this->plugin_file) . 'includes/modules/';

		$this->templates = array(
			'sitemap' 		=> "\n\t" . '<sitemap>' . "\n\t\t" . '<loc>%s</loc>%s' . "\n\t" . '</sitemap>',
			'url' 			=> "\n\t" . '<url>' . "\n\t\t" . '<loc>%1$s</loc>%2$s%3$s%4$s' . "\n\t" . '</url>',
			'lastmod' 		=> "\n\t\t" . '<lastmod>%s</lastmod>',
			'changefreq' 	=> "\n\t\t" . '<changefreq>%s</changefreq>',
			'priority' 		=> "\n\t\t" . '<priority>%.1f</priority>',
			'xslt_style' 	=> '',
			'stats'	=> "\n" . '<!-- ' . __('This sitemap was originally generated in %s second(s) (Memory usage: %s) - %s queries - %s URL(s) listed', 'bwp-simple-gxs') . ' -->'
			/*'stats_cached'	=> "\n" . '<!-- ' . __('Served from cache in %s second(s) (Memory usage: %s) - %s queries - %s URL(s) listed', 'bwp-simple-gxs') . ' -->'*/
		);

		$this->init_gzip();
		
		$this->cache_time = (int) $this->options['input_cache_age'] * (int) $this->options['select_time_type'];
		$this->oldest_time = (int) $this->options['input_oldest'] * (int) $this->options['select_oldest_type'];
		$this->options['input_cache_dir'] = plugin_dir_path($this->plugin_file) . 'cache/';
		$this->options['input_cache_dir'] = $this->uni_path_sep($this->options['input_cache_dir']);

		$module_map = apply_filters('bwp_gxs_module_mapping', array());
		$this->module_map = wp_parse_args($module_map, array('post_format' => 'post_tag'));
		
		// Logs
		$this->logs = get_option(BWP_GXS_LOG);
		if (!$this->logs)
			$this->logs = array('log' => array(), 'sitemap' => array());
		foreach ($this->logs as $key => $log)
			if (is_array($log) && $this->num_log < sizeof($log))
			{
				$log = array_slice($log, (-1) * $this->num_log);
				$this->logs[$key] = $log;
			}

		// Sitemap based on permastruct
		$permalink = get_option('permalink_structure');
		if (!$permalink)
		{
			$this->use_permalink = false;
			$this->query_var_non_perma = apply_filters('bwp_gxs_query_var_non_perma', 'bwpsitemap');
			$this->options['input_sitemap_url'] = home_url() . '/?' . $this->query_var_non_perma . '=sitemapindex';
			$this->options['input_sitemap_struct'] = home_url() . '/?' . $this->query_var_non_perma . '=%s';
		}
		else
		{
			// If user is using index.php in their permalink structure, we will have to include it also
			$indexphp = (strpos($permalink, 'index.php') === false) ? '' : '/index.php';
			$this->options['input_sitemap_url'] = home_url() . $indexphp . '/sitemapindex.xml';
			$this->options['input_sitemap_struct'] = home_url() . $indexphp . '/%s.xml';
		}

		// No more than 50000 URLs per sitemap
		if (50000 < (int) $this->options['input_item_limit'])
			$this->options['input_item_limit'] = 50000;
		
		// Limit per split sitemap - @since 1.1.0
		// Not higher than 50000 URLs and must be >= SQL cycling limit
		if ($this->options['input_split_limit_post'] < $this->options['input_sql_limit'])
			$this->options['input_split_limit_post'] = $this->options['input_sql_limit'];
		if (50000 < (int) $this->options['input_split_limit_post'])
			$this->options['input_split_limit_post'] = 50000;
		
		// XSLT style sheet
		$this->xslt = '';

		// Late init
		add_action('init', array($this, 'late_init'));

		// Some stats
		$this->build_stats['mem'] = memory_get_usage();
	}

	function late_init()
	{
		remove_action('bwp_option_action_before_form', array($this, 'show_donation'), 12);	
	}

	function enqueue_media()
	{
		if (is_admin())
			wp_enqueue_style('bwp-gxs-admin', BWP_GXS_CSS . '/bwp-simple-gxs.css');
	}

	function insert_query_vars($vars)
	{
		if (!$this->use_permalink)
			array_push($vars, $this->query_var_non_perma);
		else
		{
			array_push($vars, 'gxs_module');
			array_push($vars, 'gxs_sub_module');
		}
		return $vars;
	}

	function insert_rewrite_rules($rules)
	{
		// More compatible with blogs that are set up with sitemap.xml - @since 1.0.1
		$rewrite_rules = array(
			'sitemap\.xml$' => 'index.php?gxs_module=sitemapindex',
			'sitemapindex\.xml$' => 'index.php?gxs_module=sitemapindex',
			'site\.xml$' => 'index.php?gxs_module=site',
			'page\.xml$' => 'index.php?gxs_module=page',
			'post\.xml$' => 'index.php?gxs_module=post',
			'author\.xml$' => 'index.php?gxs_module=author',
			'([a-z0-9]+)_([a-z0-9_-]+)\.xml$' => 'index.php?gxs_module=$matches[1]&gxs_sub_module=$matches[2]'
		);
		// @since 1.0.3
		$custom_rules = apply_filters('bwp_gxs_rewrite_rules', array());
		$rules = array_merge($custom_rules, $rewrite_rules, $rules);
		return $rules;
	}

	private static function flush_rewrite_rules()
	{
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	function add_hooks()
	{
		add_filter('rewrite_rules_array', array($this, 'insert_rewrite_rules'));
		add_filter('query_vars', array($this, 'insert_query_vars'));
		add_action('parse_request', array($this, 'request_sitemap'));
		if ('yes' == $this->options['enable_ping'])
		{
			add_action('draft_to_publish', array($this, 'ping'), 1000);
			add_action('new_to_publish', array($this, 'ping'), 1000);
			add_action('pending_to_publish', array($this, 'ping'), 1000);
			add_action('future_to_publish', array($this, 'ping'), 1000);
		}
		if ('yes' == $this->options['enable_robots'])
		{
			add_filter('robots_txt', array($this, 'do_robots'), 1000, 2);
		}
	}

	function install()
	{
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	
	function uninstall()
	{
		global $wp_rewrite;
		$this->logs = array('log' => array(), 'sitemap' => array());
		$this->commit_logs();
		$wp_rewrite->flush_rules();
	}

	/**
	 * Build the option pages
	 *
	 * Utilizes BWP Option Page Builder (@see BWP_OPTION_PAGE)
	 */
	function build_option_pages()
	{
		if (!current_user_can(BWP_GXS_CAPABILITY))
			wp_die(__('You do not have sufficient permissions to access this page.'));

		// Init the class
		$page = $_GET['page'];		
		$bwp_option_page = new BWP_OPTION_PAGE($page, $this->site_options);
		
		$options = array();
		$dynamic_options = array();

if (!empty($page))
{
	if ($page == BWP_GXS_GENERAL)
	{
		$bwp_option_page->set_current_tab(1);

		// Clear logs = @since 1.1.0
		if (isset($_POST['clear_log']) && !$this->is_normal_admin())
		{
			check_admin_referer($page);
			$this->logs = array('log' => array(), 'sitemap' => array());
			$this->commit_logs();
			$this->add_notice('<strong>' . __('Notice', 'bwp-simple-gxs') . ':</strong> ' . __("All logs have been cleared successfully!", 'bwp-simple-gxs'));
		}

		$form = array(
			'items'			=> array('heading', 'heading', 'input', 'select', 'select', 'select', 'checkbox', 'checkbox', 'heading', 'heading', 'heading', 'checkbox', 'section', 'heading', 'checkbox'),
			'item_labels'	=> array
			(
				__('What are Sitemaps?', 'bwp-simple-gxs'),
				__('Basic Settings', 'bwp-simple-gxs'),
				__('Output no more than', 'bwp-simple-gxs'),
				__('Default change frequency', 'bwp-simple-gxs'),
				__('Default priority', 'bwp-simple-gxs'),
				__('Minimum priority', 'bwp-simple-gxs'),
				__('Enable Gzip?', 'bwp-simple-gxs'),
				__('Add sitemapindex to individual blog\'s virtual robots.txt?', 'bwp-simple-gxs'),
				__('Your sitemaps', 'bwp-simple-gxs'),
				__('Submit your sitemaps', 'bwp-simple-gxs'),
				__('Pinging search engines', 'bwp-simple-gxs'),
				__('Enable pinging functionality?', 'bwp-simple-gxs'),
				__('Enable pinging individual SE', 'bwp-simple-gxs'),
				__('Sitemap Generator\'s Log', 'bwp-simple-gxs'),
				__('Enable logging?', 'bwp-simple-gxs'),
				__('Enable debugging?', 'bwp-simple-gxs')
			),
			'item_names'	=> array('h1', 'h6', 'input_item_limit', 'select_default_freq', 'select_default_pri', 'select_min_pri', 'cb8', 'cb9', 'h2', 'h4', 'h5', 'cb1', 'sec1', 'h3', 'cb2'),
			'sec1' => array(
					array('checkbox', 'name' => 'cb4'),
					array('checkbox', 'name' => 'cb6'),
					array('checkbox', 'name' => 'cb7')
			),
			'heading'			=> array(
				'h1'	=> __('In its simplest form, a Sitemap is an XML file that lists URLs for a site along with additional metadata about each URL (when it was last updated, how often it usually changes, and how important it is, relative to other URLs in the site) so that search engines can more intelligently crawl the site &mdash; <em>http://www.sitemaps.org/</em>', 'bwp-simple-gxs') . '<br /><br />' . __('This plugin helps you generate both Sitemap Index files as well as normal Sitemap files. A Sitemap Index, as its name suggests, is one kind of sitemaps that allows you to group multiple sitemap files inside it.', 'bwp-simple-gxs'),
				'h2'	=> __('<em>Basic information about all your sitemaps.</em>', 'bwp-simple-gxs'),
				'h3'	=> __('<em>More detailed information about how your sitemaps are generated including <span style="color: #999999;">notices</span>, <span style="color: #FF0000;">errors</span> and <span style="color: #009900;">success messages</span>.</em>', 'bwp-simple-gxs'),
				'h4'	=> sprintf(__('<em>Submit your sitemapindex to major search engines like <a href="%s" target="_blank">Google</a>, <a href="%s" target="_blank">Bing</a> or <a href="%s" target="_blank">Ask</a>.</em>', 'bwp-simple-gxs'), 'https://www.google.com/webmasters/tools/home?hl=en', 'http://www.bing.com/toolbox/webmasters/', 'http://about.ask.com/en/docs/about/webmasters.shtml#22'),
				'h5'	=> __('<em>Now when you post something new to your blog, you can <em>ping</em> those search engines to tell them your blog just got updated. Pinging could be less effective than you think it is but you should enable such feature anyway.</em>', 'bwp-simple-gxs'),
				'h6'	=> __('<em>Choose how you want your sitemaps to be generated.</em>', 'bwp-simple-gxs'),
			),
			'select' => array(
				'select_default_freq' => array(),
				'select_default_pri' => $this->priority,
				'select_min_pri' => $this->priority
			),
			'input'		=> array(
				'input_item_limit' => array('size' => 5, 'label' => __('item(s) in one sitemap. You can not go over 50,000. Keep this small if you\'re in a shared hosting environment.', 'bwp-simple-gxs'))
			),
			'checkbox'	=> array(
				'cb1' => array(__('Selected SE below will be pinged when you publish new posts.', 'bwp-simple-gxs') => 'enable_ping'),
				'cb2' => array(__('No additional load is needed so enabling this is recommended.', 'bwp-simple-gxs') => 'enable_log'),
				'cb3' => array(__('Minor errors will be printed on screen. Also, when debug is on, no caching is used, useful when you develop new modules.', 'bwp-simple-gxs') => 'enable_debug'),
				'cb4' => array(__('Google', 'bwp-simple-gxs') => 'enable_ping_google'),
				'cb6' => array(__('Bing', 'bwp-simple-gxs') => 'enable_ping_bing'),
				'cb7' => array(__('Ask.com', 'bwp-simple-gxs') => 'enable_ping_ask'),
				'cb8' => array(__('make your sitemaps ~ 70% smaller. <strong>Important:</strong> If you see an error after enabling this, it\'s very likely that you have gzip active on your server already.', 'bwp-simple-gxs') => 'enable_gzip'),
				'cb9' => array(__('If you\'re on a Multi-site installation with Sub-domain enabled, each site will have its own robots.txt, sites in sub-directory will not.', 'bwp-simple-gxs') => 'enable_robots')
			),
			'container'	=> array(
				'h4' => __('After you activate this plugin, all sitemaps should be available right away. The next step is to submit the sitemapindex to major search engines. You only need the <strong>sitemapindex</strong> and nothing else, those search engines will automatically recognize other included sitemaps.', 'bwp-simple-gxs'),
				'h3' => $this->get_logs(),
				'h2' => $this->get_logs(true)
			)
		);

		// Add a clear log button - @since 1.1.0
		if (!$this->is_normal_admin())
			add_filter('bwp_option_submit_button', array($this, 'add_clear_log_button'));

		foreach ($this->frequency as $freq)
			$changefreq[ucfirst($freq)] = $freq;
		$form['select']['select_default_freq'] = $changefreq;

		// Get the options
		$options = $bwp_option_page->get_options(array('enable_ping', 'enable_ping_google', 'enable_ping_bing', 'enable_ping_ask', 'enable_log', 'input_item_limit', 'select_default_freq', 'select_default_pri', 'select_min_pri', 'enable_gzip', 'enable_robots'), $this->options);

		// Get option from the database
		$options = $bwp_option_page->get_db_options($page, $options);
		$option_ignore = array('input_update_services');
		$option_formats = array('input_item_limit' => 'int', 'input_split_limit_post' => 'int');
		// [WPMS Compatible]
		$option_super_admin = $this->site_options;
	}
}
		// Flush the cache
		if (isset($_POST['flush_cache']) && !$this->is_normal_admin())
		{
			check_admin_referer($page);
			if ($deleted = $this->flush_cache())
				$this->add_notice('<strong>' . __('Notice', 'bwp-simple-gxs') . ':</strong> ' . sprintf(__("<strong>%d</strong> cached sitemaps have been flushed successfully!", 'bwp-simple-gxs'), $deleted));
			else
				$this->add_notice('<strong>' . __('Notice', 'bwp-simple-gxs') . ':</strong> ' . __("Could not delete any cached sitemaps. Please manually check the cache directory.", 'bwp-simple-gxs'));
		}

		// Get option from user input
		if (isset($_POST['submit_' . $bwp_option_page->get_form_name()]) && isset($options) && is_array($options))
		{
			check_admin_referer($page);
			$need_flushed = false;
			foreach ($options as $key => &$option)
			{
				$pre_option = $option;
				// Get rid of options that do not have a key
				if (preg_match('/^[0-9]+$/i', $key))
				{
					unset($options[$key]);
					continue;
				}
				// [WPMS Compatible]
				if ($this->is_normal_admin() && in_array($key, $option_super_admin))
				{}
				else if (in_array($key, $option_ignore))
				{}
				else
				{
					if (isset($_POST[$key]))
						$bwp_option_page->format_field($key, $option_formats);
					if (!isset($_POST[$key]))
						$option = '';
					else if (isset($option_formats[$key]) && 0 == $_POST[$key] && 'int' == $option_formats[$key])
						$option = 0;
					else if (isset($option_formats[$key]) && empty($_POST[$key]) && 'int' == $option_formats[$key])
						$option = $this->options_default[$key];
					else if (!empty($_POST[$key]))
						$option = trim(stripslashes($_POST[$key]));
					else
						$option = $this->options_default[$key];
					// Mark that we need to flush rewrite rules
					if (false !== strpos($key, 'enable_sitemap_') && $pre_option != $option)
						$need_flushed = true;
				}
			}
			update_option($page, $options);
			// Flush rewrite rules if needed
			if ($need_flushed)
				self::flush_rewrite_rules();
			// [WPMS Compatible]
			if (!$this->is_normal_admin())
				update_site_option($page, $options);
		}

		if ($this->is_normal_admin())
		{
			$bwp_option_page->kill_html_fields($form, array(10,11,12,13,14,15));
		}

		if (!@file_exists($this->options['input_cache_dir']) || !@is_writable($this->options['input_cache_dir']))
			$this->add_notice('<strong>' . __('Warning') . ':</strong> ' . __("Cache directory does not exist or is not writable. Please read more about directory permission <a href='http://www.zzee.com/solutions/unix-permissions.shtml'>here</a> (Unix).", 'bwp-simple-gxs'));

		// Assign the form and option array		
		$bwp_option_page->init($form, $options + $dynamic_options, $this->form_tabs);

		// Build the option page	
		echo $bwp_option_page->generate_html_form();
	}

	function add_clear_log_button($button)
	{
		$button = str_replace('</p>', ' <input type="submit" class="button-secondary action" name="clear_log" value="' . __('Clear All Logs', 'bwp-simple-gxs') . '" /></p>', $button);
		return $button;
	}

	function flush_cache()
	{
		$dir = trailingslashit($this->options['input_cache_dir']);
		$deleted = 0;
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (preg_match('/^gxs_[a-z0-9]+\.(xml|xml\.gz)$/i', $file))
					{
						@unlink($dir . $file);
						$deleted++;
					}
				}
				closedir($dh);
			}
		}
		return $deleted;
	}

	function get_options()
	{
		return $this->options;
	}

	function get_current_time()
	{
		return current_time('timestamp');
	}

	function format_header_time($time)
	{
		return gmdate('D, d M Y H:i:s \G\M\T', (int) $time);
	}

	function uni_path_sep($path = '')
	{
		return str_replace('\\', '/', $path);
	}

	function commit_logs()
	{
		update_option(BWP_GXS_LOG, $this->logs);
	}

	function do_log($message, $error = true, $sitemap = false)
	{
		$time = $this->get_current_time();
		$debug = ('yes' == $this->options['enable_debug']) ? __('(Debug is on)', 'bwp-simple-gxs') : '';
		if (!$sitemap && 'yes' == $this->options['enable_log'] && !empty($message))
			$this->logs['log'][] = array('log' => $message . ' ' . $debug, 'time' => $time, 'error' => $error);
		else if (!is_bool($sitemap))
			$this->logs['sitemap'][$sitemap] = array('time' => $time, 'url' => $sitemap);
	}

	function elog($message, $die = false, $errorCode = 503)
	{
		$this->do_log($message);
		if (true == $die && true == $this->debug)
		{
			$this->commit_logs();
			wp_die(__('<strong>BWP Google XML Sitemaps Error:</strong> ', 'bwp-simple-gxs') . $message, __('BWP Google XML Sitemaps Error', 'bwp-simple-gxs'), array('response' => $errorCode));
		}
	}

	function slog($message)
	{
		$this->do_log($message, false);
	}

	function nlog($message)
	{
		$this->do_log($message, 'notice');
	}

	function smlog($url)
	{
		$this->do_log('', false, $url);
	}

	function get_logs($sitemap = false)
	{
		$logs = (!$sitemap) ? $this->logs['log'] : $this->logs['sitemap'];
		if (!$logs || !is_array($logs) || 0 == sizeof($logs))
			return ($sitemap) ? sprintf(__('Nothing here... yet! Try submitting your <a href="%s">sitemapindex</a> first!', 'bwp-simple-gxs'), $this->options['input_sitemap_url']) : __('No log yet!', 'bwp-simple-gxs');
		if (!$sitemap)
			krsort($logs);
		else
		{
			$log_time = array();
			foreach ($logs as $key => $row)
				$log_time[$key] = $row['time'];		
			array_multisort($log_time, SORT_DESC, $logs);
		}
		$log_str = (!$sitemap) ? '<li class="clear" style="margin-top: 5px; line-height: 1.7;"><span style="float: left; margin-right: 5px;">%s &mdash;</span> <span style="color: #%s;">%s</span></li>' : '<span style="margin-top: 5px; display: inline-block;">' . __('<a href="%s" target="_blank">%s</a> has been successfully built on <strong>%s</strong>.', 'bwp-simple-gxs') . '</span><br />';
		$output = '<ul class="bwp-gxs-log">' . "\n";
		foreach ($logs as $log)
		{
			if (isset($log['error']))
			{
				$color = (!is_bool($log['error']) && 'notice' == $log['error']) ? '999999' : '';
				if ('' ==  $color)
					$color = (!$log['error']) ? '009900' : 'FF0000';
				/* translators: date format, see http://php.net/date */
				$output .= sprintf($log_str, date(__('M j, Y : H:i:s', 'bwp-simple-gxs'), $log['time']), $color, $log['log']) . "\n";
			}
			else
			{
				// @since 1.1.5 - check for mapped domain
				global $wpdb, $blog_id;
				if (!empty($wpdb->dmtable) && !empty($wpdb->blogs) && $this->is_multisite())
				{
					$mapped_domain = $wpdb->get_var($wpdb->prepare('
							SELECT wpdm.domain as mapped_domain FROM ' . $wpdb->blogs . ' wpblogs
								INNER JOIN ' . $wpdb->dmtable . ' wpdm
									ON wpblogs.blog_id = wpdm.blog_id
							WHERE wpblogs.public = 1 AND wpblogs.spam = 0 AND wpblogs.deleted = 0 AND wpblogs.blog_id = %d', $blog_id));
				}
				// Default to the main site's scheme
				$home = @parse_url(home_url());
				$sitemap_struct = (!empty($mapped_domain)) ? str_replace($home['host'], str_replace(array('http', 'https'), '', $mapped_domain), $this->options['input_sitemap_struct']) : $this->options['input_sitemap_struct'];
				$output .= sprintf($log_str, sprintf($sitemap_struct, $log['url']), $log['url'], date(__('M j, Y : H:i:s', 'bwp-simple-gxs'), $log['time'])) . "\n";
			}
		}

		return $output . '</ul>' . "\n";
	}

	function format_label($label)
	{
		return str_replace(' ', '_', strtolower($label));
	}

	function do_robots($output, $public)
	{
		global $blog_id, $wpdb;

		if ('0' == $public)
			return $output;

		if ((defined('SUBDOMAIN_INSTALL') && true == SUBDOMAIN_INSTALL) || (isset($blog_id) && 1 == $blog_id))
		{
			$output .= "\n";
			$output .= 'Sitemap: ' . $this->options['input_sitemap_url'];
			$output .= "\n";
		}
		// Add all other sitemapindex within the network into the primary blog's robots.txt
		if ($this->is_multisite() && 'yes' == $this->options['enable_global_robots'] && isset($blog_id) && 1 == $blog_id)
		{
			$blogs = $wpdb->get_results("SELECT * FROM $wpdb->blogs WHERE public = 1 AND spam = 0 AND deleted = 0");
			foreach ($blogs as $blog)
			{
				if (1 == $blog->blog_id)
					continue;
				$scheme = is_ssl() ? 'https://' : 'http://';
				$path = rtrim($blog->path, '/');
				$output .= 'Sitemap: ' . str_replace(home_url(), $scheme . $blog->domain . $path, $this->options['input_sitemap_url']) . "\n";
			}
			$output .= "\n";
		}
		return $output;
	}

	/** 
	 * Redirect to correct domain
	 *
	 * This plugin generates sitemaps dynamically and exits before WordPress does any canonical redirection.
	 * This function makes sure non-www domain is redirected and vice versa.
	 * @since 1.0.1
	 */
	function canonical_redirect($xml_slug)
	{
		$requested_url  = is_ssl() ? 'https://' : 'http://';
		$requested_url .= $_SERVER['HTTP_HOST'];
		$requested_url .= $_SERVER['REQUEST_URI'];
		$original = @parse_url($requested_url);
		if (false === $original)
			return;
		// www.example.com vs example.com
		$user_home = @parse_url(home_url());
		if (!empty($user_home['host']))
			$host = $user_home['host'];
		if (strtolower($original['host']) == strtolower($host) ||
		(strtolower($original['host']) != 'www.' . strtolower($host) && 'www.' . strtolower($original['host']) != strtolower($host)))
			$host = $original['host'];
		else
		{
			wp_redirect(sprintf($this->options['input_sitemap_struct'], $xml_slug), 301);
			exit;
		}
	}

	/** 
	 * A convenient function to add wanted modules or sub modules
	 *
	 * When you filter the 'bwp_gxs_modules' hook it is recommended that you use this function.
	 */
	function add_module($module = '', $sub_module = '')
	{
		if (empty($module))
			return false;
		// Make sure the names are well-formed
		$module = preg_replace('/[^a-z0-9-_\s]/ui', '', $module);
		$module = trim(str_replace(' ', '_', $module));
		$sub_module = preg_replace('/[^a-z0-9-_\s]/ui', '', $sub_module);
		$sub_module = trim(str_replace(' ', '_', $sub_module));
		if (empty($sub_module))
			$this->allowed_modules[$module] = array();
		if (!isset($this->allowed_modules[$module]) || !is_array($this->allowed_modules[$module]))
			$this->allowed_modules[$module] = array($sub_module);
		else if (!in_array($sub_module, $this->allowed_modules[$module]))
			$this->allowed_modules[$module][] = $sub_module;
	}

	/** 
	 * A convenient function to remove unwanted modules or sub modules
	 *
	 * When you filter the 'bwp_gxs_modules' hook it is recommended that you use this function.
	 */
	function remove_module($module = '', $sub_module = '')
	{
		if (empty($module) || !isset($this->allowed_modules[$module]))
			return false;
		if (empty($sub_module))
			unset($this->allowed_modules[$module]);
		else
		{
			$module = trim($module);
			$sub_module = trim($sub_module);
			$temp = $this->allowed_modules[$module];
			foreach ($temp as $key => $subm)
				if ($sub_module == $subm)
				{
					unset($this->allowed_modules[$module][$key]);
					return false;
				}
		}
	}

	function allowed_modules()
	{
		$allowed_modules = array();
		$this->allowed_modules = &$allowed_modules;
		// Site home URL sitemap - @since 1.1.5
		if ('yes' == $this->options['enable_sitemap_site'])
			$this->add_module('site');
		// Module exclusion list
		$excluded_post_types = explode(',', $this->options['input_exclude_post_type']);
		$excluded_taxonomies = explode(',', $this->options['input_exclude_taxonomy']);
		// Add public post types to module list
		$post_types = $this->post_types = get_post_types(array('public' => true), 'objects');
		foreach ($this->post_types as $post_type)
		{
			// Page will have its own
			if ('page' != $post_type->name && !in_array($post_type->name, $excluded_post_types))
				$allowed_modules['post'][] = $post_type->name;
		}
		// Add pages to module list
		if (!in_array('page', $excluded_post_types))
			$allowed_modules['page'] = array('page');
		// Add archive pages to module list
		if ('yes' == $this->options['enable_sitemap_date'])
			$allowed_modules['archive'] = array('monthly', 'yearly');
		// Add taxonomies to module list
		$this->taxonomies = get_taxonomies(array('public' => true), '');
		if ('yes' == $this->options['enable_sitemap_taxonomy'])
		{
			foreach ($this->taxonomies as $taxonomy)
				if (!in_array($taxonomy->name, $excluded_taxonomies))
					$allowed_modules['taxonomy'][] = $taxonomy->name;
		}
		// Remove some unnecessary sitemap
		$this->remove_module('post', 'attachment');
		$this->remove_module('taxonomy', 'post_format');
		$this->remove_module('taxonomy', 'nav_menu');
		// Add / Remove modules based on users' preferences
		if ('yes' == $this->options['enable_sitemap_author'])
			$this->add_module('author');
		if ('yes' == $this->options['enable_sitemap_external'])
			$this->add_module('page', 'external');
		// Hook for a custom module list
		do_action('bwp_gxs_modules_built', $this->allowed_modules, $this->post_types, $this->taxonomies);
		return $this->allowed_modules;
	}

	function build_requested_modules($allowed)
	{
		$this->requested_modules = array();
		foreach ($allowed as $module_name => $module)
		{
			foreach ($module as $sub_module)
			{
				if (isset($this->post_types[$sub_module])) // Module is a post type
				{
					// @since 1.0.4 - do not use label anymore, ugh
					// $label = $this->format_label($this->post_types[$sub_module]->label);
					$label = $this->format_label($this->post_types[$sub_module]->name);
					if ('post' == $sub_module || 'page' == $sub_module || 'attachment' == $sub_module)
						$data = array($label, array('post' => $this->post_types[$sub_module]->name));
					else
						$data = array($module_name . '_' . $label, array('post' => $this->post_types[$sub_module]->name));
					$this->requested_modules[] = $data;
				}
				else if ('yes' == $this->options['enable_sitemap_taxonomy'] && isset($this->taxonomies[$sub_module])) // Module is a taxonomy
				{
					// $label = $this->format_label($this->taxonomies[$sub_module]->label);
					$label = $this->format_label($this->taxonomies[$sub_module]->name);
					$this->requested_modules[] = array($module_name . '_' . $label, array('taxonomy' => $sub_module));
				}
				/*else if ('term' == $module_name) // Module is a term {} */
				else if (!empty($sub_module))
					$this->requested_modules[] = array($module_name . '_' . $sub_module, array('archive' => $sub_module));
				else
					$this->requested_modules[] = array($module_name);
			}
		}
	}

	function convert_label(&$sub_module, $module)
	{
		if ('taxonomy' == $module)
		{
			foreach ($this->taxonomies as $taxonomy)
				if ($this->format_label($taxonomy->label) == $sub_module)
					$sub_module = $taxonomy->name;
		}
		else if ('post' == $module)
		{
			foreach ($this->post_types as $post_type)
				if ($this->format_label($post_type->label) == $sub_module)
					$sub_module = $post_type->name;
		}
	}

	function convert_module($module)
	{
		preg_match('/([a-z0-9]+)_([a-z0-9_-]+)$/iu', $module, $matches);
		if (0 == sizeof($matches))
			return false;
		else
			return $matches;
	}

	function request_sitemap($wpquery)
	{
		// Currently requested module
		if (isset($wpquery->query_vars['gxs_module']))
		{
			$module = $wpquery->query_vars['gxs_module'];
			$sub_module = (isset($wpquery->query_vars['gxs_sub_module'])) ? $wpquery->query_vars['gxs_sub_module'] : '';
			if (!empty($module))
				$this->load_module($module, $sub_module);
		}
		else if (isset($wpquery->query_vars[$this->query_var_non_perma]))
		{
			$module = $wpquery->query_vars[$this->query_var_non_perma];
			$parsed_module = $this->convert_module($module);
			if ($parsed_module && is_array($parsed_module))
				$this->load_module($parsed_module[1], $parsed_module[2]);
			else
				$this->load_module($module, '');
		}
	}

	function load_module($module, $sub_module)
	{
		// Assuming we fail, ugh!
		$success = false;
		// Remember to use $wpdb->prepare or $wpdb->escape when developing module
		$module = stripslashes($module);
		$sub_module = stripslashes($sub_module);
		// Check whether or not splitting is enabled and the sub_module has a 'part' part, 
		// if so, remove it and pass a parameter to let the module knows that it must produce a split sitemap
		$module_part = 0;
		$module_virtual = '';
		if (('yes' == $this->options['enable_sitemap_split_post'] && preg_match('/part([0-9]+)$/i', $sub_module, $matches)) || 'property' == $module)
		{
			$module_virtual = str_replace($matches[0], '', $sub_module);
			$module_virtual = rtrim($module_virtual, '_');
			$module_part = (int) $matches[1];
		}
		$true_sub_module = $sub_module;
		$pre_module = $module;
		$pre_module .= (!empty($sub_module)) ? '_' . $sub_module : '';
		// @since 1.0.1 - Redirect to correct domain, with or without www
		$this->canonical_redirect($pre_module);
		// Allowed modules
		$allowed_modules = $this->allowed_modules();
		$this->build_requested_modules($allowed_modules);
		// $this->convert_label($sub_module, $module);
		if ('sitemapindex' != $module && isset($allowed_modules[$module]))
		{
			if (!empty($sub_module))
			{
				if (in_array($sub_module, $allowed_modules[$module]) || (empty($module_virtual) && !empty($module_part) && in_array($module, $allowed_modules[$module])) || (!empty($module_virtual) && in_array($module_virtual, $allowed_modules[$module])))
					$module_key = $module . '_' . $sub_module;
				else if ('property' == $module && preg_match('/^[0-9]+$/', $module_virtual))
					$module_key = $module . '_' . $sub_module;
				else
					$module_key = '';
			}
			else
				$module_key = $module;
			$module_name = str_replace($sub_module, $true_sub_module, $module_key);
		}
		else if ('sitemapindex' == $module)
		{
			$module_key = 'sitemapindex';
			$module_name = 'sitemapindex';
		}

		// Pass the real sub_module back
		$sub_module = (!empty($module_part)) ? $module_virtual : $sub_module;

		if (empty($module_key))
		{
			$this->elog(sprintf(__('Requested module (<em>%s</em>) not found or not allowed.', 'bwp-simple-gxs'), $pre_module), true, 404);
			$this->commit_logs();
			// If debugging is not enabled, redirect to homepage
			wp_redirect(home_url());
			exit;
		}

		// @since 1.0.1 - Start counting correct build time and queries
		timer_start();
		$this->build_stats['query'] = get_num_queries();

		// Don't let other instrusive plugins mess up our permalnks - @since 1.1.4
		remove_filter('post_link', 'syndication_permalink', 1, 3);
		remove_filter('page_link', 'suffusion_unlink_page', 10, 2);

		// If cache is enabled, we check the cache first
		if ('yes' == $this->options['enable_cache'])
		{
			require_once(dirname(__FILE__) . '/class-bwp-gxs-cache.php');
			$bwp_gxs_cache = new BWP_GXS_CACHE(array('module' => $module_key, 'module_name' => $module_name));
			// If cache is ok, output the cached sitemap, only if debug is off
			if ('yes' != $this->options['enable_debug'] && true == $bwp_gxs_cache->has_cache)
			{
				$this->send_header($bwp_gxs_cache->get_header());
				$file = $bwp_gxs_cache->get_cache_file();
				// Decompress the gz file only if the server or script is not already gzipping, and gzip is enabled
				// This is to avoid double compression
				if ('yes' == $this->options['enable_gzip'] && !self::is_gzipped())
					readfile($file);
				else
					readgzfile($file);
				// Get from cache successfully
				$this->slog(sprintf(__('Successfully served a cached version of <em>%s.xml</em>.', 'bwp-simple-gxs'), $module_name), true);
				$this->commit_logs();
				exit;
			}
		}

		// If the user uses a custom module dir, also check that dir for usable module files
		$custom_module_dir = (!empty($this->options['input_alt_module_dir']) && $this->options_default['input_alt_module_dir'] != $this->options['input_alt_module_dir']) ? trailingslashit($this->options['input_alt_module_dir']) : false;
		$custom_module_dir = trailingslashit(apply_filters('bwp_gxs_module_dir', $custom_module_dir));
		// Begin loading modules
		require_once(dirname(__FILE__) . '/class-bwp-gxs-module.php');
		if ('sitemapindex' != $module && isset($allowed_modules[$module]))
		{
			$sub_loaded = $mapped_sub_loaded = false;
			if (!empty($sub_module) && in_array($sub_module, $allowed_modules[$module]))
			{
				// Try to load the mapped sub-module first
				if (!empty($this->module_map[$sub_module]))
				{
					$module_file = $module . '_' . $this->module_map[$sub_module] . '.php';
					$path_custom = ($custom_module_dir) ? $this->uni_path_sep($custom_module_dir . $module_file) : '';
					$path = $this->uni_path_sep($this->module_directory . $module_file);
					if (!empty($path_custom) && @file_exists($path_custom))
					{
						$module_key = $module . '_' . $this->module_map[$sub_module];
						include_once($path_custom);
						$mapped_sub_loaded = true;	
						$this->nlog(sprintf(__('Loaded a custom sub-module file: <strong>%s</strong>.', 'bwp-simple-gxs'), $module_file));					
					}
					else if (@file_exists($path))
					{
						$module_key = $module . '_' . $this->module_map[$sub_module];
						include_once($path);
						$mapped_sub_loaded = true;
					}
					else // Don't fire a wp_die
						$this->nlog(sprintf(__('Mapped sub-module file: <strong>%s</strong> is not available in both default and custom module directory. The plugin will now try loading the requested sub-module instead.', 'bwp-simple-gxs'), $module_file));					
				}
				if (false == $mapped_sub_loaded)
				{
					$module_file = $module . '_' . $sub_module . '.php';
					$path_custom = ($custom_module_dir) ? $this->uni_path_sep($custom_module_dir . $module_file) : '';
					$path = $this->uni_path_sep($this->module_directory . $module_file);
					if (!empty($path_custom) && @file_exists($path_custom))
					{
						include_once($path_custom);
						$sub_loaded = true;
						$this->nlog(sprintf(__('Loaded a custom sub-module file: <strong>%s</strong>.', 'bwp-simple-gxs'), $module_file));					
					}
					else if (@file_exists($path))
					{
						include_once($path);
						$sub_loaded = true;
					}
					else // Don't fire a wp_die
						$this->nlog(sprintf(__('Sub-module file: <strong>%s</strong> is not available in both default and custom module directory. The plugin will now try loading the parent module instead.', 'bwp-simple-gxs'), $module_file));
				}
			}
			if (false == $sub_loaded && false == $mapped_sub_loaded)
			{
				// Try loading the module
				$module_file = $module . '.php';
				$module_key = $module;
				$path_custom = ($custom_module_dir) ? $this->uni_path_sep($custom_module_dir . $module_file) : '';
				$path = $this->uni_path_sep($this->module_directory . $module_file);
				if (!empty($path_custom) && @file_exists($path_custom))
				{
					include_once($path_custom);
					$this->nlog(sprintf(__('Loaded a custom module file: <strong>%s</strong>.', 'bwp-simple-gxs'), $module_file));					
				}
				else if (@file_exists($path))
					include_once($path);
				else
				{
					$error_log = sprintf(__('Could not load module file: <strong>%s</strong> in both default and custom module directory. Please recheck if that module file exists.', 'bwp-simple-gxs'), $module_file);
					$this->elog($error_log, true);
				}
			}
			
			$this->module_data = array('module' => $module, 'sub_module' => $sub_module, 'module_key' => $module_key, 'module_name' => $module_name, 'module_part' => $module_part);
			
			if (class_exists('BWP_GXS_MODULE_' . $module_key))
			{
				$class_name = 'BWP_GXS_MODULE_' . $module_key;
				$the_module = new $class_name();
				if ('url' == $the_module->type)
					$success = $this->generate_sitemap($the_module->data);
				else
					$success = $this->generate_sitemap_index($the_module->data);
				unset($the_module->data);
			}
			else
				$this->elog(sprintf(__('There is no class named <strong>%s</strong> in the module file <strong>%s</strong>.', 'bwp-simple-gxs'), 'BWP_GXS_MODULE_' . strtoupper($module_key), $module_file), true);
		}
		else if ('sitemapindex' == $module)
		{
			$module_file = 'sitemapindex.php';
			$path_custom = ($custom_module_dir) ? $this->uni_path_sep($custom_module_dir . $module_file) : '';
			if (!empty($path_custom) && @file_exists($path_custom))
			{
				include_once($path_custom);
				$this->nlog(sprintf(__('Loaded a custom sitemapindex module file: <strong>%s</strong>.', 'bwp-simple-gxs'), $module_file));
			}
			else
				include_once(dirname(__FILE__) . '/modules/sitemapindex.php');
			if (class_exists('BWP_GXS_MODULE_INDEX'))
			{
				$the_module = new BWP_GXS_MODULE_INDEX($this->requested_modules);
				$success = $this->generate_sitemap_index($the_module->data);
				unset($the_module->data);
			}
		}

		// Output succeeded
		if (true == $success)
		{
			// Output additional stats
			if ('yes' == $this->options['enable_stats'])
				$this->sitemap_stats();
			// Now cache the sitemap if we have to
			if ('yes' == $this->options['enable_cache'] && true == $bwp_gxs_cache->cache_ok) 
				$bwp_gxs_cache->write_cache();
			// Output the requested sitemap
			$this->output_sitemap();
			$this->slog(sprintf(__('Successfully generated <em>%s.xml</em> using module <em>%s</em>.', 'bwp-simple-gxs'), $module_name, $module_file), true);
			$this->smlog($module_name);
			$this->commit_logs();
			exit;
		}
		else
			$this->commit_logs();
	}

	function send_header($header = array())
	{
		global $bwp_gxs_ob_start, $bwp_gxs_ob_level, $bwp_gxs_gzipped;

		// If debug is not enabled and gzip is not turned on in between, 
		// we try to clean all errors before sending new headers - @since 1.1.2
		$clean_ok = ((int) $bwp_gxs_gzipped == (int) self::is_gzipped() && 'yes' == $this->options['enable_php_clean']) ? true : false;
		$ob_contents = '';
		if ($bwp_gxs_ob_start && @ob_get_level())
		{
			$ob_level = @ob_get_level() - $bwp_gxs_ob_level;
			while ($ob_level > 0)
			{
				$ob_level -= 1;
				$ob_contents .= ob_get_contents();
				if ('yes' != $this->options['enable_debug'] && $clean_ok)
					@ob_end_clean();
			}
		}

		// If there are some contents but we can't clean them, show a friendly error
		if (!empty($ob_contents) && (!$clean_ok || 'yes' == $this->options['enable_debug']))
			wp_die(__('<strong>BWP Google XML Sitemap Message:</strong> Unexpected output (most of the time PHP errors) is preventing BWP GXS from showing any sitemap contents. Try disabling <code>WP_DEBUG</code> or this plugin\'s debug mode, whichever is on. All unexpected outputs should be shown just above this message. If you don\'t see any, contact me and I might be able to help.', 'bwp-simple-gxs'));

		if (!empty($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'], 'Apache/2'))
			header ('Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0');
		else
			header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');

		$content_types = array('google' => 'text/xml');
		
		$time = time();
		$expires = $this->format_header_time($time + $this->cache_time);

		$default_headers = array(
			'lastmod' => $this->format_header_time($time),
			'expires' => $expires,
			'etag' => ''
		);

		$header = wp_parse_args($header, $default_headers);
		
		header('Expires: ' . $header['expires']);
		header('Last-Modified: ' . $header['lastmod']);
		if (!empty($header['etag']))
			header('Etag: ' . $header['etag']);
		header('Accept-Ranges: bytes');
		header('Content-Type: ' . $content_types['google'] . '; charset=UTF-8');
		if ('yes' == $this->options['enable_gzip'])
			header('Content-Encoding: ' . $this->check_gzip_type());

		// Everything's cool
		status_header(200);

		return;
	}

	public static function is_gzipped()
	{
		if (ini_get('zlib.output_compression') || ini_get('output_handler') == 'ob_gzhandler' || in_array('ob_gzhandler', @ob_list_handlers()))
			return true;
		return false;
	}

	function init_gzip() 
	{
		if (!$this->check_gzip() && 'yes' == $this->options['enable_gzip'])
			$this->options['enable_gzip'] = 'no';
		return;
	}

	function check_gzip()
	{
		if (headers_sent())
			return false;

		if (!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && ((strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)
		 || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false))
			return true;
		else
			return false;
	}

	function check_gzip_type()
	{
		if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false)
			return 'gzip';
		else if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)
			return 'x-gzip';

		return 'gzip';
	}

	function ping()
	{
		$time = time();
		$ping_data = get_option(BWP_GXS_PING);
		if (!$ping_data || !is_array($ping_data))
			$ping_data = array(
				'data_pinged' => array('google' => 0, 'bing' => 0, 'ask' => 0),
				'data_last_pinged' => array('google' => 0, 'bing' => 0, 'ask' => 0)
			);
		foreach ($this->options['input_ping'] as $key => $service)
		{
			if ('yes' == $this->options['enable_ping_' . $key])
			{
				// A day has gone, reset the count
				if ($time - $ping_data['data_last_pinged'][$key] > 86400)
				{
					$ping_data['data_pinged'][$key] = 0;
					$ping_data['data_last_pinged'][$key] = $time;
				}
				// Ping limit has not been reached
				if ($this->ping_per_day > $ping_data['data_pinged'][$key])
				{
					$ping_data['data_pinged'][$key]++;
					$url = sprintf($service, urlencode(str_replace('&', '&amp;', $this->options['input_sitemap_url'])));
					$response = wp_remote_post($url, array('timeout' => $this->timeout));
					if (is_wp_error($response))
					{
						$errno		= $response->get_error_code();
						$errorstr 	= $response->get_error_message();
						$this->elog($errorstr);
					}
					else if (isset($response['response']))
					{
						$the_response = $response['response'];
						if (empty($the_response['code']))
							$this->elog(__('Unknown response code from search engines. Ping failed.', 'bwp-simple-gxs'));
						else if (200 == (int) $the_response['code'])
							$this->slog(sprintf(__('Pinged %s successfully!', 'bwp-simple-gxs'), ucfirst($key)));
						else if (200 != (int) $the_response['code'])
						{
							$errno		= $the_response['code'];
							$errorstr 	= $the_response['message'];
							$this->elog(sprintf(__('Error %s from %s', 'bwp-simple-gxs'), $errno, ucfirst($key)) . ': ' . $errorstr);
						}
					}
				}
				else
					$this->elog(sprintf(__('Ping limit for today to %s has been reached, sorry!', 'bwp-simple-gxs'), ucfirst($key)));
			}
		}
		// Update statistics
		$this->commit_logs();
		update_option(BWP_GXS_PING, $ping_data);
	}

	function output_sitemap()
	{
		if ('yes' == $this->options['enable_gzip'])
		{
			$this->output = (!self::is_gzipped()) ? gzencode($this->output, 6) : $this->output;
			$this->send_header();
			echo $this->output;
		} 
		else
		{
			$this->send_header();
			echo $this->output;
		}
	}

	function check_output($output)
	{
		// If output is empty we log it so the user knows what's going on, but let the page load normally
		if (empty($output) || 0 == sizeof($output))
		{
			$this->elog(sprintf(__('<em>%s.xml</em> does not have any item. The plugin has fired a 404 header to the search engine that requests it. You should check the module that generates that sitemap (<em>%s.php</em>).', 'bwp-simple-gxs'), $this->module_data['module_name'], $this->module_data['module_key']), true, 404);
			return false;
		}
		else
			return true;
	}

	function sitemap_stats($type = '')
	{
		$time = timer_stop(0, 3);
		$sql = get_num_queries() - $this->build_stats['query'];
		$memory = size_format(memory_get_usage() - $this->build_stats['mem'], 2);
		if (empty($type))
			$this->output .= "\n" . sprintf($this->templates['stats'], $time, $memory, $sql, $this->output_num);
		else
			echo "\n" . sprintf($this->templates['stats_cached'], $time, $memory, $sql);
	}

	function generate_url_item($url = '', $priority = 1.0, $freq = 'always', $lastmod = 0)
	{
		$freq = sprintf($this->templates['changefreq'], $freq);
		$priority = str_replace(',', '.', sprintf($this->templates['priority'], $priority));
		$lastmod = (!empty($lastmod)) ? sprintf($this->templates['lastmod'], $lastmod) : '';
		if (!empty($url))
			return sprintf($this->templates['url'], $url, $lastmod, $freq, $priority);
		else
			return '';
	}

	function generate_sitemap_item($url = '', $lastmod = 0)
	{
		$lastmod = (!empty($lastmod)) ? sprintf($this->templates['lastmod'], $lastmod) : '';
		if (!empty($url))
			return sprintf($this->templates['sitemap'], $url, $lastmod);
		else
			return '';
	}

	function credit()
	{
		$xml = '<!-- Generated by BWP Google XML Sitemaps ' . $this->get_version() . ' (c) 2011 Khang Minh - betterwp.net' . "\n";
		$xml .=' Plugin homepage: ' . $this->plugin_url . ' -->' . "\n";
		return $xml;
	}

	function generate_sitemap($output = array())
	{
		$xml = '<' . '?xml version="1.0" encoding="UTF-8"?'.'>' . "\n";
		$xml .= (!empty($this->xslt)) ? '<?xml-stylesheet type="text/xsl" href="' . $this->xslt . '"?>' . "\n\n" : '';
		$xml .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n\t" . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n\t" . 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"' . "\n\t" . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		if ('yes' != $this->options['enable_xslt'] && 'yes' == $this->options['enable_credit'])
			$xml .= $this->credit();

		if (!$this->check_output($output))
			return false;

		foreach ($output as &$url)
		{
			$url['location'] = (!empty($url['location'])) ? $url['location'] : '';
			$url['lastmod'] = (!empty($url['lastmod'])) ? $url['lastmod'] : '';
			$url['freq'] = (isset($url['freq']) && in_array($url['freq'], $this->frequency)) ? $url['freq'] : $this->options['select_default_freq'];
			$url['priority'] = (isset($url['priority']) && $url['priority'] <= 1 && $url['priority'] > 0) ? $url['priority'] : $this->options['select_default_pri'];
			$xml .= $this->generate_url_item(htmlspecialchars($url['location']), $url['priority'], $url['freq'], $url['lastmod']);
			$this->output_num++;
		}

		$xml .= "\n" . '</urlset>';
		$this->output = $xml;

		return true;
	}

	function generate_sitemap_index($output = array())
	{
		$xml = '<' . '?xml version="1.0" encoding="UTF-8"?'.'>' . "\n";
		$xml .= (!empty($this->xslt_index)) ? '<?xml-stylesheet type="text/xsl" href="' . $this->xslt_index . '"?>' . "\n\n" : '';
		$xml .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n\t" . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n\t" . 'http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"' . "\n\t" . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		if ('yes' != $this->options['enable_xslt'] && 'yes' == $this->options['enable_credit'])
			$xml .= $this->credit();

		if (!$this->check_output($output))
			return false;

		foreach ($output as &$sitemap)
		{
			$sitemap['location'] = (!empty($sitemap['location'])) ? $sitemap['location'] : '';
			$sitemap['lastmod'] = (!empty($sitemap['lastmod'])) ? $sitemap['lastmod'] : '';
			$xml .= $this->generate_sitemap_item(htmlspecialchars($sitemap['location']), $sitemap['lastmod']);
			$this->output_num++;
		}

		$xml .= "\n" . '</sitemapindex>';
		$this->output = $xml;

		return true;
	}
}
?>