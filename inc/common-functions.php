<?php
if (!function_exists('spoton_get_adm_settings')) :

function spoton_get_adm_settings()
{
	global $spoton_idx;
	
	$sv = explode('^', $spoton_idx->options['search_adm_string']);

	for ($i = 0; $i <= 17; $i++)
	{
		if (!isset($sv[$i]))
		{
			if ($i == 3)
				$sv[$i] = 20;
			else if ($i == 2)
				$sv[$i] = '0,0';
			else
				$sv[$i] = '0';
		}
	}

	return $sv;

}

function spoton_get_default_location($sv)
{
	return (is_array($sv) && 3 < sizeof($sv)) ? explode(',' , $sv[2]) : array(0, 0);
}

function spoton_get_sff_filters()
{
	global $spoton_idx;
	$sff_fields = $spoton_idx->get_sff_fields();
	$return = array();
	foreach ($sff_fields as $title => $fd)
		$return[$title] = (int) $fd->filter;
	return $return;
}

function spoton_get_sff_default()
{
	global $spoton_idx;
	$sff_fields = $spoton_idx->get_sff_fields();
	$return = array();
	foreach ($sff_fields as $title => $fd)
		$return[$title] = str_replace(' ', '_', $fd->def_value);
	return $return;
}

function spoton_get_ppt_addr($property)
{
	$addr = '';

	if (!empty($property->HouNo))
		$addr .= $property->HouNo . ' ';
    if (!empty($property->DirPre))
		$addr .= $property->DirPre . ' ';
	if (!empty($property->Stre))
		$addr .= $property->Stre . ' ';
	if (!empty($property->StrSuff))
		$addr .= $property->StrSuff . ' ';
	if (!empty($property->DirSuff))
		$addr .= $property->DirSuff . ' ';
	if (!empty($property->City))
		$addr .= $property->City . ', ';
	if (!empty($property->State))
		$addr .= $property->State . ' ';
	if (!empty($property->Zip))
		$addr .= preg_replace('/[^a-z0-9]/i', '', $property->Zip);

	return $addr;
}

function spoton_get_ppt_permalink($property)
{
	$addr = spoton_get_ppt_addr($property);
	$addr = strtolower($addr);
	$addr = preg_replace('/[\s]+/ui', '-', $addr);
	$addr = preg_replace('/[^a-z0-9-]/ui', '', $addr);
	$addr = preg_replace('/[\-]+/ui', '-', $addr);
	$pkey = (empty($property->PKey)) ? 1 : $property->PKey;
	return user_trailingslashit(home_url() . '/property/mls-' . strtolower($property->LN) . '-' . $addr);
}

function spoton_get_property_status($get = false)
{
	global $spoton_idx, $spoton_pkey;

	static $spoton_pstatus;

	$return = '';
	
	if (isset($spoton_pstatus) && is_array($spoton_pstatus) && 0 < sizeof($spoton_pstatus))
	{}
	else
	{
		$query = 'PKey eq ' . $spoton_pkey;
		require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
		$status_ary = $spoton_idx->options['property_status'];

		if (class_exists('XpioMapRealEstateEntities'))
		{
			$proxy = new XpioMapRealEstateEntities(); // This class contain the service url.
			$response = $proxy->RetsListingsStatus()->filter($query)->Select('Status')->IncludeTotalCount()->Execute();
			$count = $response->TotalCount();
			if (!empty($count))
			{
				foreach ($response->Result as $prop_status)
				{
					if (empty($prop_status->Status))
						continue;
					$prop_name = !empty($status_ary[$prop_status->Status]) ? $status_ary[$prop_status->Status] : $prop_status->Status;
					$prop_attr = $prop_status->Status;
					$prop_value = preg_replace('/[^a-z0-9]/ui', '_', strtolower($prop_attr));
					$spoton_pstatus['spto_' . $prop_value] = $prop_attr;
					unset($prop_name);
					unset($prop_value);
					unset($prop_attr);
				}
			}
		}
	}

	if (isset($spoton_pstatus) && is_array($spoton_pstatus) && 0 < sizeof($spoton_pstatus))
	{
		foreach ($spoton_pstatus as $prop_value => $prop_attr)
		{
			$prop_name = !empty($status_ary[$prop_attr]) ? $status_ary[$prop_attr] : $prop_attr;
			$checked = (!empty($_POST[$prop_value])) ? ' checked="checked" ' : '';
			$prop_value = esc_attr($prop_value);
			$return .= '<input type="checkbox" id="' . $prop_value . '" name="' . $prop_value . '" value="' . esc_attr($prop_attr) . '"' . $checked . '> ' . esc_html($prop_name) . '</input><br />' . "\n";
		}
	}
	else
		$return = __('No property status found.', $spoton_idx->domain);

	if ($get)
		return $spoton_pstatus;

	return $return;
}

