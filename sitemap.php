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
	

/*

Sitemap Flow

1. Index
2. Build
3. Root
4. Root Sub
5. Post Sitemap
6. Tax Sitemap
7. Posts by Offset
8. Get Latest Modified

*/


	$pluginurl = plugin_dir_url( __FILE__ );
	define( 'SITEMAP_PLUGIN_URL', $pluginurl );

	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-index.php");

	require_once(plugin_dir_path( __FILE__ ) . "/controller/sitemaps-filters.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-postsbyoffset.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-get-latestmodified.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-getterms.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-post-sitemap.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-root.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-root-sub.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-build.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-ping-searchengines.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-image.php");
	require_once(plugin_dir_path( __FILE__ ) . "/model/sitemaps-image-build.php");
	

	$SitemapsPostsOffset = new SitemapsPostsOffset();
	$GetLastModified = new GetLastModified();
	$SitemapsGetTerms = new SitemapsGetTerms();
	$BuildPostSitemap = new BuildPostSitemap();
	$BuildRootSub = new BuildRootSub();
	$BuildRoot = new BuildRoot();
	$Build = new Build();
	$PingSearchEngines = new PingSearchEngines();
	$IndexSitemap = new IndexSitemap();
	$ImageSitemap = new ImageSitemap();
	$ImageSitemapBuild = new ImageSitemapBuild();

	
?>