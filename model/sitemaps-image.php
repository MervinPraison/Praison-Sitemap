<?php

	

Class ImageSitemap {

	//private $ImageSitemap ='';

	public function image_sitemap($post) {

				global $ImageSitemapBuild;

				$p = $post;

				$link['images'] = array();
				if ( preg_match_all( '/<img [^>]+>/', $p->post_content, $matches ) ) {
					// print_r($p);
					foreach ( $matches[0] as $img ) {
						// FIXME: get true caption instead of alt / title
						if ( preg_match( '/src=("|\')([^"|\']+)("|\')/', $img, $match ) ) {
							$src = $match[2];
							if ( strpos($src, 'http') !== 0 ) {
								if ( $src[0] != '/' )
									continue;
								$src = get_bloginfo('url') . $src;
							}
							if ( $src != esc_url( $src ) )
								continue;
							if ( isset( $link['images'][$src] ) )
								continue;
							$image = array();
							if ( preg_match( '/title=("|\')([^"\']+)("|\')/', $img, $match ) )
								$image['title'] = str_replace( array('-','_'), ' ', $match[2] );
							if ( preg_match( '/alt=("|\')([^"\']+)("|\')/', $img, $match ) )
								$image['alt'] = str_replace( array('-','_'), ' ', $match[2] );
							$link['images'][$src] = $image;
						}
					}
				}
				if ( preg_match_all( '/\[gallery/', $p->post_content, $matches ) ) {
					$attachments = get_children( array('post_parent' => $p->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
					foreach( $attachments as $att_id => $attachment ) {
						$src = wp_get_attachment_image_src( $att_id, 'large', false );
						$src = $src[0];
						$image = array();
						if ( $alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true) )
							$image['alt'] = $alt;
						
						$image['title'] = $attachment->post_title;
						$link['images'][$src] = $image;
					}
				}
				//$link['images'] = apply_filters( 'sitemap_images_link', $link['images'], $p->ID );
				//print_r($link[images]);

				$ImageSitemap = $ImageSitemapBuild->image_sitemap_build($link);
				//print_r($ImageSitemap);

				return $ImageSitemap;

			}
}

?>