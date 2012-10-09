<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

if (!class_exists('Spoton_QuickSearchWidget')) :

class Spoton_QuickSearchWidget extends WP_Widget
{
	private static $_options = array();
	private static $_domain = '';

	function Spoton_QuickSearchWidget()
	{
		global $spoton_idx;
		self::$_options = $spoton_idx->options;
		self::$_domain = $spoton_idx->domain;
		$widget_ops = array('classname' => 'spoton-widget', 'description' => __( 'A quick property search form that can be put inside any widget area.', 'bwp-rc') );
		$control_ops = array();
		$this->WP_Widget('spoton_qsw', __('Spot-on IDX &mdash; Quick Search', self::$_domain), $widget_ops, $control_ops);
	}

	function widget($args, $instance)
	{
		global $spoton_pkey;

		$state = 'all';
		$style = (!empty($instance['style'])) ? $instance['style'] : 'v';
		$style_css = ('v' == $style) ? ' class="qsw-vertical widget clearfix"' : ' class="qsw-horizontal clearfix"';
		$title = (!empty($instance['title'])) ? $instance['title'] : '';

		// Only show fields that are allowed
		$v_fields = spoton_get_adm_settings();

		// Search form filteres
		$sff_filters = spoton_get_sff_filters();

		// Get cities based on State
		if (empty($v_fields[6]) && 1 != $sff_filters['City'])
		{
			$proxy = spoton_get_entities();
			$query = 'PKey eq ' . $spoton_pkey . "&$" . 'orderby=City';
			$cities = $proxy->RetsCities()->filter($query)->Select('City')->Execute();
			$options = '';
			$options .= '<option value="0" selected="selected">' . __('Any', self::$_domain) . '</option>' . "\n";
			$included = array();
			if (0 < sizeof($cities->Result))
			{
				foreach ($cities->Result as $city_obj)
				{
					if (in_array($city_obj->City, $included))
						continue;
					$included[] = $city_obj->City;
					$city_val = esc_attr(preg_replace('/[\s]+/ui', '_', $city_obj->City));
					$selected = (!empty($_POST['qsw_spto_city']) && $_POST['qsw_spto_city'] == $city_val) ? ' selected="selected" ' : '';
					$options .= '<option value="' . $city_val . '"' . $selected . '>' . esc_html($city_obj->City) . '</option>' . "\n";
				}
			}
		}
?>
<div id="spoton-qsw"<?php echo $style_css; ?>>
<div class="widget-wrap">
	<form class="formEl_a sepH_b" action="<?php echo get_page_link(self::$_options['select_qsw_landing']); ?>" method="POST">
<?php if (!empty($title)) { ?>
	<h4 class="widget-title"><?php echo esc_html($title); ?></h4>
<?php } ?>
<?php if (empty($v_fields[16])) { ?>
	<div class="dp100 sepH_b">
		<div class="lbl_a"><?php _e('Property Types:', self::$_domain); ?></div>
		<select class="large" id="qsw_spto_ptype" name="qsw_spto_ptype">
			<option value="all"><?php _e('All', self::$_domain); ?></option>
<?php 
	if (1 == $sff_filters['Property Type'])
		echo spoton_get_qsw_proptype();
	else
		spoton_get_provider_types(false, true);
?>
		</select>
	</div>
<?php } ?>
<?php if (empty($v_fields[6])) { ?>
	<div class="dp100 sepH_b">
		<div class="lbl_a"><?php _e('City:', self::$_domain); ?></div>
		<select  class="large" id="qsw_spto_city" name="qsw_spto_city">
<?php 
	if (1 == $sff_filters['City'])
		echo spoton_get_qsw_cities();
	else
		echo $options;
?>
		</select>
	</div>
<?php } ?>
	<div class="dp0 sepH_b clearfix">
<?php if (empty($v_fields[8])) { ?>
		<div class="dp50">
			<div class="lbl_a"><?php _e('Price from:', self::$_domain); ?></div>
			<select class="medium" id="qsw_spto_pricefrom" name="qsw_spto_pricefrom" class="medium">
<?php
	if (1 == $sff_filters['Price From'])
		spoton_get_qsw_price();
	else
		spoton_get_price_range();
?>
			</select>
		</div>
<?php } ?>
<?php if (empty($v_fields[9])) { ?>
		<div class="dp50">
			<div class="lbl_a"><?php _e('Price to:', self::$_domain); ?></div>
			<select id="qsw_spto_priceto" name="qsw_spto_priceto" class="medium">
<?php
	if (1 == $sff_filters['Price To'])
		spoton_get_qsw_price(true);
	else
		spoton_get_price_range(true);
?>
			</select>
		</div>
<?php } ?>
	</div>
	<div class="dp0 sepH_b clearfix">
<?php if (empty($v_fields[10])) { ?>
		<div class="dp50">
			<div class="lbl_a"><?php _e('Bedrooms:', self::$_domain); ?></div>
			<select id="qsw_spto_bedrooms" name="qsw_spto_bedrooms" class="medium">
<?php
	if (1 == $sff_filters['Beds'])
		spoton_get_qsw_rooms();
	else
		spoton_get_rooms();
?>
			</select>
		</div>
<?php } ?>
<?php if (empty($v_fields[11])) { ?>
		<div class="sepH_b dp50">
			<div class="lbl_a"><?php _e('Bathrooms:', self::$_domain); ?></div>
			<select id="qsw_spto_bathrooms" name="qsw_spto_bathrooms" class="medium">
<?php
	if (1 == $sff_filters['Baths'])
		spoton_get_qsw_rooms(true);
	else
		spoton_get_rooms(true);
?>
			</select>
		</div>
<?php } ?>
	</div>
	<div class="soidx-button">
		<input class="btn btn_a btn_medium" type="submit" id="spoton_qsw_submit" name="spoton_qsw_submit" value="<?php _e('Search', self::$_domain); ?>" />
	</div>
	</form>
	</div>
</div>
<?php
	}

