<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Admin_FileImport {

	/**
	 * File data
	 */
	public $data = array();

	/**
	 * File type allowed
	 */
	private $allowed = array();

	public function __construct($data, $allowed)
	{
		$this->data = $data;
		$this->allowed = $allowed;
		$this->make_filename_safe();
	}

	private function make_filename_safe()
	{
		$temp = explode('.', $this->data['name']);
		if (1 < sizeof($temp))
			$ext = array_pop($temp);
		$this->data['name'] = implode('.', $temp);
		$this->data['name'] = str_replace('.', '', $this->data['name']);
		$this->data['name'] = str_replace('/', '', $this->data['name']);
		$this->data['name'] .= '.' . $ext;
	}

	public function is_allowed()
	{
		if (!in_array($this->data['type'], $this->allowed))
			return false;
		return true;
	}

	public function import($form_name, $data)
	{
		do_action('bwp_opa_import_start_' . $form_name, $this, $data);
	}

	public static function import_csv($filename, $tmp_name = '', $fields, $args = array(), $delim = ",", $enclosure = '"', $escape = '\\')
	{
		if (!empty($tmp_name))
			move_uploaded_file($tmp_name, $filename);

		$args = wp_parse_args($args, array('test' => false, 'start_at' => 0, 'limit' => 5000));

		$imported_data = array();
		$last_position = $args['start_at'];
		if (($fp = fopen($filename, "rb")) !== false)
		{
			$row = 0;
			$real_row = 0;
			if (!empty($last_position))
				fseek($fp, $last_position);
			while (($data = fgetcsv($fp, 0, $delim, $enclosure, $escape)) !== false)
			{
				$num = count($data);
				if (!empty($num) && !empty($data[0]))
				{
					$real_row++;
					$col = 0;
					$row++;
					foreach ($fields as $field)
					{
						$temp[$field] = trim($data[$col]);
						$col++;
					}
					$imported_data[] = $temp;
					unset($temp);
					if (true == $args['test'] || $row > $args['limit'])
					{
						$last_position = ftell($fp);
						break;
					}
				}
			}
			fclose($fp);
			return array('data' => $imported_data, 'last_position' => $last_position);
		}
		else
			return false;
	}

}
?>