function spoton_get_provider_types($get = false, $alt_display = false)
{
	global $spoton_idx, $spoton_pkey;

	static $spoton_ptypes;

	$return = '';

	if (!is_admin())
		$sff_filters = spoton_get_sff_filters();

	if (isset($spoton_ptypes) && is_array($spoton_ptypes) && 0 < sizeof($spoton_ptypes))
	{}
	else if (!is_admin() && 'yes' == $spoton_idx->options['enable_sff'] && 1 == $sff_filters['Property Type'])
	{
		$sff_proptypes = $spoton_idx->get_sff_values_by_title('Property Type');
		foreach ($sff_proptypes as $prop_type)
		{
			$prop_name = str_replace('_', ' ', $prop_type);
			$prop_value = preg_replace('/[\s]+/', '_', $prop_name);
			$spoton_ptypes['spto_' . $prop_value] = $prop_name;
			unset($prop_name);
			unset($prop_value);
		}
	}
	else
	{
		$query = 'PKey eq ' . $spoton_pkey;
		require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
		if (class_exists('XpioMapRealEstateEntities'))
		{
			$proxy = new XpioMapRealEstateEntities(); // This class contain the service url.
			$response = $proxy->RetsProperties()->filter($query)->Select('PropType,PropName')->IncludeTotalCount()->Execute();
			$count = (int) $response->TotalCount();

			if (!empty($count))
			{
				foreach ($response->Result as $prop_type)
				{
					$prop_name = $prop_type->PropName;
					$prop_value = preg_replace('/[^a-z0-9]/ui', '_', $prop_name);
					$spoton_ptypes['spto_' . $prop_value] = $prop_name;
					unset($prop_name);
					unset($prop_value);
				}
			}
		}
	}

	$item_op_tag = ($alt_display) ? '' : '<tr><td>';
	$item_cl_tag = ($alt_display) ? '<br />' : '</td></tr>';

	if (isset($spoton_ptypes) && is_array($spoton_ptypes) && 0 < sizeof($spoton_ptypes))
	{
		foreach ($spoton_ptypes as $prop_value => $prop_name)
		{
			$checked = (!empty($_POST[$prop_value])) ? ' checked="checked" ' : '';
			$prop_value = esc_attr($prop_value);
			$item_html = ($alt_display) ? '<option value="' . esc_attr($prop_name) . '"> ' . esc_html($prop_name) . '</option>' : '<input type="checkbox" id="' . $prop_value . '" name="' . $prop_value . '" value="' . esc_attr($prop_name) . '"' . $checked . '> ' . esc_html($prop_name) . '</input>';
			$return .= $item_op_tag . $item_html . $item_cl_tag . "\n";
		}
	}
	else
		$return = $item_op_tag . __('No property types found.', $spoton_idx->domain) . $item_cl_tag;

	if ($get)
		return $spoton_ptypes;

	return $return;

}

function spoton_get_qsw_odata_query($pkey)
{
	return spoton_get_odata_query($pkey, 'qsw_');
}