	function update($new_instance, $old_instance)
	{
		$instance['title'] = trim(strip_tags($new_instance['title']));
		$instance['style'] = trim(strip_tags($new_instance['style']));
		return $instance;
	}

	function form($instance)
	{
		global $spoton_pkey;

		$title = (!empty($instance['title'])) ? trim(strip_tags($instance['title'])) : '';
		$style = (!empty($instance['style'])) ? trim(strip_tags($instance['style'])) : 'v';

		/*$state = 'all';
		require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
		$proxy = new XpioMapRealEstateEntities();
		$query = 'PKey eq ' . $spoton_pkey;
		$states = $proxy->RetsCounties()->filter($query)->Select('State')->Execute();

		$options = '';
		$included = array();
		if (0 < sizeof($states->Result))
		{
			foreach ($states->Result as $state_obj)
			{
				if (in_array($state_obj->State, $included))
					continue;
				$included[] = $state_obj->State;
				$state_val = esc_attr(preg_replace('/[\s]+/ui', '_', $state_obj->State));
				$selected = ($state == $state_val) ? ' selected="selected" ' : '';
				$options .= '<option value="' . $state_val . '"' . $selected . '>' . esc_html($state_obj->State) . '</option>' . "\n";
			}
		}

		$all_selected = ('all' == $state) ? ' selected="selected" ' : '';
		$options .= '<option value="all"' . $all_selected . '>&nbsp;' . __('All', self::$_domain) . '&nbsp;</option>' . "\n";*/
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', self::$_domain); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label style="width: 45px; display: inline-block;" for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style:', self::$_domain); ?></label>
			<select id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
				<option value="v" <?php selected($style, 'v'); ?>><?php _e('Vertical', self::$_domain); ?></option>
				<option value="h" <?php selected($style, 'h'); ?>><?php _e('Horizontal', self::$_domain); ?></option>
			</select>
		</p>
<?php
	}
}

endif;