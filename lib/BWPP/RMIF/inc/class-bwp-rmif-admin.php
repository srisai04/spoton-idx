<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('BWP_RMIF')) :

class BWP_RMIF extends BWP_PluginBase {

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
		if ($this->is_admin_page() && BWP_RMIF_GENERAL == $this->admin_page)
		{
			wp_enqueue_script('form-reset-js', BWP_FRW_ADM_JS . '/form-reset.js', array('jquery'), BWP_Init::$rev);
			wp_localize_script('form-reset-js', BWP_FRW_FORM_RESET_L10N, $this->default_options);
		}
	}

	public function build_menus()
	{
		add_submenu_page(SPOTON_IDX_GENERAL, __('Contact Form', $this->domain), __('Contact Form', $this->domain), $this->cap, BWP_RMIF_GENERAL, array($this, 'build_admin_pages'));
	}

	protected function build_forms()
	{
		global $wpdb;

		$page			= $this->admin_page;
		$form			= array();
		$form_values	= array();
		$this->op		= new BWP_Admin_OptionPages($this->form_tabs, $this, $this->domain);

		/*----------First Page------------*/
		if (BWP_RMIF_GENERAL == $page)
		{
			$options = $this->get_oobj($page);

		$form = array(
			'items'	=> array('heading', 'input', 'input', 'input', 'input', 'input', 'textarea', 'heading', 'checkbox', 'input', 'input', 'input', 'textarea'),
			'item_labels' => array(
				__('Request More Info Email Settings', $this->domain),
				__('Send to Email', $this->domain),
				__('From Name', $this->domain),
				__('From Email', $this->domain),
				__('BCC', $this->domain),
				__('Subject', $this->domain),
				__('Message', $this->domain),
				__('Email Notification to Users', $this->domain),
				__('Enable email notifications to users?', $this->domain),
				__('From Name', $this->domain),
				__('From Email', $this->domain),
				__('Subject', $this->domain),
				__('Message', $this->domain)
			),
			'item_names'	=> array('h1', 'inp_rmif_email', 'inp_rmif_from_name', 'inp_rmif_from_email', 'inp_rmif_bcc', 'inp_rmif_subject', 'inp_rmif_message', 'h2', 'cb1', 'inp_are_from_name', 'inp_are_from_email', 'inp_are_subject', 'inp_are_message'),
			'checkbox' => array(
				'cb1' => array(__('<em>Send users an automatic response when they submit Request More Info Form.</em>', $this->domain) => 'enable_auto_response')
			),
			'input' => array(
				'inp_rmif_email' => array('size' => 50, 'label' => __('Set to your admin email by default.', $this->domain)),
				'inp_rmif_from_name' => array('size' => 50, 'label' => __('Set to your blog name by default.', $this->domain)),
				'inp_rmif_from_email' => array('size' => 50, 'label' => __('Set to a dummy no-reply email by default.', $this->domain)),
				'inp_rmif_reply_to' => array('size' => 50, 'label' => ''),
				'inp_rmif_bcc' => array('size' => 50, 'label' => __('Separate email addresses using semicolons.', $this->domain)),
				'inp_rmif_subject' => array('size' => 80, 'label' => ''),
				'inp_are_from_name' => array('size' => 50, 'label' => __('Set to your blog name by default.', $this->domain)),
				'inp_are_from_email' => array('size' => 50, 'label' => __('Set to your admin email by default.', $this->domain)),
				'inp_are_subject' => array('size' => 80, 'label' => '')
			),
			'textarea' => array(
				'inp_rmif_message' => array('cols' => 80, 'rows' => 7),
				'inp_are_message' => array('cols' => 80, 'rows' => 7)
			),
			'heading' => array(
				'h1' => __('<em>Customize various settings for emails that are sent from users to you via the Contact Form.</em>', $this->domain),
				'h2' => __('<em>Notify users of their successful more property info requests.</em>', $this->domain)
			),
			'inline' => array(
				'inp_rmif_message' => '<br /><br />' . __('Available form fields: <code>{name}</code>, <code>{email}</code>, <code>{phone}</code> and <code>{message}</code>', $this->domain),
				'inp_are_message' => '<br /><br />' . __('Available form fields: <code>{name}</code>, <code>{email}</code>, <code>{phone}</code> and <code>{message}</code>', $this->domain)
			),
			'divider' => array(
				'inp_rmif_message' => array()
			)
		);
			$this->op->add_form($page, $form, $options, 'save_all');
		}
	}

	public function build_admin_pages()
	{
		add_filter('bwp_op_submit_button', create_function('', 'return "";'));
		$this->op->generate_html_forms();
	}

}

endif;