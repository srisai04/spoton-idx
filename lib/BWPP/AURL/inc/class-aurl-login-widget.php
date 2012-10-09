<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

if (!class_exists('BWP_AURL_LoginWidget')) :

class BWP_AURL_LoginWidget extends WP_Widget
{
	private static $_options = array();
	private static $_domain = '';

	function BWP_AURL_LoginWidget()
	{
		global $bwp_aurl;

		self::$_options = $bwp_aurl->options;
		self::$_domain = $bwp_aurl->domain;
		$widget_ops = array('classname' => 'bwp-aurl-widget', 'description' => __( 'A simple widget that shows a registration/login link, which will open a popup when clicked, to unregistered users.', self::$_domain) );
		$control_ops = array();
		$this->WP_Widget('bwp_aurl', __('Spot-on IDX &mdash; AURL Link Widget', self::$_domain), $widget_ops, $control_ops);
	}

	function get_register_link()
	{
		return site_url('wp-login.php?action=register', 'login');
	}

	function widget($args, $instance)
	{
		if (get_option('users_can_register') && !is_user_logged_in())
		{
?>
	<div class="spto_aurl_link_widget">
		<p>
			<?php printf(__('You are not logged in. Please <a href="%s" title="%s" class="bwp_aurl_link">Register/Login</a>.', self::$_domain), $this->get_register_link(), __('Click to register or login', self::$_domain)); ?>
		</p>
	</div>
<?php
		}
	}

	function update($new_instance, $old_instance)
	{
		return $new_instance;
	}

	function form($instance)
	{
?>
		<p>
			<?php _e('There are no options for this widget.', self::$_domain); ?>
		</p>
<?php
	}
}

endif;