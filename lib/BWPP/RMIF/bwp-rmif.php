<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
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

if (class_exists('BWP_Init')) :

require_once(dirname(__FILE__) . '/inc/class-bwp-rmif' . $plugin_class . '.php');

$bwp_plugin_data = array(
	'title' 	=> 'BWP Request More Info Form',
	'req_wp'	=> '3.0',
	'version'	=> '1.0.0'
);

$bwp_plugin_wp_data = array(
	'key'		=> 'bwp_rmif',
	'domain'	=> 'bwp-rmif',
	'file'		=> __FILE__,
	'cap'		=> 'manage_options'
);

$bwp_rmif = new BWP_RMIF($bwp_plugin_data, $bwp_plugin_wp_data);

endif;

?>