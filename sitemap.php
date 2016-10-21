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
//require_once (plugin_dir_url('controller/sitemaps-filters.php'));
	require_once(plugin_dir_path( __FILE__ ) . "/controller/sitemaps-filters.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-postsbyoffset.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-get-latestmodified.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-getterms.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-post-sitemap.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-root.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-root-sub.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-build.php");



	$SitemapsPostsOffset = new SitemapsPostsOffset();
	$GetLastModified = new GetLastModified();
	$SitemapsGetTerms = new SitemapsGetTerms();
	$BuildPostSitemap = new BuildPostSitemap();
	$BuildRootSub = new BuildRootSub();
	$BuildRoot = new BuildRoot();
	$Build = new Build();
	//$Build->build_sitemaps

	$SitemapFilters = new SitemapsFilters();
	$post_slugs = $SitemapFilters->post_slugs;	
	$tax_slugs = $SitemapFilters->tax_slugs;

 	$max_posts_per_page = $SitemapFilters->max_posts_per_page; // Define on Build_Root_Maps( )Function 
	$offset = $SitemapFilters->offset; // Define on Build_Root_Maps( )Function
	$query_variable = $SitemapFilters->query_variable; // This will apply filter

	$SitemapFilters = new SitemapsFilters();
	$post_slugs = $SitemapFilters->post_slugs;	

	$exclude_post_type = array('tag' => array('post'));	

	$exclude_post_types_post = array_values ($exclude_post_type);	
	foreach ($exclude_post_types_post as $exclude_post_types_post) {
		$exclude_post_types_post_change = $exclude_post_types_post;		
	}
	$exclude_post_types_post = $exclude_post_types_post_change;
	$exclude_post_types_slugs = array_keys ($exclude_post_type);
	
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
		global $Build;
		$type = get_query_var( 'sitemaps' );
		if ( empty( $type ) )
			return;
		$Build->build_sitemaps( $type );
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
		
	



	/* Get Structure Sitemap Function */

	function structure_sitemap() {

	}


	function output() {
		// Prevent the search engines from indexing the XML Sitemap.
		header( 'X-Robots-Tag: noindex, follow', true );
		
		header( 'Content-Type: text/xml' );
		global $stylesheet;
		global $Build;
		echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>';
		echo $stylesheet;
		//echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		echo $Build->build_sitemaps();
		echo add_sitemap();
		//echo '</sitemapindex>';
		echo "\n" . '<!-- XML Sitemap generated by Praison SEO -->';

		
		
	}




?>