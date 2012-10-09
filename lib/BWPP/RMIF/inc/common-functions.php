<?php
add_action('widgets_init', 'bwp_rmif_register_widgets');
function bwp_rmif_register_widgets()
{
	require_once(dirname(__FILE__) . '/class-rmif-button-widget.php');
	register_widget('BWP_RMIF_ButtonWidget');
}
?>