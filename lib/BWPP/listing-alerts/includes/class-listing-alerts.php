<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!class_exists('BWP_FRAMEWORK'))
	require_once(dirname(__FILE__) . '/class-bwp-framework.php');

class Listing_Alerts extends BWP_FRAMEWORK {

	public $user_action = '';

	public $success_message = '';

	public $wp_list_table = NULL;

	/**
	 * Constructor
	 */
	function __construct($version = '1.0.0')
	{
		// Plugin's title
		$this->plugin_title = 'IDX Listing Alerts';
		// Plugin's version
		$this->set_version($version);
		$this->set_version('3.0', 'wp');
		// Basic version checking
		if (!$this->check_required_versions())
			return;

		$this->build_properties('LIST_ALERTS', 'list-alerts', array(), 'IDX Listing Alerts', dirname(dirname(__FILE__)) . '/listing-alerts.php', 'http://betterwp.net/', false);

		/*$this->add_extra_option_key('LIST_ALERTS', 'list_alerts', __('Listing Alerts Statistics', 'list-alerts'));*/
		$this->add_extra_option_key('LIST_ALERTS_USERS', 'list_alerts_users', __('All People', 'list-alerts'));

		$this->init();
	}

	public function install()
	{
		wp_schedule_event(current_time('timestamp'), 'daily', 'list_alert_notify');
	}

	public function uninstall()
	{
		wp_clear_scheduled_hook('list_alert_notify');
	}

	public function add_hooks()
	{
		add_action('template_redirect', array($this, 'handle_user_request'));
		add_action('widgets_init', array($this, 'register_login_widget'));
		add_action('list_alerts_list', array($this, 'show_user_listings'));
		add_action('list_alerts_edit', array($this, 'edit_user_listings'));
		add_action('list_alert_notify', 'list_alert_notify');
		// CRM
		if (is_admin())
		{
			add_action('load-spot-on-idx_page_list_alerts_users', array($this, 'handle_admin_request'));
			add_action('load-spot-on-idx_page_list_alerts_users', array($this, 'load_user_table_class'));
		}
	}

	public function handle_admin_request()
	{
		$actions = array('dis', 'enb');
		$user_action = (!empty($_GET['la_action']) && in_array($_GET['la_action'], $actions)) ? trim($_GET['la_action']) : '';
		if (empty($user_action))
			return;
		if ('dis' == $user_action || 'enb' == $user_action)
		{
			// Validate nonce
			$nonce = ('enb' == $user_action) ? 'idx_listing_alerts_enb' : 'idx_listing_alerts_dis';
			if (empty($_GET['_lanonce']) || !wp_verify_nonce($_GET['_lanonce'], $nonce))
				wp_die(__('Nonce validation failed.', 'list-alerts'));
			// Check necessary variable
			if (empty($_GET['id']) || empty($_GET['la_user']))
				wp_die(__('Invalid or missing supplied ID.', 'list-alerts'));
			$id = (int) trim($_GET['id']);
			$user_id = (int) trim($_GET['la_user']);
			// Disable / Enable selected umeta_id
			global $wpdb;
			$listing = $wpdb->get_var('SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE umeta_id = ' . $id . ' LIMIT 1');
			// If this umeta_id is invalid
			if (empty($listing))
				wp_die(__('Invalid meta ID', 'list-alerts'));
			$listing = maybe_unserialize($listing);
			$old_listing = $listing;
			$listing['options']['enable'] = ('enb' == $user_action) ? true : false;
			update_user_meta($user_id, 'listings', $listing, $old_listing);
			$message = ($listing['options']['enable']) ? __('List <strong>%s</strong> enabled.', 'list-alerts') : __('List <strong>%s</strong> disabled.', 'list-alerts');
			$this->add_notice(sprintf($message, $listing['name']));
		}
	}

	public function load_user_table_class()
	{
		$list_table_class = 'class-la-users-list-table';
		if (!empty($_GET['la_user']) && isset($_GET['page']) && LIST_ALERTS_USERS == $_GET['page'])
			$list_table_class = 'class-la-user-list-table';
		require_once(dirname(__FILE__) . '/' . $list_table_class . '.php');
		$this->wp_list_table = new LA_User_List_Table();
	}

