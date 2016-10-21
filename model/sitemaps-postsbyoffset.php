<?php

Class SitemapsPostsOffset {

		function __construct () {
			
		}

		public function sitemap_postsbyoffset($args) {
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

}

		// End of Function call to get 1 post
?>