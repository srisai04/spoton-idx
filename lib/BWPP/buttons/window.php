<?php
$test_path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
if (file_exists($test_path . '/wp-load.php'))
	require_once($test_path . '/wp-load.php');
else if (file_exists(dirname($test_path) . '/wp-load.php'))
	require_once(dirname($test_path) . '/wp-load.php');
else
{
	echo 'Could not initialize WordPress environment (wp-config.php is missing).';
	exit;
}
// check for rights
if (!is_user_logged_in() || !current_user_can('edit_posts'))
	wp_die(__("Sorry, but you do not have the permission to view this file."));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SpotonIDX - Saved Search Shortcode</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') . '/' . WPINC; ?>/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript">

	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertSpoton_IDX() {

		var name = jQuery('#saved_search_name').val();
		if (!name || /^\s*$/.test(name))
			rtr = '[saved_search]';
		else
			rtr = '[saved_search name="' + name + '"]';

        if (window.tinyMCE) 
		{
			window.tinyMCE.execInstanceCommand('content', 'mceReplaceContent', false, rtr);
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}

		return;
	}
	</script>
	<base target="_self" />
</head>
<body>
	<form action="#">
	
	<div class="panel_wrapper" style="border-top: 1px solid #919B9C; padding: 5px 10px;">
		<table border="0" cellpadding="4" cellspacing="0" width="100%">
		  <tr>
		  	<td><?php _e('Saved Search Name', $spoton_idx->domain); ?></td>
			<td>				
<?php
	$spoton_saved_searches = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->spoton_saved_search WHERE blog_id = %d ORDER BY qid DESC", $blog_id));
?>
				<select id="saved_search_name">
<?php foreach ($spoton_saved_searches as $search) { ?>
					<option value="<?php echo esc_attr($search->title); ?>"><?php echo esc_html($search->title); ?></option>
<?php } ?>
				</select>
			</td>
		  </tr>
        </table>
    </div>

	<div class="mceActionPanel clearfix">
			<input type="button" id="cancel" name="cancel" value="Cancel" onclick="tinyMCEPopup.close();" />
			<input type="submit" id="insert" name="insert" value="Insert" onclick="insertSpoton_IDX();" />
	</div>

	</form>
</body>
</html>