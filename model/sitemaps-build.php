<?php

/*

1. Index
2. Build
3. Root
4. Root Sub
5. 

*/

	$SitemapFilters = new SitemapsFilters();
	$post_slugs = $SitemapFilters->post_slugs;	
	$tax_slugs = $SitemapFilters->tax_slugs;

 	$max_posts_per_page = $SitemapFilters->max_posts_per_page; // Define on Build_Root_Maps( )Function 
	$offset = $SitemapFilters->offset; // Define on Build_Root_Maps( )Function
	$query_variable = $SitemapFilters->query_variable; // This will apply filter

	$exclude_post_type = $SitemapFilters->exclude_post_type;	

	$exclude_post_types_post = array_values ($exclude_post_type);	
	foreach ($exclude_post_types_post as $exclude_post_types_post) {
		$exclude_post_types_post_change = $exclude_post_types_post;		
	}
	$exclude_post_types_post = $exclude_post_types_post_change;
	$exclude_post_types_slugs = array_keys ($exclude_post_type);
	
	$exclude_tax_type = $SitemapFilters->exclude_tax_type;

	$exclude_tax_types_post = array_values ($exclude_tax_type);		
	foreach ($exclude_tax_types_post as $exclude_tax_types_post) {
		$exclude_tax_types_post_change = $exclude_tax_types_post;		
	}
	$exclude_tax_types_post = $exclude_tax_types_post_change;
	$exclude_tax_types_slugs = array_keys ($exclude_tax_type);	

Class Build {

	public function build_sitemaps() {
		global $wpdb;
		global $BuildRoot;

		//$sitemap .= build_one_tags_category_sitemap();
		global $query_variable;		
			if ($query_variable) {
				add_filter('sitemap_change_url', 'sitemap_change_url_test');
			}
		$parameter = get_query_var(sitemaps);
		// echo $post_type;
		$args = array ('parameter' => $parameter);
		$pieces = explode("-", $parameter);		
		// print_r($pieces);
		if ( count($pieces) > 1) 
			{
				$parameter = $pieces[0];
			}
		//global $wp_query;	
		//$arraykeys = array_keys($wp_query->query_vars);

		global $tax_slugs;
		global $post_slugs;
		global $SitemapsGetTerms;
		global $BuildPostSitemap;

		// Default Taxonomy without extra Slugs

		if(count($pieces) < 2 && !in_array($parameter, get_post_types()) && $parameter != '1'  ){
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			$sitemap .= $SitemapsGetTerms->get_terms_sitemap(array('taxonomy'=>$parameter));
			$sitemap .= '</urlset>';
		} 
		// Taxonomy with extra Slugs
		elseif (in_array($pieces[1], $tax_slugs) && count($pieces) > 1 && !in_array($pieces[0], get_post_types()) )  {			
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			array_shift($pieces);
			$new_pieces = implode('/', $pieces);
			$sitemap .= $SitemapsGetTerms->get_terms_sitemap(array('taxonomy'=>$parameter, 'slug' => $new_pieces));
			$sitemap .= '</urlset>';

		}elseif ( $args != null && get_query_var(sitemaps) != 1 && count($pieces) < 2) {
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			$sitemap .= $BuildPostSitemap->build_post_sitemap($args);
			$sitemap .= '</urlset>';
		} elseif (in_array($pieces[1], $post_slugs) && count($pieces) > 1)  {			
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			array_shift($pieces);
			$new_pieces = implode('/', $pieces);
			$sitemap .= $BuildPostSitemap->build_post_sitemap(array('parameter'=>$parameter, 'slug' => $new_pieces));
			$sitemap .= '</urlset>';
			

		} elseif(get_query_var(sitemaps)=='1' && get_query_var(sitemaps_n)){
			
			
			$count = min(count($post_slugs), count($tax_slugs));
			$post_tax_combine = array_combine(array_slice($post_slugs, 0, $count), array_slice($tax_slugs, 0, $count));			
			$post_tax_merge = array_merge($post_slugs, $tax_slugs);			
			global $exclude_post_types_slugs;
			global $exclude_post_types_post;
			global $exclude_tax_types_slugs;
			global $exclude_tax_types_post;
			// For key and value pairing

			if ($post_tax_combine) {
				$i = 1;
				foreach ($post_tax_combine as $key => $value) {
					if ( get_query_var(sitemaps_n) == $i ) {						
						if(in_array($key, $exclude_post_types_slugs)) {
							$exclude_post_type = $exclude_post_types_post;							
						} else {
							$exclude_post_type = NULL;
						}
						if(in_array($value, $exclude_tax_types_slugs)) {
							$exclude_tax_type = $exclude_tax_types_post;							
						} else {
							$exclude_tax_type = NULL;
						}

						$args = array (
							'post_slugs' => $key,
							'tax_slugs' => $value,
							'exclude_post_type' => $exclude_post_type,
							'exclude_tax_type' => $exclude_tax_type,
						);
						$sitemap .= $BuildRoot->build_root_maps($args);	
					}
					$i++;
				}
			}

			if ($post_tax_combine){
				foreach($post_tax_combine as $key => $value ){
					$post_tax_done[] = $key;
					$post_tax_done[] = $value;
				}
			}

			// For other slugs apart from key and value pairing from above

			if (count($post_slugs) != count($tax_slugs)){
				if(!$post_slugs) {
					$post_tax_diff=$tax_slugs;
					$i = count($post_tax_combine)+1;
				} elseif(!$tax_slugs) {
					$post_tax_diff=$post_slugs;
					$i = count($post_tax_combine)+1;
				} else {
					$post_tax_diff = array_diff($post_tax_merge, $post_tax_done);
				}
					foreach ($post_tax_diff as $value) {


						if(in_array($value, $exclude_post_types_slugs)) {
							$exclude_post_type = $exclude_post_types_post;							
						} else {
							$exclude_post_type = NULL;
						}
						if(in_array($value, $exclude_tax_types_slugs)) {
							$exclude_tax_type = $exclude_tax_types_post;							
						} else {
							$exclude_tax_type = NULL;
						}


						if ( get_query_var(sitemaps_n) == $i ) {
							if (count($post_slugs)<count($tax_slugs) ){
								$args = array (
									'post_slugs' => '',
									'tax_slugs' => $value,
									'show_posts' => FALSE,
									'show_tax' => TRUE,
									'exclude_post_type' => $exclude_post_type,
									'exclude_tax_type' => $exclude_tax_type,
								);
							} else {
								$args = array (
									'post_slugs' => $value,
									'tax_slugs' => '',
									'show_tax' => FALSE,
									'show_posts' => TRUE, 
									'exclude_post_type' => $exclude_post_type,
									'exclude_tax_type' => $exclude_tax_type,
								);
							}
							$sitemap .= $BuildRoot->build_root_maps($args);	
						}
						$i++;
					}
			}



		} else {
			$args = null; 
			$sitemap .= $BuildRoot->build_root_maps($args);

		}
		


		return $sitemap;
			
	}
}

?>