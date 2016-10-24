<?php
Class PingSearchEngines {
	public function ping_searchengines() {
		
		$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '';
		$sitemapurl = urlencode( home_url( $base . 'sitemaps.xml' ) );
		// ping Google, Bing, Ask and Yahoo!
		wp_remote_get('http://www.google.com/webmasters/tools/ping?sitemap='.$sitemapurl);
		wp_remote_get('http://www.bing.com/webmaster/ping.aspx?sitemap='.$sitemapurl);
		wp_remote_get('http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=3usdTDLV34HbjQpIBuzMM1UkECFl5KDN7fogidABihmHBfqaebDuZk1vpLDR64I-&url='.$sitemapurl);
		wp_remote_get('http://submissions.ask.com/ping?sitemap='.$sitemapurl);
	}
}
?>