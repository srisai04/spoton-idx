<?php
/**
 * Copyright (c) 2012 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */
if (!class_exists('BWP_PropertySEO')) :

class BWP_PropertySEO {

	private static $_sep = ' - ';
	private static $_meta_desc = '';
	private static $_meta_thumb = '';
	private static $_meta_title = '';
	private static $_meta_url = '';
	private static $_post = NULL;
	private static $_options = array();
	private static $_domain = '';
	private static $_property = NULL;

	public static function construct(array $options, $domain = '')
	{
		// Assign options from main application
		self::$_options = $options;
		self::$_domain = $domain;

		// Inject our headers
		add_action('template_redirect', array(__CLASS__, 'init'));
		add_filter('wp_title', array(__CLASS__, 'do_title'), 9999, 3);

		// Do the rest if SEO is enabled
		if ('yes' == self::$_options['enable_seo_feature'])
		{
			add_action('wp_head', array(__CLASS__, 'do_head'), 9);
			// Remove some default things
			remove_action('wp_head', 'wp_shortlink_wp_head');
			remove_action('wp_head', 'start_post_rel_link');
			remove_action('wp_head', 'index_rel_link');
			remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
			remove_action('wp_head', 'wp_generator');
			// Remove all version generators
			foreach (array('rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head') as $action) {
				remove_action($action, 'the_generator');
			}
		}
	}

	public function init()
	{
		global $post;

		if (!is_object($post) && is_singular())
			self::$_post = get_queried_object();
		else
			self::$_post = $post;
	}

	/**
	* Return the length (in characters) of a UTF-8 string
	* @ignore
	*/
	private static function utf8_strlen($text)
	{
		return mb_strlen($text, 'utf-8');
	}

	/**
	* UTF-8 aware alternative to strtolower
	* @ignore
	*/
	private static function utf8_strtolower($str)
	{
		return mb_strtolower($str);
	}

	private static function is_property()
	{
		global $spoton_idx;
		return $spoton_idx->is_property();
	}

	public static function set_property($property)
	{
		if (!empty($property->LN))
			self::$_property = $property;
	}

	public static function set_thumb($thumb_url)
	{
		self::$_meta_thumb = $thumb_url;
	}

	public static function do_head()
	{
		echo "<!-- Search Engine Optimized -->\n";

		if (self::is_property())
		{
			self::do_meta_desc();
			/*self::do_meta_keywords();*/
		}

		if ('yes' == self::$_options['enable_seo_meta_robot']) self::do_robots();
		if (!empty(self::$_options['input_seo_google_verify'])) self::do_verification();
		if ('yes' == self::$_options['enable_seo_canonical']) self::do_canonical();
		if ('yes' == self::$_options['enable_seo_meta_og']) self::do_opengraph();

		echo "<!-- / Search Engine Optimized -->\n";
	}

	public static function do_title($wp_title, $sep, $seplocation)
	{
		$title = $wp_title;

		$blog_name = ('yes' == self::$_options['enable_seo_blogname']) ? get_bloginfo('name') : '';
		self::$_sep = (!empty($sep)) ? ' ' . trim($sep) . ' ' : self::$_sep;

		if (self::is_property() && !empty(self::$_post->post_title))
			$title = self::$_post->post_title;
		else if (is_home())
			return $title;
		else if (is_singular())
			return $title;
		else if (is_search())
			return $title;
		else if (is_tag())
			return $title;
		else if (is_404())
			return $title;

		// Paged?
		/*$paged = get_query_var('paged');
		if (!empty($paged))
			$title .= self::$_sep . __('Page', self::$_domain) . ' ' . $paged;*/

		// Comment Page?
		/*$cpage = get_query_var('cpage');
		if (!empty($cpage))
			$title .= self::$_sep . __('Comment Page', self::$_domain) . ' ' . $cpage;*/

		// OG thing
		if (strpos($wp_title, '<title>') === false)
		{
			self::$_meta_title = $title . self::$_sep . $blog_name;
			return esc_html($title . self::$_sep . $blog_name);
		}
		else
		{
			$title = '<title>' . esc_html($title . self::$_sep . $blog_name) . '</title>';
			self::$_meta_title = $title;
			return $title;
		}
	}

