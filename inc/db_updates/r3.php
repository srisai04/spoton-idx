<?php
	// Drop wrong index
	$sql = 'ALTER TABLE ' . $wpdb->spoton_sff_values . ' DROP INDEX value';
	$wpdb->query($sql);
	// Add new index
	$sql = 'ALTER TABLE ' . $wpdb->spoton_sff_values . ' ADD UNIQUE `fid_value` (`fid` , `value`)';
	$wpdb->query($sql);
	$sql = 'ALTER TABLE ' . $wpdb->spoton_sff_values . ' ADD INDEX `fid` (`fid`)';
	$wpdb->query($sql);
?>