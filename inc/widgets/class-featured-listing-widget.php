<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

if (!class_exists('Spoton_FeaturedListingWidget')) :

class Spoton_FeaturedListingWidget extends WP_Widget
{
	private static $_options = array();
	private static $_domain = '';

	function Spoton_FeaturedListingWidget()
	{
		global $spoton_idx;
		self::$_options = $spoton_idx->options;
		self::$_domain = $spoton_idx->domain;
		$widget_ops = array('classname' => 'spoton-widget', 'description' => __( 'Show saved property searches using a stylish slider.', 'bwp-rc') );
		$control_ops = array();
		$this->WP_Widget('spoton_flw', __('Spot-on IDX &mdash; Featured Listing Slider', self::$_domain), $widget_ops, $control_ops);
	}

	private static function small_widget($property)
	{
		$permalink = esc_url(spoton_get_ppt_permalink($property));
?>
		<div class="showcase-slide">
			<div class="showcase-content ma clearfix">
				<div class="soidx-listing-thumbnail-single-wrapper sepH_a">
				<div class="aspectcorrect soidx-listing-thumbnail-single">
				<?php if (!empty($property->ImgNam)) { ?>
					<a href="<?php echo $permalink; ?>"><img src=<?php echo str_replace(array('mapinfoboximages', 'retsmapinfoboximages'), array('mapthumbnailimages', 'retsmapthumbnailimages'), $property->ImgNam); ?> /></a>
				<?php } else { ?>
					<a href="<?php echo $permalink; ?>"><img src="http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg" /></a>
				<?php } ?>
				</div>
				</div>
				<h2 class="soidx-property-title-single dp100 tac sepH_a">
					<a href="<?php echo $permalink; ?>"><?php echo esc_html(spoton_get_ppt_addr($property)); ?></a>
				</h2>
				<div class="soidx-featured-price large green_color bld tac sepH_a">
					<?php echo '$' . str_replace('.00', '', number_format($property->Price, 2, '.', ',')); ?>
				</div>
				<div class="soidx-featured-meta-single tac">
				<?php echo str_replace('.00', '', $property->Beds) . ' Bed '; ?>|
				<?php echo str_replace('.00', '', $property->Baths) . ' Bath ';?>|
				<?php echo str_replace('.00', '', number_format($property->SquFeet, 2, '.', ',')) . ' Sq Ft'; ?>
				<div>MLS# <?php echo $property->LN; ?></div>
				</div>
			</div>
		</div>
<?php
	}

	private static function med_widget($property)
	{
		$permalink = esc_url(spoton_get_ppt_permalink($property));
?>
			<div class="showcase-content showcase-medium dp33 clearfix">
				<div class="soidx-listing-thumbnail-medium-wrapper sepH_a">
				<div class="aspectcorrect soidx-listing-thumbnail-medium">
				<?php if (!empty($property->ImgNam)) { ?>
					<a href="<?php echo $permalink; ?>"><img src=<?php echo str_replace(array('mapinfoboximages', 'retsmapinfoboximages'), array('mapthumbnailimages', 'retsmapthumbnailimages'), $property->ImgNam); ?> /></a>
				<?php } else { ?>
					<a href="<?php echo $permalink; ?>"><img src="http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg" /></a>
				<?php } ?>
				</div>
				</div>
				<h2 class="soidx-property-title-medium dp100 tac sepH_a">
					<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html(spoton_get_ppt_addr($property)); ?></a>
				</h2>
				<div class="soidx-featured-price large green_color bld tac">
					<?php echo '$' . str_replace('.00', '', number_format($property->Price, 2, '.', ',')); ?>
				</div>
			</div>
<?php
	}

