<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('BWP_AURL')) :

class BWP_AURL extends BWP_PluginBase {

	public function __construct($plugin_data, $plugin_wp_data)
	{
		if (!parent::__construct($plugin_data, $plugin_wp_data))
			return false;

		require_once(dirname(__FILE__) . '/def-constants.php');
		require_once(dirname(__FILE__) . '/def-options.php');
		require_once(dirname(__FILE__) . '/common-functions.php');

		$this->init();
	}

	protected function add_hooks()
	{
	}

	protected function add_admin_hooks()
	{
	}

	public function enqueue_media()
	{
		// Reset to default
		if ($this->is_admin_page() && BWP_AURL_GENERAL == $this->admin_page)
		{
			wp_enqueue_script('form-reset-js', BWP_FRW_ADM_JS . '/form-reset.js', array('jquery'), BWP_Init::$rev);
			wp_localize_script('form-reset-js', BWP_FRW_FORM_RESET_L10N, $this->default_options);
		}
	}

	public function build_menus()
	{
		add_submenu_page(SPOTON_IDX_GENERAL, __('Advanced User Registration/Login', $this->domain), __('Advanced User Registration', $this->domain), $this->cap, BWP_AURL_GENERAL, array($this, 'build_admin_pages'));
	}

	private function build_dynamic_options($option = '')
	{
		$return = array(__('a modal popup', $this->domain) => 0);
		$pages = get_pages(array('number' => 1000));
		foreach ($pages as $page)
			$return[$page->post_title] = $page->ID;

		return $return;
	}

	protected function build_forms()
	{
		global $wpdb;

		$page			= $this->admin_page;
		$form			= array();
		$form_values	= array();
		$this->op		= new BWP_Admin_OptionPages($this->form_tabs, $this, $this->domain);

		/*----------First Page------------*/
		if (BWP_AURL_GENERAL == $page)
		{
			$options = $this->get_oobj($page);

		$form = array(
			'items'	=> array('heading', 'checkbox', 'checkbox', 'checkbox', 'input', 'input'),
			'item_labels' => array(
				__('General Settings', $this->domain),
				__('This module will', $this->domain),
				__('Enable Privacy Policy in form', $this->domain),
				__('Enable Terms of Service in form', $this->domain),
				__('Your Business Name', $this->domain),
				__('Your Business State', $this->domain)
			),
			'item_names'	=> array('h1', 'cb1', 'cb2', 'cb3', 'inp_com_name', 'inp_com_state'),
			'checkbox' => array(
				'cb1' => array(__('<em>log users in automatically after they have successfully registered.</em>', $this->domain) => 'enable_instant_login'),
				'cb2' => array(__('and use', $this->domain) => 'enable_pp_link'),
				'cb3' => array(__('and use', $this->domain) => 'enable_tos_link')
			),
			'input' => array(
				'inp_com_name' => array('size' => 50, 'label' => __('will be used in default Privacy Policy and TOS.', $this->domain)),
				'inp_com_state' => array('size' => 50, 'label' => __('will be used in default Privacy Policy and TOS.', $this->domain))
			),
			'select' => array(
				'sel_pp_page' => $this->build_dynamic_options(),
				'sel_tos_page' => $this->build_dynamic_options()
			),
			'post' => array(
				'sel_pp_page' => __('to display its contents.', $this->domain),
				'sel_tos_page' => __('to display its contents.', $this->domain)
			),
			'inline_fields' => array(
				'cb2' => array('sel_pp_page' => 'select'),
				'cb3' => array('sel_tos_page' => 'select')
			),
			'heading' => array(
				'h1' => __('<em>This module allows your visitors to register/login using fancy modal popup.</em>', $this->domain)
			)
		);
			$this->op->add_form($page, $form, $options, 'save');
		}
	}

	public function build_admin_pages()
	{
		$this->op->generate_html_forms();
	}

}

endif;