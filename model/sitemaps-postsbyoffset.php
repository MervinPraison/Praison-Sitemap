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
  			global $OutputPost;


			$the_query = new WP_Query(array('post_type'=>$post_type,'offset' =>$offset, 'posts_per_page' => $max_posts_per_page, 
    'nopaging' => false, 'orderby' => 'modified', 'order'=>'DESC'));		
			
			// The Loop for Post sitemaps
			$string .= ''; 
			if (get_query_var('sitemaps')=='page' && get_query_var('sitemaps_n')=='1')  {
			    	$frontpage_id = get_option( 'page_on_front' );			    	
			    	$frontpage_post = get_post($frontpage_id);
			   		$link['latest_modified_date'] = $frontpage_post->post_modified_gmt;
			   		$link['priority'] = 1.0;			    	
			    	$link['post_slug']='';			    	
			    	$link['url'] = home_url('/');			    	
			    	$link['frequency'] = 'daily';
			    	$link['image_sitemap'] = $ImageSitemap->image_sitemap($frontpage_post);
					if(!$frontpage_id)$string .= $OutputPost->output_sitemap($link);
					
			}
			if ( $the_query->have_posts() ) {
				
				while ( $the_query->have_posts() ) {
					$the_query->the_post();						
			    	$link['post_slug']=$post->post_name;
			    	$link['latest_modified_date'] = $post->post_modified_gmt;
			    	$link['url'] = get_permalink();
			    	$link['priority'] = $this->get_priority($post);
			    	$link['frequency'] = $this->get_frequency($post);
			    	$link['image_sitemap'] = $ImageSitemap->image_sitemap($post);
					$string .= $OutputPost->output_sitemap($link);

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

		public function get_priority($post){				
				// $pri = '';
				$frontpage_id = get_option( 'page_on_front' );
				$blog_id = get_option( 'page_for_posts' );
				if (is_numeric($pri))
					$priority = $pri;
				elseif ($post->post_parent == 0 && $post->post_type == 'page')
					$priority = 0.8;
				else
					$priority = 0.6;				
				if ( $post->ID == $frontpage_id || $post->ID == $blog_id)
					$priority = 1.0;
				return $priority;
		}

		public function get_frequency($post){
			$frontpage_id = get_option( 'page_on_front' );
			$blog_id = get_option( 'page_for_posts' );
			if ( $post->ID != $frontpage_id && $post->ID != $blog_id)
					$frequency = 'weekly'; 
			else 
				$frequency = 'daily'; 
			return $frequency;
		}

}

		// End of Function call to get 1 post
?>