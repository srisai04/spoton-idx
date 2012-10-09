<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE
 */

class BWP_GXS_MODULE_PROPERTY extends BWP_GXS_MODULE {

	private $_qid, $_proxy, $_filter;

	function __construct()
	{
		global $bwp_gxs, $wpdb;

		$this->set_current_time();
		$this->part = $bwp_gxs->module_data['module_part'];

		$this->_qid = (int) $bwp_gxs->module_data['sub_module'];
		if (empty($this->_qid))
			return false;

		require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
		$this->_proxy = new XpioMapRealEstateEntities();
		$this->_filter = $wpdb->get_var($wpdb->prepare('SELECT query FROM ' . $wpdb->spoton_saved_search . ' WHERE qid = %d', $this->_qid));

		if (empty($this->_filter))
			return false;

		/* Build the actual data */
		$this->build_data();
	}

	function query()
	{
		global $bwp_gxs;

		$skip 		= (!empty($this->url_sofar)) ? $this->offset + (int) $this->url_sofar : $this->offset;
		$end 		= (int) $bwp_gxs->options['input_sql_limit'];
		$limit 		= (empty($this->part)) ? $bwp_gxs->options['input_item_limit'] : $bwp_gxs->options['input_split_limit_post'];

		if ($this->url_sofar + $end > $limit)
			$end = $limit - $this->url_sofar;

		$fa = explode('^', $this->_filter);
		$filter = $fa[0];

		$response = $this->_proxy->GenericRetsSearchListings()->skip((int) $skip)->Top($end)->filter($filter)->Select('PKey,LN,HouNo,Stre,StrSuff,DirSuff,City,State,Zip')->IncludeTotalCount()->Execute();

		return $response->Result;

	}

	function build_data()
	{
		global $bwp_gxs;

		// Use part limit or global item limit - @since 1.1.0
		$limit = (empty($this->part)) ? $bwp_gxs->options['input_item_limit'] : $bwp_gxs->options['input_split_limit_post'];
		$this->offset = (empty($this->part)) ? 0 : ($this->part - 1) * $bwp_gxs->options['input_split_limit_post'];

		while ($this->url_sofar < $limit && false != $this->generate_data())
			$this->url_sofar = sizeof($this->data);
	}


	function generate_data()
	{
		global $wpdb, $bwp_gxs;

		$properties = $this->query();

		if (!isset($properties) || 0 == sizeof($properties))
			return false;

		$data = array();
		for ($i = 0; $i < sizeof($properties); $i++)
		{
			$property = $properties[$i];
			$data = $this->init_data($data);
			$data['location'] = spoton_get_ppt_permalink($property);
			$data['lastmod'] = $this->format_lastmod(current_time('timestamp'));
			$data['freq'] = $this->cal_frequency(NULL);
			$data['priority'] = $this->cal_priority(NULL, $data['freq']);
			$this->data[] = $data;
		}

		return true;

	}

}
?>