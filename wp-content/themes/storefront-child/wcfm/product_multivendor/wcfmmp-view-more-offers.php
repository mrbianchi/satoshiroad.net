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
	?>
				  <div class="wcfm-shipping-policies">
						<h2 class="wcfm_policies_heading"><?php _e( 'More Offers', 'wc-multivendor-marketplace' ); ?></h2>
						<p class="woocommerce-noreviews"><?php _e( 'No more offers for this product!', 'wc-multivendor-marketplace' ); ?></p>
				  </div>
				  <?php
				  return;
	}


$more_offers = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}wcfm_marketplace_product_multivendor` WHERE `parent_product_id` = $parent_product_id" );

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
			$WCFMmp->template->get_template( 'product_multivendor/wcfmmp-view-more-offer-single.php', array( 'offer_product_id' => $parent_product_id, 'store_id' => $store_id ) );
		}
		
		foreach( $more_offers as $more_offer ) {
			$offer_product_id = absint($more_offer->product_id);
			if( $product_id == $offer_product_id ) continue;
			$store_id         = absint($more_offer->vendor_id);
			$WCFMmp->template->get_template( 'product_multivendor/wcfmmp-view-more-offer-single.php', array( 'offer_product_id' => $offer_product_id, 'store_id' => $store_id ) );
		}
		?>
	</div>
	<?php
} else {
	 _e( 'No more offers for this product!', 'wc-multivendor-marketplace' );
}