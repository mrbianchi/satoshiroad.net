<?php
/**
 * The Template for displaying product multivenor more offers.
 *
 * @package WCfM Markeplace Views More Offers
 *
 * For edit coping this to yourtheme/wcfm/product_multivendor 
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp, $product, $wpdb;

$product_id = $product->get_id();

$parent_product_id = 0;
$multi_selling = get_post_meta( $product_id, '_has_multi_selling', true );
$multi_parent  = get_post_meta( $product_id, '_is_multi_parent', true );

if( $multi_parent ) {
	$parent_product_id = absint($multi_parent);
} elseif( $multi_selling ) {
	$parent_product_id = absint($multi_selling);
} elseif( !$multi_parent && !$multi_selling ) {
  _e( 'No more offers for this product!', 'wc-multivendor-marketplace' );
  return;
}


$more_offers = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}wcfm_marketplace_product_multivendor` WHERE `parent_product_id` = $parent_product_id" );

$button_style     = '';
$hover_color      = '';
$hover_text_color = '#ffffff';
$wcfm_options = $WCFM->wcfm_options;
$wcfm_store_color_settings = get_option( 'wcfm_store_color_settings', array() );
if( !empty( $wcfm_store_color_settings ) ) {
	if( isset( $wcfm_store_color_settings['button_bg'] ) ) { $button_style .= 'background: ' . $wcfm_store_color_settings['button_bg'] . ';border-bottom-color: ' . $wcfm_store_color_settings['button_bg'] . ';'; }
	if( isset( $wcfm_store_color_settings['button_text'] ) ) { $button_style .= 'color: ' . $wcfm_store_color_settings['button_text'] . ';'; }
	if( isset( $wcfm_store_color_settings['button_active_bg'] ) ) { $hover_color = $wcfm_store_color_settings['button_active_bg']; }
	if( isset( $wcfm_store_color_settings['button_active_text'] ) ) { $hover_text_color = $wcfm_store_color_settings['button_active_text']; }
} else {
	if( isset( $wcfm_options['wc_frontend_manager_button_background_color_settings'] ) ) { $button_style .= 'background: ' . $wcfm_options['wc_frontend_manager_button_background_color_settings'] . ';border-bottom-color: ' . $wcfm_options['wc_frontend_manager_button_background_color_settings'] . ';'; }
	if( isset( $wcfm_options['wc_frontend_manager_button_text_color_settings'] ) ) { $button_style .= 'color: ' . $wcfm_options['wc_frontend_manager_button_text_color_settings'] . ';'; }
	if( isset( $wcfm_options['wc_frontend_manager_base_highlight_color_settings'] ) ) { $hover_color = $wcfm_options['wc_frontend_manager_base_highlight_color_settings']; }
}

if( !empty( $more_offers ) ) {
	?>
	<div class="wcfmmp_product_mulvendor_container">		
		<div class="wcfmmp_product_mulvendor_row wcfmmp_product_mulvendor_rowhead">
			<div class="wcfmmp_product_mulvendor_rowsub "><?php _e('Store', 'wc-multivendor-marketplace'); ?></div>
			<div class="wcfmmp_product_mulvendor_rowsub"><?php _e('Price', 'wc-multivendor-marketplace'); ?></div>
			<div class="wcfmmp_product_mulvendor_rowsub"><?php _e('Details', 'wc-multivendor-marketplace'); ?></div>
			<div class="wcfm_clearfix"></div>
		</div>			
		<?php
		// Adding Parent Product in Table
		if( $product_id != $parent_product_id ) {
			$store_id         = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $parent_product_id );
			$WCFMmp->template->get_template( 'product_multivendor/wcfmmp-view-more-offer-single.php', array( 'offer_product_id' => $parent_product_id, 'store_id' => $store_id, 'button_style' => $button_style ) );
		}
		
		foreach( $more_offers as $more_offer ) {
			$offer_product_id = absint($more_offer->product_id);
			if( $product_id == $offer_product_id ) continue;
			$store_id         = absint($more_offer->vendor_id);
			$WCFMmp->template->get_template( 'product_multivendor/wcfmmp-view-more-offer-single.php', array( 'offer_product_id' => $offer_product_id, 'store_id' => $store_id, 'button_style' => $button_style ) );
		}
		?>
		<?php if( $hover_color ) { ?>
			<style>a.wcfmmp_product_multivendor_action_button:hover{background: <?php echo $hover_color; ?> !important;background-color: <?php echo $hover_color; ?> !important;border-bottom-color: <?php echo $hover_color; ?> !important;color: <?php echo $hover_text_color; ?> !important;}</style>
		<?php } ?>
	</div>
	<?php
} else {
	_e( 'No more offers for this product!', 'wc-multivendor-marketplace' );
}