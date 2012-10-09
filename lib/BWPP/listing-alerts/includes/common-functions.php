<?php
/* Global Listing Alerts functions */
if (!function_exists('list_alert_notify')) :

function list_alert_convert_query($query, $type = 'criteria')
{
	$conversion_ary = array();

	switch ($type)
	{
		case 'criteria':

			$conversion_ary = array(
				"State eq '([a-z]+)'" => 'State is $1',
				"City eq '(.*?)'"	=> 'City is $1',
				'Price ge ([0-9]+)' => 'Price From $1',
				'Price le ([0-9]+)' => 'Price To $1',
				'Beds ge ([0-9]+)'	=> 'At least $1 Beds',
				'Baths ge ([0-9]+)' => 'At least $1 Bathrooms',
				'SquFeet ge ([0-9]+)' => 'At least $1 Square Feet',
				'Acre ge ([0-9]*\.?[0-9]+)' => 'Lot Size From $1 Acres',
				'Acre ge ([0-9]*\.?[0-9]+)' => 'Lot Size To $1 Acres',
				'PropView gt 0' => 'With a View',
				'WatFront gt 0' => 'Waterfront',
				"PropType eq '([\/a-z0-9_-\s]+)'" => '$1',
				"Status eq '([a-z0-9_-\s]+)'" => '$1'
			);

		break;

		case 'email':

			$conversion_ary = array(
				"State eq '([a-z]+)'" => 'located in $1 State',
				"City eq '(.*?)'"	=> 'in $1 City',
				'Price ge ([0-9]+)' => 'priced at $1 or more',
				'Price le ([0-9]+)' => 'priced at $1 or less',
				'Beds ge ([0-9]+)'	=> 'with at least $1 bedrooms',
				'Baths ge ([0-9]+)' => 'with at least $1 bathrooms',
				'SquFeet ge ([0-9]+)' => 'at least $1 square feet large',
				'Acre ge ([0-9]*\.?[0-9]+)' => 'with lot size of $1 acres or more',
				'Acre ge ([0-9]*\.?[0-9]+)' => 'with lot size of $1 acres or less',
				'PropView gt 0' => 'With a View',
				'WatFront gt 0' => 'Waterfront',
				"PropType eq '([\/a-z0-9_-\s]+)'" => '$1',
				"Status eq '([a-z0-9_-\s]+)'" => 'is $1'
			);

			$conversion_any_ary = array(
				"State eq '([a-z]+)'" => 'in all states',
				"City eq '(.*?)'"	=> 'in all cities',
				'Price ge ([0-9]+)' => 'with no minimum price',
				'Price le ([0-9]+)' => 'with no maximum price',
				'Beds ge ([0-9]+)'	=> 'with any number of bedrooms',
				'Baths ge ([0-9]+)' => 'with any number of bathrooms'
			);

			$conversion_needed = array();
			foreach ($conversion_ary as $key => $value)
				$conversion_needed[$key] = 0;

		break;
	}

	// Do the conversion, should be improved in next version to catch all proptypes and propstatuses
	$new_query = array();
	if ('criteria' == $type)
	{
		foreach ($conversion_ary as $search => $replace)
			if (preg_match('/' . $search . '/uis', $query, $match))
				$new_query[] = ucfirst(str_replace('$1', $match[1], $replace));
	}
	else
	{
		foreach ($conversion_needed as $search => &$needed)
			if (preg_match('/' . $search . '/uis', $query, $match))
				$needed = 1;

		if (1 == $conversion_needed['Price ge ([0-9]+)'] && 1 == $conversion_needed['Price le ([0-9]+)'])
		{
			$conversion_ary['Price ge ([0-9]+)'] = 'priced from $1';
			$conversion_ary['Price le ([0-9]+)'] = 'to $1';
		}

		foreach ($conversion_ary as $search => $replace)
		{
			if (preg_match('/' . $search . '/uis', $query, $match))
				$new_query[] = str_replace('$1', $match[1], $replace);
			else if (!empty($conversion_any_ary[$search]))
				$new_query[] = str_replace('$1', $match[1], $conversion_any_ary[$search]);
		}
		
	}

	return ('email' == $type) ? str_replace(', to', ' to', implode(', ', $new_query)) : implode(', ', $new_query);

}

function list_alert_notify()
{
	global $wpdb, $listing_alerts;

	// Fetch all listings
	$listings = $wpdb->get_results($wpdb->prepare('
		SELECT u.user_email, u.display_name, um.meta_value as listing_data
			FROM ' . $wpdb->usermeta . ' um
				INNER JOIN ' . $wpdb->users . ' u
					ON u.ID = um.user_id' . "
				WHERE um.meta_key = 'listings'" . '
					AND u.user_status = 0
			ORDER BY um.umeta_id DESC
	'));

	// Load the Odata PHP SDK
	require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
	$proxy = new XpioMapRealEstateEntities();

	// Loop through listings, but only process ones that are enabled
	foreach ($listings as $listing)
	{
		$listing_data = maybe_unserialize($listing->listing_data);
		if (!$listing_data['options']['enable'])
			continue;
		$listing_name = $listing_data['name'];
		$filter = $listing_data['query'];
		$savedQuery = explode('^', $filter);
		$filter = $savedQuery[0];
		$friendly_filter = list_alert_convert_query($filter, 'email');
		// Back-compat compatible
		$filter = str_replace('&$orderby=', ' and (CreDate ge DateTime' . "'" . str_replace(',', 'T', date('Y-m-d,H:i:s', strtotime('-1 day', strtotime(date('Y-m-d,H:i:s'))))) . "'" . ')&$orderby=', $filter);
		$filter = trim(str_replace('PKey eq  and and', 'PKey eq 1 and', $filter));
		// Get all available properties
		$response = $proxy->GenericRetsSearchListings()->skip(0)->Top((int) $savedQuery[1])->filter($filter)->Select('LN,HouNo,Stre,StrSuff,DirSuff,City,State,Zip')->IncludeTotalCount()->Execute();
		// Only email user if there's something new
		if (isset($response->Result) && 0 < sizeof($response->Result))
		{
			$message = '';
			$message .= sprintf(__('Dear %s,<br /><br />', 'list-alerts'), $listing->display_name) . "\r\n";
			$message .= sprintf(__('We\'ve located some properties that have just been listed for sale or that now meets your criteria. These properties fit the specific criteria that you provided when you signed up for <strong>%s</strong> property alert.<br /><br />', 'list-alerts'), $listing_name) . "\r\n";
			$message .= sprintf(__('Alert criteria: Properties %s.<br /><br />', 'list-alerts'), $friendly_filter) . "\r\n";
			foreach ($response->Result as $property)
			{
				$property_addr = spoton_get_ppt_addr($property);
				$message .= '<a href="' . esc_url(spoton_get_ppt_permalink($property)) . '">' . ucfirst($property->LN . ' ' . $property_addr) . '</a><br />' . "\r\n"; 
			}
			// Build headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			// Additional headers
			$headers .= sprintf('To: %s <%s>' . "\r\n", $listing->display_name, $listing->user_email);
			// Get the site domain and get rid of www.
			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) == 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}
			$headers .= sprintf('From: %s <%s>' . "\r\n", get_bloginfo('name'), 'no-reply@' . $sitename);
			wp_mail($listing->user_email, sprintf(__('Listing Alert for %s', 'list-alerts'), date('Y-m-d')), $message, $headers);
		}
	}
}

endif;

if (isset($_GET['la']))
	add_action('init', 'list_alert_notify');

?>