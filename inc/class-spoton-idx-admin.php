<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('Spoton_IDX')) :

class Spoton_IDX extends BWP_PluginBase {

	private static $_spt = false;

	private static $_ppt_templates = array();

	private static $_tables = array();

	public function __construct($plugin_data, $plugin_wp_data)
	{
		if (!parent::__construct($plugin_data, $plugin_wp_data))
			return false;

		require_once(dirname(__FILE__) . '/def-constants.php');
		require_once(dirname(__FILE__) . '/def-options.php');
		require_once(dirname(__FILE__) . '/def-tables.php');
		require_once(dirname(__FILE__) . '/common-functions.php');

		$this->init();
		$this->check_for_updates();
	}

	public function install()
	{
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	protected function update_plugin_db($old, $new)
	{
		global $wpdb;

		$db_update_path = dirname(__FILE__) . '/db_updates';

		switch ($old)
		{
			default:
			case '1':
				// Install two new tables
				$this->install_table('spoton_sff_fields');
				$this->install_table('spoton_sff_values');
				// Populate default fields
				include_once($db_update_path . '/r2.php');
			break;

			case '2':
				// Change value table's index
				include_once($db_update_path . '/r3.php');
			break;
		}
	}

	protected function init_properties()
	{
		self::$_ppt_templates = array(
			'ps' 	=> array(__('Property Search Page', $this->domain), 'property-search.php'),
			'psl' 	=> array(__('Property Search List', $this->domain), 'property-search-list.php'),
			'psm' 	=> array(__('Property Search Map', $this->domain), 'property-search-map.php'),
      'regfrm' 	=> array(__('Registration Form', $this->domain), 'registration.php')
		);
	}

	protected function add_hooks()
	{
		add_action('init', array($this, 'add_editor_button'));
		add_action('do_meta_boxes', array($this, 'add_page_meta_box'));
		// Property Search Page Template
		add_action('init', array($this, 'saving_page_template'));
		add_action('save_post', array($this, 'save_page_template'), 10, 2);
		// Handle ajax request
		add_action('wp_ajax_spoton_idx_sff', array($this, 'sff_ajax_post'));
	}

	protected function add_admin_hooks()
	{
		if (SPOTON_IDX_FORM_ADM == $this->admin_page)
		{
			add_filter('bwp_opf_before_save_spoton_idx_sff', array($this, 'sff_save_post'));
			add_action('sff_save_settings', array($this, 'sff_post'));
		}

		if (SPOTON_IDX_SAVED == $this->admin_page || SPOTON_IDX_FORM_ADM == $this->admin_page)
		{
			add_action('bwp_opa_forms_init', array($this, 'handle_misc_actions'));
		}
	}

	public function enqueue_media()
	{
		// Reset to default
		if ($this->is_admin_page() && SPOTON_IDX_GENERAL == $this->admin_page)
		{
			wp_enqueue_script('form-reset-js', BWP_FRW_ADM_JS . '/form-reset.js', array('jquery'), BWP_Init::$rev);
			wp_localize_script('form-reset-js', BWP_FRW_FORM_RESET_L10N, $this->default_options);
		}
		else if ($this->is_admin_page() && SPOTON_IDX_FORM_ADM == $this->admin_page)
		{
			wp_enqueue_script('spoton-adm', SPOTON_IDX_JS . '/jquery/jquery.dualListBox.min.js', array(), '1.3');
			wp_enqueue_style('spoton-adm', SPOTON_IDX_CSS . '/spoton-idx-adm.css', array(), $this->get_pd('version'));
		}
	}

	public function build_menus()
	{
		add_menu_page(__('Spoton Plugin', $this->domain), 'Spot-on IDX', $this->cap, SPOTON_IDX_GENERAL, array($this, 'build_admin_pages'), SPOTON_IDX_IMAGES . '/icon_menu.png');
		add_submenu_page(SPOTON_IDX_GENERAL, __('Spoton - General Settings', $this->domain), 'General Settings', $this->cap, SPOTON_IDX_GENERAL, array($this, 'build_admin_pages'));
		add_submenu_page(SPOTON_IDX_GENERAL, __('Spoton - Saved Search', $this->domain), 'Saved Search', $this->cap, SPOTON_IDX_SAVED, array($this, 'build_admin_pages'));
		add_submenu_page(SPOTON_IDX_GENERAL, __('Spoton - Search Form Settings', $this->domain), 'Search Form Settings', $this->cap, SPOTON_IDX_FORM_ADM, array($this, 'build_admin_pages'));
		add_submenu_page(SPOTON_IDX_GENERAL, __('Spoton - Listing Settings', $this->domain), 'Listing Settings', $this->cap, SPOTON_IDX_LIST_ADM, array($this, 'build_admin_pages'));
		// BWP GXS Plugin Menu
		if ('yes' == $this->get_o('enable_xml_sitemap'))
		{
			global $bwp_gxs;
			add_submenu_page(SPOTON_IDX_GENERAL, __('Google XML Sitemaps', 'bwp-simple-gxs'), __('XML Sitemaps', 'bwp-simple-gxs'), BWP_GXS_CAPABILITY, BWP_GXS_GENERAL, array($bwp_gxs, 'build_option_pages'));
		}
		// BWP Listing Alerts - All People
		if ('yes' == $this->get_o('enable_listing_alert'))
		{
			global $listing_alerts;
			/*add_submenu_page(SPOTON_IDX_GENERAL, __('Listing Alerts CRM', 'list-alerts'), __('Listing Alerts', 'list-alerts'), LIST_ALERTS_CAPABILITY, LIST_ALERTS, array($listing_alerts, 'build_option_pages'));*/
			add_submenu_page(SPOTON_IDX_GENERAL, __('Listing Alerts - All People', 'list-alerts'), __('All People', 'list-alerts'), LIST_ALERTS_CAPABILITY, LIST_ALERTS_USERS, array($listing_alerts, 'build_option_pages'));
		}
	}

	public function sff_ajax_post()
	{
		global $wpdb;

		check_ajax_referer($_POST['action']);

		$jobs = array('get_field', 'save_field');
		if (empty($_POST['job']) || !in_array($_POST['job'], $jobs))
			return;
		$job = $_POST['job'];
		$fid = (!empty($_POST['fid'])) ? preg_replace('/[^0-9]/ui', '', $_POST['fid']) : '';
		if (empty($fid))
			return;

		switch ($job)
		{
			default:
			case 'get_field':

				$fd = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->spoton_sff_fields . ' WHERE fid = %d', $fid));

				if (empty($fd->fid))
					return 'fid';

				// If this is a dynamic field, we allow filtering values
				if (!$fd->static)
				{
					// Load entities class
					require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
					$proxy = new XpioMapRealEstateEntities();
					global $spoton_pkey;

					// First, we get saved values
					$db_values = $wpdb->get_col($wpdb->prepare('SELECT value FROM ' . $wpdb->spoton_sff_values . ' WHERE fid = %d', $fd->fid));

					// Fields that have relationship must be treated differently
					if (in_array($fd->title, array('State', 'County', 'City')))
					{
						// Available values, get from Odata
						$entity_ppt = str_replace(' ', '', $fd->title);
						if ('City' != $fd->title)
						{
							$entity_method = 'RetsCounties';
						}
						else
							$entity_method = 'RetsCities';
						$query = 'PKey eq ' . $spoton_pkey . '&$orderby=' . $entity_ppt;
					}
					else
					{
						// Available values, get from Odata
						if ('Property Type' != $fd->title)
						{
							$entity_ppt = str_replace(' ', '', $fd->title);
							$entity_method = 'Rets' . $entity_ppt . 's';
							$query = 'PKey eq ' . $spoton_pkey . '&$orderby=' . $entity_ppt;
						}
						else
						{
							$entity_ppt = 'PropName';
							$entity_method = 'RetsProperties';
							$query = 'PKey eq ' . $spoton_pkey;
						}
					}

						$return = '';

						$response = $proxy->$entity_method()->filter($query)->Select($entity_ppt)->Execute();
						$result = $response->Result;
						$return = '<tr><td>' . "\n";
						$return .= __('Filter:', $this->domain) . ' <input type="text" id="sff_dnm_f1" /> <button type="button" class="button-secondary" id="sff_dnm_f1_clear">X</button><br />' . "\n";
						$return .= '<select class="sff_dnm_v" id="sff_dnm_v1" multiple="multiple">' . "\n";

						$found = 0;
						$fetched = array();
						foreach ($result as $value)
						{
							if (empty($value->$entity_ppt) || in_array($value->$entity_ppt, $db_values) || in_array($value->$entity_ppt, $fetched))
								continue;
							$found++;
							$fetched[] = $value->$entity_ppt;
							$return .= '<option value="' . esc_attr(preg_replace('/[\s]+/ui', '_', $value->$entity_ppt)) . '">' . esc_html($value->$entity_ppt) . '</option>' . "\n";
						}

						// If we find nothing, tell the user so
						if (empty($found) && 0 == sizeof($db_values))
						{
							echo 'none';
							exit;
						}
						$return .= '</select><br />' . "\n";
						$return .= '<span id="sff_dnm_c1" class="sff_dnm_counter"></span>' . "\n";
						$return .= '<select class="sff_dnm_s" id="sff_dnm_s1"></select>' . "\n";
						$return .= '</td>' . "\n";

						// Arrow buttons
						$return .= '<td>' . "\n";
						$return .= '<button class="button-secondary sff_dnm_trans_button" id="sff_dnm_to2" type="button">&nbsp;&gt;&nbsp;</button>' . "\n";
						$return .= '<button class="button-secondary sff_dnm_trans_button" id="sff_dnm_ato2" type="button">&nbsp;&gt;&gt;&nbsp;</button>' . "\n";
						$return .= '<button class="button-secondary sff_dnm_trans_button" id="sff_dnm_ato1" type="button">&nbsp;&lt;&lt;&nbsp;</button>' . "\n";
						$return .= '<button class="button-secondary sff_dnm_trans_button" id="sff_dnm_to1" type="button">&nbsp;&lt;&nbsp;</button>' . "\n";
						$return .= '</td>' . "\n";

						// Displayed values
						$db_values = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->spoton_sff_values . ' WHERE fid = %d ORDER BY `order` ASC, value ASC', $fd->fid));
						$return .= '<td>' . "\n";
						$return .= '<span style="visibility: hidden;">' . __('Filter:', $this->domain) . ' <input type="text" id="sff_dnm_f2" /> <button type="button" class="button-secondary" id="sff_dnm_f2_clear">X</button><br />' . '</span>' . "\n";
						$return .= '<select class="sff_dnm_v" id="sff_dnm_v2" name="sff_dnm_values[]" multiple="multiple">' . "\n";
						foreach ($db_values as $value)
						{
							$return .= '<option value="' . esc_attr(preg_replace('/[\s]+/ui', '_', $value->value)) . '">' . esc_html($value->value) . '</option>' . "\n";
						}
						$return .= '</select><br />' . "\n";
						$return .= '<span id="sff_dnm_c2" class="sff_dnm_counter"></span>' . "\n";
						$return .= '<select class="sff_dnm_s" id="sff_dnm_s2"></select>' . "\n";
						$return .= '</td>' . "\n";

