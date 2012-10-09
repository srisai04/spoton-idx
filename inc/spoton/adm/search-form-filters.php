<?php

// Get all available fields
$sff_fields = $wpdb->get_results('SELECT * FROM ' . $wpdb->spoton_sff_fields . ' ORDER BY fid ASC');

$sff_field_html = '<select name="spto_sff_field" id="spto_sff_field">' . "\n";
$sff_field_html .= '<option value="0">' . __('Please select a field&nbsp;', $this->domain) . '</option>' . "\n";

$sff_def = array();

foreach ($sff_fields as $field)
{
	// Assign default values for later use
	$sff_def[$field->title] = $field->def_value;
	if ($field->static)
		continue;
	$sff_field_html .= '<option value="' . esc_attr($field->fid) . '">' . esc_html($field->title) . '</option>' . "\n";
}

$sff_field_html .= '</select>' . "\n";

?>

<script type="text/javascript">
<!-- All magic happens here -->
function sff_move_opt_up(selectId)
{
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for (var i = 1; i < selectOptions.length; i++) {
		var opt = selectOptions[i];
		if (opt.selected) {
			selectList.removeChild(opt);
			selectList.insertBefore(opt, selectOptions[i - 1]);
		}
	}
}
function sff_move_opt_down(selectId)
{
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for (var i = selectOptions.length - 2; i >= 0; i--)
	{
		var opt = selectOptions[i];
		if (opt.selected) {
			var nextOpt = selectOptions[i + 1];
			selectList.removeChild(nextOpt);
			selectList.insertBefore(nextOpt , selectOptions[i]); 
		}
	}
}
function loading_sff()
{
	jQuery('.bwp-ajax-loadera').css('visibility', 'visible');
}
function complete_sff()
{
	jQuery('.bwp-ajax-loadera').css('visibility', 'hidden');
}
jQuery(document).ready(function() {
	jQuery('#sff_dnm_up').live('click', function(){
		sff_move_opt_up('sff_dnm_v2');
	});
	jQuery('#sff_dnm_down').live('click', function(){
		sff_move_opt_down('sff_dnm_v2');
	});
	jQuery('#sff_dnm_get_val').live('click', function(){
		selectList = jQuery('#sff_dnm_v2 option');
		if (0 == selectList.length)
			alert('<?php _e('Please select some values from available values first', $this->domain); ?>');
		else
		{
			jQuery(this).parent().html('<select name="sff_dnm_def_value">' + jQuery('#sff_dnm_v2').html() + '</select>');
		}
	});
	jQuery('#spto_sff_field').change(function() {
		if (0 == jQuery(this).val())
			return false;
		loading_sff();
		jQuery(this).attr('disabled', 'disabled');
		jQuery('#sff_error_mess').hide();
		jQuery('#sff_dnm_filter').hide();
		jQuery('#sff_dynamic_fields').hide();
		var data = {
			action: 'spoton_idx_sff',
			job: 'get_field',
			_wpnonce: jQuery('form[name="spoton_idx_sff"] input[name="_wpnonce"]').val(),
			fid: jQuery(this).val()
		};
		jQuery.post(ajaxurl, data, function(response) {
			if ('fid' == response)
			{
				jQuery('#sff_error_mess').text('<?php _e('You have selected an invalid field', $this->domain); ?>');
				jQuery('#sff_error_mess').show();
			}
			else if ('none' == response)
			{
				jQuery('#sff_error_mess').text('<?php _e('No value found for the selected field', $this->domain); ?>');
				jQuery('#sff_error_mess').show();					
			}
			else
			{
				jQuery('#sff_error_mess').hide();
				jQuery('#sff_dynamic_fields table tbody').html(response);
				jQuery.configureBoxes({
						'box1View': 'sff_dnm_v1',
						'box1Storage': 'sff_dnm_s1',
						'box1Filter': 'sff_dnm_f1',
						'box1Clear': 'sff_dnm_f1_clear',
						'box1Counter': 'sff_dnm_c1',
						'box2View': 'sff_dnm_v2',
						'box2Storage': 'sff_dnm_s2',
						'box2Filter': 'sff_dnm_f2',
						'box2Clear': 'sff_dnm_f2_clear',
						'box2Counter': 'sff_dnm_c2',
						'to1': 'sff_dnm_to1',
						'to2': 'sff_dnm_to2',
						'allTo1': 'sff_dnm_ato1',
						'allTo2': 'sff_dnm_ato2',
						'useSorting': false
					}
				);
				if (1 == jQuery('input[name="sff_dnm_filtered"]').val())
					jQuery('#sff_dnm_enb_filter').attr('checked', 'checked');
				jQuery('#sff_dnm_filter').toggle('fast');
				jQuery('#sff_dynamic_fields').toggle('fast');
			}
			complete_sff();
			jQuery('#spto_sff_field').removeAttr('disabled');
		});
	});
});
</script>