function spoton_get_odata_query($pkey, $post_prefix = '')
{
	global $spoton_idx;

    $state = '';
    if (!empty($_POST[$post_prefix . "spto_state"])) {
        $state = " and State eq '" . trim($_POST[$post_prefix . "spto_state"]) . "'";
    }

    $pricefrom = '';
	if (!empty($_POST[$post_prefix . "spto_pricefrom"])) {
	   if($_POST[$post_prefix . "spto_pricefrom"]!="0")
			$pricefrom=" and Price ge " . trim($_POST[$post_prefix . "spto_pricefrom"]);
	}

    $priceto = '';
	if (!empty($_POST[$post_prefix . "spto_priceto"])) {
	   if($_POST[$post_prefix . "spto_priceto"]!="99999999")
			$priceto = " and Price le " . trim($_POST[$post_prefix . "spto_priceto"]);
	}

    $bedrooms = '';
	if (!empty($_POST[$post_prefix . "spto_bedrooms"])) {
	   if($_POST[$post_prefix . "spto_bedrooms"]!="0")
			$bedrooms=" and Beds ge " . (int) $_POST[$post_prefix . "spto_bedrooms"];
	}

    $bathrooms = '';
	if (!empty($_POST[$post_prefix . "spto_bathrooms"])) {
	   if($_POST[$post_prefix . "spto_bathrooms"]!="0")
			$bathrooms =" and Baths ge " . (int) $_POST[$post_prefix . "spto_bathrooms"];
	}

    $squarefeet ="";
	if (!empty($_POST[$post_prefix . "spto_squarefeet"])) {
	   if($_POST[$post_prefix . "spto_squarefeet"]!="0")
		   $squarefeet =" and SquFeet ge " . trim($_POST[$post_prefix . "spto_squarefeet"]);
	}

    $acreagefrom ="";
	if (!empty($_POST[$post_prefix . "spto_acreagefrom"])) {
	   if($_POST[$post_prefix . "spto_acreagefrom"]!="0")
		   $acreagefrom =" and Acre ge " . trim($_POST[$post_prefix . "spto_acreagefrom"]);
	}

    $acreageto ="";
	if (!empty($_POST[$post_prefix . "spto_acreageto"])) {
	   if($_POST[$post_prefix . "spto_acreageto"]!="999999") 
			$acreageto =" and Acre le " . trim($_POST[$post_prefix . "spto_acreageto"]);
	}

    $countyQuery='';
    if(!empty($_POST[$post_prefix . "spto_county"]))
    {
        if($_POST[$post_prefix . "spto_county"]!="0")
        {
            $countyQuery .=" and County eq '" . trim(str_replace('_',' ', $_POST[$post_prefix . "spto_county"])) . "'";
        }
    }

    $cityQuery='';
    if(!empty($_POST[$post_prefix . "spto_city"]))
    {
        if($_POST[$post_prefix . "spto_city"]!="0")
        {
            $cityQuery .=" and City eq '" . trim(str_replace('_',' ',$_POST[$post_prefix . "spto_city"])) . "'";
        }
    }
   
    $areaQuery='';
    if(!empty($_POST[$post_prefix . "spto_area"]))
    {
        if($_POST[$post_prefix . "spto_area"]!="0")
        {
            $areaQuery .=" and Area eq '" . trim(str_replace('_',' ',$_POST[$post_prefix . "spto_area"])) . "'";
        }
    }
    
    $subdivQuery='';
    if(!empty($_POST[$post_prefix . "spto_subdiv"]))
    {
        if($_POST[$post_prefix . "spto_subdiv"]!="0")
        {
            $subdivQuery .=" and substringof('" . trim($_POST[$post_prefix . "spto_subdiv"]) . "',SubDiv) ";
        }
    }
    
    $schooldistrictQuery='';
    if(!empty($_POST[$post_prefix . "spto_schooldistrict"]))
    {
        if($_POST[$post_prefix . "spto_schooldistrict"]!="0")
        {
            $schooldistrictQuery .=" and SchDist eq '" . trim(str_replace('_',' ',$_POST[$post_prefix . "spto_schooldistrict"])) . "'";
        }
    }
    
    $elementaryschoolQuery='';
    if(!empty($_POST[$post_prefix . "spto_elementaryschool"]))
    {
        if($_POST[$post_prefix . "spto_elementaryschool"]!="0")
        {
            $elementaryschoolQuery .=" and EleSch eq '" . trim(str_replace('_',' ',$_POST[$post_prefix . "spto_elementaryschool"])) . "'";
        }
    }
    
    $middleschoolQuery='';
    if(!empty($_POST[$post_prefix . "spto_middleschool"]))
    {
        if($_POST[$post_prefix . "spto_middleschool"]!="0")
        {
            $middleschoolQuery .=" and JuHiSch eq '" . trim(str_replace('_',' ',$_POST[$post_prefix . "spto_middleschool"])) . "'";
        }
    }
    
    $highschoolQuery='';
    if(!empty($_POST[$post_prefix . "spto_highschool"]))
    {
        if($_POST[$post_prefix . "spto_highschool"]!="0")
        {
            $highschoolQuery .=" and HiSch eq '" . trim(str_replace('_',' ',$_POST[$post_prefix . "spto_highschool"])) . "'";
        }
    }

	$spoton_ptypes = spoton_get_provider_types(true);
   	$propertyQuery = '';
	if (!empty($_POST[$post_prefix . 'spto_ptype']) && 'all' != $_POST[$post_prefix . 'spto_ptype'] && in_array($_POST[$post_prefix . 'spto_ptype'], $spoton_ptypes))
	{
		$propertyQuery .= " and (PropType eq '" . trim($_POST[$post_prefix . 'spto_ptype']) . "')";
	}
	else if (isset($_POST['spto_PropertyType']) && is_array($_POST['spto_PropertyType']))
	{
		foreach ($_POST['spto_PropertyType'] as $ptype)
		{
			$ptype = str_replace('_', ' ', $ptype);
			if (in_array($ptype, $spoton_ptypes))
			{
				$propertyQuery .= " or PropType eq '" . $ptype . "'";
			}
		}
    	if(strlen($propertyQuery) > 0)
        	$propertyQuery = ' and (' . substr($propertyQuery, 3, strlen($propertyQuery)) . ')';
	    else
    	    $propertyQuery = '';
	}
	else
	{
		foreach ($spoton_ptypes as $ptype_key => $ptype)
		{
	    	if (!empty($_POST[$ptype_key]))
	        	$propertyQuery .= " or PropType eq '" . $ptype . "'";
		}
    	if(strlen($propertyQuery) > 0)
        	$propertyQuery = ' and (' . substr($propertyQuery, 3, strlen($propertyQuery)) . ')';
	    else
    	    $propertyQuery = '';
	}

    $YearBuiltQuery='';
    if (!empty($_POST[$post_prefix . "spto_yearbuilt"])) {
    if($_POST[$post_prefix . "spto_yearbuilt"]==0)
        $YearBuiltQuery = " and YrBuilt ge 0";        
    else if($_POST[$post_prefix . "spto_yearbuilt"]==111)
        $YearBuiltQuery = " and YrBuilt eq ".date(Y);        
    else
        $YearBuiltQuery = " and YrBuilt ge ".(date(Y)-intval($_POST[$post_prefix . "spto_yearbuilt"]));
    }

    $zipcodeQuery="";
    if (!empty($_POST[$post_prefix . "spto_zipcode"])) {
        if($_POST[$post_prefix . "spto_zipcode"]!="0")
        {
            $zipcodeQuery=" and substringof('" . trim($_POST[$post_prefix . "spto_zipcode"]) . "',Zip) ";
        }
    }

    $viewpropertyQuery="";
     if (!empty($_POST[$post_prefix . "spto_viewproperty"])) {
        $viewpropertyQuery=" and PropView gt 0 ";
     }

    $waterfrontQuery="";
     if (!empty($_POST[$post_prefix . "spto_waterfront"])) {
        $waterfrontQuery=" and WatFront gt 0 ";
     }

	$spoton_pstatus = spoton_get_property_status(true);
    $statusQuery = '';  
	foreach ($spoton_pstatus as $post_key => $status)
	{
	    if (!empty($_POST[$post_key]))
	        $statusQuery .= " or Status eq '" . $status . "'";
	}
    if (strlen($statusQuery)>0)
        $statusQuery=" and (".substr($statusQuery, 3, strlen($statusQuery)).")";
    else
        $statusQuery="";

    $marketQuery='';
    if (!empty($_POST[$post_prefix . "spto_market"])) {
	date_default_timezone_set('America/Los_Angeles');
    if($_POST[$post_prefix . "spto_market"]==1)        
        $marketQuery = ' and (CreDate ge DateTime'."'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-1 day',strtotime(date('Y-m-d,H:i:s')))))."'".')';
    else if($_POST[$post_prefix . "spto_market"]==3)
        $marketQuery = ' and (CreDate ge DateTime'."'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-3 day',strtotime(date('Y-m-d,H:i:s')))))."'".')';
    else if($_POST[$post_prefix . "spto_market"]==7)
        $marketQuery = ' and (CreDate ge DateTime'."'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-7 day',strtotime(date('Y-m-d,H:i:s')))))."'".')';
    else if($_POST[$post_prefix . "spto_market"]==14)
        $marketQuery = ' and (CreDate ge DateTime'."'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-14 day',strtotime(date('Y-m-d,H:i:s')))))."'".')';
    else
        $marketQuery = '';
    }

    $officeidQuery="";
    if (!empty($_POST[$post_prefix . "spto_officeid"])) {
            $officeidQuery=" and OffID eq '" . trim($_POST[$post_prefix . "spto_officeid"])."'";
    }

    $agentidQuery="";
    if (!empty($_POST[$post_prefix . "spto_agentid"])) {
            $agentidQuery=" and AgID eq '" . trim($_POST[$post_prefix . "spto_agentid"])."'";
    }

    $schooldistrictQuery="";
    if (!empty($_POST[$post_prefix . "spto_schooldistrict"])) {
            $schooldistrictQuery=" and SchDist eq '" . trim($_POST[$post_prefix . "spto_schooldistrict"]) . "'";
    }

    $sortpriceQuery="";
    if (!empty($_POST[$post_prefix . "spto_pricesort"])) {
    if($_POST[$post_prefix . "spto_pricesort"]==0)        
        $sortpriceQuery = "&$"."orderby=Price desc";
    else if($_POST[$post_prefix . "spto_pricesort"]==1)
        $sortpriceQuery = "&$"."orderby=Price";
    else if($_POST[$post_prefix . "spto_pricesort"]==2)        
        $sortpriceQuery = "&$"."orderby=CreDate desc";
    else if($_POST[$post_prefix . "spto_pricesort"]==3)
        $sortpriceQuery = "&$"."orderby=CreDate";
    else if($_POST[$post_prefix . "spto_pricesort"]==4)
        $sortpriceQuery = "&$"."orderby=Acre desc";   
    else
        $sortpriceQuery = "&$"."orderby=Acre";
    } else 
    {
        $sortpriceQuery = "&$"."orderby=Price desc";
    }

    $displayRecord="";
    if (!empty($_POST[$post_prefix . "spto_records"]))
            $displayRecord="^" . (int) $_POST[$post_prefix . "spto_records"];
    else
        $displayRecord="^1000";

	$PKey_query = 'PKey eq 1 and';
	$Provider_Key = $pkey;
	if (!empty($Provider_Key))
		$PKey_query = 'PKey eq ' . $Provider_Key;

	$query='';
	$query = $PKey_query.$state.$countyQuery.$cityQuery.$pricefrom.$priceto.$bathrooms.$bedrooms.$squarefeet.$acreagefrom.$acreageto.$YearBuiltQuery.$zipcodeQuery.$viewpropertyQuery.$waterfrontQuery.$propertyQuery.$statusQuery.$marketQuery.$officeidQuery.$areaQuery.$subdivQuery.$schooldistrictQuery.$elementaryschoolQuery.$middleschoolQuery.$highschoolQuery.$agentidQuery.$sortpriceQuery.$displayRecord;

	return trim($query);
}

