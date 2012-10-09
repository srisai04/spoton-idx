<?php
	$this->add_option(
		new BWP_Options(SPOTON_IDX_GENERAL, array(
				// Widget Settings
				'enable_widget_qsw'		=> 'yes',
				'select_qsw_landing'	=> 0,
				'select_qsw_type'		=> 'def',
				'enable_widget_flw'		=> 'yes',
				// Misc Settings
				'input_guest_views'		 => 0,
				'input_guest_prmt_views' => 0,
				// End of Widget Settings
				'enable_update_notify'	=> 'yes',
				'enable_xml_sitemap'	=> 'yes',
				'enable_listing_alert'	=> 'yes',
				'select_la_wizard_page' => 0,
				'enable_module_aurl'	=> 'yes',
				'enable_module_rmif'	=> 'yes',
				// SEO options
				'enable_seo_feature'	=> '',
				'enable_seo_blogname'	=> 'yes',
				'enable_seo_meta_robot'	=> 'yes',
				'enable_seo_meta_og'	=> 'yes',
				'enable_seo_canonical'	=> 'yes',
				'input_seo_google_verify' => '',
				'input_seo_fb_appid'	=> '',
				'input_seo_default_thumb' => '',
				// End of SEO options
				'update_notify_email'	=> '',
				'provider_key'			=> 1
			), 'db', 
				array(
					'provider_key' => 'int', 
					'select_qsw_landing' => 'int', 
					'input_guest_views' => 'int',
					'input_guest_prmt_views' => 'int'
				)
			)
	);

	$this->add_option(
		new BWP_Options(SPOTON_IDX_FORM_ADM, array(
				'search_adm_string'	=> '',
				'enable_sff'		=> ''
			), 'db')
	);

	$this->add_option(
		new BWP_Options(SPOTON_IDX_PSTATUS, array(
				'property_status'	=> array(
					'A'		=> 'Active',
					'CA' 	=> 'Cancelled',
					'CT' 	=> 'Contingent',
					'E' 	=> 'Expired',
					'P' 	=> 'Pending',
					'PB' 	=> 'Pending BU Requested',
					'PF' 	=> 'Pending Feasibility',
					'PI' 	=> 'Pending Inspection',
					'R' 	=> 'Rented',
					'SFR' 	=> 'Sale Fail Release',
					'S' 	=> 'Sold',
					'T' 	=> 'Temporarily Off Market'
				)
			), 'misc')
	);

	$this->add_option(
		new BWP_Options(SPOTON_IDX_LIST_ADM, array(
				'hide_list_search_office'	=> '',
				'hide_list_search_agent'	=> '',
				'hide_list_details_office'	=> '',
				'hide_list_details_agent'	=> '',
                'input_company_name'        => ''
			), 'db', 
				array(
					'input_company_name' => 'varchar' ))
	);

?>