<?php

Class SitemapsFilters {
	
	public $exclude_post_type = array('tag' => array('post'));	
	public $exclude_tax_type = array('roadworks' => array('category','section'));

	public $max_posts_per_page = 1000; // Define on Build_Root_Maps( )Function 
	public $offset = 1; // Define on Build_Root_Maps( )Function
	public $query_variable ='' ; // This will apply filter
	public $post_slugs = array('tags', 'tag', 'xml');	
	public $tax_slugs = array('weather', 'timeanddate', 'traffic', 'roadworks');	

	function __construct() {
		add_filter('praison_sitemap_post_slugs', $this->add_post_slugs);
	}


	public function post_slugs(){

		if (has_filter('praison_sitemap_post_slugs')){
			$post_slugs = apply_filters('praison_sitemap_post_slugs', $post_slugs);
		} else {
		//public $post_slugs = array('tags', 'tag', 'xml');	
			$post_slugs = array();	
		}
		return $post_slugs;

	}

	public function tax_slugs(){

		if (has_filter('praison_sitemap_tax_slugs')){
			$tax_slugs = apply_filters('praison_sitemap_tax_slugs', $tax_slugs);
		} else {	
			$tax_slugs = array();
			//public $tax_slugs = array('weather', 'timeanddate', 'traffic', 'roadworks');	
		}
		return $tax_slugs;
	}

	function add_post_slugs($slugs){
		$slugs = array();
		//$slugs[] = 'tags';
		return $slugs;
	}

	function set_slugs ($Slugs){
		$slugs = $Slugs;
	}

	function get_slugs () {
		return $slugs;
	}


}

new SitemapsFilters;

?>