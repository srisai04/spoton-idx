<?php
/**
 * Copyright (c) scribu <scribu.net>, Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Tables {

	protected $name;
	protected $engine;
	protected $columns;
	protected $upgrade_method;
	protected $install_method;

	public function __construct($name, $columns, array $args, $upgrade_method)
	{
		global $wpdb;

		$this->name = $wpdb->$name = $wpdb->base_prefix . $name;
		$this->engine = $args['engine'];
		$this->columns = $columns;
		$this->upgrade_method = $upgrade_method;
		$this->install_method = $args['install_method'];
	}

	public function get_install_method()
	{
		return $this->install_method;
	}

	public function install()
	{
		global $wpdb;

		$charset_collate = '';
		if ($wpdb->has_cap('collation'))
		{
			if (!empty($wpdb->charset))
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if (!empty($wpdb->collate))
				$charset_collate .= " COLLATE $wpdb->collate";
		}

		$db_engine = '';
		if (!empty($this->engine))
			$db_engine = " ENGINE = $this->engine";

		if ('dbDelta' == $this->upgrade_method)
		{
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta("CREATE TABLE $this->name ($this->columns) $db_engine $charset_collate");
			return;
		}

		if ('delete_first' == $this->upgrade_method)
			$wpdb->query("DROP TABLE IF EXISTS $this->name;");

		$wpdb->query("CREATE TABLE IF NOT EXISTS $this->name ($this->columns) $db_engine $charset_collate;");
	}

	public function uninstall()
	{
		global $wpdb;

		$wpdb->query("DROP TABLE IF EXISTS $this->name");
	}
}

?>