add_action('init', 'spoton_register_sidebar');
function spoton_register_sidebar()
{
	 register_sidebar(array('name' => 'Property Search Page', 'id' => 'spoton_property', 'description' => "Sidebar used to display property details", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h3>','after_title' => '</h3>'));
	 register_sidebar(array('name' => 'Property Details Page', 'id' => 'spoton_property_details', 'description' => "Sidebar for the property details page", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h3>','after_title' => '</h3>')); 
}

add_filter('rewrite_rules_array', 'spoton_add_rule');
function spoton_add_rule($rules)
{
	// Insert rewrite rules
	$rule = array();
	/*$rule['property/mls-([0-9]{1,3})-([0-9]+)-([a-z0-9-]+)$'] = 'index.php?spoton_page=$matches[2]&spoton_ppkey=$matches[1]';*/
	$rule['property/mls-([a-z0-9]+)-([a-z0-9-]+)$'] = 'index.php?spoton_page=$matches[1]&spoton_title=$matches[2]';
	return $rule + $rules;
}

// Get provider key to use whenever we want
add_action('init', 'spoton_get_provider_key');
function spoton_get_provider_key()
{
	global $spoton_idx, $spoton_pkey;
	$spoton_pkey = $spoton_idx->options['provider_key'];
}

// Helper functions
function spoton_get_states()
{
	global $spoton_idx;

	$return = '<label for="spto_state" class="lbl_a">' . __('State', $spoton_idx->domain) . '</label>' . "\n"; 
	$return .= '<select name="spto_state" id="spto_state" class="medium">' . "\n";
	$return .= '<option value="0">' . __('All', $spoton_idx->domain) . '</option>' . "\n";
	// The main plugin will hook to this filter to filter values, this hook should provide:
	// 1. Title of the field
	// 2. Type of HTML markup
	$return .= apply_filters('spoton_idx_sff_get_html', '', 'State', 'select');

	$return .= '</select>' . "\n";
	return $return;
}

function spoton_get_counties()
{
	global $spoton_idx;

	$return = '<label for="spto_county" class="lbl_a">' . __('County', $spoton_idx->domain) . '</label>' . "\n"; 
	$return .= '<select name="spto_county" id="spto_county" class="medium">' . "\n";
	$return .= '<option value="0">' . __('All', $spoton_idx->domain) . '</option>' . "\n";
	$return .= apply_filters('spoton_idx_sff_get_html', '', 'County', 'select');

	$return .= '</select>' . "\n";
	return $return;
}

function spoton_get_cities()
{
	global $spoton_idx;

	$return = '<label for="spto_city" class="lbl_a">' . __('City', $spoton_idx->domain) . '</label>' . "\n"; 
	$return .= '<select name="spto_city" id="spto_city" class="medium">' . "\n";
	$return .= '<option value="0">' . __('All', $spoton_idx->domain) . '</option>' . "\n";
	$return .= apply_filters('spoton_idx_sff_get_html', '', 'City', 'select');

	$return .= '</select>' . "\n";
	return $return;
}

function spoton_get_qsw_cities()
{
	$return = apply_filters('spoton_idx_sff_get_html', '', 'City', 'select');

	return $return;
}

function spoton_get_subdivisions()
{
	global $spoton_idx;

	$return = '<label for="spto_subdiv" class="lbl_a">' . __('Sub-Division', $spoton_idx->domain) . '</label>' . "\n"; 
	$return .= '<select name="spto_subdiv" id="spto_subdiv" class="medium">' . "\n";
	$return .= '<option value="">' . __('All', $spoton_idx->domain) . '</option>' . "\n";
	$return .= apply_filters('spoton_idx_sff_get_html', '', 'SubDivision', 'select');

	$return .= '</select>' . "\n";
	return $return;
}

function spoton_get_areas()
{
	global $spoton_idx;

	$return = '<label for="spto_area" class="lbl_a">' . __('Area', $spoton_idx->domain) . '</label>' . "\n"; 
	$return .= '<select name="spto_area" id="spto_area" class="medium">' . "\n";
	$return .= '<option value="0">' . __('All', $spoton_idx->domain) . '</option>' . "\n";
	$return .= apply_filters('spoton_idx_sff_get_html', '', 'Area', 'select');

	$return .= '</select>' . "\n";
	return $return;
}

function spoton_get_proptype()
{
	global $spoton_idx;

	$return = '<label for="spto_PropertyType" class="lbl_a">' . __('Property Type', $spoton_idx->domain) . '</label>' . "\n"; 
	$return .= '<select name="spto_PropertyType[]" id="spto_PropertyType" multiple="multiple" class="large multiple" size="6">' . "\n";
	$return .= apply_filters('spoton_idx_sff_get_html', '', 'Property Type', 'select');

	$return .= '</select>' . "\n";
	$return .= '<span class="f_help">' . __('ctrl+click to select multiple items.', $spoton_idx->domain) . '</span>';

	return $return;
}

function spoton_get_qsw_proptype()
{
	$return = apply_filters('spoton_idx_sff_get_html', '', 'Property Type', 'select');

	return $return;	
}

function spoton_get_schools($type = 'SchDist')
{
	global $spoton_idx;

	switch ($type)
	{
		default:
		case 'SchDist':
			$post_key = 'spto_schooldistrict';
			$title = __('School District', $spoton_idx->domain);
			$fk = 'School District';
		break;

		case 'Ele':
			$post_key = 'spto_elementaryschool';
			$title = __('Elementary School', $spoton_idx->domain);
			$fk = 'Elementary School';
		break;

		case 'JuHi':
			$post_key = 'spto_middleschool';
			$title = __('Middle School', $spoton_idx->domain);
			$fk = 'Middle School';
		break;

		case 'Hi':
			$post_key = 'spto_highschool';
			$title = __('High School', $spoton_idx->domain);
			$fk = 'High School';
		break;

	}

	$return = '<label for="' . $post_key . '" class="lbl_a">' . $title . '</label>' . "\n"; 
	$return .= '<select name="' . $post_key . '" id="' . $post_key . '" class="medium">' . "\n";
	$return .= '<option value="0">' . __('All', $spoton_idx->domain) . '</option>' . "\n";
	$return .= apply_filters('spoton_idx_sff_get_html', '', $fk, 'select');

	$return .= '</select>' . "\n";
	return $return;
}

function spoton_get_prices($to = false)
{
	global $spoton_idx;

	$post_key = ($to) ? 'spto_priceto' : 'spto_pricefrom';
	$title = ($to) ? __('Price To', $spoton_idx->domain) : __('Price From', $spoton_idx->domain);
	$fk = ($to) ? 'Price To' : 'Price From';
	$def_value = apply_filters('spoton_idx_sff_get_static', 0, $fk, 'def_value');
?>
<label for="<?php echo $post_key; ?>" class="lbl_a"><?php echo $title; ?></label>
<select name="<?php echo $post_key; ?>" id="<?php echo $post_key; ?>" class="medium">
<?php
	spoton_get_price_range($to, $def_value);
?>
</select>
<?php
}

function spoton_get_qsw_price($to = false)
{
	$fk = ($to) ? 'Price To' : 'Price From';
	$def_value = apply_filters('spoton_idx_sff_get_static', 0, $fk, 'def_value');
	spoton_get_price_range($to, $def_value);
}

function spoton_get_price_range($to = false, $def_value = 0)
{
	global $spoton_idx;

	$post_key = ($to) ? 'spto_priceto' : 'spto_pricefrom';
	$default_label = ($to) ? __('No maximum', $spoton_idx->domain) : __('No minimum', $spoton_idx->domain);
	$null_val = ($to) ? 99999999 : 0;
?>
<option value="<?php echo $null_val; ?>"><?php echo $default_label; ?></option>
<?php
	$step = 10000;
	for ($i = 50000; $i <= 2000000; $i = $i + $step)
	{
		if ($i >= 100000) $step = 25000;
		if (!empty($def_value))
			$selected = ($i == (int) $def_value) ? ' selected="selected" ' : '';
		else
			$selected = (!empty($_POST[$post_key]) && $i == (int) $_POST[$post_key]) ? ' selected="selected" ' : '';
		echo "\t\t\t\t\t" . '<option value="' . $i . '"' . $selected . '>' . '$' . str_replace('.00', '', number_format($i, 2, '.', ',')) . '</option>' . "\n";
	}
}

function spoton_get_sff_rooms($bath = false)
{
	global $spoton_idx;

	$post_key = ($bath) ? 'spto_bathrooms' : 'spto_bedrooms';
	$title = ($bath) ? __('Baths', $spoton_idx->domain) : __('Beds', $spoton_idx->domain);
	$fk = ($bath) ? 'Baths' : 'Beds';
	$def_value = apply_filters('spoton_idx_sff_get_static', 0, $fk, 'def_value');
?>
<label for="<?php echo $post_key; ?>" class="lbl_a"><?php echo $title; ?></label>
<select name="<?php echo $post_key; ?>" id="<?php echo $post_key; ?>" class="medium">
<?php
	spoton_get_rooms($bath, $def_value);
?>
</select>
<?php
}

function spoton_get_qsw_rooms($bath = false)
{
	$fk = ($bath) ? 'Baths' : 'Beds';
	$def_value = apply_filters('spoton_idx_sff_get_static', 0, $fk, 'def_value');
	spoton_get_rooms($bath, $def_value);
}

function spoton_get_rooms($bath = false, $def_value = 0)
{
	global $spoton_idx;

	$post_key = ($bath) ? 'spto_bathrooms' : 'spto_bedrooms';
	$default_label = __('Any Number', $spoton_idx->domain);
?>
<option value="0"><?php echo $default_label; ?></option>
<?php
	for ($i = 1; $i <= 10; $i++)
	{
		if (!empty($def_value))
			$selected = ($i == (int) $def_value) ? ' selected="selected" ' : '';
		else
			$selected = (!empty($_POST[$post_key]) && $i == (int) $_POST[$post_key]) ? ' selected="selected" ' : '';
		echo "\t\t\t\t\t" . '<option value="' . $i . '"' . $selected . '>' . sprintf(__('at least %d'), $i) . '</option>' . "\n";
	}	
}

function spoton_get_sff_sqfeet($reserved = false)
{
	global $spoton_idx;

	$post_key = 'spto_squarefeet';
	$title = __('Square Feet', $spoton_idx->domain);
	$def_value = apply_filters('spoton_idx_sff_get_static', 0, 'Square Feet', 'def_value');
?>
<label for="<?php echo $post_key; ?>" class="lbl_a"><?php echo $title; ?></label>
<select name="<?php echo $post_key; ?>" id="<?php echo $post_key; ?>" class="medium">
<?php
	spoton_get_sqfeet($bath, $def_value);
?>
</select>
<?php
}

function spoton_get_sqfeet($to = false, $def_value = 0)
{
	global $spoton_idx;

	$post_key = 'spto_squarefeet';
	$default_label = __('Any Size', $spoton_idx->domain);
?>
<option value="0"><?php echo $default_label; ?></option>
<?php
	for ($i = 500; $i <= 7500; $i = $i + 500)
	{
		if (!empty($def_value))
			$selected = ($i == (int) $def_value) ? ' selected="selected" ' : '';
		else
			$selected = (!empty($_POST[$post_key]) && $i == (int) $_POST[$post_key]) ? ' selected="selected" ' : '';
		echo "\t\t\t\t\t" . '<option value="' . $i . '"' . $selected . '>' . sprintf('%d+', $i) . '</option>' . "\n";
	}	
}

function spoton_get_acres($to = false)
{
	global $spoton_idx;

	$post_key = ($to) ? 'spto_acreageto' : 'spto_acreagefrom';
	$title = ($to) ? __('Acreage To', $spoton_idx->domain) : __('Acreage From', $spoton_idx->domain);
	$fk = ($to) ? 'Acreage To' : 'Acreage From';
	$def_value = apply_filters('spoton_idx_sff_get_static', 0, $fk, 'def_value');
?>
<label for="<?php echo $post_key; ?>" class="lbl_a"><?php echo $title; ?></label>
<select name="<?php echo $post_key; ?>" id="<?php echo $post_key; ?>" class="medium">
<?php
	spoton_get_acre_range($to, $def_value);
?>
</select>
<?php
}

function spoton_get_acre_range($to = false, $def_value = 0)
{
	global $spoton_idx;

	$post_key = ($to) ? 'spto_acreageto' : 'spto_acreagefrom';
	$default_label = ($to) ? __('No maximum', $spoton_idx->domain) : __('No minimum', $spoton_idx->domain);
	$null_val = ($to) ? 999999 : 0;
?>
<option value="<?php echo $null_val; ?>"><?php echo $default_label; ?></option>
<?php
	$step = 0.25;
	for ($i = 0.25; $i <= 20; $i = $i + $step)
	{
		if ($i >= 1) $step = 1;
		if (!empty($def_value))
			$selected = ($i == (int) $def_value) ? ' selected="selected" ' : '';
		else
			$selected = (!empty($_POST[$post_key]) && $i == (int) $_POST[$post_key]) ? ' selected="selected" ' : '';
		echo "\t\t\t\t\t" . '<option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n";
	}
}

function spoton_get_sff_tom($size = 'large')
{
	global $spoton_idx;

	$def_value = apply_filters('spoton_idx_sff_get_static', 0, 'Time on Market', 'def_value');
?>
<label for="spto_market" class="lbl_a"><?php _e('Time on Market', $spoton_idx->domain); ?></label>
<select id="spto_market" class="<?php echo $size; ?>">
<?php
	spoton_get_tom(false, $def_value);
?>
</select>
<?php
}

function spoton_get_tom($reserved = false, $def_value = 0)
{
	global $spoton_idx;

	$post_key = 'spto_market';

	$options = array(
		__('All Properties', $spoton_idx->domain) => 0,
		__('2 Weeks', $spoton_idx->domain) => 14,
		__('1 Week', $spoton_idx->domain) => 7,
		__('3 Days', $spoton_idx->domain) => 3,
		__('1 Day', $spoton_idx->domain) => 1
	);

	foreach ($options as $key => $value)
	{
		if (!empty($def_value))
			$selected = ($value == (int) $def_value) ? ' selected="selected" ' : '';
		else
			$selected = (!empty($_POST[$post_key]) && $value == (int) $_POST[$post_key]) ? ' selected="selected" ' : '';
		echo "\t\t\t\t\t" . '<option value="' . $value . '"' . $selected . '>' . $key . '</option>' . "\n";
	}	
}

// Add more widgets
add_action('widgets_init', 'spoton_register_widgets');
function spoton_register_widgets()
{
	global $spoton_idx;

	if ('yes' == $spoton_idx->options['enable_widget_qsw'])
	{
		require_once(dirname(__FILE__) . '/widgets/class-quick-search-widget.php');
		register_widget('Spoton_QuickSearchWidget');
	}

	if ('yes' == $spoton_idx->options['enable_widget_flw'])
	{
		require_once(dirname(__FILE__) . '/widgets/class-featured-listing-widget.php');
		register_widget('Spoton_FeaturedListingWidget');
	}
}

// System functions

function spoton_is_property()
{
	global $spoton_idx;
	return $spoton_idx->is_property();
}

function spoton_get_entities()
{
	global $spoton_idx;
	return $spoton_idx->get_entities();
}

function spoton_list_hide($field)
{
	global $spoton_idx;
	$field = 'hide_list_' . $field;
	if (!empty($spoton_idx->options[$field]) && 'yes' == $spoton_idx->options[$field])
		return true;
	return false;
}

endif;
?>