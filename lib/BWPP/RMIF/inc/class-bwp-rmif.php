<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('BWP_RMIF')) :

class BWP_RMIF extends BWP_PluginBase {

	private static $_ver = '';

	public function __construct($plugin_data, $plugin_wp_data)
	{
		if (!parent::__construct($plugin_data, $plugin_wp_data))
			return false;

		require_once(dirname(__FILE__) . '/def-constants.php');
		require_once(dirname(__FILE__) . '/def-options.php');
		require_once(dirname(__FILE__) . '/common-functions.php');

		$this->init();

		self::$_ver = $this->get_pd('version');
	}

	protected function add_hooks()
	{
		add_action('init', array($this, 'do_ajax'), 9);
	}

	public function enqueue_media()
	{
		global $wp_styles;
		// styles
		wp_enqueue_style('aurl-css', BWP_RMIF_CSS . '/modal.css', array(), self::$_ver);
		wp_register_style('aurl-css-ie-only', BWP_RMIF_CSS . '/modal-ie.css', array(), self::$_ver);
		$wp_styles->add_data('aurl-css-ie-only', 'conditional', 'lt IE 7');
		wp_enqueue_style('aurl-css-ie-only');
		// scripts
		wp_enqueue_script('aurl-modal', BWP_RMIF_JS . '/jquery.jqModal.js', array('jquery'), self::$_ver);
	}

	private static function sanitize($val)
	{
		return trim(strip_tags(stripslashes($val)));
	}

	public function do_ajax()
	{
		if (!empty($_GET['bwp_rmif_ajax']))
		{
			$post_keys = array('name', 'email', 'comment');
			$response = '';
			foreach ($post_keys as $post_key)
			{
				if (empty($_POST['rmif_' . $post_key]))
				{
					echo 'rmif_' . $post_key;
					exit;
				}

				${$post_key} = self::sanitize($_POST['rmif_' . $post_key]);
			}

			$phone = (!empty($_POST['rmif_phone'])) ? self::sanitize($_POST['rmif_phone']) : '';

			if (!is_email($email))
				$response = 'invalid_email';
			else if (!empty($phone) && !preg_match('/^[0-9\-]+$/ui', $phone))
				$response = 'invalid_phone';
			else
			{
				// Preparing the contact email's contents
				$to = $this->get_o('inp_rmif_email');
				$from = $this->get_o('inp_rmif_from_email');
				$from_name = $this->get_o('inp_rmif_from_name');
				$reply_to = $email;
				$bcc = explode(';', $this->get_o('inp_rmif_bcc'));
				$bcc = implode(',', $bcc);
				$subject = $this->get_o('inp_rmif_subject');
				$phone = (empty($phone)) ? __('not provided', $this->domain) : $phone;
				$message = $this->get_o('inp_rmif_message');
				$message = str_replace(
					array('{name}', '{email}', '{phone}', '{message}'),
					array($name, $email, $phone, $comment),
					$message
				);

				// Setting up headers
				$headers = 'From: ' . $from_name . ' <' . $from . '>' . "\r\n";
				$headers .= 'Reply-To: ' . $reply_to . "\r\n";
				$headers .= 'Bcc: ' . $bcc . "\r\n";

				// Try sending the email
				add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
				$response = @wp_mail($to, $subject, $message, $headers);
				$response = ($response) ? 'sent' : 'failed';

				// If we have to send an automatic response message, do it here
				if ('yes' == $this->get_o('enable_auto_response'))
				{
					// Preparing the auto email's contents
					$to = $email;
					$from = $this->get_o('inp_are_from_email');
					$from_name = $this->get_o('inp_are_from_name');
					$reply_to = $this->get_o('inp_rmif_email');
					$subject = $this->get_o('inp_are_subject');
					$phone = (empty($phone)) ? __('not provided', $this->domain) : $phone;
					$message = $this->get_o('inp_are_message');
					$message = str_replace(
						array('{name}', '{email}', '{phone}', '{message}'),
						array($name, $email, $phone, $comment),
						$message
					);

					// Setting up headers
					$headers = 'From: ' . $from_name . ' <' . $from . '>' . "\r\n";
					$headers .= 'Reply-To: ' . $reply_to . "\r\n";

					// Try sending the email
					@wp_mail($to, $subject, $message, $headers);

					/* That's all, we don't need to care if this message was sent successfully or not */
				}
			}

			echo $response;
			exit;
		}
	}

}

endif;