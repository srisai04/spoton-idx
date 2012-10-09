<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('BWP_AURL')) :

class BWP_AURL extends BWP_PluginBase {

	private static $_is_ajax = false;
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
		add_action('init', array($this, 'check_aurl'), 9);
		add_action('wp_footer', array($this, 'aurl_html'));
		add_action('wp_footer', array($this, 'aurl_html_success'));
	}

	public function enqueue_media()
	{
		global $wp_styles;
		// styles
		wp_enqueue_style('aurl-css', BWP_AURL_CSS . '/modal.css', array(), self::$_ver);
		wp_register_style('aurl-css-ie-only', BWP_AURL_CSS . '/modal-ie.css', array(), self::$_ver);
		$wp_styles->add_data('aurl-css-ie-only', 'conditional', 'lt IE 7');
		wp_enqueue_style('aurl-css-ie-only');
		// scripts
		wp_enqueue_script('aurl-modal', BWP_AURL_JS . '/jquery.jqModal.js', array('jquery'), self::$_ver);
	}

	private static function is_allowed($username)
	{
		return true;
	}

	private static function create_user($username, $password, $email, $first_name, $last_name)
	{
		$user_login = esc_sql( $username );
		$user_email = esc_sql( $email    );
		$user_pass = $password;

		$userdata = compact('user_login', 'user_email', 'user_pass', 'first_name', 'last_name');
		return wp_insert_user($userdata);
	}

	private static function register_new_user($aurl_email, $aurl_password, $aurl_first_name, $aurl_last_name, $aurl_phone)
	{
		$errors = new WP_Error();

		$sanitized_user_login = sanitize_user($aurl_email);
		$user_email = apply_filters('user_registration_email', $aurl_email);

		// Check the e-mail address/username
		if ( ! is_email( $user_email ) || ! self::is_allowed( $user_email )) {
			$errors->add( 'invalid_email', __( 'Invalid email address.' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			$errors->add( 'email_exists', __( 'This email is already registered, please choose another one.' ) );
		}

		do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

		$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

		if ( $errors->get_error_code() )
			return $errors;

		// If this is an ajax request, just pass the success status back without actually adding the user
		if (self::$_is_ajax)
			return 1;

		$user_pass = $aurl_password;
		$user_id = self::create_user( $sanitized_user_login, $user_pass, $user_email, $aurl_first_name, $aurl_last_name );
		if ( ! $user_id ) {
			$errors->add('registerfail', sprintf( __( 'Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a>!' ), get_option('admin_email')));
			return $errors;
		}

		// Update the phone if needed
		if (!empty($aurl_phone))
			update_user_meta( $user_id, 'aurl_phone', $aurl_phone );

		wp_new_user_notification( $user_id, '' );

		return $user_id;
	}

	private static function sanitize($val)
	{
		return trim(strip_tags(stripslashes($val)));
	}

	public function check_aurl()
	{
		if (is_user_logged_in())
			return;

		if (!empty($_POST['aurl_is_reg']))
		{
			// General check for all required fields
			$required = array('first_name', 'last_name', 'email', 'password', 'password_cf');
			foreach ($required as $req_post_name)
			{
				$post_key = 'aurl_' . $req_post_name;
				if (empty($_POST[$post_key]))
					return $post_key;
				${$post_key} = self::sanitize($_POST[$post_key]);
			}

			// Check valid password
			if ($aurl_password != $aurl_password_cf)
				return 'invalid_cf_pass';
			// Get Phone just in case
			$aurl_phone = (!empty($_POST['aurl_phone'])) ? self::sanitize($_POST['aurl_phone']) : '';
			if (!empty($aurl_phone) && !preg_match('/^[0-9-]+$/ui', $aurl_phone))
				return 'invalid_phone';

			$errors = self::register_new_user($aurl_email, $aurl_password, $aurl_first_name, $aurl_last_name, $aurl_phone);

			if (!is_wp_error($errors))
			{
				$user_id = $errors;
				// Set up a one-time welcome message, default to 'please login' welcome message
				update_user_meta( $user_id, 'aurl_welcome', 1 );

				// Try logging in if required
				if ('yes' == $this->get_o('enable_instant_login'))
				{
					$user = wp_signon(array('user_login' => sanitize_user($aurl_email), 'user_password' => $aurl_password, 'remember' => true), false);
					// If we can log the user in, switch to 'already login' welcome message
					if (!is_wp_error($user))
						update_user_meta( $user_id, 'aurl_welcome', 2 );
				}

				// Always return a success response
				return 'succeeded';
			}
			else if ('email_exists' == $errors->get_error_code() || 'invalid_email' == $errors->get_error_code())
				return $errors->get_error_code();
			else if ('registerfail' == $errors->get_error_code())
				return 'failed';
		}
		else if (!empty($_POST['aurl_is_login']))
		{
			// General check for all required fields
			$required = array('login_email', 'login_password');
			foreach ($required as $req_post_name)
			{
				$post_key = 'aurl_' . $req_post_name;
				if (empty($_POST[$post_key]))
					return $post_key;
				${$post_key} = self::sanitize($_POST[$post_key]);
			}

			// Remember me?
			$aurl_auto = (isset($_POST['aurl_login_auto'])) ? true : false;

			// Check if user exists
			$user_name = sanitize_user($aurl_login_email);
			$user = get_user_by('login', $user_name);
			if (!$user)
				return 'invalid';
			
			$user = wp_signon(array('user_login' => $user_name, 'user_password' => $aurl_login_password, 'remember' => $aurl_auto), false);
			if (!is_wp_error($user))
			{
				// Successfully logged in
				return 'succeeded';
			}
			else
				return 'invalid';
		}
	}

	public function do_ajax()
	{
		if (is_user_logged_in())
			return;

		if (!empty($_GET['bwp_aurl_ajax']))
		{
			/*self::$_is_ajax = true;*/
			$user_added = $this->check_aurl();
			echo $user_added;
			exit;
		}
		else if (!empty($_GET['bwp_aurl_doc']))
		{
			$docs = array('pp', 'tos');
			if (!in_array($_GET['bwp_aurl_doc'], $docs))
				echo 'none';
			
			$doc = ('pp' == $_GET['bwp_aurl_doc']) ? 'pp.php' : 'tos.php';
			$doc_html = '';
			include_once(dirname(__FILE__) . '/' . $doc);

			echo str_replace(array('{business_name}', '{business_state}'), array($this->get_o('inp_com_name'), $this->get_o('inp_com_state')), $doc_html);
			exit;
		}
	}

	public function aurl_html()
	{
		if (is_user_logged_in())
			return;

		// Get PP and TOS message if aaplicable
		$pp = $tos = $pptos = '';
		$please = ('yes' == $this->get_o('enable_pp_link') && 'yes' == $this->get_o('enable_tos_link')) ? '' : 'Please read ';
		$and = ('yes' == $this->get_o('enable_pp_link') && 'yes' == $this->get_o('enable_tos_link')) ? ' and ' : '';
		// Privacy Policy Link
		$pp_link = home_url('?bwp_aurl_doc=pp');
		$pp_class = ' class="spto_pp_handle"';
		if (!empty($this->options['sel_pp_page']))
		{
			$pp_link = get_permalink($this->options['sel_pp_page']);
			$pp_class = ' onclick="this.target=\'_blank\';"';
		}
		$pp = ('yes' == $this->get_o('enable_pp_link')) ? sprintf(__('Please read our <a href="%s"%s>Privacy Policy</a>' . $and, $this->domain), $pp_link, $pp_class) : '';
		// TOS Link
		$tos_link = home_url('?bwp_aurl_doc=tos');
		$tos_class = ' class="spto_tos_handle"';
		if (!empty($this->options['sel_tos_page']))
		{
			$tos_link = get_permalink($this->options['sel_tos_page']);
			$tos_class = ' onclick="this.target=\'_blank\';"';
		}
		$tos = ('yes' == $this->get_o('enable_tos_link')) ? sprintf(__($please . 'our <a href="%s"%s>Terms of Service</a>', $this->domain), $tos_link, $tos_class) : '';
		// Now we have a complete string
		$pptos = $pp . $tos;
?>
		<script type="text/javascript">
			// A custom alert function that makes use of jqModal
			function jqm_alert(msg)
			{
				jQuery('.jqmAlert').jqmShow().find('div.jqmAlertContents').html(msg);
			}

			jQuery(document).ready(function(){
				var bwp_aurl_ajaxurl = '<?php echo home_url('?bwp_aurl_ajax=1'); ?>';
				var dummy_frm = jQuery('form[name="aurl_frm_dummy"]');
				// Register alert jqm
				jQuery('.jqmAlert').jqm({modal: true, trigger: false});
				// Popup reg/login
				jQuery('#spto_aurl_popup').jqm({modal: true, trigger: '.bwp_aurl_link'});
				jQuery('.spto_pp_wrapper').jqm({modal: true, trigger: 'a.spto_pp_handle', ajax: '@href', ajaxText: '<div style="text-align: center;"><img src="<?php echo BWP_AURL_IMAGES . '/ajax_black.gif'; ?>" alt="<?php _e('Loading...', $this->domain); ?>" /></div>', target: '.spto_pptos_contents'});
				jQuery('.spto_tos_wrapper').jqm({modal: true, trigger: 'a.spto_tos_handle', ajax: '@href', ajaxText: '<div style="text-align: center;"><img src="<?php echo BWP_AURL_IMAGES . '/ajax_black.gif'; ?>" alt="<?php _e('Loading...', $this->domain); ?>" /></div>', target: '.spto_pptos_contents'});
				// Ajax login
				jQuery('input[name="aurl_login_submit"]').click(function(){
					var aurl_btn = jQuery(this);
					aurl_btn.attr('disabled', 'disabled');
					aurl_btn.val('Checking ...');
					var aurl_frm = jQuery('form[name="aurl_login_form"]');
					var data = aurl_frm.serialize();
					jQuery.post(bwp_aurl_ajaxurl, data, function(response) {
						jQuery('form[name="aurl_login_form"] input').removeClass('aurl_field_error');
						if ('succeeded' == response) {
							dummy_frm.submit();
						}
						else
						{
							if ('invalid' == response)
								jqm_alert('<?php _e('Either username or password is incorrect.', $this->domain); ?>');
							else 
								jQuery('form[name="aurl_login_form"] input[name="' + response + '"]').addClass('aurl_field_error');
							aurl_btn.removeAttr('disabled');
							aurl_btn.val('Login');
						}
					});
					return false;
				});
				// Ajax check validity of reg info
				jQuery('input[name="aurl_reg_submit"]').click(function(){
					var aurl_btn = jQuery(this);
					aurl_btn.attr('disabled', 'disabled');
					aurl_btn.val('Checking ...');
					var aurl_frm = jQuery('form[name="aurl_reg_form"]');
					var data = aurl_frm.serialize();
					jQuery.post(bwp_aurl_ajaxurl, data, function(response) {
						jQuery('form[name="aurl_reg_form"] input').removeClass('aurl_field_error');
						if ('succeeded' == response) {
							dummy_frm.submit();
						}
						else
						{
							if ('invalid_cf_pass' == response)
								jqm_alert('<?php _e('Passwords did not match!', $this->domain); ?>');
							else if ('invalid_phone' == response)
								jqm_alert('<?php _e('You have typed in an invalid phone number.', $this->domain); ?>');
							else if ('invalid_email' == response)
								jqm_alert('<?php _e('You have typed in an invalid email address.', $this->domain); ?>');
							else if ('email_exists' == response)
								jqm_alert('<?php _e('The email address you typed in has already been registered, please use another one.', $this->domain); ?>');
							else 
								jQuery('form[name="aurl_reg_form"] input[name="' + response + '"]').addClass('aurl_field_error');
							aurl_btn.removeAttr('disabled');
							aurl_btn.val('Register');
						}
					});
					return false;
				});
			});
		</script>
		<!-- alerts -->
		<div class="jqmWindow jqmdAbove jqmNarrow jqmAlert">
			<div class="jqmAlertContents"><!-- --></div>
			<div class="soidx-button" style="text-align: right; margin-top: 10px;">
				<input class="btn btn_a btn_medium jqmClose" type="button" style="padding: 3px 16px;" value="<?php _e('Close Message', $this->domain); ?>" />
			</div>
		</div>
		<!-- default privacy policy and TOS -->
		<div class="spto_pp_wrapper jqmWindow jqmdAbove jqmWide">
			<div class="spto_pptos_contents"><!-- --></div>
			<a href="#" class="jqmClose"><em><?php _e('Close', $this->domain); ?></em></a>
		</div>
		<div class="spto_tos_wrapper jqmWindow jqmdAbove jqmWide">
			<div class="spto_pptos_contents"><!-- --></div>
			<a href="#" class="jqmClose"><em><?php _e('Close', $this->domain); ?></em></a>
		</div>
		<!-- forms -->
		<div id="spto_aurl_popup" class="jqmWindow">
			<a href="#" class="jqmClose"><em><?php _e('Close', $this->domain); ?></em></a>
			<div class="cf sepH_b">
			<div class="dp50">
				<div class="soidx-text large bld sepH_b"><?php _e('Create an Account', $this->domain); ?></div>
				<form name="aurl_frm_dummy" action="" method="GET"><!-- --></form>
				<form class="formEl_a" name="aurl_reg_form" action="" method="POST">
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_first_name"><?php _e('First Name', $this->domain); ?></label>
						<input class="inpt_a large" type="text" name="aurl_first_name" id="aurl_first_name" />
			        </div>
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_last_name"><?php _e('Last Name', $this->domain); ?></label>
						<input class="inpt_a large" type="text" name="aurl_last_name" id="aurl_last_name" />
					</div>
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_email"><?php _e('Email (use for login)', $this->domain); ?></label>
						<input class="inpt_a large" type="text" name="aurl_email" id="aurl_email" />
					</div>
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_password"><?php _e('Create a Password', $this->domain); ?></label>
						<input class="inpt_a large" type="password" name="aurl_password" id="aurl_password" />
					</div>
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_password_cf"><?php _e('Confirm Password', $this->domain); ?></label>
						<input class="inpt_a large" type="password" name="aurl_password_cf" id="aurl_password_cf" />
					</div>
					<div class="sepH_b">
						<label class="lbl_a" for="aurl_phone"><?php _e('Phone', $this->domain); ?></label>
						<input class="inpt_a large" type="text" name="aurl_phone" id="aurl_phone" />
					</div>
					<div class="soidx-button">
						<input class="btn btn_a btn_medium" type="submit" name="aurl_reg_submit" value="<?php _e('Register', $this->domain); ?>" />
						<input type="hidden" name="aurl_is_reg" value="1" />
					</div>
				</form>
			</div>
			<div class="dp50">
				<div class="soidx-text large bld sepH_b"><?php _e('Already have an Account?', $this->domain); ?></div>
				<form class="formEl_a" name="aurl_login_form" action="" method="POST">
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_login_email"><?php _e('Your Email', $this->domain); ?></label>
						<input class="inpt_a large" type="text" name="aurl_login_email" id="aurl_login_email" />
					</div>
					<div class="sepH_a">
						<label class="lbl_a" for="aurl_login_password"><?php _e('Your Password', $this->domain); ?></label>
						<input class="inpt_a large" type="password" name="aurl_login_password" id="aurl_login_password" />
					</div>
					<div class="sepH_b">
						<input type="checkbox" name="aurl_login_auto" id="aurl_login_auto" class="inpt_c" />
						<label for="aurl_login_auto" class="lbl_c"><?php _e('Remember me?', $this->domain); ?></label>
					</div>
					<div class="soidx-button">
						<input class="btn btn_a btn_medium" type="submit" name="aurl_login_submit" value="<?php _e('Login', $this->domain); ?>" />
						<input type="hidden" name="aurl_is_login" value="1" />
					</div>
				</form>
			</div>
			</div>
<?php if (!empty($pptos)) { ?>
			<div class="dp100"><?php echo $pptos; ?></div>
<?php } ?>
		</div>
<?php
	}

	public function aurl_html_success()
	{
		if (!is_user_logged_in())
			return;

		$user_id = get_current_user_id();
		$wm = (int) get_user_meta($user_id, 'aurl_welcome', true);
		// Disable welcome message for this user
		update_user_meta($user_id, 'aurl_welcome', 0);

		if (empty($wm))
			return;

		$m = (1 == $wm) ? __('<strong>Registration has succeeded!</strong><br />You can now login using your email address and chosen password.', $this->domain) : __('<strong>Registration has succeeded!</strong><br />The system has also logged you in successfully.', $this->domain);
?>
		<!-- first welcome message -->
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#spto_aurl_popup').jqm({modal: true, trigger: false});
				jQuery('#spto_aurl_popup').jqmShow();
			});
		</script>
		<div id="spto_aurl_popup" class="jqmWindow" style="top: 45%;">
			<div class="jqmAlertContents aurl_reg_success"><?php echo $m; ?></div>
			<a href="#" class="jqmClose"><em><?php _e('Close', $this->domain); ?></em></a>
		</div>
<?php		
	}

}

endif;