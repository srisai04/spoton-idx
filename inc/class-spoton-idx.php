<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('Spoton_IDX') && class_exists('BWP_PluginBase')) :

class Spoton_IDX extends BWP_PluginBase {

	private static $_is_fake_page = false;
	private static $_fake_page = NULL;
	private static $_entities_loaded = false;
	private static $_req_login = false;
	private static $_sff_fields = array();
	private static $_sff_values = array();
	public $thesis_mode = '';

	public function __construct($plugin_data, $plugin_wp_data)
	{
		if (!parent::__construct($plugin_data, $plugin_wp_data))
			return false;

		require_once(dirname(__FILE__) . '/def-constants.php');
		require_once(dirname(__FILE__) . '/def-options.php');
		require_once(dirname(__FILE__) . '/def-tables.php');
		require_once(dirname(__FILE__) . '/common-functions.php');

		$this->init();
	}

	protected function add_hooks()
	{
		add_filter('query_vars', array($this, 'spoton_query_vars'));
		add_filter('template_include', array($this, 'spoton_dynamic_page'), 9999);
		add_action('parse_query', array($this, 'parse_request'));
		// Spoton AJAX
		add_action('init', array($this, 'do_ajax'));
		// Shortcodes
		add_shortcode('saved_search', array($this, 'display_saved_search'));
		add_shortcode('listing_alert_wizard', array($this, 'display_la_wizard'));
		// Search Form Filters
		add_filter('spoton_idx_sff_get_html', array($this, 'get_sff_html'), 10, 3);
		add_filter('spoton_idx_sff_get_static', array($this, 'get_sff_static'), 10, 3);
		// Load other libraries
		add_action('spoton_idx_loaded', array($this, 'load_libraries'));
	}

	protected function init_properties()
	{
		// If SFF is not enabled, there's nothing to do
		if ('yes' != $this->get_o('enable_sff'))
			return;

		global $wpdb;
		// Get all sff fields
		self::$_sff_fields = $wpdb->get_results('SELECT title, fid, filter, def_value FROM ' . $wpdb->spoton_sff_fields, OBJECT_K);
		// Get all sff values
		self::$_sff_values = $wpdb->get_results('SELECT * FROM ' . $wpdb->spoton_sff_values . ' ssv INNER JOIN ' . $wpdb->spoton_sff_fields . ' ssf ON ssv.fid = ssf.fid AND ssf.hide = 0 AND ssf.filter = 1 ORDER BY ssv.fid ASC, ssv.`order` ASC');
	}

	/*public function init_properties()
	{
		// Activate Thesis mode to correctly load our template files
		if (defined('THESIS_LIB'))
		{
			$this->thesis_mode = '-thesis';
		}
	}*/

