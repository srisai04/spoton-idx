<?php
	// Some default options from WP
	$admin_email = trim(get_option('admin_email'));

	/* Get From name for auto response message the hard way */
	/*global $wpdb;
	$user_data = $wpdb->get_col("SELECT um.meta_value FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON (u.ID = um.user_id AND (um.meta_key = 'first_name' OR um.meta_key = 'last_name')) WHERE ID = 1");
	$first_name = (!empty($user_data[0])) ? $user_data[0] : '';
	$last_name = (!empty($user_data[1])) ? $user_data[1] : '';
	$admin_name = trim($first_name . ' ' . $last_name);*/
	/* Done */

	$blog_name = get_bloginfo('name');
	// Email sent to admin
	$email_message = __('Details of the request are listed below:', $this->domain) . "\n\n";
	$email_message .= __('Name: {name}' . "\n" . 'Email: {email}' . "\n" . 'Phone: {phone}' . "\n\n" . 'Comment:' . "\n" . '{message}', $this->domain);
	// Email sent to users
	$are_email_message = __('Your request for more property info has been sent with following details:', $this->domain) . "\n\n";
	$are_email_message .= __('Your Name: {name}' . "\n" . 'Your Email: {email}' . "\n" . 'Your Phone: {phone}' . "\n\n" . 'Your Comment:' . "\n" . '{message}', $this->domain);

	$url_parts = @parse_url(home_url());

	$this->add_option(
		new BWP_Options(BWP_RMIF_GENERAL, array(
				// Request more info form
				'inp_rmif_email'  		=> $admin_email,
				'inp_rmif_from_name'	=> $blog_name,
				'inp_rmif_from_email'	=> 'no-reply@' . $url_parts['host'],
				'inp_rmif_reply_to'		=> '',
				'inp_rmif_bcc'			=> '',
				'inp_rmif_subject'		=> sprintf(__('New Property Information Request - %s', $this->domain), $blog_name),
				'inp_rmif_message'		=> $email_message,
				// Automatic response email
				'enable_auto_response'	=> 'yes',
				'inp_are_from_name'		=> $blog_name,
				'inp_are_from_email'	=> $admin_email,
				'inp_are_subject'		=> sprintf(__('Your request was successfully sent - %s', $this->domain), $blog_name),
				'inp_are_message'		=> $are_email_message
			), 'db', array())
	);
?>