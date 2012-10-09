<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

if (!class_exists('BWP_RMIF_ButtonWidget')) :

class BWP_RMIF_ButtonWidget extends WP_Widget
{
	private static $_options = array();
	private static $_domain = '';

	function BWP_RMIF_ButtonWidget()
	{
		global $bwp_rmif;

		self::$_options = $bwp_rmif->options;
		self::$_domain = $bwp_rmif->domain;
		$widget_ops = array('classname' => 'bwp-rmif-widget', 'description' => __( 'This widget adds a button that brings up a modal contact form to a property details page.', self::$_domain) );
		$control_ops = array();
		$this->WP_Widget('bwp_rmif', __('Spot-on IDX &mdash; Contact Form', self::$_domain), $widget_ops, $control_ops);
	}

	function get_name($userdata)
	{
		if (empty($userdata->first_name) && empty($userdata->last_name))
			return $userdata->display_name;
		else
			return trim($userdata->first_name . ' ' . $userdata->last_name);
	}

	function widget($args, $instance)
	{
		global $spoton_idx;

		if (!spoton_is_property())
			return;

		$name = $email = $phone = '';
		$userdata = get_userdata(get_current_user_id());
		if ($userdata)
		{
			$name = esc_attr($this->get_name($userdata));
			$email = esc_attr($userdata->user_email);
			$phone = !empty($userdata->aurl_phone) ? esc_attr($userdata->aurl_phone) : '';
		}
		$message = esc_html(sprintf(__('I would like to receive more information on this property: %s, property details page is located at: %s', self::$_domain), $spoton_idx->get_property_title(), $spoton_idx->get_property_permalink()));
?>
	<div class="spto_rmif_button_widget">
		<script type="text/javascript">
			// A custom alert function that makes use of jqModal
			function jqm_rmif_alert(msg)
			{
				jQuery('.jqmRmifAlert').jqmShow().find('div.jqmAlertContents').html(msg);
			}
			jQuery(document).ready(function(){
				var bwp_rmif_ajaxurl = '<?php echo home_url('?bwp_rmif_ajax=1'); ?>';
				// Register alert jqm
				jQuery('.jqmRmifAlert').jqm({modal: true, trigger: false});
				// Popup reg/login
				jQuery('#spto_rmif_form').jqm({
					modal: true,
					trigger: 'input[name="rmif_open_form"]'
				});
				// Ajax login
				jQuery('input[name="rmif_form_submit"]').click(function(){
					var rmif_btn = jQuery(this);
					rmif_btn.attr('disabled', true);
					rmif_btn.val('Sending Message ...');
					var rmif_frm = jQuery('form[name="rmif_contact_form"]');
					var data = rmif_frm.serialize();
					jQuery.post(bwp_rmif_ajaxurl, data, function(response) {
						jQuery('form[name="rmif_contact_form"] :input').removeClass('aurl_field_error');
						if ('sent' == response) {
							jQuery('form[name="rmif_contact_form"] :input').attr('disabled', true);
							jQuery('form[name="rmif_contact_form"]').slideUp('fast');
							jQuery('.spto_rmif_success_message').fadeIn('fast');
							rmif_btn.val('Submit');
						}
						else
						{
							if ('failed' == response)
								jqm_rmif_alert('<?php _e('The email could not be sent, please contact site&#39;s administrator directly for futher explanation.', self::$_domain); ?>');
							else if ('invalid_email' == response)
								jqm_rmif_alert('<?php _e('You have typed in an invalid email address.', self::$_domain); ?>');
							else if ('invalid_phone' == response)
								jqm_rmif_alert('<?php _e('You have typed in an invalid phone number.', self::$_domain); ?>');
							else 
								jQuery('form[name="rmif_contact_form"] :input[name="' + response + '"]').addClass('aurl_field_error');
							rmif_btn.removeAttr('disabled');
							rmif_btn.val('Submit');
						}
					});
					return false;
				});
			});
		</script>
		<!-- alerts -->
		<div class="jqmWindow jqmdAbove jqmNarrow jqmRmifAlert" style="top: 35%;">
			<div class="jqmAlertContents"><!-- --></div>
			<div class="soidx-button" style="text-align: right; margin-top: 10px;">
				<input class="btn btn_a btn_medium jqmClose" type="button" style="padding: 3px 16px;" value="<?php _e('Close Message', $this->domain); ?>" />
			</div>
		</div>
		<!-- forms -->
		<div id="spto_rmif_form" class="jqmWindow">
			<a href="#" class="jqmClose"><em><?php _e('Close', self::$_domain); ?></em></a>
			<div class="cf">
				<div class="soidx-text large bld sepH_b"><?php _e('Information Request', self::$_domain); ?></div>
				<div class="spto_rmif_success_message aurl_reg_success" style="display: none;"><?php _e('Your request has been sent!', self::$_domain); ?></div>
				<form class="formEl_a" name="rmif_contact_form" action="" method="POST">
					<div class="sepH_a">
						<label class="lbl_a" for="rmif_name"><?php _e('Name (Required)', self::$_domain); ?></label>
						<input class="inpt_a large" type="text" name="rmif_name" id="rmif_name" value="<?php echo $name; ?>" />
					</div>
					<div class="sepH_a">
						<label class="lbl_a" for="rmif_email"><?php _e('Email (Required)', self::$_domain); ?></label>
						<input class="inpt_a large" type="text" name="rmif_email" id="rmif_email" value="<?php echo $email; ?>" />
					</div>
					<div class="sepH_a">
						<label class="lbl_a" for="rmif_phone"><?php _e('Phone Number', self::$_domain); ?></label>
						<input class="inpt_a large" type="text" name="rmif_phone" id="rmif_phone" value="<?php echo $phone; ?>" />
					</div>
					<div class="sepH_b">
						<label for="rmif_comment" class="lbl_a"><?php _e('Comment (Required)', self::$_domain); ?></label>
						<textarea name="rmif_comment" cols="50" rows="7" class="inpt_a large"><?php echo $message; ?></textarea>
					</div>
					<div class="soidx-button">
						<input class="btn btn_a btn_large" type="submit" name="rmif_form_submit" value="<?php _e('Submit', self::$_domain); ?>" />
					</div>
				</form>
			</div>
		</div>
		<div class="soidx-button sepH_b">
			<input class="btn btn_a btn_large" type="button" name="rmif_open_form" style="padding: 3px 16px;" value="<?php _e('Request More Information', self::$_domain); ?>" />
		</div>
	</div>
<?php
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