	public function enqueue_media()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery.datajs', SPOTON_IDX_JS . '/datajs-1.0.1.min.js', array('jquery'));
		wp_enqueue_script('jquery.template', SPOTON_IDX_JS . '/jquery/jquery.tmpl.min.js', array('jquery'));
		wp_enqueue_script('jquery.cookie', SPOTON_IDX_JS . '/jquery/jquery.cookie.js', array('jquery'));
		wp_enqueue_script('jquery.gallery', SPOTON_IDX_JS . '/adgallery/jquery.ad-gallery.js', array('jquery'));
		wp_enqueue_script('jquery.accordion', SPOTON_IDX_JS . '/jquery/jquery.microaccordion.js', array('jquery'));
		wp_enqueue_script('jquery.tools', SPOTON_IDX_JS . '/jquery/jquery.tools.min.js', array('jquery'));
		wp_enqueue_script('jquery.imagesloaded', SPOTON_IDX_JS . '/jquery/jquery.imagesloaded.min.js', array('jquery'));
		wp_enqueue_script('spoton-idx', SPOTON_IDX_JS . '/spoton-idx.js', array('jquery', 'jquery.imagesloaded'), $this->get_pd('version'));
		wp_enqueue_script('jquery.aw-showcase', SPOTON_IDX_JS . '/jquery/jquery.aw-showcase.min.js', array('jquery'));
		wp_enqueue_script('jquery.fancybox-mouse', SPOTON_IDX_JS . '/fancyapps/lib/jquery.mousewheel-3.0.6.pack.js', array('jquery'));
       	wp_enqueue_script('jquery.fancybox', SPOTON_IDX_JS . '/fancyapps/source/jquery.fancybox.pack.js', array('jquery'));         
        wp_enqueue_script('jquery.fancybox-buttton', SPOTON_IDX_JS . '/fancyapps/source/helpers/jquery.fancybox-buttons.js', array('jquery'), '2.0.4');
		wp_enqueue_script('jquery.fancybox-thumb', SPOTON_IDX_JS . '/fancyapps/source/helpers/jquery.fancybox-thumbs.js', array('jquery'), '2.0.4');
		// PhotoSwipe
		wp_enqueue_script('photoswipe.klass', SPOTON_IDX_JS . '/photoSwipe/klass.min.js', array(), '3.0.4');
		wp_enqueue_script('photoswipe', SPOTON_IDX_JS . '/photoSwipe/code.photoswipe.jquery.min.js', array(), '3.0.4');
		wp_enqueue_style('photoswipe.css', SPOTON_IDX_JS . '/photoSwipe/photoswipe.css');