	private static function large_widget($property)
	{
		$permalink = esc_url(spoton_get_ppt_permalink($property));
?>
		<div class="showcase-slide">
			<div class="showcase-content clearfix">
				<div class="soidx-listing-thumbnail-large-wrapper sepH_a sepV_b">
				<div class="aspectcorrect soidx-listing-thumbnail-large">
				<?php if (!empty($property->ImgNam)) { ?>
					<a href="<?php echo $permalink; ?>"><img src=<?php echo str_replace(array('mapinfoboximages', 'retsmapinfoboximages'), array('mapthumbnailimages', 'retsmapthumbnailimages'), $property->ImgNam); ?> /></a>
				<?php } else { ?>
					<a href="<?php echo $permalink; ?>"><img src="http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg" /></a>
				<?php } ?>
				</div>
				</div>
				<div class="showcase-content-right">
					<h2 class="soidx-property-title-large sepH_a"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html(spoton_get_ppt_addr($property)); ?></a></h2>
					<div class="soidx-featured-meta-single large">
				<?php echo str_replace('.00', '', $property->Beds) . ' Bed '; ?>|
				<?php echo str_replace('.00', '', $property->Baths) . ' Bath ';?>|
				<?php echo str_replace('.00', '', number_format($property->SquFeet, 2, '.', ',')) . ' Sq Ft'; ?>
				<div>MLS# <?php echo $property->LN; ?></div>
				<div class="property sepH_b"><?php echo $property->PropType; ?></div>
				</div>
				<div class="soidx-featured-price xlarge green_color bld sepH_b">
						<?php echo '$' . str_replace('.00', '', number_format($property->Price, 2, '.', ',')); ?>
				</div>
				<div class="dp0">
				<a href="<?php echo $permalink; ?>" class="btn btn_aL fl sepV_a"><?php _e('View Property', self::$_domain); ?></a></div>
				</div>
			</div>
		</div>

<?php
	}

	private static function s4_widget($property)
	{
		$permalink = esc_url(spoton_get_ppt_permalink($property));
?>
		<div class="showcase-slide">
			<div class="showcase-content clearfix">
				<div class="soidx-listing-thumbnail-s4-wrapper">
				<div class="aspectcorrect soidx-listing-thumbnail-s4">
				<?php if (!empty($property->ImgNam)) { ?>
					<a href="<?php echo $permalink; ?>"><img src=<?php echo str_replace(array('mapinfoboximages', 'retsmapinfoboximages'), array('mapslideshowimages', 'retsmapslideshowimages'), $property->ImgNam); ?> /></a>
				<?php } else { ?>
					<a href="<?php echo $permalink; ?>"><img src="http://xpioimages.blob.core.windows.net/mapslideshowimages/default.jpg" /></a>
				<?php } ?>
				</div>
				<div class="cf">
				<h2 class="soidx-property-title-s4 sepH_a"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html(spoton_get_ppt_addr($property)); ?></a></h2>
				</div>
				<div class="soidx-featured-meta-single large fl">
				<?php echo str_replace('.00', '', $property->Beds) . ' Bed '; ?>|
				<?php echo str_replace('.00', '', $property->Baths) . ' Bath ';?>|
				<?php echo str_replace('.00', '', number_format($property->SquFeet, 2, '.', ',')) . ' Sq Ft'; ?>
				</div>
				<div class="soidx-featured-price xlarge green_color bld fr">
						<?php echo '$' . str_replace('.00', '', number_format($property->Price, 2, '.', ',')); ?>
				</div>
				<!--<div class="dp0">
				<a href="<?php echo $permalink; ?>" class="btn btn_aL fl sepV_a"><?php _e('View Property', self::$_domain); ?></a></div>-->
				</div>
			</div>
		</div>

<?php
	}

