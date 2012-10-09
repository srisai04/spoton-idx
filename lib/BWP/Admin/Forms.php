<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Admin_Forms extends BWP_Options {

	/**
	 * Form name
	 */
	public $form_name = '';

	/**
	 * Encoding type for form
	 */
	public $form_enctype = '';

	/**
	 * Button name to fire action
	 */
	private $post_action = '';

	/**
	 * The form
	 */
	public $form = array();

	/**
	 * This holds the form items, determining the position
	 */
	public $form_items = array();
	
	/**
	 * This holds the name for each items (an item can have more than one fields)
	 */
	public $form_item_names = array();
	
	/**
	 * This holds the form label
	 */
	public $form_item_labels = array();
	
	/**
	 * This holds the form option aka data
	 */
	public $form_options = array(), $form_site_options = array();

	public $domain = '', $custom_error = '', $style = '';
	
	public function __construct($form_name, $post_action = '') 
	{
		$this->form_name			= $form_name;
		$this->post_action			= $post_action;
	}

	public function populate_form(array $form, array $form_values, array $form_global_values)
	{
		$this->form_items 			= $form['items'];
		$this->form_item_names 		= $form['item_names'];
		$this->form_item_labels		= $form['item_labels'];
		$this->form					= $form;
		$this->form_options			= $form_values;
		$this->form_site_options	= $form_global_values;
	}

	public function replaced_by($form, array $form_values, array $form_global_values, $post_action = '')
	{
		$this->post_action			= $post_action;
		$this->form_items 			= $form['items'];
		$this->form_item_names 		= $form['item_names'];
		$this->form_item_labels		= $form['item_labels'];
		$this->form					= $form;
		$this->form_options			= $form_values;
		$this->form_site_options	= $form_global_values;
	}

	public function set_domain($domain)
	{
		$this->domain = $domain;
	}

	public function set_form_values($values)
	{
		$this->form_options	= $values;
	}

	public function set_form_enctype($enctype)
	{
		$this->form_enctype = $enctype;
	}

	public function show_general_notice()
	{
		echo '<div class="bwp-notices"><p>' . $this->custom_error . '</p></div>';
	}

	public function show_general_error()
	{
		echo '<div class="bwp-notices bwp-errors"><p>' . $this->custom_error . '</p></div>';
	}

	private static function convert_field_formats($format)
	{
		switch ($format)
		{
			case 'float':
				return '%f';
			break;
			case 'int':
				return '%d';
			break;
			default:
				return '%s';
			break;
		}
	}

	private function handle_duplicate(array $data)
	{
		$is_dup = apply_filters('bwp_opf_handle_dup_' . $this->form_name, false, $this->form_name, $data);
		return $is_dup;
	}

	public function handle_form_insert(BWP_Options $options)
	{
		global $wpdb;

		if (empty($this->post_action))
			return;

		if (!is_array($options->defaults) || 0 == sizeof($options->defaults))
			return;

		$defaults = $options->defaults;
		$formats  = $options->formats;
		$required = $options->required;
		$hidden_fields = $options->extra;

		if (isset($_POST[$this->post_action]))
		{
			check_admin_referer($this->form_name);
			$data = array();
			$data_format = array();
			$valid = true;

			foreach ($defaults as $k => $v)
			{
				if (!isset($_POST[$k]) || '' == $_POST[$k])
				{
					if ('all' == $required || (is_array($required) && !in_array($k, $required)))
					{
						$this->custom_error = __('Please fill all required fields and re-submit.', $this->domain);
						add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_error'));
						$valid = false;
					}
					$data[$k] = $v;
				}
				else				
					$data[$k] = stripslashes(self::format_field($k, $formats));

				$data_format[] = (isset($formats[$k])) ? self::convert_field_formats($formats[$k]) : '%s';
			}

			// Hidden fields
			foreach ($hidden_fields as $k => $v)
			{
				if (isset($_POST[$k]))
					$hidden_fields[$k] = stripslashes(self::format_field($k, $formats));
			}

			if (true == $valid)
			{
				// Filter data just before we insert it into db
				$data = apply_filters('bwp_opf_before_insert_' . $this->form_name, $data);
				if ('insert' == $options->type)
				{
					// Check for duplication if we need to
					$dup = $this->handle_duplicate($data + $hidden_fields);
					if (!empty($dup))
					{
						if ('_update_succeeded' == $dup)
						{
							$this->custom_error = __('Entry updated.', $this->domain);
							add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_notice'));							
						}
						else
						{
							$this->custom_error = $dup;
							add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_notice'));
						}
					}
					else
					{
						// Insert things into the database
						$success = $wpdb->insert($options->table, $data, $data_format);
						if ($success)
						{
							do_action('bwp_opa_after_insert_' . $this->form_name, $data);
							$this->custom_error = __('New item successfully added!', $this->domain);
							add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_notice'));
						}
						else
						{
							$this->custom_error = __($wpdb->last_error);
							add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_error'));
						}
					}
				}
				else if ('import' == $options->type)
				{
					if (!class_exists('BWP_Admin_FileImport'))
						require_once(BWP_Init::$lib_path . '/Admin/FileImport.php');

					if (!empty($_FILES['the_file']['name']) && !empty($_FILES['the_file']['tmp_name']))
						$the_file = $_FILES['the_file'];
					else
					{
						$this->custom_error = __('There was a problem with the file you want to import: file too large or no file selected.', $this->domain);
						add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_error'));
						return $data;
					}

					$allowed = explode('|', $data['bwp_import_allowed']);
					// New file to import
					$file = new BWP_Admin_FileImport($the_file, $allowed);
					if (!$file->is_allowed())
					{
						$this->custom_error = __('File type not allowed.', $this->domain);
						add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_error'));
					}
					else
						$file->import($this->form_name, $data);
				}
				else if ('export' == $options->type)
				{
					if (!class_exists('BWP_Admin_FileExport'))
						require_once(BWP_Init::$lib_path . '/Admin/FileExport.php');
					$file = new BWP_Admin_FileExport();
					$file->export($this->form_name, $data);
				}
				return $data + $hidden_fields;
			}
			else
				return $data + $hidden_fields;
		}
		else
			return $defaults + $hidden_fields;
	}

	public function handle_form_save(BWP_Options $oo, BWP_PluginBase $bwpp_obj)
	{
		$options = $oo->options;

		if (isset($_POST['save_' . $this->form_name]))
		{
			check_admin_referer($this->form_name);

			foreach ($options as $key => &$option)
			{
				// Get rid of options that do not have a key
				if (preg_match('/^[0-9]+$/i', $key))
				{
					unset($options[$key]);
					continue;
				}
				// WPMS Compatible
				if (BWP_PluginBase::is_normal_admin() && in_array($key, $oo->site_o))
				{}
				else if (in_array($key, $oo->required))
				{}
				else
				{
					if (isset($_POST[$key]))
						self::format_field($key, $oo->formats);
					if (!isset($_POST[$key]))
						$options[$key] = '';
					else if (isset($oo->formats[$key]) && 0 == $_POST[$key] && 'int' == $oo->formats[$key])
						$option = 0;
					else if (isset($oo->formats[$key]) && empty($_POST[$key]) && ('int' == $oo->formats[$key] || 'float' == $oo->formats[$key]))
						$option = $oo->defaults[$key];
					else if (isset($oo->formats[$key]) && empty($_POST[$key]) && 'opt' == $oo->formats[$key])
						$option = '';
					else if (!empty($_POST[$key]))
						$option = $_POST[$key];
					else
						$option = $oo->defaults[$key];
				}
			}
			// Change the options based on other things
			$options = apply_filters('bwp_opf_before_save_' . $this->form_name, $options, $oo, $this->form_name);
			// Update the database options as well as the options in our object
			$bwpp_obj->update_options($oo->get_option_key(), $options);
			// WPMS Compatible
			if (!BWP_PluginBase::is_normal_admin())
				update_site_option($oo->get_option_key(), $options);
			// Update options succeeded
			if (!has_filter('bwp_opf_before_save_' . $this->form_name))
			{
				$this->custom_error = ('save_all' == $this->post_action) ? __('All options have been saved.', $this->domain) : __('Options saved.', $this->domain);
				add_action('bwp_opa_before_form_' . $this->form_name, array($this, 'show_general_notice'));
			}
		}

		$options = apply_filters('bwp_opf_after_save_' . $this->form_name, $options, $oo, $this->form_name);

		return $options + $oo->extra;
	}

	public function handle_form_misc_actions()
	{
		if (isset($_POST[$this->post_action]))
			do_action('bwp_opa_handle_form_misc_' . $this->form_name, $this->form_name);
	}

	public static function kill_html_fields(array $ids)
	{
		$in_keys = array('items', 'item_labels', 'item_names');
		foreach ($ids as $id)
			foreach ($in_keys as $key)
				if (isset($this->form[$key][$id]))
					unset($this->form[$key][$id]);
	}

	private static function format_field($key, array $option_formats)
	{
		$_POST[$key] = trim(stripslashes($_POST[$key]));
		if (!empty($option_formats[$key]))
		{
			if ('int' == $option_formats[$key])
				$_POST[$key] = (int) $_POST[$key];
			else if ('float' == $option_formats[$key])
				$_POST[$key] = (float) $_POST[$key];
			else if ('html' == $option_formats[$key])
				$_POST[$key] = wp_filter_post_kses($_POST[$key]);
			else if ('slug' == $option_formats[$key])
				$_POST[$key] = preg_replace('/([^a-zA-z0-9-_])+/ius', '-', $_POST[$key]);
			else
				$_POST[$key] = strip_tags($_POST[$key]);
		}
		else
			$_POST[$key] = strip_tags($_POST[$key]);

		return $_POST[$key];
	}

	/**
	 * Generate HTML field
	 *
	 * @params	they explain themselves
	 */
	private function generate_html_field($type = '', $data, $name = '', $in_section = false)
	{
		$pre_html_field 	= '';
		$post_html_field 	= '';
		$checked			= 'checked="checked" ';
		$selected			= 'selected="selected" ';
		$value				= (isset($this->form_options[$name])) ? $this->form_options[$name] : '';
		$value				= (!empty($this->domain) && ('textarea' == $type || 'input' == $type)) ? __($value, $this->domain) : $value;
		$value				= ('textarea' == $type) ? esc_html($value) : esc_attr($value);
		$array_replace 		= array();
		$array_search 		= array('size', 'name', 'value', 'cols', 'rows', 'label', 'disabled', 'pre', 'post');
		$return_html = '';
		$br					= (isset($this->form['inline_fields'][$name]) && is_array($this->form['inline_fields'][$name])) ? '' : "<br />\n";
		$pre				= (!empty($data['pre'])) ? $data['pre'] : '';
		$post				= (!empty($data['post'])) ? $data['post'] : '';

		switch ($type)
		{
			case 'heading':	
				$html_field = '%s';
			break;

			case 'input':	
				$html_field = (!$in_section) ? '%pre%<input%disabled% size="%size%" type="text" id="' . $name . '" name="' . $name . '" value="' . $value . '" /> <em>%label%</em>' : '<label for="' . $name . '">%pre%<input%disabled% size="%size%" type="text" id="' . $name . '" name="' . $name . '" value="' . $value . '" /> <em>%label%</em></label>';
			break;

			case 'hidden':
				$html_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '" />';
			break;

			case 'file':	
				$html_field = (!$in_section) ? '%pre%<input%disabled% size="%size%" type="file" id="' . $name . '" name="' . $name . '" /> <em>%label%</em>' : '<label for="' . $name . '">%pre%<input%disabled% size="%size%" type="file" id="' . $name . '" name="' . $name . '" /> <em>%label%</em></label>';
			break;

			case 'select':
				$pre_html_field = '%pre%<select id="' . $name . '" name="' . $name . '">' . "\n";
				$html_field = '<option %selected%value="%value%" />%option%</option>';
				$post_html_field = '</select>%post%' . $br;
			break;
			
			case 'checkbox':
				$html_field = '<label for="%name%">' . '<input %checked%type="checkbox" id="%name%" name="%name%" value="yes" /> %label%</label>';
			break;
			
			case 'radio':
				$html_field = '<label>' . '<input %checked%type="radio" name="' . $name . '" value="%value%" /> %label%</label>';
			break;
			
			case 'textarea':
				$html_field = '%pre%<textarea%disabled% id="' . $name . '" name="' . $name . '" cols="%cols%" rows="%rows%">' . $value . '</textarea> <em>%label%</em>%post%';
			break;
		}

		if (!isset($data))
			return;

		if ($type == 'heading' && !is_array($data))
		{			
			$return_html .= sprintf($html_field, $data) . $br;
		}
		else if ($type == 'radio' || $type == 'checkbox' || $type == 'select')
		{
			foreach ($data as $key => $value)
			{
				// handle checkbox a little bit differently
				if ($type == 'checkbox')
				{
					if ($this->form_options[$value] == 'yes')
						$return_html .= str_replace(array('%value%', '%name%', '%label%', '%checked%'), array($value, $value, $key, $checked), $html_field) . $br;
					else
						$return_html .= str_replace(array('%value%', '%name%', '%label%', '%checked%'), array($value, $value, $key, ''), $html_field) . $br;
				}
				else if (isset($this->form_options[$name]) && $this->form_options[$name] == $value)
					$return_html .= str_replace(array('%value%', '%name%', '%label%', '%option%', '%checked%', '%selected%', '%pre%', '%post%'), array($value, $value, $key, $key, $checked, $selected, $pre, $post), $html_field) . $br;
				else
					$return_html .= str_replace(array('%value%', '%name%', '%label%', '%option%', '%checked%', '%selected%', '%pre%', '%post%'), array($value, $value, $key, $key, '', '', $pre, $post), $html_field) . $br;
			}
		}
		else
		{
			foreach ($array_search as &$keyword)
			{
				$array_replace[$keyword] = '';
				if (!empty($data[$keyword]))
				{
					$array_replace[$keyword] = $data[$keyword];
				}
				$keyword = '%' . $keyword . '%';
			}
			$return_html = str_replace($array_search, $array_replace, $html_field) . $br;
		}

		// inline fields
		$inline_html = '';
		if (isset($this->form['inline_fields'][$name]) && is_array($this->form['inline_fields'][$name]))
		{
			foreach ($this->form['inline_fields'][$name] as $field => $field_type)
			{
				if (isset($this->form[$field_type][$field]))
					$inline_html = ' ' . $this->generate_html_field($field_type, $this->form[$field_type][$field], $field, $in_section);
			}
		}
		
		// Post
		$post = (!empty($this->form['post'][$name])) ? ' ' . $this->form['post'][$name] : $post;
		
		return str_replace('%pre%', $pre, $pre_html_field) . $return_html . str_replace('%post%', $post, $post_html_field) . $inline_html;
	}

	/**
	 * Generate HTML fields
	 *
	 * @params	they explain themselves
	 */
	public function generate_html_fields($type, $name)
	{
		$item_label = '';	
		$return_html = '';				
		
		$item_key = array_keys($this->form_item_names, $name);
		
		$input_class = ($type == 'heading') ? 'bwp-option-page-heading-desc' : 'bwp-option-page-inputs';

		// An inline item can hold any HTML markup
		// An example is to display some kinds of button right be low the label
		$inline = '';		
		if (isset($this->form['inline']) && is_array($this->form['inline']) && array_key_exists($name, $this->form['inline']))
			$inline = (empty($this->form['inline'][$name])) ? '' : $this->form['inline'][$name];
		$inline .= "\n";

		switch ($type)
		{
			case 'section':	
			
				if (!isset($this->form[$name]) || !is_array($this->form[$name]))
				return;
				
				$item_label = '<span class="bwp-opton-page-label">' . $this->form_item_labels[$item_key[0]] . $inline . '</span>';
				
				foreach ($this->form[$name] as $section_field)
				{
					$type = $section_field[0];
					$name = $section_field['name'];

					if (isset($this->form[$section_field[0]]))
					{
						$return_html .= $this->generate_html_field($section_field[0], $this->form[$type][$name], $name, true);
					}
				}	
			break;

			default:
			
				if (!isset($this->form[$type][$name]) || ($type != 'heading' && !is_array($this->form[$type][$name])))
				return;

				/*$item_label = (empty($this->form[$type][$name]['label'])) ? '<label class="bwp-opton-page-label" for="' . $name . '">' . $this->form_item_labels[$item_key[0]] . '</label>' : '<span class="bwp-opton-page-label">' . $this->form_item_labels[$item_key[0]] . '</span>';*/
				$item_label = ($type != 'checkbox' && $type != 'radio') ? '<label class="bwp-opton-page-label" for="' . $name . '">' . $this->form_item_labels[$item_key[0]] . $inline . '</label>' : '<span class="bwp-opton-page-label type-' . $type . '">' . $this->form_item_labels[$item_key[0]] . $inline . '</span>';
				$item_label = ('hidden' == $type) ? '' : $item_label;
				$h = (isset($this->form['h4']) && in_array($name, $this->form['h4'])) ? '4' : '3';
				$item_label = ($type == 'heading') ? "<h$h>" . $this->form_item_labels[$item_key[0]] . "</h$h>" . $inline : $item_label;
				if (isset($this->form[$type]))
					$return_html = $this->generate_html_field($type, $this->form[$type][$name], $name);

			break;
		}
		
		// A container can hold some result executed by customized script, 
		// such as displaying something when user press the submit button
		$containers = '';
		if (isset($this->form['container']) && is_array($this->form['container']) && array_key_exists($name, $this->form['container']))
		{
			$container_array = (array) $this->form['container'][$name];
			foreach ($container_array as $container)
			{
				$containers .= (empty($container)) ? '<div style="display: none;"><!-- --></div>' : '<div class="clear">' . $container . '</div>' . "\n";
			}
		}
		
		$pure_return = trim(strip_tags($return_html));
		if ('hidden' == $type)
			return str_replace('<br />', '', $return_html);
		if (empty($pure_return) && $type == 'heading')
			return $item_label . $containers;
		else
			return '<div class="clearfix">' . $item_label . '<p class="' . $input_class . '">' . $return_html . '</p>' . '</div>' . $containers;
	}

}

?>