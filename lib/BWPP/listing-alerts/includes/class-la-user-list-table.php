<?php
/**
 * Listing Alerts List Table
 */
if (class_exists('WP_List_Table') && !class_exists('LA_User_List_Table')) :

class LA_User_List_Table extends WP_List_Table {

	var $site_id;
	var $is_site_users;

	function __construct() {
		$screen = get_current_screen();
		$this->is_site_users = false;

		if ( $this->is_site_users )
			$this->site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

		parent::__construct( array(
			'singular' => 'user',
			'plural'   => 'users'
		) );
	}

	function ajax_user_can() {
		current_user_can('manage_options');
	}

	function prepare_items() {
		global $wpdb;

		$listings = $wpdb->get_results($wpdb->prepare('
			SELECT u.*, um.*
				FROM ' . $wpdb->usermeta . ' um
					INNER JOIN ' . $wpdb->users . ' u
						ON u.ID = um.user_id' . "
					WHERE um.meta_key = 'listings'" . '
						AND u.user_status = 0
						AND u.ID = ' . (int) $_GET['la_user'] . '
				ORDER BY um.umeta_id DESC
		'));

		$per_page = 'users_per_page';
		$users_per_page = $this->get_items_per_page($per_page);
		$total = sizeof($listings);

		$this->items = $listings;

		$this->set_pagination_args( array(
			'total_items' => $total,
			'per_page' => $users_per_page,
		) );
	}

	function no_items() {
		_e('No list alerts were found.', 'list-alerts');
	}

	function la_user_list_url($path = '')
	{
		$path = (!empty($path)) ? '/' . ltrim($path, '/') : '';
		return 'admin.php?page=' . LIST_ALERTS_USERS . str_replace('?', '&', $path);
	}

	function get_views() {
		global $wp_roles, $role;

		if ( $this->is_site_users ) {
			$url = 'site-users.php?id=' . $this->site_id;
			switch_to_blog( $this->site_id );
			$users_of_blog = count_users();
			restore_current_blog();
		} else {
			$url = $this->la_user_list_url();
			$users_of_blog = count_users();
		}
		$total_users = $users_of_blog['total_users'];
		$avail_roles =& $users_of_blog['avail_roles'];
		unset($users_of_blog);

		$current_role = false;
		$class = empty($role) ? ' class="current"' : '';
		$role_links = array();
		$role_links['all'] = "<a href='$url'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
		foreach ( $wp_roles->get_names() as $this_role => $name ) {
			if ( !isset($avail_roles[$this_role]) )
				continue;

			$class = '';

			if ( $this_role == $role ) {
				$current_role = $role;
				$class = ' class="current"';
			}

			$name = translate_user_role( $name );
			/* translators: User role name with count */
			$name = sprintf( __('%1$s <span class="count">(%2$s)</span>'), $name, $avail_roles[$this_role] );
			$role_links[$this_role] = "<a href='" . esc_url( add_query_arg( 'role', $this_role, $url ) ) . "'$class>$name</a>";
		}

		return $role_links;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function display_tablenav( $which ) {
		if ( 'top' == $which )
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">
<?php
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}

	function get_bulk_actions() {
		return array();
	}

	function extra_tablenav( $which ) {
		if ( 'top' != $which )
			return;
		if ( ! current_user_can( 'promote_users' ) )
			return;
?>
	<div class="alignleft actions">
		<label class="screen-reader-text" for="new_role"><?php _e( 'Change role to&hellip;' ) ?></label>
		<select name="new_role" id="new_role">
			<option value=''><?php _e( 'Change role to&hellip;' ) ?></option>
			<?php wp_dropdown_roles(); ?>
		</select>
		<?php submit_button( __( 'Change' ), 'secondary', 'changeit', false ); ?>
	</div>
<?php
	}

	function current_action() {
		if ( isset($_REQUEST['changeit']) && !empty($_REQUEST['new_role']) )
			return 'promote';

		return parent::current_action();
	}

	function get_columns() {
		$c = array(
			'name' => __( 'Listing Name', 'list-alerts' ),
			'la_filter'     => __( 'Alert Criteria', 'list-alerts' ),
			'status'    => __( 'Status', 'list-alerts' ),
			'actions'     => __( 'Actions', 'list-alerts' )
		);

		return $c;
	}

	/*function count_many_users_listings($user_ids)
	{
		$listing_counts = array();
		foreach ($user_ids as $user_id)
		{
			$user = $this->items[$user_id]->data->
			$listing_counts[$user_id] = $this->items[$user_id]['listing'];
		}
		return $listing_counts;
	}*/

	function display_rows() {
		// Query the post counts for this page
		/*if ( ! $this->is_site_users )
			$post_counts = $this->count_many_users_listings( array_keys( $this->items ) );*/
		$style = '';
		foreach ( $this->items as $listing ) {
			$listing_data = maybe_unserialize($listing->meta_value);
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $listing_data, $style, $listing );
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 *
	 * @since 2.1.0
	 *
	 * @param object $user_object
	 * @param string $style Optional. Attributes added to the TR element.  Must be sanitized.
	 * @param string $role Key for the $wp_roles array.
	 * @param int $numposts Optional. Post count to display for this user.  Defaults to zero, as in, a new user has made zero posts.
	 * @return string
	 */
	function single_row( $listing_data, $style = '', $listing ) {
		$url = add_query_arg(array('la_user' => $listing->ID), $this->la_user_list_url());

		$checkbox = '';

		$r = "<tr id='user-$user_object->ID'$style>";

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";
			$status = ($listing_data['options']['enable']) ? '<span style="color: green">' . __('Enabled', 'list-alerts') . '</span>' : '<span style="color: red">' . __('Disabled', 'list-alerts') . '</span>';
			$switch_html = (!$listing_data['options']['enable']) ? '<a href="' . $url . '&amp;la_action=enb&amp;id=' . $listing->umeta_id . '&amp;_lanonce=' . wp_create_nonce('idx_listing_alerts_enb') . '">' . __('Enable', 'list-alerts') . '</a>' : '<a href="' . $url . '&la_action=dis&id=' . $listing->umeta_id . '&amp;_lanonce=' . wp_create_nonce('idx_listing_alerts_dis') . '">' . __('Disable', 'list-alerts') . '</a>';

			switch ( $column_name ) {
				case 'name':
					$r .= "<td $attributes>" . esc_html($listing_data['name']) . "</td>";
					break;
				case 'la_filter':
					$r .= "<td $attributes>" . esc_html(list_alert_convert_query($listing_data['query'])) . "</td>";
					break;
				case 'status':
					$r .= "<td $attributes>$status</td>";
					break;
				case 'actions':
					$r .= "<td $attributes>";
					$r .= $switch_html;
					$r .= "</td>";
					break;
			}
		}
		$r .= '</tr>';

		return $r;
	}
}

endif;

?>
