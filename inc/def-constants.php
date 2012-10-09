<?php
	$this->add_option_key('SPOTON_IDX_GENERAL', 'spoton_idx_general', __('General Settings', $this->domain));
	$this->add_option_key('SPOTON_IDX_SAVED', 'spoton_idx_saved', __('Saved Search', $this->domain));
	$this->add_option_key('SPOTON_IDX_FORM_ADM', 'spoton_idx_form_adm', __('Search Form Settings', $this->domain));
	$this->add_option_key('SPOTON_IDX_LIST_ADM', 'spoton_idx_list_adm', __('Listing Settings', $this->domain));
	define('SPOTON_IDX_PSTATUS', 'spoton_idx_pstatus');
	define('SPOTON_PPT_TPL_PATH', dirname(__FILE__) . '/spoton/property-template');
	define('SPOTON_SCH_TPL_PATH', dirname(__FILE__) . '/spoton/search-template');
	define('SPOTON_LST_TPL_PATH', dirname(__FILE__) . '/spoton/listing-template');
?>