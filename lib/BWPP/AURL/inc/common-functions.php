<?php
if (!function_exists('bwp_aurl_register_widgets')) :

add_action('widgets_init', 'bwp_aurl_register_widgets');
function bwp_aurl_register_widgets()
{
	require_once(dirname(__FILE__) . '/class-aurl-login-widget.php');
	register_widget('BWP_AURL_LoginWidget');
}

endif;
?>