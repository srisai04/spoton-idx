<?php
if ( ! current_user_can( 'list_users' ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

$pagenum = $wp_list_table->get_pagenum();
$title = __('Users with Listings', 'list-alerts');

add_screen_option( 'per_page', array('label' => _x( 'Users', 'users per page (screen options)' )) );

if ( empty($_REQUEST) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="'. esc_attr(stripslashes($_SERVER['REQUEST_URI'])) . '" />';
} elseif ( isset($_REQUEST['wp_http_referer']) ) {
	$redirect = remove_query_arg(array('wp_http_referer', 'updated', 'delete_count'), stripslashes($_REQUEST['wp_http_referer']));
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr($redirect) . '" />';
} else {
	$redirect = 'users.php';
	$referer = '';
}

$update = '';

switch ( $wp_list_table->current_action() ) {

default:

	if ( !empty($_GET['_wp_http_referer']) ) {
		wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
		exit;
	}

	$wp_list_table->prepare_items();
	$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
	if ( $pagenum > $total_pages && $total_pages > 0 ) {
		wp_redirect( add_query_arg( 'paged', $total_pages ) );
		exit;
	}

	$messages = array();
	if ( isset($_GET['update']) ) :
		switch($_GET['update']) {
		case 'del':
		case 'del_many':
			$delete_count = isset($_GET['delete_count']) ? (int) $_GET['delete_count'] : 0;
			$messages[] = '<div id="message" class="updated"><p>' . sprintf(_n('%s user deleted', '%s users deleted', $delete_count), $delete_count) . '</p></div>';
			break;
		case 'add':
			$messages[] = '<div id="message" class="updated"><p>' . __('New user created.') . '</p></div>';
			break;
		case 'promote':
			$messages[] = '<div id="message" class="updated"><p>' . __('Changed roles.') . '</p></div>';
			break;
		case 'err_admin_role':
			$messages[] = '<div id="message" class="error"><p>' . __('The current user&#8217;s role must have user editing capabilities.') . '</p></div>';
			$messages[] = '<div id="message" class="updated"><p>' . __('Other user roles have been changed.') . '</p></div>';
			break;
		case 'err_admin_del':
			$messages[] = '<div id="message" class="error"><p>' . __('You can&#8217;t delete the current user.') . '</p></div>';
			$messages[] = '<div id="message" class="updated"><p>' . __('Other users have been deleted.') . '</p></div>';
			break;
		case 'remove':
			$messages[] = '<div id="message" class="updated fade"><p>' . __('User removed from this site.') . '</p></div>';
			break;
		case 'err_admin_remove':
			$messages[] = '<div id="message" class="error"><p>' . __("You can't remove the current user.") . '</p></div>';
			$messages[] = '<div id="message" class="updated fade"><p>' . __('Other users have been removed.') . '</p></div>';
			break;
		}
	endif; ?>

<?php if ( isset($errors) && is_wp_error( $errors ) ) : ?>
	<div class="error">
		<ul>
		<?php
			foreach ( $errors->get_error_messages() as $err )
				echo "<li>$err</li>\n";
		?>
		</ul>
	</div>
<?php endif;

if ( ! empty($messages) ) {
	foreach ( $messages as $msg )
		echo $msg;
} ?>

<?php if (!empty($usersearch))
	printf( '<h3><span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span></h3>', esc_html($usersearch)); 
?>

<?php $wp_list_table->views(); ?>

<style type="text/css">
	.subsubsub {margin-top: 0px;}
</style>

<form action="" method="get">

<?php /*$wp_list_table->search_box( __( 'Search Users' ), 'user' );*/ ?>

<?php $wp_list_table->display(); ?>
</form>

<br class="clear" />
<?php
break;
} // end of the $doaction switch
?>