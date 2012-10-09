<?php
/*
Plugin Name: Spot-on! Connect IDX
Plugin URI: http://spotonconnect.com
Description: SEO friendly IDX for your WordPress site.
Version: 1.1.06212012
Author: Spot-on! Connect
Author URI: http://spotonconnect.com
License: GPLv3
*/

/**
 * Copyright (c) 2012 Spot-on Connect, (c) 2012 Khang Minh <betterwp.net>
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


/* Don't load this plugin if not from WordPress */
if (!defined('ABSPATH'))
	return;

$plugin_class = (is_admin()) ? '-admin' : '';
require_once(dirname(__FILE__) . '/lib/BWP/Init.php');

if (class_exists('BWP_Init')) :

require_once(dirname(__FILE__) . '/inc/class-spoton-idx' . $plugin_class . '.php');

$plugin_data = array(
	'title' 	=> 'Spot-on! Connect IDX',
	'req_wp'	=> '3.0',
	'version'	=> '1.0.06192012',
	'db'		=> '3'
);

$plugin_wp_data = array(
	'key'		=> 'spoton_idx',
	'domain'	=> 'spoton-idx',
	'file'		=> __FILE__,
	'cap'		=> 'manage_options'
);

$spoton_idx = new Spoton_IDX($plugin_data, $plugin_wp_data);

/* ----- Load other modules & libs ----- */

// Load a trimmed down version of BWP XML Sitemap plugin
if ('yes' == $spoton_idx->options['enable_xml_sitemap'])
{
	require_once(SPOTON_IDX_LIB_PATH . '/BWPP/google-xml-sitemaps/bwp-simple-gxs.php');
	add_action('bwp_gxs_modules_built', 'bwp_gxs_add_modules');
	function bwp_gxs_add_modules()
	{
		global $bwp_gxs;
		$bwp_gxs->add_module('property');
	}
}

// Load Listing Alerts Plugin and register a few crons
if ('yes' == $spoton_idx->options['enable_listing_alert'])
{
	require_once(SPOTON_IDX_LIB_PATH . '/BWPP/listing-alerts/listing-alerts.php');
	register_activation_hook(__FILE__, array($listing_alerts, 'install'));
	register_deactivation_hook(__FILE__, array($listing_alerts, 'uninstall'));
}

// Load AURL plugin
if ('yes' == $spoton_idx->options['enable_module_aurl'])
{
	require_once(SPOTON_IDX_LIB_PATH . '/BWPP/AURL/bwp-aurl.php');
}

// Load RMIF plugin
if ('yes' == $spoton_idx->options['enable_module_rmif'])
{
	require_once(SPOTON_IDX_LIB_PATH . '/BWPP/RMIF/bwp-rmif.php');
}

endif;

?>