	public function admin_show_users()
	{
		$wp_list_table = $this->wp_list_table;
		require_once(dirname(__FILE__) . '/users-list.php');
	}

	/**
	 * Build the option pages
	 *
	 * Utilizes BWP Option Page Builder (@see BWP_OPTION_PAGE)
	 */
	function build_option_pages()
	{
		if (!current_user_can(LIST_ALERTS_CAPABILITY))
			wp_die(__('You do not have sufficient permissions to access this page.'));

		// Init the class
		$page = $_GET['page'];		
		$bwp_option_page = new BWP_OPTION_PAGE($page);

		$options = array();
		$dynamic_options = array();

		// Remove all submit buttons
		add_filter('bwp_option_submit_button', create_function('', 'return "";'));
		// Remove donation box
		remove_action('bwp_option_action_before_form', array($this, 'show_donation'), 12);

if (!empty($page))
{
	if ($page == 'mrdummy')
	{
		$bwp_option_page->set_current_tab(1);

		$form = array(
			'items'			=> array('heading'),
			'item_labels'	=> array
			(
				__('Listing Alerts Statistics', 'list-alerts')
			),
			'item_names'	=> array('h1'),
			'heading'		=> array(
				'h1'	=> __('Placeholder for future features', 'list-alerts')
			),
			'container'	=> array(
				'h4' => __('After you activate this plugin, all sitemaps should be available right away. The next step is to submit the sitemapindex to major search engines. You only need the <strong>sitemapindex</strong> and nothing else, those search engines will automatically recognize other included sitemaps.', 'list-alerts')
			)
		);

		// Assign the form and option array
		$bwp_option_page->init($form, array(), $this->form_tabs);
		// Build the option page
		echo $bwp_option_page->generate_html_form();
	}
	else if ($page == LIST_ALERTS_USERS)
	{
		$bwp_option_page->set_current_tab(1);
		// Assign the form and option array
		$bwp_option_page->init(array('items' => array(), 'item_labels' => array(), 'item_names' => array()), array(), $this->form_tabs);
		// Build the option page
		add_action('bwp_option_action_before_form', array($this, 'admin_show_users'));
		echo $bwp_option_page->generate_html_form();
	}
}

	}

	public function show_success_message()
	{
?>
	<strong class="la-success-message"><?php echo $this->success_message; ?></strong>
<?php
	}

