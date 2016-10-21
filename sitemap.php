<?php
/*
Plugin Name: Praison Sitemap
Description: Praison Sitemap
Version:     0.01
Plugin URI:  https://mer.vin
Author:      Mervin Praison
Author URI:  https://mer.vin
Text Domain: praisonsitemap
License:     GPL v2 or later

*/



	$max_posts_per_page = 1000; // Define on Build_Root_Maps( )Function 
	$offset = 1; // Define on Build_Root_Maps( )Function
	$query_variable ='' ; // This will apply filter 

	$post_slugs = array('tags', 'tag', 'xml');
	$exclude_post_type = array('tag' => array('post'));	

	$exclude_post_types_post = array_values ($exclude_post_type);	
	foreach ($exclude_post_types_post as $exclude_post_types_post) {
		$exclude_post_types_post_change = $exclude_post_types_post;		
	}
	$exclude_post_types_post = $exclude_post_types_post_change;
	$exclude_post_types_slugs = array_keys ($exclude_post_type);	

	$tax_slugs = array('weather', 'timeanddate', 'traffic', 'roadworks');
	$exclude_tax_type = array('weather' => array('category','section'));

	$exclude_tax_types_post = array_values ($exclude_tax_type);		
	foreach ($exclude_tax_types_post as $exclude_tax_types_post) {
		$exclude_tax_types_post_change = $exclude_tax_types_post;		
	}
	$exclude_tax_types_post = $exclude_tax_types_post_change;
	$exclude_tax_types_slugs = array_keys ($exclude_tax_type);		
	
	$pluginurl = plugin_dir_url( __FILE__ );
	$stylesheet = '<?xml-stylesheet type="text/xsl" href="'.$pluginurl.'css/xml-sitemap-xsl.php"?>';

	function inits() {
		global $wp_rewrite;
		$GLOBALS['wp']->add_query_var( 'sitemaps' );
		$GLOBALS['wp']->add_query_var( 'sitemaps_n' );
		add_rewrite_rule( 'sitemaps\.xml$', 'index.php?sitemaps=1', 'top' );
		add_rewrite_rule( '([^/]+?)-sitemaps([0-9]+)?\.xml$', 'index.php?sitemaps=$matches[1]&sitemaps_n=$matches[2]', 'top' );
		add_rewrite_rule( 'sitemaps([0-9]+)?\.xml$', 'index.php?sitemaps=1&sitemaps_n=$matches[1]', 'top' );
		$wp_rewrite->flush_rules();
	}

	add_action( 'init', 'inits' );

	function redirects() {
		$type = get_query_var( 'sitemaps' );
		if ( empty( $type ) )
			return;
		build_sitemaps( $type );
		output();
		die();
	}

	add_action( 'template_redirect', 'redirects' );


	function canonicals( $redirect ) {
		$sitemap = get_query_var( 'sitemaps' );
		if ( ! empty( $sitemap ) )
			return false;
		return $redirect;
	}

	function request_sitemap_index() {
		$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '';
		$url = home_url( $base . 'sitemap_index.xml' );
		wp_remote_get( $url );
	}

	add_action( 'seo_request_sitemap_index', 'request_sitemap_index'  );

	function ping_searchengines() {
		
		$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '';
		$sitemapurl = urlencode( home_url( $base . 'sitemap_index.xml' ) );
		// Always ping Google, Bing, Ask and Yahoo!
		wp_remote_get('http://www.google.com/webmasters/tools/ping?sitemap='.$sitemapurl);
		wp_remote_get('http://www.bing.com/webmaster/ping.aspx?sitemap='.$sitemapurl);
		wp_remote_get('http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=3usdTDLV34HbjQpIBuzMM1UkECFl5KDN7fogidABihmHBfqaebDuZk1vpLDR64I-&url='.$sitemapurl);
		wp_remote_get('http://submissions.ask.com/ping?sitemap='.$sitemapurl);
	}

	function status_change( $new_status, $old_status, $post ) {
		if ( $new_status != 'publish' )
			return;
	
		wp_cache_delete( 'latestmodifieddate:gmt:post_type' . $post->post_type, 'sitemap_time' );
		wp_cache_delete( 'latestmodifieddate:gmt:term' . $post->term_id, 'sitemap_time' );
		//$options = get_mervin_options();
		//if ( isset($options['post_types-'.$post->post_type.'-not_in_sitemap']) && $options['post_types-'.$post->post_type.'-not_in_sitemap'] )
		//	return;
		if ( WP_CACHE )
			wp_schedule_single_event( time()+(60), 'request_sitemap_index' );
		//if ( seo_get_value( 'sitemap-include', $post->ID ) != 'never' )
		ping_searchengines();
	}

	add_action( 'transition_post_status', 'status_change', 10, 3 );


	add_filter( 'redirect_canonical', 'canonicals' );

	function add_sitemap () {
		do_action ('add_sitemap');
	}

	function new_sitemap(){
		echo "<sitemap><loc>http://localhost/aah/post-sitemaps1.xml</loc><lastmod>2016-10-03 16:31:26</lastmod></sitemap>";
	}
	//add_action('add_sitemap', 'new_sitemap');

	function add_roots_sitemap($string){
		$string .= '<sitemap><loc>http://localhost/aah/post-sitemaps1.xml</loc><lastmod>2016-10-03 16:31:26</lastmod></sitemap>';
		return $string; 
	}
	//add_filter('add_root_sitemap', 'add_roots_sitemap');


	function sitemap_change_url_test($url){
		global $query_variable;	
		$url = $url.$query_variable.'/';
		return $url; 
	}
		
	

	function build_sitemaps() {
		global $wpdb;

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

		// Default Taxonomy without extra Slugs

		if(count($pieces) < 2 && !in_array($parameter, get_post_types()) && $parameter != '1'  ){
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			$sitemap .= get_terms_sitemap(array('taxonomy'=>$parameter));
			$sitemap .= '</urlset>';
		} 
		// Taxonomy with extra Slugs
		elseif (in_array($pieces[1], $tax_slugs) && count($pieces) > 1 && !in_array($pieces[0], get_post_types()) )  {			
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			array_shift($pieces);
			$new_pieces = implode('/', $pieces);
			$sitemap .= get_terms_sitemap(array('taxonomy'=>$parameter, 'slug' => $new_pieces));
			$sitemap .= '</urlset>';

		}elseif ( $args != null && get_query_var(sitemaps) != 1 && count($pieces) < 2) {
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			$sitemap .= build_post_sitemap($args);
			$sitemap .= '</urlset>';
		} elseif (in_array($pieces[1], $post_slugs) && count($pieces) > 1)  {
			
			$sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
			$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"; 
			array_shift($pieces);
			$new_pieces = implode('/', $pieces);
			$sitemap .= build_post_sitemap(array('parameter'=>$parameter, 'slug' => $new_pieces));
			$sitemap .= '</urlset>';
			

		} elseif(get_query_var(sitemaps)=='1' && get_query_var(sitemaps_n)){
			
			
			$count = min(count($post_slugs), count($tax_slugs));
			$post_tax_combine = array_combine(array_slice($post_slugs, 0, $count), array_slice($tax_slugs, 0, $count));
			$post_tax_merge = array_merge($post_slugs, $tax_slugs);			
			global $exclude_post_types_slugs;
			global $exclude_post_types_post;
			global $exclude_tax_types_slugs;
			global $exclude_tax_types_post;
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
						$sitemap .= build_root_maps($args);	
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

			
			if (count($post_slugs) != count($tax_slugs)){
				$post_tax_diff = array_diff($post_tax_merge, $post_tax_done);					
					foreach ($post_tax_diff as $value) {
						if ( get_query_var(sitemaps_n) == $i ) {
							if (count($post_slugs)<count($tax_slugs) ){
								$args = array (
									'post_slugs' => '',
									'tax_slugs' => $value,
									'show_posts' => FALSE,
									'show_tax' => TRUE,
								);
							} else {
								$args = array (
									'post_slugs' => $value,
									'tax_slugs' => '',
									'show_tax' => FALSE,
									'show_posts' => TRUE, 
								);
							}
							$sitemap .= build_root_maps($args);	
						}
						$i++;
					}
			}



		} else {
			$args = null; 
			$sitemap .= build_root_maps($args);

		}
		


		return $sitemap;
			
	}

	function build_root_maps($arg) {
		global $max_posts_per_page;
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
					$sitemap .= build_root_maps_sub($args);
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
					$sitemap .= build_root_maps_sub($args);
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
						$latest_modified_date = get_latest_modified_date($args);
						// wp_cache_set( $key, $latest_modified_date, 'sitemap_time' );
						set_transient($key, $latest_modified_date );
					}	

					//$latest_modified_date = get_latest_modified_date($args);
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
						$latest_modified_date = get_latest_modified_date($args);
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
			$sitemap = sitemap_postsbyoffset($args);
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
			$sitemap = sitemap_postsbyoffset($args);
			return $sitemap;			
		} else {
			return;
		}
	}

	

	function build_one_tags_category_sitemap() {
		$all_cat_ids= all_category_ids();
		foreach ($all_cat_ids as $all_cat_id) {
			$sitemap = sitemap_postsbycategory($all_cat_id);
		}
		return $sitemap;
	}

	/* Get Structure Sitemap Function */

	function structure_sitemap() {

	}


	function output() {
		// Prevent the search engines from indexing the XML Sitemap.
		header( 'X-Robots-Tag: noindex, follow', true );
		
		header( 'Content-Type: text/xml' );
		global $stylesheet;
		echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>';
		echo $stylesheet;
		//echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		echo build_sitemaps();
		echo add_sitemap();
		//echo '</sitemapindex>';
		echo "\n" . '<!-- XML Sitemap generated by Praison SEO -->';

		
		
	}

	/*Functions to call */

	    function all_category_ids()
                
        {
			$args = array(
			  'parent' => 0,
			  'hide_empty' => 0
			  );
			$categories = get_categories( $args ); 
			$my_cat = get_the_category(); 

			$output_categories = array();
			  foreach($categories as $category) { 
			     $output_categories[] = $category->term_id;
			}
			return $output_categories; 			

		}


		function sitemap_postsbycategory($category) {
			// the query			
			global $max_posts_per_page;
			$the_query = new WP_Query( array( 'cat' => $category, 'posts_per_page' => $max_posts_per_page, 'category__not_in' => array() ) ); 
			
			// The Loop
			global $post;
			    
			if ( $the_query->have_posts() ) {
				$string .= '';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();						
			    	$post_slug=$post->post_name;
			    	$latest_modified_date = $post->post_modified_gmt;
					$string .= '<sitemap><loc>'.get_permalink().'</loc>';
					$string .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $latest_modified_date ) )).'</lastmod></sitemap>';
						
					}
				} else {
				// no posts found
				}
			
				return $string;
			
			/* Restore original Post Data */
			wp_reset_postdata();
			
		}
			
		// End of Function call to get 1 post

		function sitemap_postsbyoffset($args) {
			// the query			
			wp_reset_query();
			if(isset($args["offset"])) {
    			$offset = $args["offset"];
  			}
  			if (isset($args["max_posts_per_page"])) { 
  				$max_posts_per_page = $args["max_posts_per_page"];
  			}
  			if (isset($args["post_type"])) { 
  				// $post_type = $args["post_type"];
  				$post_type = $args["post_type"];
  			} 
  			if (isset($args["slug"])) { 
  				$slug = $args["slug"];
  			}

  			global $post;


			$the_query = new WP_Query(array('post_type'=>$post_type,'offset' =>$offset, 'posts_per_page' => $max_posts_per_page, 
    'nopaging' => false, 'orderby' => 'modified', 'order'=>'DESC'));		
			
			// The Loop for Post sitemaps
			    
			if ( $the_query->have_posts() ) {
				$string .= '';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();						
			    	$post_slug=$post->post_name;
			    	$latest_modified_date = $post->post_modified_gmt;
			    	$url = get_permalink();
			    	if (has_filter('sitemap_change_url')){
			    		$url = apply_filters('sitemap_change_url', $url);
			    	}
					$string .= '<url>' . "\n\t" .'<loc>'.$url;
					if ($slug) $string .= $slug.'/';	
					$string .= '</loc>' . "\n\t";
					$string .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $latest_modified_date ) )).'</lastmod>' . "\n\t" ;
					$string .= '<changefreq>weekly</changefreq>' . "\n\t" ;
					$string .= '<priority>0.6</priority>' . "\n" .'</url>' . "\n\n" ;

					}
				} else {
				// no posts found
				}
				// db_to_native();
				wp_reset_query();
				return $string;
			
			/* Restore original Post Data */
			wp_reset_postdata();

			
		}


		// End of Function call to get 1 post

		// Start of Get the list of Taxonomies Sitemap
		function get_terms_sitemap ($args) {
			global $max_posts_per_page;
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
				$latest_modified_date = get_latest_modified_date($args);
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

		// Start : Function to get the latest modified date for a group of pages

		function get_latest_modified_date($args) {

			if(isset($args["offset"])) {
    			$offset = $args["offset"];
  			}
  			if (isset($args["max_posts_per_page"])) { 
  				$max_posts_per_page = $args["max_posts_per_page"];
  			}
  			if (isset($args['post_type']))	{
  				$post_type = $args['post_type'];
  			} elseif (isset($args['term']))	{
  				$term = $args['term'];
  			} elseif (isset($args['taxonomy']))	{
  				$taxonomy = $args['taxonomy'];
  			} 
  			if ($post_type){
				$the_query = new WP_Query(array('post_type'=>$post_type, 'offset' =>$offset, 'posts_per_page' => 1, ));							
			} elseif ($taxonomy) {
				$term_datas = get_terms($taxonomy);
				foreach ($term_datas as $term_data){
					 $term_data_ids[] = $term_data->term_id;
				}
				//$max_posts_per_page_2 = $max_posts_per_page * $max_posts_per_page;
				$max_posts_per_page_2 = 1;
				$offset_2 = $offset * $max_posts_per_page;				
				$the_query = new WP_Query(array('post_type'=>get_post_types(), 'tax_query'=> array(array('taxonomy'=> $taxonomy, 'field'=>'term_id', 'terms'=>$term_data_ids, 'offset' =>$offset_2, 'posts_per_page' => 1))));				
			} elseif ($term) {
				$term_data = get_term($term);
				$the_query = new WP_Query(array('post_type'=>get_post_types(), 'tax_query'=> array(array('taxonomy'=> $term_data->taxonomy, 'terms' =>$term_data->term_id)), 'offset' =>$offset, 'posts_per_page' => 1, ));								
			}

			// The Loop for Sitemaps
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						//print_r($the_query->post->post_modified);
						return $the_query->post->post_modified_gmt;					
					}
				}
		}

		// End  : Function to get the latest modified date for a group of pages


// Using an array as needles in strpos http://stackoverflow.com/questions/6284553/using-an-array-as-needles-in-strpos

function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

function strposav($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return $query; // stop on first true result
    }
    return false;
}

// End of Using an array as needles in strpos

// Multi Dimentionalal in_array ()

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

// Multidimentional to single dimentional array

function array_flatten($array) { 
  if (!is_array($array)) { 
    return FALSE; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } 
    else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
} 




?>