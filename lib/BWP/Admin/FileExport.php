<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Admin_FileExport {

	public function __construct()
	{
	}

	public function export($form_name, $form_data)
	{
		do_action('bwp_opa_export_start_' . $form_name, $form_data);
	}

	public static function export_csv($filename = '', $data, $delim = ',', $enclosure = '"', $args = array())
	{
		if (!empty($filename))
			$fp = fopen($filename, 'w');
		else
			$fp = tmpfile();

		$args = wp_parse_args($args, array('filename' => '', 'content_type' => 'application/csv', 'limit' => 5000));

		if ($fp !== false)
		{
			foreach ($data as $fields)
				fputcsv($fp, $fields, $delim, $enclosure);
			if (empty($filename))
			{
				header('Content-Type: ' . $args['content_type']);
				header('Content-Disposition: attachment; filename=' . $args['filename']);
				header('Pragma: no-cache');
				fseek($fp, 0);
				fpassthru($fp);
				fclose($fp);
				die();
			}
			else
				fclose($fp);
			return true;
		}
		else
			return false;
	}

}
?>