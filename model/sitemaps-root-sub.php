<?php

Class BuildRootSub {

	function build_root_maps_sub($args) {

		if(isset($args['post_type'])) {
			$post_type = $args['post_type'];
		}elseif ( isset($args['term'])) {
			$term = $args['term'];
			//$post_type = $term; // Just to make it easy , but actually post_type and category are not same 
		}
		if(isset($args['total_pages'])) {
			$total_pages = $args['total_pages'];
		}
		if (isset ($args['offset'])) {
			$offset = $args['offset'];
		}
		if (isset ($args['post_slugs'])) {
			$post_slugs = $args['post_slugs'];
		}
		if (isset ($args['tax_slugs'])) {
			$tax_slugs = $args['tax_slugs'];
		}

		global $max_posts_per_page;
		global $GetLastModified;

		if (get_query_var('sitemaps')==1 && substr(str_replace('/', '', $_SERVER[REQUEST_URI]), -7) != 'ps0.xml') {			

			$number_of_loops = round($total_pages/$max_posts_per_page);
			if ($number_of_loops<1) $number_of_loops = 1;
			$string = '';
			$start = microtime(true);
			for($i=1;$i<$number_of_loops+1;$i++) {

					
				$offset = ($i-1) * $max_posts_per_page;
				
				if ($post_type && $post_type!='st_kb' && $post_type!='page') {
					$string .= '<sitemap>' . "\n\t" .'<loc>'.home_url().'/'.$post_type;
					if ($post_slugs)$string .= '-'.$post_slugs;
					$string .= '-sitemaps'.$i.'.xml</loc>' . "\n\t";
					$args = array (
						'offset' => $offset,
						'max_posts_per_page' => $max_posts_per_page,
						'post_type'=>$post_type);
					$key = "latestmodifieddate:gmt:post_type:".$post_type.$i;
					//$latest_modified_date = wp_cache_get( $key, 'sitemap_time' );
					//delete_transient($key);
					$latest_modified_date = get_transient($key);
					//print_r($latest_modified_date);
					if (!$latest_modified_date) {
						$latest_modified_date = $GetLastModified->get_latest_modified_date($args);
						// wp_cache_set( $key, $latest_modified_date, 'sitemap_time' );
						set_transient($key, $latest_modified_date );
					}
					$string .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $latest_modified_date ) )).'</lastmod>' . "\n" .'</sitemap>' . "\n\n";
				} elseif ($term){
					$string .= '<sitemap>' . "\n\t" .'<loc>'.home_url().'/'.$term;
					if ($tax_slugs)$string .= '-'.$tax_slugs;
					$string .= '-sitemaps'.$i.'.xml</loc>' . "\n\t";
					//$max_posts_per_page_2 = $max_posts_per_page * $max_posts_per_page;
					//$offset = ($i-1) * $max_posts_per_page_2;					
					$args = array (
						'offset' => $offset, // get offset from the parameter
						'max_posts_per_page' => $max_posts_per_page_2,
						'taxonomy'=>$term);
					$key = "latestmodifieddate:gmt:term:".$term.$i;
					//$latest_modified_date = wp_cache_get( $key, 'sitemap_time' );
					//delete_transient($key);
					$latest_modified_date = get_transient($key);
					//print_r($latest_modified_date);
					if (!$latest_modified_date) {
						$latest_modified_date = $GetLastModified->get_latest_modified_date($args);
						// wp_cache_set( $key, $latest_modified_date, 'sitemap_time' );
						set_transient($key, $latest_modified_date);
					}						
					$string .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $latest_modified_date ) )).'</lastmod>' . "\n" .'</sitemap>' . "\n\n";					
				}

				$time_elapsed_secs = microtime(true) - $start;
				//print_r($time_elapsed_secs);
				if ($time_elapsed_secs > 30 ) header("Refresh:0");
				

			}


			return $string;
		} else {
			return;
		}
	}

	
}

?>