	function widget($args, $instance)
	{
		global $spoton_pkey, $wpdb, $blog_id;

		$title = (!empty($instance['title'])) ? $instance['title'] : '';
		$qid = (!empty($instance['qid'])) ? trim(strip_tags($instance['qid'])) : 0;
		$style = (!empty($instance['style'])) ? trim(strip_tags($instance['style'])) : 'sml';
		$bg = (!empty($instance['bg'])) ? trim(strip_tags($instance['bg'])) : 'FFFFFF';

		switch ($style)
		{
			default:
			case 'sml':
				$def_h = 300;
				$def_w = 240;
			break;

			case 'med':
				$def_h = 300;
			break;

			case 'lar':
				$def_h = 250;
			break;

			case 's4':
				$def_h = 350;
				$def_w = 600;
			break;

		}

		$w = (!empty($instance['w'])) ? (int) trim(strip_tags($instance['w'])) : $def_w;
		$h = (!empty($instance['h'])) ? (int) trim(strip_tags($instance['h'])) : $def_h;
		$s = (!empty($instance['s'])) ? (int) trim(strip_tags($instance['s'])) : 500;

		$wid = esc_attr($args['widget_id']);
		$wid_w = esc_attr($args['widget_id'] . '-wrapper');

		$query = $wpdb->get_var($wpdb->prepare('SELECT query FROM ' . $wpdb->spoton_saved_search . ' WHERE qid = %d AND blog_id = %d', $qid, $blog_id));
		if (!empty($query))
		{
			$query = explode('^', $query);
			$query = $query[0];
			$proxy = spoton_get_entities();
			$response = $proxy->GenericRetsSearchListings()->Top(12)->filter($query)->Select('PKey,LN,HouNo,Stre,StrSuff,DirSuff,City,State,Zip,PropType,Status,SquFeet,Price,Acre,Beds,Baths,ImgNam')->IncludeTotalCount()->Execute();
		}
?>
<div class="spoton-flw <?php echo $wid_w; ?>">
<?php if (!empty($title)) { ?>
	<h4 class="widget-title"><?php echo esc_html($title); ?></h4>
<?php } ?>
<?php if (!empty($bg)) { ?>
<style type="text/css">
.<?php echo $wid; ?> .showcase-content {
	background-color: #<?php echo esc_html($bg); ?>;
}
</style>
<?php } ?>
	<div class="spoton-flw-loading" style="display: none;"><?php _e('<em>Loading widget ...</em>', self::$_domain); ?></div>
	<div class="spoton-flw-slider <?php echo $wid; ?>">
<?php
	$count = !empty($query) ? sizeof($response->Result) : 0;
	if (empty($count))
	{
?>
		<?php _e('<strong>Spot-on IDX</strong>: No properties found for this search.', self::$_domain); ?>
<?php 
	} 
	else 
	{
		$med_properties = array();
		$property_count = 0;
		foreach ($response->Result as $property)
		{
			$property_count++;
			if ('med' == $style)
			{
				$med_properties[] = $property;
				if (3 == sizeof($med_properties) || $count == $property_count)
				{
?>
		<div class="showcase-slide clearfix">
<?php
					foreach ($med_properties as $med_property)
						self::med_widget($med_property);
?>
		</div>
<?php
					$med_properties = array();
				}
			}
			else if ('lar' == $style)
				self::large_widget($property);
			else if ('s4' == $style)
				self::s4_widget($property);

			else
				self::small_widget($property);
		}
	}
?>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery('.<?php echo $wid_w; ?> .spoton-flw-loading').show();
		jQuery('.<?php echo $wid_w; ?> .spoton-flw-slider').hide();
		jQuery('.<?php echo $wid_w; ?>').imagesLoaded(function(){
			jQuery('.<?php echo $wid_w; ?> .spoton-flw-loading').hide();
			jQuery('.<?php echo $wid_w; ?> .spoton-flw-slider').show();
			FixImages(false, this);
			// AW-Showcase Slider
			jQuery(".<?php echo $wid; ?>").awShowcase(
			{
<?php if (0 != $w) { ?>
				content_width: <?php echo $w; ?>,
<?php } ?>
				content_height: <?php echo $h; ?>,
				auto: false,
				continuous: true,
				loading: true,
				buttons: false,
				btn_numbers: false,
				transition_speed: <?php echo $s . "\n"; ?>
			});
		});
		// Fix for other browsers when a user clicks on arrows
		/*jQuery('.spoton-flw .showcase-arrow-next, .spoton-flw .showcase-arrow-previous').live('click', function(){
			FixImages(false, jQuery(this).parent('.spoton-flw-slider'));
		});*/
	});
	</script>
