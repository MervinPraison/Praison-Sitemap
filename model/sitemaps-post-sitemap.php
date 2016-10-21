<?php

Class BuildPostSitemap {

	function build_post_sitemap($args) {

		// $page = get_query_var( 'sitemaps_n' ) ? get_query_var( 'sitemaps_n' ) : 1;
		// $offset = ( $page - 1 ) * $display_count;
		//if(isset($args['post_type'])) {
		//	$post_type = $args['post_type'];
		//}
		if ( isset ($args['parameter'])) {
			$post_type = $args['parameter'];
		}
		
		if ( isset ($args['slug'])) {
			$slug = $args['slug'];
		}
		global $SitemapsPostsOffset;


		if(get_query_var(sitemaps_n)&&get_query_var(sitemaps)!='') {
			global $max_posts_per_page;
			$sitemaps_n = get_query_var(sitemaps_n);
			if (!$sitemaps_n) $sitemaps_n = 0;
			$offset = ($sitemaps_n-1) * $max_posts_per_page;
			if ($post_type) {
				$args = array (
					'offset' => $offset,
					'max_posts_per_page' => $max_posts_per_page,
					'post_type' => $post_type,
					'slug' => $slug,
				);
			} elseif ($term){
				$args = array (
					'offset' => $offset,
					'max_posts_per_page' => $max_posts_per_page,
					'term' => $term,
					'slug' => $slug,
				);
			}

			$sitemap = $SitemapsPostsOffset->sitemap_postsbyoffset($args);
			return $sitemap;
		} 
		elseif (substr(str_replace('/', '', $_SERVER[REQUEST_URI]), -7) == 'ps0.xml'){
			// This could be wrong, please change later 

			global $offset;
			global $max_posts_per_page;
			$args = array (
				'offset' => $offset,
				'max_posts_per_page' => $max_posts_per_page,
				'post_type' => $post_type,
			);
			$sitemap = $SitemapsPostsOffset->sitemap_postsbyoffset($args);
			return $sitemap;			
		} else {
			return;
		}
	}

}

?>