<?php

/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Options extends BWP_PluginCore {

	/**
	 * Option Key
	 */
	private $option_key = '';

	/**
	 * Type: DB / Insert / Import / Upload
	 */
	protected $type = '';

	/**
	 * Default values
	 */
	protected $defaults = array();

	/**
	 * DB Values
	 */
	protected $options = array();

	/**
	 * Site options
	 */
	protected $site_o = array();

	/**
	 * Options' formats
	 */
	protected $formats = array();

	/**
	 * Requirements, whether option is required or not
	 */
	protected $required = array();

	/**
	 * Extra options that can act as place holders for hidden / extra fields
	 */
	protected $extra = array();

	/**
	 * The DB table used for these options
	 */
	protected $table = '';

	/**
	 * Default constructor
	 */
	public function __construct($key, array $defaults, $type = 'db', $formats = array(), $required = array(), $site_o = array())
	{
		$this->option_key 	= (string) $key;
		$this->type			= (string) $type;
		$this->defaults		= $defaults;
		$this->site_o		= (array) $site_o;
		$this->formats		= (array) $formats;
		$this->required		= (array) $required;
		// Get options from database if we have to
		if ('db' == $type)
			$this->options = $this->get_options();
		else
			$this->options = $this->defaults;
	}

	/**
	 * Choose table to interact with
	 */
	public function uses_table($table_name)
	{
		if (!is_string($table_name))
			printf(__('Invalid table name to use with this option key (%s)'), $this->option_key);
		$this->table = $table_name;
	}

	/**
	 * Ignore some options for certain forms (hidden fields)
	 */
	public function set_extra(array $fields)
	{
		$this->extra = $fields;
	}

	/**
	 * Ignore some options for certain forms (split form)
	 */
	public function ignores($from, $num)
	{
		if ('none' == $from)
		{
			$this->required = array();
			return;
		}

		$num = (int) $num;
		$count = 0;
		$option_keys = array_keys($this->options);
		foreach ($option_keys as $v)
		{
			if ($v == $from)
			{
				$this->required = array_slice($option_keys, $count, $num);
				break;
			}
			$count++;
		}
	}

	/**
	 * Get option key
	 */
	public function get_option_key()
	{
		return $this->option_key;
	}

	/**
	 * Get the options from database and merge them
	 */
	public function get_options()
	{
		$options = $this->defaults;
		$db_option = get_option($this->option_key);

		if ($db_option && is_array($db_option))
			foreach ($options as $k => &$v)
				if (array_key_exists($k, $db_option))
					$v = $db_option[$k];
		unset($db_option);

		// Also check for global options if in Multi-site
		if (BWP_PluginBase::is_multisite())
		{
			$db_option = get_site_option($this->option_key);
			if ($db_option && is_array($db_option))
			{
				$temp = array();
				foreach ($db_option as $k => $o)
					if (in_array($k, $this->site_o))
						$temp[$k] = $o;
				$options = array_merge($options, $temp);
			}
		}

		return $options;
	}

	/**
	 * Update options
	 */
	public function update_options(array $values)
	{
		foreach ($this->options as $option => &$value)
			if (array_key_exists($option, $values))
				$value = $values[$option];

		update_option($this->option_key, $this->options);
	}
}

?>