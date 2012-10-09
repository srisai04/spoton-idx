<?php
/*
Author: Khang Minh
Author URI: http://betterwp.net
License: GPLv3
*/

if (class_exists('BWP_SIMPLE_GXS'))
	return;

// Pre-emptive
$bwp_gxs_ob_level = @ob_get_level();
$bwp_gxs_ob_start = ob_start();

// Frontend
require_once(dirname(__FILE__) . '/includes/class-bwp-simple-gxs.php');
$bwp_gxs_gzipped = (!BWP_SIMPLE_GXS::is_gzipped()) ? false : true;
$bwp_gxs = new BWP_SIMPLE_GXS();

// Backend
add_action('admin_menu', 'bwp_gxs_init_admin', 1);

function bwp_gxs_init_admin()
{
	global $bwp_gxs;
	$bwp_gxs->init_admin();
}
?>