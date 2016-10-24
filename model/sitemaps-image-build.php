<?php



Class ImageSitemapBuild {

	public $ImageSitemap='';

	public function image_sitemap_build($link){

		//$ImageSitemap = $this->$ImageSitemap;

		if ( isset($link['images']) && count($link['images']) > 0 ) {
			foreach( $link['images'] as $src => $img ) {
				$ImageSitemap .= "\t\t<image:image>\n";
				$ImageSitemap .= "\t\t\t<image:loc>".htmlspecialchars( $src )."</image:loc>\n";
				if ( isset($img['title']) )
					$ImageSitemap .= "\t\t\t<image:title>".htmlspecialchars( $img['title'] )."</image:title>\n";
				if ( isset($img['alt']) )
					$ImageSitemap .= "\t\t\t<image:caption>".htmlspecialchars( $img['alt'] )."</image:caption>\n";
				$ImageSitemap .= "\t\t</image:image>\n";
			}
		}

		return $ImageSitemap;
	}

}

?>