<?php
/**
 * WCFMmp Shipping Gateway for shipping by country
 *
 * Plugin Shipping Gateway
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/includes
 * @version   1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

class WCFMmp_Shipping_By_Country extends WC_Shipping_Method {
  /**
  * Constructor for your shipping class
  *
  * @access public
  *
  * @return void
  */
  public function __construct() {
    $this->id                 = 'wcfmmp_product_shipping_by_country';
    $this->method_title       = __( 'Marketplace Shipping by Country', 'wc-multivendor-marketplace' );
    $this->method_description = __( 'Enable vendors to set marketplace shipping per country', 'wc-multivendor-marketplace' );

    $this->enabled      = $this->get_option( 'enabled' );
    $this->title        = $this->get_option( 'title' );
    $this->tax_status   = $this->get_option( 'tax_status' );
    
    if( !$this->title ) $this->title = __( 'Shipping Cost', 'wc-multivendor-marketplace' );

    $this->init();
  }


  /**
  * Init your settings
  *
  * @access public
  * @return void
  */
  function init() {
     // Load the settings API
     $this->init_form_fields();
     $this->init_settings();

     // Save settings in admin if you have any defined
     add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }

  /**
  * Checking is gateway enabled or not
  *
  * @return boolean [description]
  */
  public function is_method_enabled() {
     return $this->enabled == 'yes';
  }

  /**
  * Initialise Gateway Settings Form Fields
  *
  * @access public
  * @return void
  */
  function init_form_fields() {

     $this->form_fields = array(
         'enabled' => array(
             'title'         => __( 'Enable/Disable', 'wc-multivendor-marketplace' ),
             'type'          => 'checkbox',
             'label'         => __( 'Enable Shipping', 'wc-multivendor-marketplace' ),
             'default'       => 'yes'
         ),
         'title' => array(
             'title'         => __( 'Method Title', 'wc-multivendor-marketplace' ),
             'type'          => 'text',
             'description'   => __( 'This controls the title which the user sees during checkout.', 'wc-multivendor-marketplace' ),
             'default'       => __( 'Regular Shipping', 'wc-multivendor-marketplace' ),
             'desc_tip'      => true,
         ),
         'tax_status' => array(
             'title'         => __( 'Tax Status', 'wc-multivendor-marketplace' ),
             'type'          => 'select',
             'default'       => 'taxable',
             'options'       => array(
                 'taxable'   => __( 'Taxable', 'wc-multivendor-marketplace' ),
                 'none'      => _x( 'None', 'Tax status', 'wc-multivendor-marketplace' )
             ),
         ),

     );
  }

  /**
  * calculate_shipping function.
  *
  * @access public
  *
  * @param mixed $package
  *
  * @return void
  */
  public function calculate_shipping( $package = array() ) {
   //print_r($package); die;
   
    if( !apply_filters( 'wcfm_is_allow_store_shipping', true ) ) return; 
		
		$wcfm_shipping_options = get_option( 'wcfm_shipping_options', array() );
		$wcfmmp_store_shipping_enabled = isset( $wcfm_shipping_options['enable_store_shipping'] ) ? $wcfm_shipping_options['enable_store_shipping'] : 'yes';
		if( $wcfmmp_store_shipping_enabled != 'yes' ) return;

    $products = $package['contents'];
    $destination_country = isset( $package['destination']['country'] ) ? $package['destination']['country'] : '';
    $destination_state = isset( $package['destination']['state'] ) ? $package['destination']['state'] : '';

    $amount = 0.0;

    if ( ! $this->is_method_enabled() ) {
       return;
    }
    $vendor_id = isset($package['vendor_id']) ? $package['vendor_id'] : '';
    if ( !self::is_shipping_enabled_for_seller( $vendor_id ) ) {
  //          $tax_rate = false;
  //          
  //          $rate = array(
  //            'id'    => $this->id,
  //            'label' => $this->title,
  //            'cost'  => $amount,
  //            'taxes' => $tax_rate
  //          );
  //          $this->add_rate( $rate );  
       return;
    }

    if( apply_filters('hide_country_shiping_default_zero_cost', false ) ) {
      $wcfmmp_state_rates   = get_user_meta( $vendor_id, '_wcfmmp_state_rates', true );
      $wcfmmp_country_rates = get_user_meta( $vendor_id, '_wcfmmp_country_rates', true );
      if ( isset( $wcfmmp_state_rates[$destination_country] ) ) { 
        if( !array_key_exists( $destination_state, $wcfmmp_state_rates[$destination_country] ) &&
            !array_key_exists( 'everywhere', $wcfmmp_state_rates[$destination_country] ) ) {
          return;
        }
      } else {
        if( !array_key_exists( $destination_country, $wcfmmp_country_rates ) && 
            !array_key_exists( 'everywhere', $wcfmmp_country_rates ) ) {
          return;
        }
      }
    }

     if ( $products ) {
       $amount = $this->calculate_per_seller( $products, $destination_country, $destination_state );
     }

     $tax_rate = ( $this->tax_status == 'none' ) ? false : '';
     
     if( !$amount ) {
     	 $this->title = __('Free Shipping', 'wc-multivendor-marketplace');
     }

     $rate = array(
         'id'    => $this->id . ':1',
         'label' => $this->title,
         'cost'  => $amount,
         'taxes' => $tax_rate
     );

     // Register the rate
     $this->add_rate( $rate );
  }


  /**
  * Check if shipping for this product is enabled
  *
  * @param  integet  $product_id
  *
  * @return boolean
  */
  public static function is_shipping_enabled_for_seller( $vendor_id ) {
    global  $WCFMmp;
    $vendor_shipping_details = get_user_meta( $vendor_id, '_wcfmmp_shipping', true );
    if( !empty($vendor_shipping_details) ) {
      $enabled = $vendor_shipping_details['_wcfmmp_user_shipping_enable'];
      $type = !empty( $vendor_shipping_details['_wcfmmp_user_shipping_type'] ) ? $vendor_shipping_details['_wcfmmp_user_shipping_type'] : '';
      if ( ( !empty($enabled) && $enabled == 'yes' ) && ( !empty($type) ) && 'by_country' === $type ) {
          return true;
      }
    }
    return false;
  }

  /**
  * Check if seller has any shipping enable product in this order
  *
  * @since  2.4.11
  *
  * @param  array $products
  *
  * @return boolean
  */
  public function has_shipping_enabled_product( $products ) {
    foreach ( $products as $product ) {
        if ( !self::is_product_disable_shipping( $product['product_id'] ) ) {
            return true;
        }
    }

    return false;
  }


  /**
  * Get product shipping costs
  *
  * @param  integer $product_id
  *
  * @return array
  */
  public static function get_seller_country_shipping_costs( $vendor_id ) {
    $country_cost = get_user_meta( $vendor_id, '_wcfmmp_country_rates', true );
    $country_cost = is_array( $country_cost ) ? $country_cost : array();

    return $country_cost;
  }


  /**
  * Calculate shipping per seller
  *
  * @param  array $products
  * @param  array $destination
  *
  * @return float
  */
  public function calculate_per_seller( $products, $destination_country, $destination_state  ) {
     $amount = 0.0;
     $price = array();

     $seller_products = array();

     foreach ( $products as $product ) {
			 $vendor_id                     = get_post_field( 'post_author', $product['product_id'] );
			 $seller_products[$vendor_id][] = $product;
     }

     if ( $seller_products ) {

			 foreach ( $seller_products as $vendor_id => $products ) {

				 if ( !self::is_shipping_enabled_for_seller( $vendor_id ) ) {
					continue;
				 }

				 $wcfmmp_shipping_by_country = get_user_meta( $vendor_id, '_wcfmmp_shipping_by_country', true );
				 
				 $wcfmmp_free_shipping_amount = isset($wcfmmp_shipping_by_country['_free_shipping_amount']) ? $wcfmmp_shipping_by_country['_free_shipping_amount'] : '';
				 $wcfmmp_free_shipping_amount = apply_filters( 'wcfmmp_free_shipping_minimum_order_amount', $wcfmmp_free_shipping_amount );

				 $default_shipping_price     = $wcfmmp_shipping_by_country['_wcfmmp_shipping_type_price'];
				 $default_shipping_add_price = $wcfmmp_shipping_by_country['_wcfmmp_additional_product'];

				 $downloadable_count  = 0;
				 $products_total_cost = 0;
				 foreach ( $products as $product ) {

					 if ( isset( $product['variation_id'] ) ) {
							 $is_virtual      = get_post_meta( $product['variation_id'], '_virtual', true );
							 $is_downloadable = get_post_meta( $product['variation_id'], '_downloadable', true );
					 } else {
							 $is_virtual      = get_post_meta( $product['product_id'], '_virtual', true );
							 $is_downloadable = get_post_meta( $product['product_id'], '_downloadable', true );
					 }

					 if ( ( $is_virtual == 'yes' ) || ( $is_downloadable == 'yes' ) ) {
							 $downloadable_count++;
							 continue;
					 }

					 if ( get_post_meta( $product['product_id'], '_overwrite_shipping', true ) == 'yes' ) {
							 $default_shipping_qty_price = get_post_meta( $product['product_id'], '_additional_qty', true );
							 $price[ $vendor_id ]['addition_price'][] = get_post_meta( $product['product_id'], '_additional_price', true );
					 } else {
							 $default_shipping_qty_price = $wcfmmp_shipping_by_country['_wcfmmp_additional_qty'];
							 $price[ $vendor_id ]['addition_price'][] = 0;
					 }

					 $price[ $vendor_id ]['default'] = floatval( $default_shipping_price );

					 if ( $product['quantity'] > 1 ) {
							 $price[ $vendor_id ]['qty'][] = ( ( $product['quantity'] - 1 ) * floatval( $default_shipping_qty_price ) );
					 } else {
							 $price[ $vendor_id ]['qty'][] = 0;
					 }
					 
					 $products_total_cost += (float) get_post_meta( $product['product_id'], '_price', true ) * absint( $product['quantity'] );
				 }
				 
				 if( $wcfmmp_free_shipping_amount && ( $wcfmmp_free_shipping_amount <= $products_total_cost ) ) {
				 	 return apply_filters( 'wcfmmp_shipping_country_calculate_amount', 0, $price, $products, $destination_country, $destination_state );
				 }

				 if ( count( $products ) > 1 ) {
					 $price[ $vendor_id ]['add_product'] =  floatval( $default_shipping_add_price ) * ( count( $products) - ( 1 + $downloadable_count ) );
				 } else {
					 $price[ $vendor_id ]['add_product'] = 0;
				 }

				 $wcfmmp_country_rates = get_user_meta( $vendor_id, '_wcfmmp_country_rates', true );
				 $wcfmmp_state_rates   = get_user_meta( $vendor_id, '_wcfmmp_state_rates', true );

				 if ( isset( $wcfmmp_state_rates[$destination_country] ) ) {
					 if ( array_key_exists( $destination_state, $wcfmmp_state_rates[$destination_country] ) ) {
						 if ( isset( $wcfmmp_state_rates[$destination_country][$destination_state] ) ) {
							 $price[$vendor_id]['state_rates'] = floatval( $wcfmmp_state_rates[$destination_country][$destination_state] );
						 } else {
							 $price[$vendor_id]['state_rates'] = ( isset( $wcfmmp_country_rates[$destination_country] ) ) ? floatval( $wcfmmp_country_rates[$destination_country] ) : 0;
						 }
					 } elseif ( array_key_exists( 'everywhere', $wcfmmp_state_rates[$destination_country] ) ) {
						 $price[$vendor_id]['state_rates'] = ( isset( $wcfmmp_state_rates[$destination_country]['everywhere'] ) ) ? floatval( $wcfmmp_state_rates[$destination_country]['everywhere'] ) : 0;
					 } elseif ( array_key_exists( $destination_country, $wcfmmp_country_rates ) ) {
					 	 $price[$vendor_id]['state_rates'] = ( isset( $wcfmmp_country_rates[$destination_country] ) ) ? floatval( $wcfmmp_country_rates[$destination_country] ) : 0;
					 } else {
						 $price[$vendor_id]['state_rates'] = 0;
					 }
				 } else {
					 if ( !array_key_exists( $destination_country, $wcfmmp_country_rates ) ) {
						 $price[$vendor_id]['state_rates'] = isset( $wcfmmp_country_rates['everywhere'] ) ? floatval( $wcfmmp_country_rates['everywhere'] ) : 0;
					 } else {
						 $price[$vendor_id]['state_rates'] = ( isset( $wcfmmp_country_rates[$destination_country] ) ) ? floatval( $wcfmmp_country_rates[$destination_country] ) : 0;
					 }
				 }
			 }
     }

     if ( !empty( $price ) ) {
			 foreach ( $price as $s_id => $value ) {
				 $amount = $amount + ( ( isset( $value['addition_price'] ) ? array_sum( $value['addition_price'] ) : 0 ) +  ( isset($value['default'] ) ? $value['default'] : 0 ) + ( isset( $value['qty'] ) ? array_sum( $value['qty'] ) : 0 ) + $value['add_product'] + ( isset( $value['state_rates'] ) ? $value['state_rates'] : 0 ) );
			 }
     }
     
     return apply_filters( 'wcfmmp_shipping_country_calculate_amount', $amount, $price, $products, $destination_country, $destination_state );
  }
}