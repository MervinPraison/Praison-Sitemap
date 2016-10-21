<?php

Class SitemapPostsCategory {

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

		function build_one_tags_category_sitemap() {
			$all_cat_ids= all_category_ids();
			foreach ($all_cat_ids as $all_cat_id) {
				$sitemap = sitemap_postsbycategory($all_cat_id);
			}
			return $sitemap;
		}


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

}

?>