<ul style="margin: 0 10px;" class="spoton_sff_fields">
	<li class="clear">
		<div class="clearfix">
			<h4><?php _e('Set default values for static fields', $this->domain); ?></h4>
			<table class="bwp-table" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th><?php _e('Field', $this->domain); ?></th>
						<th class="bwp-hidden"><?php _e('Available Values', $this->domain); ?></th>
						<th class="bwp-td-med"><?php _e('Default Value', $this->domain); ?></th>
						<th><?php _e('Filtering', $this->domain); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 
	foreach ($sff_fields as $field) : 
		if (!$field->static)
			continue;
?>
					<tr>
						<td class="sff_field_label"><?php _e($field->title, $this->domain); ?></td>
<?php 
		if ('Price From' == $field->title || 'Price To' == $field->title)
		{ 
			$to = ('Price From' == $field->title) ? false : true;
			$post_key = ($to) ? 'spto_sff_priceto' : 'spto_sff_pricefrom';
?>
						<td class="bwp-hidden">
							<select>
								<?php spoton_get_price_range($to); ?>
							</select>
						</td>
						<td class="bwp-td-med">
							<select id="<?php echo $post_key; ?>" name="<?php echo $post_key; ?>">
								<?php spoton_get_price_range($to, $sff_def[$field->title]); ?>
							</select>
						</td>
<?php 
		}
		else if ('Beds' == $field->title || 'Baths' == $field->title)
		{
			$baths = ('Beds' == $field->title) ? false : true;
			$post_key = ($baths) ? 'spto_sff_bathrooms' : 'spto_sff_bedrooms';
?>
						<td class="bwp-hidden">
							<select>
								<?php spoton_get_rooms($baths); ?>
							</select>
						</td>
						<td class="bwp-td-med">
							<select id="<?php echo $post_key; ?>" name="<?php echo $post_key; ?>">
								<?php spoton_get_rooms($baths, $sff_def[$field->title]); ?>
							</select>
						</td>
<?php
		}
		else if ('Square Feet' == $field->title)
		{
			$post_key = 'spto_sff_squarefeet';
?>
						<td class="bwp-hidden">
							<select>
								<?php spoton_get_sqfeet(); ?>
							</select>
						</td>
						<td class="bwp-td-med">
							<select id="<?php echo $post_key; ?>" name="<?php echo $post_key; ?>">
								<?php spoton_get_sqfeet(false, $sff_def[$field->title]); ?>
							</select>
						</td>
<?php
		}
		else if ('Acreage From' == $field->title || 'Acreage To' == $field->title)
		{ 
			$to = ('Acreage From' == $field->title) ? false : true;
			$post_key = ($to) ? 'spto_sff_acreageto' : 'spto_sff_acreagefrom';
?>
						<td class="bwp-hidden">
							<select>
								<?php spoton_get_acre_range($to); ?>
							</select>
						</td>
						<td class="bwp-td-med">
							<select id="<?php echo $post_key; ?>" name="<?php echo $post_key; ?>">
								<?php spoton_get_acre_range($to, $sff_def[$field->title]); ?>
							</select>
						</td>
<?php 
		}
		else if ('Time on Market' == $field->title)
		{ 
			$post_key = 'spto_sff_market';
?>
						<td class="bwp-hidden">
							<select>
								<?php spoton_get_tom(); ?>
							</select>
						</td>
						<td class="bwp-td-med">
							<select id="<?php echo $post_key; ?>" name="<?php echo $post_key; ?>">
								<?php spoton_get_tom(false, $sff_def[$field->title]); ?>
							</select>
						</td>
<?php 
		}
?>
						<td>
							<?php
								$checked = (1 == $field->filter) ? 'checked="checked"' : '';
							?>
							<input type="checkbox" name="<?php echo $post_key . '_ft'; ?>" <?php echo $checked; ?> /> <?php _e('Yes', $this->domain); ?>
						</td>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</li>
	<li class="clear">
		<div class="clearfix">
			<h4 id="sff_dynamic_header"><?php _e('Customize values for dynamic fields', $this->domain); ?></h4>
			<label for="spto_sff_field" class="bwp-opton-page-label"><?php _e('Select a field to continue', $this->domain); ?></label>
			<p class="bwp-option-page-inputs">
				<?php echo $sff_field_html; ?>
				&nbsp;&nbsp;<span class="bwp-red" id="sff_error_mess" style="display: none;"></span>
				<span class="bwp-ajax-loadera" style="visibility: hidden;"><!-- --></span>
				<br />
			</p>
		</div>
	</li>
	<li class="clear" style="display: none;" id="sff_dnm_filter">
		<div class="clearfix">
			<label for="sff_dnm_enb_filter" class="bwp-opton-page-label"><?php _e('Enable filtering for selected field?', $this->domain); ?></label>
			<p class="bwp-option-page-inputs">
				<input type="checkbox" name="sff_dnm_enb_filter" id="sff_dnm_enb_filter" />
				<br />
			</p>
		</div>
	</li>
	<li class="clear" style="display: none;" id="sff_dynamic_fields">
		<div class="clearfix">
			<table class="bwp-table" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th style="width: 210px;"><?php _e('Available Values', $this->domain); ?></th>
						<th style="width: 50px;"></th>
						<th style="width: 210px;"><?php _e('Displayed Values', $this->domain); ?></th>
						<th style="width: 50px;"></th>
						<th><?php _e('Default Value', $this->domain); ?></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</li>
</ul>