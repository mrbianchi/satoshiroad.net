<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-7329842606702440",
    enable_page_level_ads: true
  });
</script>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php
// define the wpseo_opengraph_image callback 
function filter_wpseo_opengraph_image( $img ) { 
	// make filter magic happen here... 
	$img = "https://www.satoshiroad.net/wp-content/themes/storefront-child/og.jpg";
    return $img; 
}; 
function filter_wpseo_opengraph_type( $type ) { 
	// make filter magic happen here... 
	$type = "product";
    return $type;
}; 
         
// add the filter 
if(is_page("marketplace"))
	add_filter( 'wpseo_opengraph_image', 'filter_wpseo_opengraph_image', 10, 1 ); 

if(is_product())
	add_filter( 'wpseo_opengraph_type', 'filter_wpseo_opengraph_type', 10, 1 ); 
wp_head();
/*
	if(is_product())
		$ogimage = get_the_post_thumbnail_url(get_queried_object_id() , 'og_image' );
	else
		$ogimage = "http://satoshiroad.net/wp-content/themes/storefront-child/og.jpg";
	if(is_home())
		$ogtitle = get_the_title( get_option('page_on_front') );
	else
		$ogtitle = get_the_title();
<meta property="og:type"        content="<?php echo is_product() ? "product" : "website"; ?>" />
<meta property="og:image"       content="<?php echo $ogimage; ?>" />
<meta property="og:url"         content="<?php echo home_url( $wp->request ); ?>" />	
*/

?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'storefront_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action( 'storefront_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">

		<?php
		/**
		 * Functions hooked into storefront_header action
		 *
		 * @hooked storefront_header_container                 - 0
		 * @hooked storefront_skip_links                       - 5
		 * @hooked storefront_social_icons                     - 10
		 * @hooked storefront_site_branding                    - 20
		 * @hooked storefront_secondary_navigation             - 30
		 * @hooked storefront_product_search                   - 40
		 * @hooked storefront_header_container_close           - 41
		 * @hooked storefront_primary_navigation_wrapper       - 42
		 * @hooked storefront_primary_navigation               - 50
		 * @hooked storefront_header_cart                      - 60
		 * @hooked storefront_primary_navigation_wrapper_close - 68
		 */
		remove_action( 'storefront_header' , 'storefront_header_cart' , 60 );
		do_action( 'storefront_header' );
		?>

	</header><!-- #masthead -->
	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	if(is_page("personal-store")) {
		remove_action( 'storefront_before_content' , 'woocommerce_breadcrumb' );
	}
	do_action( 'storefront_before_content' );
	?>
	<div class="col-full" id="deptos">
	<?php
	if(!is_search() && (is_page("home") || is_product_category() || is_product()) )
		the_widget("SRSearch_FiltersDeptos");
	?>
	</div>
	<div id="content" class="site-content" tabindex="-1">
		<?php if(!is_page("personal-store")) { ?>
		<div class="col-full">
		<?php } else { ?>
		<div class="col-full" style="max-width: 100%;padding: 0;">
		<?php } ?>
		<?php
		do_action( 'storefront_content_top' );
		if(is_page("home")) echo do_shortcode('[metaslider id="661"]');
