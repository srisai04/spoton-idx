<?php
/**
 * Copyright (c) 2011 Khang Minh <betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE
 */

class BWP_GXS_MODULE_INDEX extends BWP_GXS_MODULE {

	// Declare all properties you need for your modules here
	var $requested_modules = array();

	function __construct($requested)
	{
		// Give your properties value here
		$this->set_current_time();
		$this->requested_modules = $requested;
		// Always call this to start building data
		$this->build_data();
	}

	/**
	 * This is the main function that generates our data.
	 *
	 * If your module deals with heavy queries, for example selecting all posts from the database,
	 * you should not use build_data() directly but rather use generate_data(). Open term.php for more details.
	 */
	function build_data()
	{
		global $wpdb, $bwp_gxs, $blog_id;

		// A better limit for sites that have posts with same last modified date - @since 1.0.2
		$limit = sizeof(get_post_types(array('public' => true))) + 1000;

		$latest_post_query = '
			SELECT *
				FROM
				(
					SELECT post_type, max(post_modified) AS mpmd
					FROM ' . $wpdb->posts . "
					WHERE post_status = 'publish'" . '
					GROUP BY post_type
				) AS f
				INNER JOIN ' . $wpdb->posts . ' AS s ON s.post_type = f.post_type
				AND s.post_modified = f.mpmd
			LIMIT ' . (int) $limit;
		$latest_posts = $wpdb->get_results($latest_post_query);

		if (!isset($latest_posts) || !is_array($latest_posts) || 0 == sizeof($latest_posts))
			return false;

		// Build a temporary array holding post type and their latest modified date, sorted by post_modified
		foreach ($latest_posts as $a_post)
			$temp_posts[$a_post->post_type] = $this->format_lastmod(strtotime($a_post->post_modified));
		arsort($temp_posts);
		$prime_lastmod = current($temp_posts);
		
		// Determine whether or not to split post-based sitemaps - @since 1.1.0
		$post_count_array = array();
		if ('yes' == $bwp_gxs->options['enable_sitemap_split_post'])
		{
			$post_count_query = '
				SELECT COUNT(ID) as total, post_type
					FROM ' . $wpdb->posts . "
						WHERE post_status = 'publish'" . '
					GROUP BY post_type
			';
			$post_counts = $wpdb->get_results($post_count_query);
			// Make the result array friendly
			foreach ($post_counts as $count)
				$post_count_array[$count->post_type] = $count->total;
			unset($post_counts);
			unset($count);
		}

		$taxonomies = $bwp_gxs->taxonomies;

		$data = array();
		foreach ($this->requested_modules as $item)
		{
			$data = $this->init_data($data);
			$data['location'] = $this->get_xml_link($item[0]);
			$passed = false; // Whether or not to pass data back at the end
			if ('site' == $item[0])
			{
				// Site home URL sitemap - @since 1.1.5
				$data['lastmod'] = $prime_lastmod;
			}
			else if ('property' == $item[0])
			{
				$saved_searches = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->spoton_saved_search WHERE blog_id = %d", $blog_id));
				// No saved search pages, continue
				if (empty($saved_searches) || 0 == sizeof($saved_searches))
					continue;
				// Loop through all saved search pages and get their total records
				foreach ($saved_searches as $saved_search)
				{
					$passed = true;
					require_once(SPOTON_IDX_LIB_PATH . '/class-listing-entities.php');
					$proxy = new XpioMapRealEstateEntities();
                    
                    $savedQuerry = explode('^', $saved_search->query);
                    $query = $savedQuerry[0];
                    $response = $proxy->GenericRetsSearchListings()->skip(strval(0))->Top('1')->filter($query)->Select('LN')->IncludeTotalCount()->Execute();
					$record = $response->TotalCount();
					// If there's no record, continue
					if (empty($record))
						continue;
					// Using meta_id to dynamically create sitemaps
					$sub_module = $saved_search->qid;
					// If there are more records than the limit for one sitemap, split them up
					if ($record > $bwp_gxs->options['input_split_limit_post'])
					{
						$num_part = floor($record / $bwp_gxs->options['input_split_limit_post']) + 1;
						for ($i = 1; $i <= $num_part; $i++)
						{
							$part_data['location'] 	= $this->get_xml_link($item[0] . '_' . $sub_module . '_part' . $i);
							$part_data['lastmod'] 	= $prime_lastmod;
							$this->data[] = $part_data;
						}
					}
					else
					{
						$data['location'] = $this->get_xml_link($item[0] . '_' . $sub_module);
						$data['lastmod'] = $prime_lastmod;
						$this->data[] = $data;
					}
				}
			}
			else if (isset($item[1]))
			{
				if (isset($item[1]['post']))
				{
					$the_post = $this->get_post_by_post_type($item[1]['post'], $latest_posts);
					if ($the_post)
					{
						// If we have a matching post_type and the total number of posts reach the split limit,
						// we will split this post sitemap accordingly
						if ('yes' == $bwp_gxs->options['enable_sitemap_split_post'] && sizeof($post_count_array) > 0 && isset($post_count_array[$the_post->post_type]) && $post_count_array[$the_post->post_type] > $bwp_gxs->options['input_split_limit_post'])
						{
							$num_part 			= floor($post_count_array[$the_post->post_type] / $bwp_gxs->options['input_split_limit_post']) + 1;
							if (1 < $num_part)
							{
								$data['location'] 	= $this->get_xml_link($item[0] . '_part1');
								$data['lastmod'] 	= $this->format_lastmod(strtotime($the_post->post_modified));
								$this->data[] = $data;
								$time_step = round(7776000 / $num_part);
								$time_step = (20000 > $time_step) ? 20000 : $time_step;
								for ($i = 2; $i <= $num_part; $i++)
								{
									$part_data['location'] = $this->get_xml_link($item[0] . '_part' . $i);
									// Reduce the lastmod for about 1 month
									$part_data['lastmod']  = $this->format_lastmod(strtotime($the_post->post_modified) - $i * $time_step);
									$this->data[] = $part_data;
								}
								$passed = true;
							}
							else
								$data['lastmod'] 	= $this->format_lastmod(strtotime($the_post->post_modified));
						}
						else
							$data['lastmod'] = $this->format_lastmod(strtotime($the_post->post_modified));
					}
				}
				else if (isset($item[1]['taxonomy']))
				{
					foreach ($temp_posts as $post_type => $modified_time)
					{
						if ($this->post_type_uses($post_type, $taxonomies[$item[1]['taxonomy']]))
							$data['lastmod'] = $this->format_lastmod(strtotime($modified_time));
					}
				}
				else if (isset($item[1]['archive']))
					$data['lastmod'] = $prime_lastmod;
			}
			// Just in case something went wrong - @since 1.0.2
			if (empty($data['lastmod']))
				$data['lastmod'] = $prime_lastmod;
			// Pass data back to the plugin
			if (false == $passed)
				$this->data[] = $data;
		}
	}
}
?>