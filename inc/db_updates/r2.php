<?php
	$sql = '
		INSERT INTO ' . $wpdb->spoton_sff_fields . ' (static, title) VALUES' . "
			(0, 'State'),
			(0, 'County'),
			(0, 'City'),
			(0, 'Area'),
			(0, 'SubDivision'),
			(0, 'Property Type'),
			(0, 'School District'),
			(0, 'High School'),
			(0, 'Middle School'),
			(0, 'Elementary School'),
			(1, 'Price From'),
			(1, 'Price To'),
			(1, 'Beds'),
			(1, 'Baths'),
			(1, 'Square Feet'),
			(1, 'Acreage From'),
			(1, 'Acreage To'),
			(1, 'Time on Market')
	";

	$wpdb->query($sql);

?>