	private static function do_meta_desc()
	{
		global $post;

		$meta_desc = '';
		/*if (is_home())
			$meta_desc = bwp_get_var('seo_metadesc_home');
		else if (is_singular())
		{
			$meta_desc = get_post_meta(self::$_post->ID, bwp_get_var('meta_seo_metadesc'), true);
			if (empty($meta_desc))
			{
				$excerpt 	= get_the_excerpt();
				$excerpt	= (!empty($excerpt)) ? betterwp_trim_excerpt($excerpt, 55) : betterwp_trim_excerpt(self::$_post->post_content, 55, 1);
				$meta_desc = strip_tags($excerpt);
			}
		}
		else if (is_search())
			$meta_desc = '';
		else if (is_tax() || is_tag || is_category())
		{
			$tax = get_queried_object();
			if (!empty($tax->description))
				$meta_desc = $tax->description;
			else if (!empty($tax->name))
				$meta_desc = rlocal('ARCHIVE_META_DESC', 1, array($tax->name, $tax->name));
		}*/

		if (isset(self::$_property))
		{
			$property = self::$_property;
			$beds = (int) $property->Beds;
			$baths = (int) $property->Baths;
			$beds = (!empty($beds)) ? ', ' . (int) $property->Beds . ' bed(s)' : '';
			$baths = (!empty($baths)) ? ', ' . (int) $property->Baths . ' bath(s)' : '';
			$squfeet = (!empty($property->SquFeet)) ? ', ' . (int) $property->SquFeet . ' Square Feet' : '';
			$remark = (!empty($property->Remark)) ? '. ' . $property->Remark : '';
			$meta_desc = strip_tags($property->City . ' Real Estate' . $beds . $baths . $squfeet . ', ' . $property->PropType . ', located at ' . spoton_get_ppt_addr($property) . ' on sale for $' . number_format($property->Price, 0) . ', MLS#' . $property->LN . $remark);
		}

		$meta_desc = trim(preg_replace('/[\s]+/uis', ' ', $meta_desc));
		$meta_desc = trim($meta_desc, '.') . '.';
		// OG thing
		self::$_meta_desc = $meta_desc;
		if (!empty($meta_desc))
			echo "<meta name='description' content='" . esc_attr($meta_desc) . "'/>\n";
	}
	
	/*private static function do_meta_keywords()
	{
		global $post;
		$meta_keywords = '';
		if (is_home())
			$meta_keywords = bwp_get_var('seo_metakey_home');

		if (empty($meta_keywords))
		{
			if (is_singular())
			{
				$meta_keywords = get_post_meta(self::$_post->ID, bwp_get_var('meta_seo_metakeywords'), true);
				if (empty($meta_keywords) && !empty(self::$_post->post_content))
					$meta_keywords = $this->simple_make_keywords(strip_tags(self::$_post->post_content));
			}
			else if (is_search())
				$meta_keywords = '';
			else if (is_tax() || is_tag || is_category())
			{
				$tax = get_queried_object();
				if (!empty($tax->description))
					$meta_keywords = $this->simple_make_keywords(strip_tags($tax->description));
				else
					$meta_keywords = 'archive page, archive, ' . $tax->name;
			}
		}
		
		$meta_keywords = trim($meta_keywords);
		if (!empty($meta_keywords))
			echo "<meta name='keywords' content='" . esc_attr($meta_keywords) . "'/>\n";
	}*/
	
	private static function do_canonical()
	{
		global $wp_the_query;

		if (!is_singular() && !self::is_property())
			return;

		if ( is_singular() && !$id = $wp_the_query->get_queried_object_id() )
			return;

		$link = (isset(self::$_property) && self::is_property()) ? spoton_get_ppt_permalink(self::$_property) : get_permalink($id);

		// OG thing
		self::$_meta_url = $link;

		echo "<link rel='canonical' href='$link' />\n";
	}

	private static function do_verification()
	{
		echo '<meta name="google-site-verification" content="' . esc_attr(self::$_options['input_seo_google_verify']) . '" />' . "\n";
	}
	
	private static function do_robots()
	{
		global $wp_query;
		
		$robots 			= array();
		$robots['index'] 	= 'index';
		$robots['follow'] 	= 'follow';
		
		if (is_archive())
		{
			$robots['index']  = 'noindex';
			$robots['follow'] = 'follow';
		}
		
		$robotsstr = $robots['index'] . ',' . $robots['follow'];
		
		if (!empty($robotsstr))
			echo "<meta name='robots' content='" . $robotsstr . "'/>\n";
	}
	
	private static function do_opengraph()
	{
		if ((!is_singular() && !is_home()) || empty(self::$_meta_desc))
			return false;
		$thumb = (empty(self::$_meta_thumb)) ? self::$_options['input_seo_default_thumb'] : self::$_meta_thumb;
		$type  = (is_home()) ? 'website' : 'article';
		$url   = (is_home()) ? home_url() : self::$_meta_url;
		$struct = '<meta property="og:%s" content="%s"/>' . "\n";
		$output = sprintf($struct, 'title', esc_attr(self::$_meta_title));
		$output .= sprintf($struct, 'type', $type);
		$output .= sprintf($struct, 'image', esc_attr($thumb));
		$output .= sprintf($struct, 'url', esc_attr($url));
		$output .= sprintf($struct, 'site_name', esc_attr(get_bloginfo('name')));
		if (!empty(self::$_options['input_seo_fb_appid']))
			$output .= sprintf($struct, 'app_id', esc_attr(self::$_options['input_seo_fb_appid']));
		$output .= sprintf($struct, 'description', esc_attr(self::$_meta_desc));
		echo $output;
	}
}

endif;

?>