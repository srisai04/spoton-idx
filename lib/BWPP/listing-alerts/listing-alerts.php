<?php
/*
Author: Khang Minh
Author URI: http://betterwp.net
License: GPLv3
*/

if (class_exists('Listing_Alerts'))
	return;

// Frontend
require_once(dirname(__FILE__) . '/includes/common-functions.php');
require_once(dirname(__FILE__) . '/includes/class-listing-alerts.php');
$listing_alerts = new Listing_Alerts();

// Backend
add_action('admin_menu', 'listing_alert_init_admin', 1);

function listing_alert_init_admin()
{
	global $listing_alerts;
	$listing_alerts->init_admin();
}

?>