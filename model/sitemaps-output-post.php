<?php
Class OutputPost {

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
	
}
?>