						// Order buttons
						$return .= '<td>' . "\n";
						$return .= '<button class="button-secondary sff_dnm_trans_button" id="sff_dnm_up" type="button">&nbsp;&uarr;&nbsp;</button>' . "\n";
						$return .= '<button class="button-secondary sff_dnm_trans_button" id="sff_dnm_down" type="button">&nbsp;&darr;&nbsp;</button>' . "\n";
						$return .= '</td>' . "\n";

						// Default values
						if (empty($fd->def_value) || 0 == sizeof($db_values))
						{
							$return .= '<td>' . "\n";
							$return .= __('<em>No default value set</em>', $this->domain) . "\n";
							$return .= '<button class="button-secondary" id="sff_dnm_get_val" type="button">' . __('Get values', $this->domain) . '</button>';
							$return .= '</td>' . "\n";
						}
						else
						{
							$return .= '<td>' . "\n";
							$return .= '<select name="sff_dnm_def_value">' . "\n";
							$return .= '<option value="0">' . __('No default value', $this->domain) . '</option>' . "\n";
							foreach ($db_values as $value)
							{
								$selected = ($value->value == $fd->def_value) ? ' selected="selected" ' : '';
								$return .= '<option value="' . esc_attr(preg_replace('/[\s]+/ui', '_', $value->value)) . '"' . $selected . '>' . esc_html($value->value) . '</option>' . "\n";
							}
							$return .= '</select>' . "\n";
							$return .= '</td>' . "\n";
						}

