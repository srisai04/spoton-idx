<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
 
abstract class BWP_PluginCore {

	protected function &core_get(self $bwp_obj, $ppt_name)
	{
		return $bwp_obj->$ppt_name;
	}

	protected function core_call(self $bwp_obj, $mtd_name, array $mtd_args)
	{
		return call_user_func_array(array($bwp_obj, $mtd_name), $mtd_args);
	}
}
 
?>