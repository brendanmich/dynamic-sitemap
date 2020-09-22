<?php header('Content-type: application/xml; charset=utf-8');

	// Constants
	$SITEMAP_STATIC_PATHS = [
		[ "path" => "/", "priority" => "1.00" ],
		[ "path" => "/blog", "priority" => "0.8" ]
	];

	// Build url base
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	$URL_BASE = $protocol.$domainName;

	// Define constants
	include "constants.php";

	// Get dynamic content from WordPress
	$WP_ENABLED = false;
	try {

		// Check if WP exists
		if (!file_exists('../../wp/wp-config.php')) {
			throw new Exception ('WP does not exist');
		}

		// Initialize WP
		define('WP_USE_THEMES', false);
		require_once("../../wp/wp-config.php");

		// Create query args
		$content_args = array(
			'post_type'       => 'post',
			'posts_per_page'  => -1,
			'post_status'     => 'publish'
		);
		$profile_args = array(
			'post_type'   => 'profile',
			'posts_per_page'  => -1,
			'post_status'     => 'publish'
		);

		// Perform queries
		$content_query = new WP_Query($content_args);
		$profile_query = new WP_Query($profile_args);

		// Set state
		$WP_ENABLED = true;

	} catch (Exception $e) {}

	// Build sitemap
	echo '<?xml version="1.0" encoding="UTF-8"?>
		<urlset
			xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
			xmlns:xhtml="http://www.w3.org/1999/xhtml"
      		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
				http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd
				http://www.w3.org/1999/xhtml
				http://www.w3.org/2002/08/xhtml/xhtml1-strict.xsd">';

	// Add static urls
	foreach ($SITEMAP_STATIC_PATHS as &$value) {

		// Build url header
		echo '<url>
			<loc>' . $URL_BASE . $value['path'] . '</loc>';

		// Build alternatives
		echo '<xhtml:link rel="alternate" hreflang="en-US" href="' . $URL_BASE . $value['path'] . '"/>';
		foreach ($AVAILABLE_LOCALES as &$locale) {
			echo '<xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . $URL_BASE . '/' . $locale . $value['path'] . '"/>';
		}

		// Build url footer
		echo '<lastmod>' . date(DATE_ATOM, time()) . '</lastmod>
			<priority>' . $value['priority'] . '</priority>
			</url>';
	}
	if ($WP_ENABLED === true) {

		// Check for posts
		if ($content_query->have_posts()):
			while ($content_query->have_posts()): $content_query->the_post();

				// Get post parameters
				$post_path = "/post/" . get_post_field("post_name");

				// Build url header
				echo '<url>
					<loc>' . $URL_BASE . $post_path . '</loc>';

				// Build alternatives
				echo '<xhtml:link rel="alternate" hreflang="en-US" href="' . $URL_BASE . $post_path . '"/>';
				foreach ($AVAILABLE_LOCALES as &$locale) {
					echo '<xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . $URL_BASE . '/' . $locale . $post_path . '"/>';
				}

				// Build url footer
				echo '<lastmod>' . date(DATE_ATOM, time()) . '</lastmod>
					<priority>0.64</priority>
					</url>';
			endwhile;
		endif;

		// Check for profiles
		if ($profile_query->have_posts()):
			while ($profile_query->have_posts()): $profile_query->the_post();

				// Get post parameters
				$post_path = "/team/" . get_post_field("post_name");

				// Build url header
				echo '<url>
					<loc>' . $URL_BASE . $post_path . '</loc>';

				// Build alternatives
				echo '<xhtml:link rel="alternate" hreflang="en-US" href="' . $URL_BASE . $post_path . '"/>';
				foreach ($AVAILABLE_LOCALES as &$locale) {
					echo '<xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . $URL_BASE . '/' . $locale . $post_path . '"/>';
				}

				// Build url footer
				echo '<lastmod>' . date(DATE_ATOM, time()) . '</lastmod>
					<priority>0.64</priority>
					</url>';
			endwhile;
		endif;
	}
	echo '</urlset>';
?>
