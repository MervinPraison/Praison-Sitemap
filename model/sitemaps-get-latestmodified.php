<?php

Class GetLastModified {

		// Start : Function to get the latest modified date for a group of pages

		public function get_latest_modified_date($args) {

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

}

?>