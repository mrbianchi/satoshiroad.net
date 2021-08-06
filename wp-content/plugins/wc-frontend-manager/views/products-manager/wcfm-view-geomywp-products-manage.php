<?php
/**
 * WCFM plugin views
 *
 * Plugin GEO My WP Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/product-manager
 * @version   3.2.4
 */
global $wp, $WCFM, $wpdb;

$product_id = 0;
if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
}
?>

<!-- collapsible - GEO my WP Support -->
<div class="page_collapsible products_manage_geomywp simple variable grouped external booking" id="wcfm_products_manage_form_geomywp_head"><label class="wcfmfa fa-map-marker"></label><?php _e( 'Location', 'GMW' ); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_geomywp_expander" class="wcfm-content">
		<?php 
		echo do_shortcode( '[gmw_post_location_form post_id='.$product_id.' stand_alone=0 submit_enabled=0 ajax_enabled=0]' );
		?>
	</div>
</div>
<!-- end collapsible -->
<div class="wcfm_clearfix"></div>