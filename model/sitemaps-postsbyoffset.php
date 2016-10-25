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
  				$link['slug'] = $slug;
  			}

  			global $post;
  			global $ImageSitemap;


			$the_query = new WP_Query(array('post_type'=>$post_type,'offset' =>$offset, 'posts_per_page' => $max_posts_per_page, 
    'nopaging' => false, 'orderby' => 'modified', 'order'=>'DESC'));		
			
			// The Loop for Post sitemaps
			    
			if ( $the_query->have_posts() ) {
				$string .= '';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();						
			    	$link['post_slug']=$post->post_name;
			    	$link['latest_modified_date'] = $post->post_modified_gmt;
			    	$link['url'] = get_permalink();
			    	$link['priority'] = $this->get_priority($post);
			    	$link['frequency'] = $this->get_frequency($post);
			    	$link['image_sitemap'] = $ImageSitemap->image_sitemap($post);
					$string .= $this->output_sitemap($link);

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

		public function output_sitemap($link){
			    	if (has_filter('sitemap_change_url')){
			    		$link['url'] = apply_filters('sitemap_change_url', $link['url']);
			    	}
					$string .= '<url>' . "\n\t" .'<loc>'.$link['url'];
					if ($link['slug']) $string .= $link['slug'].'/';	
					$string .= '</loc>' . "\n\t";
					$string .= '<lastmod>'.htmlspecialchars(date( 'c', strtotime( $link['latest_modified_date'] ) )).'</lastmod>' . "\n\t" ;
					$string .= '<changefreq>'.$link['frequency'].'</changefreq>' . "\n\t" ;					
					$string .= '<priority>'.$link['priority'].'</priority>' . "\n" ; 
					$string .= $link['image_sitemap'];
					$string .= '</url>' . "\n\n" ;

			return $string;
		}

		public function get_priority($post){				
				// $pri = '';
				// $front_id = '';
				if (is_numeric($pri))
					$priority = $pri;
				elseif ($post->post_parent == 0 && $post->post_type == 'page')
					$priority = 0.8;
				else
					$priority = 0.6;
				if ( $post->ID == $front_id )
					$priority = 1.0;
				return $priority;
		}

		public function get_frequency($post){
			$frequency = 'weekly'; 
			return $frequency;
		}

}

		// End of Function call to get 1 post
?>