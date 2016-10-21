<?php

Class SitemapsGetTerms {

		// Start of Get the list of Taxonomies Sitemap
		public function get_terms_sitemap ($args) {
			global $max_posts_per_page;
			global $GetLastModified;
			$sitemaps_n = get_query_var(sitemaps_n);
			$offset = ($sitemaps_n-1) * $max_posts_per_page;; 
			if (isset($args['taxonomy'])) {
				$taxonomy = $args['taxonomy'];
			}
			if (isset($args['slug'])) {
				$slug = $args['slug'];
			}

			$all_terms = get_terms (array('taxonomy'=>$taxonomy, 'hide_empty' => false, 'offset'=>$offset, 'number' => $max_posts_per_page));
			//print_r($all_terms);
			foreach ($all_terms as $all_term){
				$args = array (
					'offset' => $offset,
					'max_posts_per_page' => $max_posts_per_page,
					'term'=>$all_term);
				$latest_modified_date = $GetLastModified->get_latest_modified_date($args);
				if (!$latest_modified_date) $latest_modified_date = get_term_meta($all_term->term_id, "latestmodifieddate:gmt", true);
				if (!$latest_modified_date) {
					add_term_meta($all_term->term_id, "latestmodifieddate:gmt", current_time('mysql', '1') );
					$latest_modified_date = current_time('mysql', '1');
				}
					$string .= '<url>' . "\n\t" .'<loc>'.get_category_link($all_term->term_id) ;
					if ($slug) $string .= $slug.'/';					
					$string .= '</loc>' . "\n\t" ;
					$string .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $latest_modified_date ) )).'</lastmod>' . "\n\t" ;
					$string .= '<changefreq>weekly</changefreq>' . "\n\t" ;
					$string .= '<priority>0.6</priority>' . "\n" .'</url>' . "\n\n" ;

			}

			return $string;
		}
		// End of Get the list of Taxonomies Sitemap

}

?>