<?php

Class BuildRoot {

	public function build_root_maps($arg) {
		global $max_posts_per_page;
		global $BuildRootSub;
		// Get Custom Post Types

		if (isset($arg['post_slugs'])){
			if($post_slugs==FALSE) $show_posts = FALSE;
			$post_slugs = $arg['post_slugs'];
		}
		if (isset($arg['tax_slugs'])){
			if($tax_slugs==FALSE) $show_tax = FALSE;
			$tax_slugs = $arg['tax_slugs'];
		}
		if (isset($arg['show_posts'])){
			$show_posts = $arg['show_posts'];
		} else {
			$show_posts = true;
		}
		if (isset($arg['show_tax'])){
			$show_tax = $arg['show_tax'];
		} else {
			$show_tax = true;
		}
		if ($arg['exclude_post_type']){
			$exclude_post_type = $arg['exclude_post_type'];
		} else {
			$exclude_post_type = array();
		}
		$arg['exclude_tax_type'] ? $exclude_tax_type = $arg['exclude_tax_type'] : $exclude_tax_type = array();

		// Listing the Post types : Start

		$default_post_types = array('post'=>'post','page'=>'page');

		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);		
		$custom_post_types = get_post_types( $args);
		$post_types = array_merge ($default_post_types, $custom_post_types);	
		//print_r($post_types);
		$start = microtime(true);
		$sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($post_types as $post_type) {
			$total_pages = wp_count_posts($post_type)->publish;
			$offset = (get_query_var(sitemaps_n)-1) * $max_posts_per_page;
			$args = array (
				'post_type'=>$post_type,
				'total_pages' =>$total_pages,
				'offset' => $offset,
				'post_slugs' => $post_slugs,
			);			
			if (!in_array($post_type, $exclude_post_type) && $show_posts) {
					$sitemap .= $BuildRootSub->build_root_maps_sub($args);
			}
			
		}
		$time_elapsed_secs = microtime(true) - $start;


		// Listing Post types : End 

		// Listing Taxonomies : Start
		if ($all_categories) {
			$args = array('_builtin' => true);				
			$default_taxonomies = array();
		} else {
			$args = array('_builtin' => false);
			$default_taxonomies = array('category'=>'category', 'post_tag' => 'post_tag');
		}
		$taxonomies = get_taxonomies($args);
		$all_taxonomies = array_merge($taxonomies, $default_taxonomies);
		//$terms = get_terms();

		foreach ($all_taxonomies as $term) {
			$total_terms = wp_count_terms($term);
			$i = 1;
			$offset = ($i-1) * $max_posts_per_page * $max_posts_per_page;
			$args = array (
				'term'=>$term,
				'total_pages' =>$total_terms,
				'offset' => $offset,
				'tax_slugs' => $tax_slugs,
			);			
			if (!in_array($term, $exclude_tax_type) && $show_tax) {
					$sitemap .= $BuildRootSub->build_root_maps_sub($args);
			}		
			$i++;
		}

		// Listing Taxonomies : End 

		// Displaying sitemap1, sitemap2..etc
		global $post_slugs;
		global $tax_slugs;

		$extra_sitemap_count = max(count($post_slugs), count($tax_slugs));
		
		if($extra_sitemap_count && get_query_var('sitemaps_n')==NULL && get_query_var('sitemaps')=='1') {
			
			for ($i=1;$i<=$extra_sitemap_count;$i++){

					$sitemap .= '<sitemap>' . "\n\t" .'<loc>'.home_url().'/';					
					$sitemap .= 'sitemaps'.$i.'.xml</loc>' . "\n\t";

					// getting the Latest modified date
					
					$key = "latestmodifieddate:gmt:sitemaps:".$i;					
					$latest_modified_date = get_transient($key);
					
					if (!$latest_modified_date) {
						$latest_modified_date = current_time('mysql', '1');
						set_transient($key, $latest_modified_date );
					}

					// End of getting Latest Modified date

					$sitemap .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $latest_modified_date ) )).'</lastmod>' . "\n" .'</sitemap>' . "\n\n";

				}
		}


		// Filter for adding extra sitemap


			if(has_filter('add_root_sitemap') && $sitemap!=null) {
				$sitemap = apply_filters('add_root_sitemap', $sitemap);
			}

		$sitemap .= '</sitemapindex>';

		return $sitemap;
	}


}

?>