	public function show_user_listings()
	{
		global $wpdb;

		if (!is_user_logged_in())
			return;
		$user_id = get_current_user_id();
		$listings = $wpdb->get_results($wpdb->prepare('
			SELECT u.*, um.*
				FROM ' . $wpdb->usermeta . ' um
					INNER JOIN ' . $wpdb->users . ' u
						ON u.ID = um.user_id' . "
					WHERE um.meta_key = 'listings'" . '
						AND u.user_status = 0
						AND u.ID = ' . (int) $user_id . '
				ORDER BY um.umeta_id DESC
		'));

		if (0 == sizeof($listings))
			echo '<h1 class="entry-title">' . __('Currently, you do not have any saved listing.', 'list-alerts') . '</h1>';
		else
		{
?>
			<h1 class="entry-title"><?php printf(__('You currently have <span style="color: green;">%d</span> Listing Alert(s) in your account.', 'list-alerts'), sizeof($listings)); ?></h1>
			<table class="la-list-table" border="0" cellpadding="5" cellspacing="5">
				<thead>
					<tr><th><?php _e('Listing Alert Name', 'list-alerts'); ?></th><th><?php _e('Actions', 'list-alerts'); ?></th></tr>
				</thead>
				<tbody>
<?php
			foreach ($listings as $listing)
			{
				$listing_data = maybe_unserialize($listing->meta_value);
				$switch_html = (empty($listing_data['options']['enable'])) ? '<a href="' . add_query_arg(array('la_action' => 'enb', 'id' => $listing->umeta_id, '_lanonce' => wp_create_nonce('idx_listing_alerts_enb'))) . '">' . __('Enable', 'list-alerts') . '</a>' : '<a href="' . add_query_arg(array('la_action' => 'dis', 'id' => $listing->umeta_id, '_lanonce' => wp_create_nonce('idx_listing_alerts_dis'))) . '">' . __('Disable', 'list-alerts') . '</a>';
?>
					<tr><td class="la-list-table-name"><?php echo esc_html($listing_data['name']); ?></td><td class="la-list-table-actions"><?php echo $switch_html; ?> | <a href="<?php echo add_query_arg(array('la_action' => 'edit', 'id' => $listing->umeta_id, '_lanonce' => wp_create_nonce('idx_listing_alerts_edit'))); ?>"><?php _e('Edit', 'list-alerts'); ?></a> | <a href="<?php echo add_query_arg(array('la_action' => 'del', 'id' => $listing->umeta_id, '_lanonce' => wp_create_nonce('idx_listing_alerts_del'))); ?>"><?php _e('Delete', 'list-alerts'); ?></a></td></tr>
<?php
			}
?>
				</tbody>
				<tfoot>
				</tfoot>
			</table>
<?php
		}
	}

	public function add_listing()
	{
		global $spoton_idx, $spoton_pkey;
		$query = spoton_get_odata_query($spoton_pkey);
		// If the query is not empty and has not been saved before, save it
		$user_id 		= get_current_user_id();
		$listings 		= get_user_meta($user_id, 'listings');
		$listing_name 	= trim(preg_replace('/[^a-z0-9-_\s]+/ui', '', $_POST['la_name']));
		$enable_alert 	= (isset($_POST['la_enable'])) ? true : false;
		$listing_data 	= array('name' => $listing_name, 'query' => trim($query), 'options' => array('enable' => $enable_alert));
		$existed		= false;

		foreach ($listings as $listing)
		{
			if ($listing_name == $listing['name'])
			{
				$existed = true;
				$old_listing_data = array('name' => $listing['name'], 'query' => $listing['query'], 'options' => $listing['options']);
			}
		}

		if (!$existed)
		{
			add_user_meta($user_id, 'listings', $listing_data, false);
			$this->success_message = __('New Listing Alert has been successfully added!', 'list-alerts');
			add_action('list_alerts_list', array($this, 'show_success_message'), 2);
		}
		else
		{
			update_user_meta($user_id, 'listings', $listing_data, $old_listing_data);
			$this->success_message = sprintf(__('Listing "%s" has been updated.', 'list-alerts'), $listing_name);
			add_action('list_alerts_list', array($this, 'show_success_message'), 2);
		}
	}

	public function edit_user_listings()
	{
		global $wpdb;

		$id = (int) trim($_GET['id']);
		$user_id = get_current_user_id();
		$listing = $wpdb->get_var('SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE umeta_id = ' . $id . ' LIMIT 1');
		// If this umeta_id is invalid
		if (empty($listing))
			wp_redirect(home_url('?la_action=list'));
		$listing = maybe_unserialize($listing);

		// If we are in editting mode
		if (isset($_POST['la_submit']))
		{
			$listing_name 	= (isset($_POST['la_name'])) ? trim(preg_replace('/[^a-z0-9-_\s]+/ui', '', $_POST['la_name'])) : '';
			if (empty($listing_name))
				wp_die(__('Please enter a name for your listing.', 'list-alerts'));
			$old_listing = $listing;
			// New data for the listing
			$listing['name'] = $listing_name;
			$listing['options']['enable'] = (isset($_POST['la_enable'])) ? true : false;
			update_user_meta($user_id, 'listings', $listing, $old_listing);
			$this->success_message = __('Listing Updated.', 'list-alerts');
		}
?>
	<p>
		<strong><a href="<?php echo add_query_arg(array('la_action' => 'list')); ?>" title="<?php _e('Click to go back to the list', 'list-alerts'); ?>">&laquo; <?php _e('Back to Listing list', 'list-alerts'); ?></a></strong><br /><br />
		<?php $this->show_success_message(); ?>
	</p>
	<form action="<?php echo add_query_arg(array('la_action' => 'edit', 'id' => $id, '_lanonce' => wp_create_nonce('idx_listing_alerts_edit'))); ?>" method="post">
	<p>
		<label for="la_name"><?php _e('A friendly name for your Listing Alert:', 'list-alerts'); ?></label>
		<input style="width:300px" type="text" id="la_name" name="la_name" class="regular-text code" value="<?php echo $listing['name']; ?>" /><br />
		<label for="la_name"><?php _e('Alert me when there are updates for this listing', 'list-alerts'); ?></label>
		<input type="checkbox" id="la_enable" <?php checked($listing['options']['enable'], true); ?> name="la_enable" /><br />
		<input type="submit" class="la-submit-buttom" id="la_submit" name="la_submit" value="<?php _e('Update Alert', 'list-alerts'); ?>">
		<input type="hidden" name="la_meta_id" value="<?php echo $id; ?>" />
	</p>
	</form>
<?php
	}

	public function handle_user_request()
	{
		if (!is_user_logged_in())
			return;

		$actions = array('list', 'add', 'edit', 'del', 'dis', 'enb');
		$user_action = (!empty($_GET['la_action']) && in_array($_GET['la_action'], $actions)) ? trim($_GET['la_action']) : '';
		$user_id = get_current_user_id();
		/*$user_sub_action = (!empty($_GET['list_alert_action'])) : trim($_GET['list_alert_action']) ? '';*/
		if (empty($user_action))
			return;
		// If user is trying to create a new listing alert
		if ('list' == $user_action && isset($_POST['la_submit']))
		{
			if (empty($_POST) || !wp_verify_nonce($_POST['_lanonce'], 'idx_listing_alerts_add'))
				wp_die(__('Nonce validation failed.', 'list-alerts'));
			if (empty($_POST['la_name']))
				wp_die(__('Please enter a name for your listing.', 'list-alerts'));
			$this->add_listing();
		}
		else if ('edit' == $user_action)
		{
			// Validate nonce
			if (empty($_GET['_lanonce']) || !wp_verify_nonce($_GET['_lanonce'], 'idx_listing_alerts_edit'))
				wp_die(__('Nonce validation failed.', 'list-alerts'));
			// Check necessary variable
			if (empty($_GET['id']))
				wp_die(__('Invalid ID.', 'list-alerts'));
		}
		else if ('del' == $user_action)
		{
			// Validate nonce
			if (empty($_GET['_lanonce']) || !wp_verify_nonce($_GET['_lanonce'], 'idx_listing_alerts_del'))
				wp_die(__('Nonce validation failed.', 'list-alerts'));
			// Check necessary variable
			if (empty($_GET['id']))
				wp_die(__('Invalid ID.', 'list-alerts'));
			$id = (int) trim($_GET['id']);
			// Delete selected umeta_id
			global $wpdb;
			$listing = $wpdb->get_var('SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE umeta_id = ' . $id . ' LIMIT 1');
			// If this umeta_id is invalid
			if (empty($listing))
				wp_redirect(home_url('?la_action=list'));
			$listing = maybe_unserialize($listing);
			$wpdb->query('DELETE FROM ' . $wpdb->usermeta . ' WHERE umeta_id = ' . $id);
			$this->success_message = sprintf(__('Listing "%s" has been deleted.', 'list-alerts'), $listing['name']);
			add_action('list_alerts_list', array($this, 'show_success_message'), 2);
		}
		else if ('dis' == $user_action || 'enb' == $user_action)
		{
			// Validate nonce
			$nonce = ('enb' == $user_action) ? 'idx_listing_alerts_enb' : 'idx_listing_alerts_dis';
			if (empty($_GET['_lanonce']) || !wp_verify_nonce($_GET['_lanonce'], $nonce))
				wp_die(__('Nonce validation failed.', 'list-alerts'));
			// Check necessary variable
			if (empty($_GET['id']))
				wp_die(__('Invalid ID.', 'list-alerts'));
			$id = (int) trim($_GET['id']);
			// Disable / Enable selected umeta_id
			global $wpdb;
			$listing = $wpdb->get_var('SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE umeta_id = ' . $id . ' LIMIT 1');
			// If this umeta_id is invalid
			if (empty($listing))
				wp_redirect(home_url('?la_action=list'));
			$listing = maybe_unserialize($listing);
			$old_listing = $listing;
			$listing['options']['enable'] = ('enb' == $user_action) ? true : false;
			update_user_meta($user_id, 'listings', $listing, $old_listing);
			$this->success_message = ('enb' == $user_action) ? sprintf(__('Listing "%s" has been enabled. Alerts will be sent in the next 24 hours.', 'list-alerts'), $listing['name']) : sprintf(__('Listing "%s" has been disabled. Alerts will no longer be sent from this listing.', 'list-alerts'), $listing['name']);
			add_action('list_alerts_list', array($this, 'show_success_message'), 2);
		}
		// Load template based on user's action
		$this->user_action = $user_action;
		add_filter('template_include', array($this, 'load_templates'), 12);
	}

	public function load_templates($template)
	{
		switch ($this->user_action)
		{
			case 'list':
			case 'del':
			case 'dis':
			case 'enb':
				$template = dirname(__FILE__) . '/templates/list.php';
			break;

			case 'edit':
				$template = dirname(__FILE__) . '/templates/edit.php';
			break;

			case 'add':
				$template = dirname(__FILE__) . '/templates/add.php';
			break;
		}

		return $template;
	}

	public function register_login_widget()
	{
		wp_register_sidebar_widget(LIST_ALERTS_USERS, 'Spot-on IDX &mdash; Login Widget', array($this, 'login_widget'), array('description' => __('Add an IDX login widget to your sidebar', 'list-alerts')));
	}

	public function login_widget()
	{
		$redirect_to = home_url($_SERVER["REQUEST_URI"]);
		if (!is_user_logged_in())
		{
?>
<div class="widget idx_login_widget">
<div class="widget-wrap">
	<h3 class="widget-title"><span><?php _e('Listing Alerts'); ?></span></h3>	
<!--	<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php', 'login_post') ?>" method="post">
	<div class="soidx-text sepH_b">Stay connected and receive updates about new properties matching your search preferences...</div>
		<p>
			<label><?php _e('Username') ?><br />
			<input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
		</p>
		<p>
			<label><?php _e('Password') ?><br />
			<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
		</p>
		<?php do_action('login_form'); ?>
		<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label></p>
		<p class="soidx-button submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="btn btn_a btn_medium button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
			<input type="hidden" name="testcookie" value="1" />
		</p>
	</form> -->
	<div class="idx_login_widget_links">
		<div><a class="bwp_aurl_link" href="<?php echo esc_url(site_url('wp-login.php?action=register', 'login')); ?>" title="<?php _e('Sign up for a new account'); ?>"><?php _e('Sign Up or Login for Listing Alerts'); ?></a></div>
		<div><a href="<?php echo esc_url(wp_lostpassword_url($redirect_to)); ?>" title="<?php _e('Get a new password sent to you'); ?>"><?php _e('Lost your password?'); ?></a></div>
	</div>
</div>
</div>
<?php
		}
		else
		{
			global $user_identity;
?>
<div class="widget-wrap">
<div class="widget idx_login_widget">
	<h3 class="widget-title"><?php _e('Welcome'); ?> <?php echo $user_identity ?></h3>
	<div class="idx_login_widget_links">
		<div><a href="<?php echo home_url('?la_action=list'); ?>" title="<?php _e('Your Listing Alerts', 'list-alerts'); ?>"><?php _e('Your Listing Alerts', 'list-alerts'); ?></a></div>
		<div><a href="<?php echo home_url('?la_action=add'); ?>" title="<?php _e('Create a new Alert', 'list-alerts'); ?>"><?php _e('Create a new Alert', 'list-alerts'); ?></a></div>
		<div><a href="<?php echo esc_url(wp_logout_url($redirect_to)); ?>" title="<?php _e('Log out'); ?>"><?php _e('Log out'); ?></a></div>
	</div>
</div>
</div>
<?php
		}
	}

}
?>