						$return .= '<input type="hidden" name="sff_dnm_fid" value="' . (int) $fd->fid . '" />';
						$return .= '<input type="hidden" name="sff_dnm_filtered" value="' . (int) $fd->filter . '" />';

						$return .= '</tr>' . "\n";

						echo $return;

				}

			break;
		}

		exit;

	}

	public function sff_save_post($options)
	{
		return $options;
	}

	private static function sff_normalize_value($value)
	{
		return str_replace('_', ' ', $value);
	}

	private static function sff_normalize_post_value($value)
	{
		return trim(stripslashes($value));
	}

	public function sff_post($form_name)
	{
		if (isset($_POST['save_' . $form_name]))
		{
			global $wpdb;

			$sff_fields = $wpdb->get_results('SELECT title, fid, filter, def_value FROM ' . $wpdb->spoton_sff_fields . ' ORDER BY fid ASC', OBJECT_K);

			// Update static fields
			$sff_static_fields = array(
				'spto_sff_pricefrom' => 'Price From',
				'spto_sff_priceto'	 => 'Price To',
				'spto_sff_bedrooms'	 => 'Beds',
				'spto_sff_bathrooms' => 'Baths',
				'spto_sff_squarefeet' => 'Square Feet',
				'spto_sff_acreagefrom' => 'Acreage From',
				'spto_sff_acreageto'  => 'Acreage To',
				'spto_sff_market'	 => 'Time on Market'
			);

			foreach ($sff_static_fields as $post_key => $sf)
			{
				$filtering = (isset($_POST[$post_key . '_ft'])) ? 1 : 0;
				if (isset($_POST[$post_key]) && ($_POST[$post_key] != $sff_fields[$sf]->def_value || $filtering != $sff_fields[$sf]->filter))
				{
					$wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->spoton_sff_fields . ' SET filter = %d, def_value = %s WHERE fid = %d', $filtering, self::sff_normalize_post_value($_POST[$post_key]), $sff_fields[$sf]->fid));
				}
			}

			// Update dynamic fields
			if (!empty($_POST['sff_dnm_fid']))
			{
				$fid = (int) $_POST['sff_dnm_fid'];
				if (!empty($fid))
				{
					$displayed_values = (isset($_POST['sff_dnm_values'])) ? (array) $_POST['sff_dnm_values'] : array();
					$def_value = (!empty($_POST['sff_dnm_def_value']) && in_array($_POST['sff_dnm_def_value'], $displayed_values)) ? self::sff_normalize_post_value($_POST['sff_dnm_def_value']) : 0;
					$filtering = (isset($_POST['sff_dnm_enb_filter'])) ? 1 : 0;

					// Update default value and filtering enability
					$wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->spoton_sff_fields . ' SET filter = %d, def_value = %s WHERE fid = %d', $filtering, self::sff_normalize_value($def_value), $fid));

					// Delete values that are no longer used
					$db_values = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->spoton_sff_values . ' WHERE fid = %d ORDER BY `order` ASC, value ASC', $fid));
					foreach ($db_values as $db_value)
					{
						$saved_value = str_replace(' ', '_', $db_value->value);
						$del_in = '';
						if (!in_array($saved_value, $displayed_values))
						{
							// Add this value to the delete queue
							$del_in .= (int) $db_value->vid . ',';
						}
						// If we have something to delete, process
						if (!empty($del_in))
							$wpdb->query('DELETE FROM ' . $wpdb->spoton_sff_values . ' WHERE vid IN (' . trim($del_in, ',') . ')');
					}

					// Insert new values
					$query = 'INSERT INTO ' . $wpdb->spoton_sff_values . ' (fid, value, `order`) VALUES ';
					foreach ($displayed_values as $order => $value)
					{
						$value = self::sff_normalize_post_value($value);
						$query .= $wpdb->prepare('(%d, %s, %d), ', $fid, self::sff_normalize_value($value), $order);
					}
					$query = trim($query, ', ');
					$query .= ' ON DUPLICATE KEY UPDATE `order` = VALUES(`order`) ';
					$wpdb->query($query);
				}
			}

			$message = __('Search form filter settings have been saved.', $this->domain);
			$this->add_notice($message, $form_name);

		}
	}

	public function build_dynamic_options($action)
	{
		global $wpdb, $spoton_pkey, $blog_id;

		$return = array();
		$adm_template_path = dirname(__FILE__) . '/spoton/adm';

		switch ($action)
		{
			case 'create_saved_search':

				include_once($adm_template_path . '/saved-search-box.php');

			break;

			case 'saved_searches':

				$searches = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->spoton_saved_search WHERE blog_id = %d ORDER BY qid DESC", $blog_id));

				if (0 < sizeof($searches))
				{
					$return = '<table class="bwp-table" cellpadding="0" cellspacing="0" border="0">' . "\n";
					$return .= '<thead><tr>' . __('<th>Search name</th><th>Search criteria</th><th>Actions</th>', $this->domain) . '</tr></thead>' . "\n";
					$return .= '<tbody>' . "\n";
					foreach ($searches as $search)
					{
						$return .= '<tr><td>' . esc_html($search->title) . '</td><td>' . esc_html($search->query) . '</td><td class="clear">' . 
						// Button
						'<input type="submit" class="button-secondary" name="delete_search_' . $search->qid . '" value="' . __('Delete this search', $this->domain) . '" />' . 
						// End of Button
						'</td></tr>' . "\n";
					}
					$return .= '</tbody>' . "\n";
					$return .= '</table>' . "\n";
				}
				else
					$return = __('No saved search found ...', $this->domain);

			break;

			case 'create_search_adm':

				include_once($adm_template_path . '/search-form-adm.php');

			break;

			case 'create_sff':

				include_once($adm_template_path . '/search-form-filters.php');

			break;

			case 'qsw_landing':

				$pages = get_pages(array('number' => 1000));
				$return = array();
				foreach ($pages as $page)
				{
					$return[$page->post_title] = $page->ID;
				}

			break;

		}
		
		return $return;
	}

	public function build_saved_search($form_name)
	{
		$this->build_dynamic_options('create_saved_search');
	}

	public function build_search_adm($form_name)
	{
		$this->build_dynamic_options('create_search_adm');
	}

	public function build_sff($form_name)
	{
		$this->build_dynamic_options('create_sff');
	}

	public function customize_buttons($buttons, $form_count)
	{
		if (1 == $form_count)
			$buttons = '<p class="submit"><input type="submit" class="button-primary" name="searchsubmit" id="searchsubmit" value="' . __('Save / Update Search', $this->domain) . '" /> &nbsp;<input type="button" class="button-secondary" name="resetsubmit" id="resetsubmit" value="' . __('Reset Data', $this->domain) . '" /></p>';
		else if ('last' == $form_count)
			$buttons = '';
		return $buttons;
	}
  public function customize_general_buttons($buttons, $form_count)
    {
        if (1 == $form_count)
        $buttons = '<p class="submit"><input type="submit" class="button-primary" name="save_' . $form->form_name . '" value="' . __('Save All Changes', $this->domain) . '" /> &nbsp;<input type="submit" class="button-secondary" name="validate_pkey" value="' . __('Validate Provider Key', $this->domain) . '"  OnClick="pkey_validate();"/> &nbsp;<input type="submit" class="button-secondary" name="reset_' . $form->form_name . '" value="' . __('Reset to Defaults', $this->domain) . '" /></p>';
        
        return $buttons;
    }
	public function customize_frm_adm_buttons($buttons)
	{
		return '<p class="submit"><input type="submit" class="button-primary" name="mapviewsubmit" id="mapviewsubmit" value="' . __('Save Settings', $this->domain) . '" /></p>';
	}

	public function customize_sff_buttons($buttons)
	{
		return '<p class="submit"><input type="submit" class="button-primary" name="save_spoton_idx_sff" id="save_spoton_idx_sff" value="' . __('Save Settings', $this->domain) . '" /></p>';
	}

	private static function append_adm_string($key, $use_value = false, $else_value = '0^')
	{
		if (!empty($_POST[$key])) 
			$view = ($use_value) ? trim(strip_tags(stripslashes($_POST[$key]))) . '^' : '1^';
		else
			$view = $else_value;
		return $view;
	}

	public static function add_puc_query_arg($query)
	{
		$query['secret'] = 'foo';
		return $query;
	}

	public static function add_puc_options($options)
	{
		$options['sslverify'] = false;
		return $options;
	}

	private function check_for_updates()
	{
		// Load auto update plugin
		if (/*'yes' == $this->options['enable_update_notify']*/1 == 1)
		{
			require_once(SPOTON_IDX_LIB_PATH . '/PluginUpdateChecker.php');
			add_filter('puc_request_info_query_args-spoton-idx', array($this, 'add_puc_query_arg'));
			add_filter('puc_request_info_options-spoton-idx', array($this, 'add_puc_options'));
			$UpdateChecker = new PluginUpdateChecker(
				'https://xpioimages.blob.core.windows.net/spotonconnect/info.json', 
				$this->get_pwpd('file')
			);
		}
	}

	/**
	 * Add a button to the TinyMCE visual editor to simplify the process of posting shortcode into post content.
	 */
	public function add_editor_button()
	{
		// Don't bother doing this stuff if the current user lacks permissions
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
			return;
		// Add only in Rich Editor mode
		if (get_user_option('rich_editing') == 'true')
		{
			add_filter("mce_external_plugins", array($this, 'add_editor_plugin'));
			add_filter('mce_buttons', array($this, 'register_editor_button'));
		}
	}

	/**
	 * Insert buttons
	 */
	public static function register_editor_button($buttons)
	{
		array_push($buttons, "separator", 'Spoton_IDX');
		return $buttons;
	}

	/**
	 * Load the TinyMCE plugin: editor_plugin.js
	 */
	public function add_editor_plugin($plugin_array)
	{
		$plugin_array['Spoton_IDX'] = plugin_dir_url($this->get_pwpd('file')) . '/lib/BWPP/buttons/editor_plugin.js';
		return $plugin_array;
	}

	/**
	 * Add a page template to the global template files of the current theme
	 */
	public function add_page_meta_box()
	{
		global $wp_meta_boxes;
		if (!is_admin())
			return;
		if (isset($wp_meta_boxes['page']['side']['core']['pageparentdiv']))
			$wp_meta_boxes['page']['side']['core']['pageparentdiv']['callback'] = array($this, 'build_page_meta_box');
		else if (isset($wp_meta_boxes['page']['side']['sorted']['pageparentdiv']))
			$wp_meta_boxes['page']['side']['sorted']['pageparentdiv']['callback'] = array($this, 'build_page_meta_box');
	}

	public function build_page_meta_box($post)
	{
		$post_type_object = get_post_type_object($post->post_type);
		if ($post_type_object->hierarchical)
		{
			$pages = wp_dropdown_pages(array('post_type' => $post->post_type, 'exclude_tree' => $post->ID, 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('(no parent)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
			if (!empty($pages))
			{
?>
<p><strong><?php _e('Parent') ?></strong></p>
<label class="screen-reader-text" for="parent_id"><?php _e('Parent') ?></label>
<?php echo $pages; ?>
<?php
			} // end empty pages check
		} // end hierarchical check.
		if ('page' == $post->post_type)
		{
			$template = !empty($post->page_template) ? $post->page_template : false;
?>
<p><strong><?php _e('Template') ?></strong></p>
<label class="screen-reader-text" for="page_template"><?php _e('Page Template') ?></label>
	<select name="page_template" id="page_template">
		<option value='default'><?php _e('Default Template'); ?></option>
		<?php page_template_dropdown($template); ?>
<?php foreach (self::$_ppt_templates as $ppt_template) {
		$selected = ($ppt_template[1] == $post->page_template) ? ' selected="selected"' : ''; ?>
		<option<?php echo $selected; ?> value='<?php echo $ppt_template[1]; ?>'><?php echo $ppt_template[0]; ?></option>
<?php } ?>
	</select>
<?php
		} 
?>
<p><strong><?php _e('Order') ?></strong></p>
<p>
	<label class="screen-reader-text" for="menu_order"><?php _e('Order') ?></label>
	<input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
<p>
<?php if ( 'page' == $post->post_type ) _e( 'Need help? Use the Help tab in the upper right of your screen.' ); ?>
</p>
<?php
	}

	private function is_search_template($template)
	{
		foreach (self::$_ppt_templates as $ppt_template)
		{
			if (in_array($template, $ppt_template))
				return $ppt_template[1];
		}

		return false;
	}

	public function saving_page_template()
	{
		if (!empty($_POST['page_template']))
		{
			$ppt_template = $this->is_search_template($_POST['page_template']);
			if ($ppt_template)
			{
				self::$_spt = $ppt_template;
				$_POST['page_template'] = 'default';
			}
		}
	}

	public function save_page_template($post_ID, $post)
	{
		if (!empty(self::$_spt))
			update_post_meta($post_ID, '_wp_page_template', self::$_spt);
	}

	public function handle_misc_actions()
	{
		global $wpdb, $blog_id;

		$is_delete = array_search('Delete this search', $_POST);

		if (isset($_POST["searchsubmit"]))
		{
			$form_name = 'spoton_idx_saved';
			check_admin_referer($form_name);
			// if search_title is empty, issue an error
			$_POST["entersearchname"] = trim(strip_tags(stripslashes($_POST["entersearchname"])));
			if (empty($_POST["entersearchname"]))
				$this->add_error_mess(__('Please enter a Search name.', $this->domain), $form_name);
			else
			{
				$pkey = $this->get_o('provider_key');
				$query = spoton_get_odata_query($pkey);
				$search_title = $_POST["entersearchname"];
				//$pkey . '_' . preg_replace('/\s+/', '_', $_POST["entersearchname"]);
				if (!empty($search_title))
				{
					$existed = $wpdb->get_var($wpdb->prepare('SELECT qid FROM ' . $wpdb->spoton_saved_search . ' WHERE title = %s AND blog_id = %d', $search_title, $blog_id));
					if (!empty($existed))
					{
						$wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->spoton_saved_search . ' SET query = %s WHERE qid = %d', $query, $existed));
						$message = __('Saved search has been successfully updated.', $this->domain);
					}
					else
					{
						$wpdb->query($wpdb->prepare('INSERT INTO ' . $wpdb->spoton_saved_search . ' (blog_id, title, query) VALUES (%d, %s, %s)', $blog_id, $search_title, $query));
						$message = __('New saved search has been successfully added.', $this->domain);
					}
					$this->add_notice($message, $form_name);
				}
			}
		}
		else if ($is_delete)
		{
			$form_name = 'spoton_idx_saved';
			check_admin_referer($form_name);
			$qid = (int) str_replace('delete_search_', '', $is_delete);
			if (!empty($qid))
			{
				$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->spoton_saved_search . ' WHERE qid = %d AND blog_id = %d', $qid, $blog_id));
				$message = __('Saved search has been successfully deleted.', $this->domain);
				$this->add_notice($message, $form_name);
			}
		}
		else if (isset($_POST["mapviewsubmit"]))
		{
			$form_name = 'spoton_idx_frm_adm';
			check_admin_referer($form_name);
			$adm_string = $this->get_o('search_adm_string');
			$view = '';
			$view .= self::append_adm_string('spto_hidemap');
			$view .= self::append_adm_string('spto_hidelist');
			$view .= self::append_adm_string('spto_maplocation', true, '0,0^');
			$view .= self::append_adm_string('spto_zoomlevel', true, '20^');
			$view .= self::append_adm_string('spto_hidestate');
			$view .= self::append_adm_string('spto_hidecounty');
			$view .= self::append_adm_string('spto_hidecity');
			$view .= self::append_adm_string('spto_hidezipcode');
			$view .= self::append_adm_string('spto_hidepricefrom');
			$view .= self::append_adm_string('spto_hidepriceto');
			$view .= self::append_adm_string('spto_hidebeds');
			$view .= self::append_adm_string('spto_hidebath');
			$view .= self::append_adm_string('spto_hideaceragefrom');
			$view .= self::append_adm_string('spto_hideacerageto');
			$view .= self::append_adm_string('spto_hidesquarefeet');
			$view .= self::append_adm_string('spto_hidetimeonmarket');
			$view .= self::append_adm_string('spto_hideproperty');
			$view .= self::append_adm_string('spto_hidesortby');            
			$view .= self::append_adm_string('spto_hideArea');
			$view .= self::append_adm_string('spto_hideSubDiv');
			$view .= self::append_adm_string('spto_hideSchDist');
			$view .= self::append_adm_string('spto_hideEleSch');
			$view .= self::append_adm_string('spto_hideJuHiSch');
			$view .= self::append_adm_string('spto_hideHiSch');

			$this->update_options(SPOTON_IDX_FORM_ADM, array('search_adm_string' => trim($view), 'enable_sff' => $this->get_o('enable_sff')));
			$message = __('Search form admin settings have been saved.', $this->domain);
			$this->add_notice($message, $form_name);
		}
	}

	protected function build_forms()
	{
		global $wpdb;

		$page			= $this->admin_page;
		$form			= array();
		$form_values	= array();
		$this->op		= new BWP_Admin_OptionPages($this->form_tabs, $this, $this->domain);

		/*----------First Page------------*/
		if (SPOTON_IDX_GENERAL == $page)
		{
			$this->op->set_current_tab(1);
			$options = $this->get_oobj($page);
			/*--Form1--*/
		$form = array(
			'items'	=> array(/*'heading', 'checkbox', 'input', */'heading', 'input', 'heading', 'checkbox', 'checkbox', 'heading', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'heading', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'input', 'input', 'input', 'heading', 'input', 'input'),
			'item_labels' => array(
				/*__('Notification Settings', $this->domain),
				__('Notify me when there are updates for this plugin', $this->domain),
				__('Email to receive update notifications', $this->domain),*/
				__('Subscription', $this->domain),
				__('Provider Key', $this->domain),
				__('Widget Settings', $this->domain),
				__('Enable Featured Listing Widget?', $this->domain),
				__('Enable Quick Search Widget?', $this->domain),
				__('Module Settings', $this->domain),
				__('Enable XML Sitemap', $this->domain),
				__('Enable Listing Alert', $this->domain),
				__('Enable AURL', $this->domain),
				__('Enable Request Information Form (Contact Form)', $this->domain),
				__('Search Engine Optimization Settings', $this->domain),
				__('Enable Search Engine Optimization', $this->domain),
				__('Add blog name after property name in <code>&lt;title&gt;</code> tag?', $this->domain),
				__('Enable Robot meta tag?', $this->domain),
				__('Enable OpenGraph meta tags?', $this->domain),
				__('Enable Canonical link tag?', $this->domain),
				__('Google Site Verification code', $this->domain),
				__('Facebook Application ID', $this->domain),
				__('Default Thumbnail', $this->domain),
				__('Miscellaneous Settings', $this->domain),
				__('Guests can view at most', $this->domain),
				__('After viewing', $this->domain)
			),
			'item_names'	=> array(/*'h1', 'cb1', 'update_notify_email', */'h2', 'provider_key', 'h5', 'cb10', 'cb9', 'h3', 'cb2', 'cb3', 'cb11', 'cb12', 'h4', 'cb4', 'cb8', 'cb5', 'cb6', 'cb7', 'input_seo_google_verify', 'input_seo_fb_appid', 'input_seo_default_thumb', 'h6', 'input_guest_views', 'input_guest_prmt_views'),
			'checkbox' => array(
				'cb1' => array(__('', $this->domain) => 'enable_update_notify'),
				'cb2' => array(__('<em>This module builds XML Sitemaps on the fly to assit Google in indexing properties on your website.</em>', $this->domain) => 'enable_xml_sitemap'),
				'cb3' => array(__('<em>This module allows registered users to create their own listing alerts and subscribe to them. Use</em>', $this->domain) => 'enable_listing_alert'),
				'cb4' => array(__('', $this->domain) => 'enable_seo_feature'),
				'cb5' => array(__('', $this->domain) => 'enable_seo_meta_robot'),
				'cb6' => array(__('<em>Add OpenGraph meta tags to your header.</em>', $this->domain) => 'enable_seo_meta_og'),
				'cb7' => array(__('<em>Add a <code>&lt;link rel=\'canonical\' /&gt;</code> tag to your header.</em>', $this->domain) => 'enable_seo_canonical'),
				'cb8' => array(sprintf(__('<em>Your blog name is <code>%s</code>.</em>', $this->domain), esc_attr(get_bloginfo('name'))) => 'enable_seo_blogname'),
				'cb9' => array(__('and use', $this->domain) => 'enable_widget_qsw'),
				'cb10' => array('' => 'enable_widget_flw'),
				'cb11' => array(__('<em>"AURL" stands for <strong>Advanced User Registration/Login</strong>.</em>', $this->domain) => 'enable_module_aurl'),
				'cb12' => array(__('<em>This module allows you to add a modal contact form widget to a property\'s details page, which visitors can use to request more information about that particular property.</em>', $this->domain) => 'enable_module_rmif')
			),
			'input' => array(
				'update_notify_email' => array('size' => 30, 'label' => __('If leave blank, your admin email will be used.', $this->domain)),
				'provider_key' => array('size' => 5, 'label' => __('', $this->domain)),
				'input_seo_google_verify' => array('size' => 50, 'label' => __('Verify your site with Google.', $this->domain)),
				'input_seo_fb_appid' => array('size' => 50, 'label' => sprintf(__('Register for a Facebook Application ID <a href="%s" target="_blank">here</a>.', $this->domain), 'https://developers.facebook.com/apps')),
				'input_seo_default_thumb' => array('size' => 80, 'label' => __('Full URL to the image you would like to use as a backup when there\'s no post thumbnail. Leave blank to disable.', $this->domain)),
				'input_guest_views' => array('size' => 5, 'label' => __('property details page(s) without logging in. After this number has been reached, guests must register for an account for further access to property details pages. Set to 0 to disable this feature.', $this->domain)),
				'input_guest_prmt_views' => array('size' => 5, 'label' => __('property details page(s) without logging in, guests will be prompted to register for an account. If not interested, he or she can simply close the prompt. Set to 0 or disable AURL module to disable this feature.', $this->domain))
			),
			'select' => array(
				'select_qsw_landing' => self::build_dynamic_options('qsw_landing'),
				'select_qsw_type'	 => array(
					__('Default&nbsp;', $this->domain) => 'def',
					__('List&nbsp;', $this->domain) => 'list',
					__('Map&nbsp;', $this->domain) => 'map'
				),
				'select_la_wizard_page' => self::build_dynamic_options('qsw_landing')
			),
			'inline_fields' => array(
				'cb9' => array('select_qsw_landing' => 'select'),
				'select_qsw_landing' => array('select_qsw_type' => 'select'),
				'cb3' => array('select_la_wizard_page' => 'select')
			),
			'post' => array(
				'select_qsw_landing' => __('as the landing page for the search form. Use the ', $this->domain),
				'select_qsw_type'	=> __('template for the selected landing page.', $this->domain),
				'select_la_wizard_page'	=> __('as the wizard page for Listing Alert.', $this->domain)
			),
			'heading' => array(
				'h1' => __('<em>Receive alerts when updates are available.</em>', $this->domain),
				'h2' => __('<em>You will be provided a key in an email.</em>', $this->domain),
				'h3' => __('<em>You can enable/disable additional modules here. Please note that you might need to refresh the page after the changes are saved for them to fully take effect.</em>', $this->domain),
				'h4' => __('<em>Optimize your property pages for Search Engines.</em>', $this->domain),
				'h5' => __('<em>Customize all provided widgets here.</em>', $this->domain),
				'h6' => __('<em>Other options that fit no where.</em>', $this->domain)
			),
			'divider' => array(
				/*'update_notify_email' => array(),*/
				'provider_key' => array(),
				'input_seo_default_thumb' => array(),
				'cb9' => array(),
				'cb12' => array()
			)
		);
            add_filter('bwp_opf_multi_submit_button_spoton_idx_general', array($this, 'customize_general_buttons'), 10, 2);
			$this->op->add_form('spoton_idx_general', $form, $options, 'save_all');
			/*--Form1--*/
		}
		else if (SPOTON_IDX_SAVED == $page)
		{
			$this->op->set_current_tab(2);

		$form = array(
			'items'	=> array('heading', 'input', 'heading'),
			'item_labels' => array(
				__('Create a new Saved Search', $this->domain),
				__('Search name', $this->domain),
				__('All saved searches', $this->domain)
			),
			'item_names'	=> array('h1', 'entersearchname', 'h2'),
			'heading' => array(
				'h1' => __('<em>Create custom searches to add to any page or post.</em>', $this->domain),
				'h2' => __('<em>All saved searches will be listed below.</em>', $this->domain)
			),
			'input' => array(
				'entersearchname' => array('size' => 40, 'label' => __('This will be used to identify this saved search later on.', $this->domain))
			),
			'divider' => array(
				'entersearchname' => array()
			),
			'container' => array(
				'h2' => $this->build_dynamic_options('saved_searches')
			)
		);
			
			add_action('bwp_opa_before_field_spoton_idx_saved_entersearchname', array($this, 'build_saved_search'));
			add_filter('bwp_opf_multi_submit_button_spoton_idx_saved', array($this, 'customize_buttons'), 10, 2);

			$this->op->add_form('spoton_idx_saved', $form);
		}
		else if (SPOTON_IDX_FORM_ADM == $page)
		{
			$this->op->set_current_tab(3);

		$form31 = array(
			'items'	=> array('heading'),
			'item_labels' => array(
				__('Search Form Administration', $this->domain)
			),
			'item_names'	=> array('h1'),
			'heading' => array(
				'h1' => __('<em>Configure your property search form preferences.</em>', $this->domain)
			)
		);
			
			add_action('bwp_opa_before_submit_button_spoton_idx_frm_adm', array($this, 'build_search_adm'));
			add_filter('bwp_opf_submit_button_spoton_idx_frm_adm', array($this, 'customize_frm_adm_buttons'));

			$this->op->add_form('spoton_idx_frm_adm', $form31);

		$options = $this->get_oobj($page);
		$options->ignores('search_adm_string', 1);
		$form32 = array(
			'items'	=> array('heading', 'checkbox'),
			'item_labels' => array(
				__('Search Form Filters', $this->domain),
				__('Enable Search Form Filters?', $this->domain)
			),
			'item_names'	=> array('h1', 'cb1'),
			'heading' => array(
				'h1' => __('<em>Customize values used in search forms.</em>', $this->domain)
			),
			'checkbox' => array(
				'cb1' => array(__('<em>Use this option to enable/disable the search form filter functionality entirely. You can choose to enable/disable filtering for each field below.</em>', $this->domain) => 'enable_sff')
			)
		);
			
			add_action('bwp_opa_before_submit_button_spoton_idx_sff', array($this, 'build_sff'));
			add_filter('bwp_opf_submit_button_spoton_idx_sff', array($this, 'customize_sff_buttons'));

			$this->op->add_form('spoton_idx_sff', $form32, $options, 'save');

			do_action('sff_save_settings', 'spoton_idx_sff');
		}
		else if (SPOTON_IDX_LIST_ADM == $page)
		{
			$this->op->set_current_tab(4);

			$options = $this->get_oobj($page);

		$form = array(
			'items'	=> array('heading', 'checkbox','checkbox', 'checkbox', 'checkbox','input'),
			'item_labels' => array(
				__('Listing Display Administration', $this->domain),
				__('Hide Office in Search Result', $this->domain),
				__('Hide Agent in Search Result', $this->domain),
				__('Hide Office in Listing Details', $this->domain),
				__('Hide Agent in Listing Details', $this->domain),
                __('Please Enter Company Name', $this->domain)
			),
			'item_names'	=> array('h1', 'cb1', 'cb2', 'cb3', 'cb4','input_company_name'),
			'heading' => array(
				'h1' => __('<em>Configure how your Listings will display.</em>', $this->domain)
			),
			'checkbox' => array(
				'cb1' => array('' => 'hide_list_search_office'),
				'cb2' => array('' => 'hide_list_search_agent'),
				'cb3' => array('' => 'hide_list_details_office'),
				'cb4' => array('' => 'hide_list_details_agent')
			),
            'input' => array(
				
				'input_company_name' => array('size' => 50, 'label' => __('', $this->domain)))
		);

			$this->op->add_form('spoton_idx_list_adm', $form, $options, 'save');

		}
	}

	public function build_admin_pages()
	{
		if (SPOTON_IDX_FORM_ADM != $this->admin_page && SPOTON_IDX_LIST_ADM != $this->admin_page)
			add_filter('bwp_op_submit_button', create_function('', 'return "";'));
		$this->op->generate_html_forms();
	}

}

endif;

?>