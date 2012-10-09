<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Admin_OptionPages extends BWP_Options {

	/**
	 * All the forms that we need to show on a particular page
	 */
	private $forms = array();

	/**
	 * Tabs to build
	 */
	private $form_tabs = array();

	/**
	 * Current tab
	 */
	private $current_tab;

	/**
	 * The current plugin for which we're building an option page
	 */
	private $current_plugin;

	/**
	 * Other things
	 */
	private $domain;

	/**
	 * Constructor
	 */
	public function __construct(array $form_tabs = array(), BWP_PluginBase $bwpp_obj, $domain)
	{
		$this->domain				= (string) $domain;
		$this->form_tabs			= $form_tabs;
		$this->current_plugin		= $bwpp_obj;
		do_action('bwp_opa_forms_init');
	}

	public function do_misc_form_actions($form_name)
	{
		do_action('bwp_opma_form_' . $form_name, $form_name);
	}

	/**
	 * Add a form
	 */	
	public function add_form($form_name, array $form = array(), BWP_Options $oo = NULL, $post_action = '')
	{
		// Type-casting
		$form_name   = (string) $form_name;
		$post_action = (string) $post_action;
		// Early filter that relates to this particular form
		do_action('bwp_opa_before_add_form_' . $form_name, $form_name);
		$form_values = (isset($oo->options)) ? $oo->options : array();
		$form_global_values = (isset($oo->site_o)) ? $oo->site_o : array();
		// Add the new form skeleton
		$new_form = new BWP_Admin_Forms($form_name, $post_action);
		$new_form->set_domain($this->domain);
		// Change form values according to user's actions
		if (!empty($post_action) && isset($oo))
		{
			if ('db' == $oo->type)
				$form_values = $new_form->handle_form_save($oo, $this->current_plugin);
			else
			{
				if ('import' == $oo->type)
					$new_form->set_form_enctype('multipart/form-data');
				$form_values = $new_form->handle_form_insert($oo);
			}
		}
		else if (!empty($post_action))
			$new_form->handle_form_misc_actions();

		$form = apply_filters('bwp_opf_before_add_form_' . $form_name, $form, $form_name);
		$new_form->populate_form($form, $form_values, $form_global_values);

		$this->forms[$form_name] = $new_form;
	}

	public function replace_form($form_name, array $form = array(), BWP_Options $oo = NULL, $post_action = '')
	{
		// Type-casting
		$form_name   = (string) $form_name;
		$post_action = (string) $post_action;
		// Replace the requested form if it exists
		if (isset($this->forms[$form_name]))
		{
			$old_form = $this->forms[$form_name];
			$form_values = (isset($oo) && $oo instanceof BWP_Options) ? $oo->options : array();
			$form_global_values = (isset($oo) && $oo instanceof BWP_Options) ? $oo->site_o : array();
			$old_form->replaced_by($form, $form_values, $form_global_values, $post_action);
			return true;
		}
	}

	public function add_form_style($form_name, $style)
	{
		$this->forms[$form_name]->style = $style;
	}

	public function set_current_tab($current_tab = 0)
	{
		$this->current_tab = (int) $current_tab;
	}

	public function kill_html_fields(array &$form, array $ids)
	{
		$in_keys = array('items', 'item_labels', 'item_names');
		foreach ($ids as $id)
			foreach ($in_keys as $key)
				unset($form[$key][$id]);
	}
	
	/**
	 * Generate all HTML forms for the current plugin page
	 */
	public function generate_html_forms()
	{
		$return_str = '<div class="bwp-wrap bwp-option-page-wrapper" style="padding-bottom: 20px;">' . "\n";
		if (sizeof($this->form_tabs) >= 2)
			$return_str .= apply_filters('bwp_admin_form_icon', '<div class="icon32" id="icon-options-general"><br></div>'  . "\n");
		else
			$return_str .= '<div class="icon32" id="icon-options-general"><br></div>';			
		
		if (sizeof($this->form_tabs) >= 2)
		{
			$count = 0;
			$return_str .= '<h2 class="bwp-option-page-tabs">' . "\n";
			$return_str .= apply_filters('bwp_admin_plugin_version', '') . "\n";
			foreach ($this->form_tabs as $title => $link)
			{
				$count++;
				$active = ($count == $this->current_tab) ? ' nav-tab-active' : '';
				$return_str .= '<a class="nav-tab' . $active . '" href="' . $link . '">' . $title . '</a>' . "\n";
			}
			$return_str .= '</h2>' . "\n";
		}
		else if (!isset($this->form_tabs[0]))
		{
			$title = array_keys($this->form_tabs);
			$return_str .= '<h2>' . $title[0] . '</h2>'  . "\n";
		}
		else
			$return_str .= '<h2>' . $this->form_tabs[0] . '</h2>'  . "\n";

		$return_str .= '<div class="bwp-option-box clear">' . "\n";

		// Begin generating each form
		foreach ($this->forms as $form)
		{
			// If this form has 'divider' items, we need to split HTML fields appropriately
			$dividers =  (isset($form->form['divider']) && is_array($form->form['divider'])) ? array_keys($form->form['divider']) : array();
			$form_style = (!empty($form->style)) ? ' style="' . $form->style . '" ' : '';
			$multi_form_style = (!empty($form->style) && 0 < sizeof($dividers)) ? ' style="' . $form->style . '" ' : '';

			if (0 == sizeof($dividers))
			{
				$return_str .= '<div class="bwp-option-box-inside"' .  $form_style . '>' . "\n";
				$return_str .= apply_filters('bwp_opf_before_form_' . $form->form_name, '');
				echo $return_str;
				do_action('bwp_opa_before_form_' . $form->form_name, $form->form_name);
			}
			else
				echo $return_str;

			$enctype = (!empty($form->form_enctype)) ? ' enctype="' . $form->form_enctype . '" ' : '';
			$return_str = '<form class="bwp-option-page" name="' . $form->form_name . '" method="post" action=""' . $enctype . $multi_form_style . '>'  . "\n";

			// Nonce
			$return_str .= wp_nonce_field($form->form_name, "_wpnonce", false, false) . "\n";
			$return_str .= apply_filters('bwp_opf_referrer_field_' . $form->form_name, wp_referer_field(false)) . "\n";

			$return_str .= '<ul>' . "\n";

			if (isset($form->form_items) && is_array($form->form_items))
			{
				// If this form needs to be divided, so be it
				if (0 < sizeof($dividers))
				{
					$return_str .= '<div class="bwp-option-box-inside">' . "\n";
					$return_str .= apply_filters('bwp_opf_before_form_' . $form->form_name, '');
					echo $return_str;
					do_action('bwp_opa_before_form_' . $form->form_name, $form->form_name);
				}
				else
					echo $return_str;
				// Reset the result
				$return_str = '';
				// Generate individual items
				$form_count = 0;
				foreach ($form->form_items as $key => $type)
				{
					if (!empty($form->form_item_names[$key]) && isset($form->form_item_labels[$key]))
					{
						$extra_classes = ('heading' == $type) ? ' bwp-li-heading' : ' bwp-li-item';
						// Before the field
						echo $return_str;
						do_action('bwp_opa_before_field_' . $form->form_name . '_' . $form->form_item_names[$key], $form->form_name);
						$return_str = '';
						// The field
						$return_str .= ('hidden' != $form->form_items[$key]) ? '<li class="clear' . $extra_classes . '">' . $form->generate_html_fields($type, $form->form_item_names[$key]) . '</li>' : $form->generate_html_fields($type, $form->form_item_names[$key]) . "\n";
						// After the field
						echo $return_str;
						do_action('bwp_opa_after_field_' . $form->form_name . '_' . $form->form_item_names[$key], $form->form_name);
						$return_str = '';
						if (in_array($form->form_item_names[$key], $dividers))
						{
							$form_count++;
							echo $return_str;
							do_action('bwp_opa_after_divided_form_' . $form->form_name . '_' . $form_count, $form->form_name);
							$return_str = '';
							$return_str .= apply_filters('bwp_opf_multi_submit_button_' . $form->form_name, '<p class="submit"><input type="submit" class="button-primary" name="save_' . $form->form_name . '" value="' . __('Save All Changes', $this->domain) . '" /> &nbsp;<input type="submit" class="button-secondary" name="reset_' . $form->form_name . '" value="' . __('Reset to Defaults', $this->domain) . '" /></p>', $form_count) . "\n";
							$return_str .= '</div>' . "\n";
							$return_str .= '<div class="bwp-option-box-inside">' . "\n";
						}
					}
				}
				// If this form needs to be divided, add the final Save all changes button
				if (0 < sizeof($dividers))
					$return_str .= apply_filters('bwp_opf_multi_submit_button_' . $form->form_name, '<p class="submit"><input type="submit" class="button-primary" name="save_' . $form->form_name . '" value="' . __('Save All Changes', $this->domain) . '" /> &nbsp;<input type="submit" class="button-secondary" name="reset_' . $form->form_name . '" value="' . __('Reset to Defaults', $this->domain) . '" /></p>', 'last') . "\n";
			}

			$return_str .= '</ul>' . "\n";		
			$return_str .= apply_filters('bwp_opf_before_submit_button_' . $form->form_name, '');
			echo $return_str;
			do_action('bwp_opa_before_submit_button_' . $form->form_name, $form->form_name);

			$submit = apply_filters('bwp_opf_submit_button_' . $form->form_name, '<p class="submit"><input type="submit" class="button-primary" name="save_' . $form->form_name . '" value="' . __('Save Changes', $this->domain) . '" /> &nbsp;<input type="submit" class="button-secondary" name="reset_' . $form->form_name . '" value="' . __('Reset to Defaults', $this->domain) . '" /></p>') . "\n";
			$return_str = apply_filters('bwp_op_submit_button', $submit) . "\n";
			$return_str .= '</form>' . "\n";

			if (0 == sizeof($dividers))
				$return_str .= '</div>' . "\n";
		}

		$return_str .= '<div class="clear"><!-- --></div>' . "\n";
		$return_str .= '</div>' . "\n";
		$return_str .= '</div>' . "\n";

		echo $return_str;
	}
}
?>