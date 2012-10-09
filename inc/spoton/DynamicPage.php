<?php
if (!class_exists('Spoton_DynamicPage')) :

class Spoton_DynamicPage {

	private $_post_title = '';	
	private $_post_contents = '';
	private $_requested_page = '';	
	private $_post = NULL;

	public function __construct($spoton_page)
	{
		$this->_requested_page = $spoton_page;
		$this->build_page();
	}

	private function build_title()
	{
		$post_title = $this->_requested_page;
		$post_title = preg_replace('/([a-z0-9]+)-/ui', '', $post_title, 1);
		$this->_post_title = ucwords(strtolower(str_replace('-', ' ', $post_title)));
	}

	private function build_contents()
	{
	}

	private function create_fake_post()
	{
		$post = new stdClass;
		$post->post_author = 1;
		$post->post_name = $this->_requested_page;
		$post->post_title = $this->_post_title;
		$post->post_content = $this->_post_contents;
		$post->post_type = 'post';
		$post->post_parent = 0;
		$post->ID = -1;
		$post->post_status = 'publish';
		$post->comment_status = 'closed';
		$post->ping_status = 'open';
		$post->comment_count = 0;
		$post->post_date = current_time('mysql');
		$post->post_date_gmt = current_time('mysql', 1);
		$this->_post = $post;
	}

	public function build_the_posts($posts)
	{
		global $wp_query;
		$posts[] 		= $this->_post;	
		$wp_query->post = $this->_post;
		return $posts;
	}

	private function build_page()
	{
		global $wp_query;
		$this->build_title();
		$this->build_contents();
		$this->create_fake_post();
		add_filter('the_posts', array($this, 'build_the_posts'));
		$wp_query->is_page 	= true;
		$wp_query->is_404 	= false;
	}

	public function get_fake_post()
	{
		return $this->_post;
	}
	
}

endif;
?>