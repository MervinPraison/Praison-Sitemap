<?php

Class SitemapsFilters {

	public $post_slugs = array('tags', 'tag', 'xml');	
	public $tax_slugs = array('weather', 'timeanddate', 'traffic', 'roadworks');	

	public $max_posts_per_page = 1000; // Define on Build_Root_Maps( )Function 
	public $offset = 1; // Define on Build_Root_Maps( )Function
	public $query_variable ='' ; // This will apply filter

	function __construct() {

	}

	function set_slugs ($Slugs){
		$slugs = $Slugs;
	}

	function get_slugs () {
		return $slugs;
	}


}


?>