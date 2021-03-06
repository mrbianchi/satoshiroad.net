<?php
/**
 * The Template for displaying store.
 *
 * @package WCfM Markeplace Views Store Sold By Simple
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

if( $vendor_id ) {
	if( apply_filters( 'wcfmmp_is_allow_sold_by', true, $vendor_id ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'sold_by' ) ) {
		// Check is store Online
		$is_store_offline = get_user_meta( $vendor_id, '_wcfm_store_offline', true );
		if ( $is_store_offline ) {
			return;
		}
		
		$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint($vendor_id) );
		$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($vendor_id) );

		echo '<div class="wcfmmp_sold_by_container">';
		echo '<div class="wcfm-clearfix"></div>';
		do_action('before_wcfmmp_sold_by_label_product_page', $vendor_id );
		echo '<div class="wcfmmp_sold_by_wrapper">';
		
		if( apply_filters( 'wcfmmp_is_allow_sold_by_label', true ) ) {
			echo '<span class="wcfmmp_sold_by_label">' . $sold_by_text . ':&nbsp;</span>';
		}
		
		/*if( apply_filters( 'wcfmmp_is_allow_sold_by_logo', true ) ) {
			$store_logo = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $vendor_id );
			if( !$store_logo ) {
				$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
			}
			echo '<img class="wcfmmp_sold_by_logo" src="' . $store_logo . '" />&nbsp;';
		}*/
		
		echo $store_name . '</div>';
		
		if( apply_filters( 'wcfmmp_is_allow_sold_by_review', true ) ) {
			echo '<div class="wcfm-clearfix"></div>';
			if( apply_filters( 'wcfm_is_pref_vendor_reviews', true ) ) { $WCFMmp->wcfmmp_reviews->show_star_rating( 0, $vendor_id ); }
			echo '<div class="wcfm-clearfix"></div>';
		}
		
		do_action('after_wcfmmp_sold_by_label_product_page', $vendor_id );
		
		echo '<div class="wcfm-clearfix"></div>';
		echo '</div>';
	}
}