<?php
/**
 * The Template for displaying store.
 *
 * @package WCfM Markeplace Views Store Sold By Advanced
 *
 * For edit coping this to yourtheme/wcfm/sold-by
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp;

if( empty($product_id) && empty($vendor_id) ) return;

if( empty($vendor_id) && $product_id ) {
	$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
}
if( !$vendor_id ) return;

if( !apply_filters( 'wcfmmp_is_allow_sold_by', true, $vendor_id ) || !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'sold_by' ) ) return;

// Check is store Online
$is_store_offline = get_user_meta( $vendor_id, '_wcfm_store_offline', true );
if ( $is_store_offline ) {
	return;
}

$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint($vendor_id) );
$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($vendor_id) );

$store_logo = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $vendor_id );
if( !$store_logo ) {
	$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
}
?>

<div class="wcfm-clearfix"></div>
<div class="wcfmmp_sold_by_container_advanced">
  
  <div class="wcfmmp_sold_by_label">
		<?php echo $sold_by_text; ?>
		<?php do_action('wcfmmp_single_product_sold_by_badges', $vendor_id ); ?>
	</div>
			
  <div class="wcfmmp_sold_by_container_left">
    <img src="<?php echo $store_logo; ?>" />
  </div>
  <div class="wcfmmp_sold_by_container_right">
		<?php do_action('before_wcfmmp_sold_by_label_single_product', $vendor_id ); ?>
		
		<div class="wcfmmp_sold_by_wrapper">
			<div class="wcfmmp_sold_by_store"><?php echo $store_name; ?></div> 
		</div>
		
		<?php if( apply_filters( 'wcfm_is_pref_vendor_reviews', true ) ) { $WCFMmp->wcfmmp_reviews->show_star_rating( 0, $vendor_id ); } ?>
		
		<?php do_action('after_wcfmmp_sold_by_label_single_product', $vendor_id ); ?>
	</div>
</div>
<div class="wcfm-clearfix"></div>