<?php
/**
* A custom page template
*/
get_header();
?>

<div id="primary">
	<div id="content" role="main" class="la-body la-edit">
		<?php do_action('list_alerts_edit'); ?>
	</div>
</div>

<?php get_footer(); ?>