		wp_enqueue_style('spoton_style', SPOTON_IDX_CSS . '/spoton-idx-style.css');
		wp_enqueue_style('spoton_gallery_style', SPOTON_IDX_JS . '/adgallery/jquery.ad-gallery.css');
		wp_enqueue_style('spoton_fancybox_button', SPOTON_IDX_JS . '/fancyapps/source/jquery.fancybox.css'); 
		wp_enqueue_style('spoton_fancybox_thumb', SPOTON_IDX_JS . '/fancyapps/source/helpers/jquery.fancybox-thumbs.css');
		wp_enqueue_style('spoton_fancybox_fancy', SPOTON_IDX_JS . '/fancyapps/source/helpers/jquery.fancybox-buttons.css');
	}

	public function load_libraries()
	{
		// SEO Library
		require_once(SPOTON_IDX_LIB_PATH . '/BWPP/PropertySEO.php');
		BWP_PropertySEO::construct($this->options, $this->domain);
	}

	public function get_entities()
	{
		if (!self::$_entities_loaded || !(self::$_entities_loaded instanceof XpioMapRealEstateEntities))
		{
			require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
			self::$_entities_loaded = new XpioMapRealEstateEntities();
		}

		return self::$_entities_loaded;
	}

	public function spoton_query_vars($vars)
	{
		// Insert query variables that will be accepted by WordPress
		array_push($vars, 'spoton_page');
		array_push($vars, 'spoton_title');
		return $vars;
	}

	private static function sanitize($value, $type)
	{
		$pattern = '';
		switch ($type)
		{
			case 'ln':
				$pattern = '[^a-z0-9]+';
			break;
		}

		return preg_replace('/' . $pattern . '/ui', '', $value);
	}

	/**
	 * Centrailize all ajax request here
	 */
	public function do_ajax()
	{
		// An array of accepted ajax action
		$actions = array('get_property');
		// An array of accepted sub-actions, this will be filled according to each action
		$sub_actions = array();
		if (!empty($_GET['spoton_do_ajax']) && !empty($_GET['action']) && in_array($_GET['action'], $actions))
		{
			$action = trim($_GET['action']);
			switch ($action)
			{
				case 'get_property':

					// We either get Property Info or Property Images
					$sub_actions = array('property', 'image');
					if (!empty($_GET['sub_action']) && in_array($_GET['sub_action'], $sub_actions))
					{
						$sub_action = trim($_GET['sub_action']);
						// Check other required variables
						global $spoton_pkey;
						$ln = (!empty($_GET['ln'])) ? self::sanitize($_GET['ln'], 'ln') : 0;
						$pkey = $spoton_pkey;
						// $ln and $pkey can be used in templates
						if (empty($ln) || empty($pkey))
							exit;
						// Load ajax template based on sub_action
						if ('property' == $sub_action)
							$template = (@file_exists(STYLESHEETPATH . '/property-ajax.php')) ? STYLESHEETPATH . '/property-ajax.php' : SPOTON_PPT_TPL_PATH . '/property-ajax.php';
						else
							$template = (@file_exists(STYLESHEETPATH . '/property-image-ajax.php')) ? STYLESHEETPATH . '/property-image-ajax.php' : SPOTON_PPT_TPL_PATH . '/property-image-ajax.php';
						// Load entities class, $proxy can be used in templates
						$proxy = $this->get_entities();
						// Load ajax template file
						include_once($template);
					}

				break;
			}
			exit;
		}
	}

	public function parse_request()
	{
		$spoton_page = get_query_var('spoton_page');
		$spoton_title = get_query_var('spoton_title');

		if (!empty($spoton_page))
			set_query_var('pagename', $spoton_page);

		if (!empty($spoton_page))
		{
			// If this user is not logged in, and we have guest_views set to something > 0
			// get number of property views store in cookie and increase by 1
			if (!is_user_logged_in() && 0 < $this->get_o('input_guest_views'))
			{
				$guest_views = (!empty($_COOKIE['spto_property_guest_views'])) ? (int) $_COOKIE['spto_property_guest_views'] : 0;
				// If this number exceeds what is set in admin, stop showing property details page
				self::$_req_login = ($guest_views >= $this->get_o('input_guest_views')) ? true : false;
				// Increase guest views, if needed
				if (!self::$_req_login)
				{
					$guest_views++;
					setcookie('spto_property_guest_views', (int) $guest_views, time() + 1209600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
				}
			}

			// If this user is not logged in, and we have guest_prmt_views set to something > 0, and AURL is enabled
			// get number of property views store in cookie and increase by 1
			if (!is_user_logged_in() && !self::$_req_login && 0 < $this->get_o('input_guest_prmt_views') && 'yes' == $this->get_o('enable_module_aurl'))
			{
				$guest_views = (!empty($_COOKIE['spto_property_guest_prmt_views'])) ? (int) $_COOKIE['spto_property_guest_prmt_views'] : 0;
				// If this number exceeds what is set in admin, prompt the user to register for an account
				if ($guest_views < 0)
				{ /* Do nothing */ }
				else if ($guest_views >= $this->get_o('input_guest_prmt_views'))
				{
					setcookie('spto_property_guest_prmt_views', -1, 0, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
					add_action('wp_footer', array($this, 'prompt_register'), 20);
				}
				else
				{
					$guest_views++;
					setcookie('spto_property_guest_prmt_views', (int) $guest_views, 0, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
				}
			}

			if (!self::$_req_login)
			{
				include_once(dirname(__FILE__) . '/spoton/DynamicPage.php');
				$spotted = new Spoton_DynamicPage($spoton_page . '-' . $spoton_title);
				self::$_is_fake_page = true;
				self::$_fake_page = $spotted->get_fake_post();
				global $wp_query;
				$wp_query->queried_object = self::$_fake_page;
				$wp_query->queried_object_id = 0;
			}
		}
	}

	public function spoton_dynamic_page($template)
	{
		global $post, $wp_rewrite;

		if (!empty($this->thesis_mode))
			return $template;

		// Dynamic page
		$spoton_page			= get_query_var('spoton_page');
		$property_template		= (@file_exists(STYLESHEETPATH . '/property.php')) ? STYLESHEETPATH . '/property.php' : dirname(__FILE__) . '/spoton/property-template/property.php';
	    // Property Search Page
		$spt_templates 			= array('property-search.php', 'property-search-list.php', 'property-search-map.php','registration.php');
		$page_template 			= get_post_meta(get_queried_object_id(), '_wp_page_template', true);
		if (!empty($page_template) && in_array($page_template, $spt_templates))
			$search_template 	= (@file_exists(STYLESHEETPATH . '/' . $page_template)) ? STYLESHEETPATH . '/' . $page_template : dirname(__FILE__) . '/spoton/search-template/' . $page_template;
		// Filter the to-be-loaded template
		if (self::$_req_login)
		{
			return dirname(__FILE__) . '/spoton/property-template/property-need-login.php';
		}
		else if (!empty($spoton_page) && @file_exists($property_template))
		{
			require_once SPOTON_IDX_LIB_PATH . '/class-listing-entities.php';
			return $property_template;
		}
		else if (!empty($search_template) && @file_exists($search_template))
		{
			require_once SPOTON_IDX_LIB_PATH . '/class-listing-entities.php';
			return $search_template;
		}
		else if (is_page() && 'yes' == $this->options['enable_widget_qsw'] && !empty($this->options['select_qsw_landing']) && $post->ID == $this->options['select_qsw_landing'])
		{
			require_once SPOTON_IDX_LIB_PATH . '/class-listing-entities.php';
			// Quick Search Widget Template
			$qsw_template_suffix = ('def' == $this->get_o('select_qsw_type')) ? '' : '-' . trim($this->get_o('select_qsw_type'));
			$qsw_template = (@file_exists(STYLESHEETPATH . '/property-search' . $qsw_template_suffix . '.php')) ? STYLESHEETPATH . '/property-search' . $qsw_template_suffix . '.php' : dirname(__FILE__) . '/spoton/search-template/property-search' . $qsw_template_suffix . '.php';
			return $qsw_template;
		}
		else if (is_page() && 'yes' == $this->options['enable_listing_alert'] && !empty($this->options['select_la_wizard_page']) && $post->ID == $this->options['select_la_wizard_page'])
		{
			if (empty($_GET['la_action']) || !is_user_logged_in())
			{
				// Listing Alert Wizard Template
				$la_wizard_template = (@file_exists(STYLESHEETPATH . '/la-wizard.php')) ? STYLESHEETPATH . '/la-wizard.php' : dirname(__FILE__) . '/spoton/misc-template//la-wizard.php';
				add_action('list_alerts_wizard', array($this, 'display_la_wizard'), 1);
				return $la_wizard_template;
			}
			else
			{
				add_action('list_alerts_add', array($this, 'display_la_wizard_tabs'), 1);
				add_action('list_alerts_edit', array($this, 'display_la_wizard_tabs'), 1);
				add_action('list_alerts_list', array($this, 'display_la_wizard_tabs'), 1);
				return $template;
			}
		}
		else
			return $template;
	}

	public function display_saved_search($atts)
	{
		global $wpdb, $blog_id;

		if (is_singular())
		{
			extract(shortcode_atts(array(
				'name' => ''
			), $atts));

			// Get correct saved search to show
			if (empty($name))
				$saved_search_template = '';
			else
			{
				$saved_query = $wpdb->get_var($wpdb->prepare('SELECT query FROM ' . $wpdb->spoton_saved_search . ' WHERE title = %s AND blog_id = %d', trim($name), $blog_id));
				if (!empty($saved_query))
					$saved_search_template = (@file_exists(STYLESHEETPATH . '/saved-property-search.php')) ? STYLESHEETPATH . '/saved-property-search.php' : dirname(__FILE__) . '/spoton/listing-template/saved-property-search.php';
				else
					$saved_search_template = '';
			}

			// Getting the template's contents and then return it
			if (!empty($saved_search_template))
			{
				ob_start();
				$provider_key = $this->get_o('provider_key');
				// Include necessary libraries
				require_once SPOTON_IDX_LIB_PATH . '/class-listing-entities.php';
				include_once($saved_search_template);
				$content = ob_get_clean();
				return $content;
			}
		}
	}

	public function display_la_wizard()
	{
		$logged_in = (is_user_logged_in()) ? true : false;
		$permalink = get_permalink();
		if ($logged_in)
		{
			wp_redirect(add_query_arg(array('la_action' => 'add'), $permalink));
			exit;
		}
?>
		<div id="la_wizard">
			
<?php
		$this->display_la_wizard_tabs();
?>
			<div class="la_wizard_contents">
<?php
		_e('Please <a class="bwp_aurl_link" href="#">register/login</a> to access our Listing Alert feature.', $this->domain);
?>
			</div>
		</div>
<?php
	}

	public function display_la_wizard_tabs()
	{
		$logged_in = (is_user_logged_in()) ? true : false;
		$permalink = get_permalink();
		$la_active = (!empty($_GET['la_action'])) ? trim($_GET['la_action']) : 'add';
		$la_add_active = ($logged_in && 'add' == $la_active) ? ' class="la_nav_active"' : '';
		$la_list_active = ($logged_in && 'add' != $la_active) ? ' class="la_nav_active"' : '';
?>
			<div class="la_wizard_nav">
<?php if (!$logged_in) : ?>
				<a class="la_nav_active" href="<?php echo esc_url($permalink); ?>"><?php _e('Create Account', $this->domain); ?></a>
<?php endif; ?>
				<a<?php echo $la_add_active; ?> href="<?php echo add_query_arg(array('la_action' => 'add'), $permalink); ?>"><?php _e('Create Alert', $this->domain); ?></a>
				<a<?php echo $la_list_active; ?> href="<?php echo add_query_arg(array('la_action' => 'list'), $permalink); ?>"><?php _e('Manage Alerts', $this->domain); ?></a>
			</div>
<?php
	}

	public static function get_sff_fields($title = '')
	{
		$sff_fields = self::$_sff_fields;
		if (!empty($title) && isset($sff_fields[$title]))
			return $sff_fields[$title];
		else
			return $sff_fields;
	}

	public static function get_sff_values($fid = 0)
	{
		$sff_values = self::$_sff_values;
		if (!empty($fid))
		{
			$return = array();
			foreach ($sff_values as $value)
			{
				if ($fid == $value->fid)
					$return[$value->value] = str_replace(' ', '_', $value->value);
			}
			return $return;
		}
		else
			return $sff_values;
	}

	public function get_sff_html($return, $field_title, $type)
	{
		$fd = $this->get_sff_fields($field_title);

		if (!empty($fd->fid) && 1 == $fd->filter)
		{
			$values = $this->get_sff_values($fd->fid);
			foreach ($values as $display => $value)
			{
				$selected = (!empty($fd->def_value) && $display == $fd->def_value) ? ' selected="selected" ' : '';
				$return .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($display) . '</option>' . "\n";
			}
		}

		return $return;
	}

	public function get_sff_values_by_title($field_title)
	{
		$fd = $this->get_sff_fields($field_title);
		if (!empty($fd->fid) && 1 == $fd->filter)
		{
			$values = $this->get_sff_values($fd->fid);
			return $values;
		}
	}

	public function get_sff_static($return, $field_title, $type)
	{
		$fd = $this->get_sff_fields($field_title);
		if (!empty($fd->fid) && 1 == $fd->filter && !empty($fd->def_value))
			return $fd->def_value;
		else 
			return $return;
	}

	public function get_sff_data()
	{
		return array('fields' => self::$_sff_fields, 'values' => self::$_sff_values);
	}

	public function is_property()
	{
		return self::$_is_fake_page;
	}

	public function get_property_permalink()
	{
		if ($this->is_property())
		{
			$post_name = user_trailingslashit(self::$_fake_page->post_name);
			return trim(home_url('property/mls-' . $post_name));
		}
	}

	public function get_property_title()
	{
		if ($this->is_property())
			return self::$_fake_page->post_title;
	}

	public function prompt_register()
	{
?>
<script type="text/javascript">
			jQuery(document).ready(function(){
				// Prompt user for registration
				jQuery('#spto_aurl_popup').jqmShow();
			});
</script>
<?php
	}
    
    
    

}

endif;

?>