</div>
<?php
	}

	function update($new_instance, $old_instance)
	{
		$int = array('w', 'h', 's');
		$instance = $new_instance;
		foreach ($instance as $key => &$field_value)
		{
			if (in_array($key, $int))
				$field_value = (int) trim(strip_tags($field_value));
			else
				$field_value = trim(strip_tags($field_value));
		}

		return $instance;
	}

	function form($instance)
	{
		global $spoton_pkey, $wpdb, $blog_id;

		$title = (!empty($instance['title'])) ? trim(strip_tags($instance['title'])) : '';
		$qid = (!empty($instance['qid'])) ? trim(strip_tags($instance['qid'])) : 0;
		$style = (!empty($instance['style'])) ? trim(strip_tags($instance['style'])) : 'sml';
		$bg = (!empty($instance['bg'])) ? trim(strip_tags($instance['bg'])) : 'FFFFFF';

		$w = (!empty($instance['w'])) ? (int) trim(strip_tags($instance['w'])) : 0;
		$h = (!empty($instance['h'])) ? (int) trim(strip_tags($instance['h'])) : 0;
		$s = (!empty($instance['s'])) ? (int) trim(strip_tags($instance['s'])) : 0;

		// Get all saved searches for this blog
		$searches = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->spoton_saved_search WHERE blog_id = %d ORDER BY qid DESC", $blog_id));
		$options = '<option value="0">&nbsp;' . __('<< Please select a saved search >>', self::$_domain) . '&nbsp;</option>' . "\n";
		if (0 < sizeof($searches))
		{
			foreach ($searches as $search)
			{
				$selected = ($qid == $search->qid) ? ' selected="selected" ' : '';
				$options .= '<option value="' . esc_attr($search->qid) . '"' . $selected . '>' . esc_html($search->title) . '</option>' . "\n";
			}
		}
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', self::$_domain); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('qid'); ?>"><?php _e('Saved Search:', self::$_domain); ?></label><br />
			<select id="<?php echo $this->get_field_id('qid'); ?>" name="<?php echo $this->get_field_name('qid'); ?>">
				<?php echo $options; ?>
			</select>
		</p>
		<p>
			<label style="width: 54px; display: inline-block;" for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style:', self::$_domain); ?></label>
			<select id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
				<option value="sml" <?php selected($style, 'sml'); ?>><?php _e('Small', self::$_domain); ?></option>
				<option value="med" <?php selected($style, 'med'); ?>><?php _e('Medium', self::$_domain); ?></option>
				<option value="lar" <?php selected($style, 'lar'); ?>><?php _e('Large', self::$_domain); ?></option>
				<option value="s4" <?php selected($style, 's4'); ?>><?php _e('Style 4', self::$_domain); ?></option>

			</select>
		</p>
		<p>
			<label style="width: 54px; display: inline-block;" for="<?php echo $this->get_field_id('bg'); ?>"><?php _e('BG Color:', self::$_domain); ?></label>
			<strong>#</strong><input size="5" type="text" id="<?php echo $this->get_field_id('bg'); ?>" name="<?php echo $this->get_field_name('bg'); ?>" value="<?php echo esc_attr($bg); ?>" />
			<em>(<?php _e('color hexcode', self::$_domain); ?>)</em>
		</p>
		<p>
			<label style="width: 54px; display: inline-block;" for="<?php echo $this->get_field_id('w'); ?>"><?php _e('Width:', self::$_domain); ?></label>
			<input size="5" type="text" id="<?php echo $this->get_field_id('w'); ?>" name="<?php echo $this->get_field_name('w'); ?>" value="<?php echo esc_attr($w); ?>" />
			px. <em>(<?php _e('0 for auto', self::$_domain); ?>)</em>
		</p>
		<p>
			<label style="width: 54px; display: inline-block;" for="<?php echo $this->get_field_id('h'); ?>"><?php _e('Height:', self::$_domain); ?></label>
			<input size="5" type="text" id="<?php echo $this->get_field_id('h'); ?>" name="<?php echo $this->get_field_name('h'); ?>" value="<?php echo esc_attr($h); ?>" />
			px. <em>(<?php _e('0 for auto', self::$_domain); ?>)</em>
		</p>
		<p>
			<label style="width: 54px; display: inline-block;" for="<?php echo $this->get_field_id('s'); ?>"><?php _e('Speed:', self::$_domain); ?></label>
			<input size="5" type="text" id="<?php echo $this->get_field_id('s'); ?>" name="<?php echo $this->get_field_name('s'); ?>" value="<?php echo esc_attr($s); ?>" />
			<?php _e('ms. <em>(0 for default)</em>', self::$_domain); ?>
		</p>
<?php
	}
}

endif;