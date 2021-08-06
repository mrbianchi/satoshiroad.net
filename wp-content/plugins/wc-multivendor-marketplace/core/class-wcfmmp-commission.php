<?php
/**
 * WCFMmp plugin core
 *
 * WCfMmp Commission
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Commission {

	public function __construct() {
		global $WCFM, $WCFMmp;
		
		// Generating Marketplace Order on WC Process Checkout
		add_action( 'woocommerce_checkout_order_processed', array(&$this, 'wcfmmp_checkout_order_processed'), 30, 3 );
		
		// Generating Marketplace Order on WCFM Manual Order Process
		add_action( 'wcfm_manual_order_processed', array(&$this, 'wcfmmp_checkout_order_processed'), 30, 3 );
		
		// Recheck Marketplace New Order after WC New Order object update
		add_action( 'woocommerce_order_object_updated_props', array( $this, 'wcfmmp_new_order_check' ), 100, 2 );
		
		// WC POS Order Process
		add_action( 'woocommerce_pos_process_payment', array( $this, 'wcfmmp_pos_order_check' ), 100, 2 );
		
		// Generating Marketplace Order for Subscription Renewal Order
		add_filter( 'wcs_renewal_order_created', array(&$this, 'wcfmmp_renewal_order_processed'), 30, 2 );
		
		// Update Marketplace Order Status on WC Order Status changed
		add_action( 'woocommerce_order_status_changed', array(&$this, 'wcfmmp_order_status_changed'), 30, 4 );
		
		// Withdrawal Status Completed
		add_action( 'wcfmmp_withdraw_status_completed_by_commission', array(&$this, 'wcfmmp_commission_withdrawal_id_update' ), 10, 2 );
		
		// On Order Item Refund
		add_action( 'woocommerce_order_refunded', array(&$this, 'wcfmmp_commission_order_item_refund' ), 30, 2 );
		
		// On New Item added to Order
		add_action( 'woocommerce_ajax_order_items_added', array(&$this, 'wcfmmp_commission_order_item_add' ), 30, 2 );
		
		// On Order Item Edit
		add_action( 'woocommerce_saved_order_items', array(&$this, 'wcfmmp_commission_order_item_edit' ), 30, 2 );
		
		// ON Delete Order Item Delete Commision Order
		add_action( 'woocommerce_before_delete_order_item', array(&$this, 'wcfmmp_commission_order_item_delete' ), 30 );
		add_action( 'woocommerce_delete_order_item', array(&$this, 'wcfmmp_commission_order_item_delete' ), 30 );
		
		// ON Trashed Order Trash Commision Order
		add_action( 'woocommerce_trash_order', array(&$this, 'wcfmmp_commission_order_trash' ), 30 );
		add_action( 'wp_trash_post', array(&$this, 'wcfmmp_commission_order_trash' ), 30 );
		
		// ON Delete Order delete Commision Order
		add_action( 'woocommerce_delete_order', array(&$this, 'wcfmmp_commission_order_delete' ), 30 );
		add_action( 'before_delete_post', array(&$this, 'wcfmmp_commission_order_delete' ), 30 );
	}
	
	/**
	 * WCfM Marketplace Order create on WC Order Process
	 */
	public function wcfmmp_checkout_order_processed( $order_id, $order_posted, $order ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$order_id ) return;
		if ( get_post_meta( $order_id, '_wcfmmp_order_processed', true ) ) return;
		
		if (!$order)
      $order = wc_get_order( $order_id );
    
    if( !is_a( $order , 'WC_Order' ) ) return;
    
    $wcfmmp_order_processed = false;
    
    $customer_id = 0;
    if ( $order->get_user_id() ) 
    	$customer_id = $order->get_user_id();
    
    $payment_method = ! empty( $order->get_payment_method() ) ? $order->get_payment_method() : '';
    $order_status = $order->get_status();
    $shipping_status = 'pending'; 
    $is_withdrawable = 1;
    $is_auto_withdrawal = 0;
    
    $disallow_payment_methods = get_wcfm_marketplace_disallow_active_order_payment_methods();
    $withdrawal_reverse = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : '';
    if( $withdrawal_reverse && !empty( $disallow_payment_methods ) && in_array( $payment_method, array_keys( $disallow_payment_methods ) ) ) {
    	$is_auto_withdrawal = 1;
    }
    
    // Set Shipping Status Complete for Virtual Products
    if( !$order->get_formatted_shipping_address() ) {
    	$shipping_status = 'completed'; 
    }
   
    // Ger Shipping Vendor Packages
    $vendor_shipping = array();
    if( $WCFMmp && $WCFMmp->wcfmmp_shipping ) {
    	$vendor_shipping = $WCFMmp->wcfmmp_shipping->get_order_vendor_shipping( $order );
    }
   
    $items = $order->get_items( 'line_item' );
    if( !empty( $items ) ) {
			foreach( $items as $order_item_id => $item ) {
				
				// Check whether order item already processed or not
				$order_item_processed = wc_get_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed', true );
				if( $order_item_processed ) continue;
				
				$line_item = new WC_Order_Item_Product( $item );
				$product  = $line_item->get_product();
				$product_id = $line_item->get_product_id();
				$variation_id = $line_item->get_variation_id();
				
				if( $product_id ) {
					$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
					
					if( $vendor_id ) {
						
						// Updating Order Item meta with Vendor ID
						wc_update_order_item_meta( $order_item_id, '_vendor_id', $vendor_id );
						
						$discount_amount   = 0;
						$discount_type     = '';
						$other_amount      = 0;
						$other_amount_type = '';
						$withdraw_charges  = 0;
						$refunded_qty      = 0;
						$refund_status     = 'pending';
						$refunded_amount   = $refunded_total_tax = $refunded_shipping_amount = $refunded_shipping_tax = 0;
						$grosse_total      = $gross_tax_cost = $gross_shipping_cost = $gross_shipping_tax = $gross_sales_total = 0;
						$total_commission  = $commission_amount = $tax_cost = $shipping_cost = $shipping_tax = 0;
						$is_partially_refunded = 0;
						
						// Item Refunded Amount
						if ( $refunded_amount = $order->get_total_refunded_for_item( $order_item_id ) ) {
							$refunded_qty = $order->get_qty_refunded_for_item( $order_item_id );
							$is_partially_refunded = 1;
							$refund_status = 'completed';
						}
						
						// Item commission calculation
						$commission_rule = '';
						if( $WCFMmp->wcfmmp_vendor->is_vendor_deduct_discount( $vendor_id, $order_id ) ) {
							$commission_rule   = $WCFMmp->wcfmmp_product->wcfmmp_get_product_commission_rule( $product_id, $variation_id, $vendor_id, ( $line_item->get_total() - $refunded_amount ), ( $line_item->get_quantity() - $refunded_qty ), $order_id );
							$commission_amount = $this->wcfmmp_get_order_item_commission( $order_id, $vendor_id, $product_id, $variation_id, ( $line_item->get_total() - $refunded_amount ), ( $line_item->get_quantity() - $refunded_qty ), $commission_rule );
							$grosse_total      = $line_item->get_total();
						} else {
							$commission_rule   = $WCFMmp->wcfmmp_product->wcfmmp_get_product_commission_rule( $product_id, $variation_id, $vendor_id, ( $line_item->get_subtotal() - $refunded_amount ), ( $line_item->get_quantity() - $refunded_qty ), $order_id );
							$commission_amount = $this->wcfmmp_get_order_item_commission( $order_id, $vendor_id, $product_id, $variation_id, ( $line_item->get_subtotal() - $refunded_amount ), ( $line_item->get_quantity() - $refunded_qty ), $commission_rule );
							$grosse_total      = $line_item->get_subtotal();
						}
						$gross_sales_total   = $grosse_total;
						$total_commission    = $commission_amount;
						
						$discount_amount     = ( $line_item->get_subtotal() - $line_item->get_total() );
						
						// Shipping commission calculation
						if ( !empty($vendor_shipping) && isset($vendor_shipping[$vendor_id]) && $product->needs_shipping() ) {
							$shipping_cost            = (float) round(($vendor_shipping[$vendor_id]['shipping'] / $vendor_shipping[$vendor_id]['package_qty']) * $line_item->get_quantity(), 2);
							$shipping_tax             = (float) round(($vendor_shipping[$vendor_id]['shipping_tax'] / $vendor_shipping[$vendor_id]['package_qty']) * $line_item->get_quantity(), 2);
							$refunded_shipping_amount = (float) round(($vendor_shipping[$vendor_id]['refunded_amount'] / $vendor_shipping[$vendor_id]['package_qty']) * $line_item->get_quantity(), 2);
							$refunded_shipping_tax    = (float) round(($vendor_shipping[$vendor_id]['refunded_tax'] / $vendor_shipping[$vendor_id]['package_qty']) * $line_item->get_quantity(), 2);
						}
						$gross_shipping_cost = $shipping_cost;
						$gross_shipping_tax  = $shipping_tax;
						$shipping_cost       = apply_filters( 'wcfmmmp_commission_shipping_cost', ( $shipping_cost - $refunded_shipping_amount ), $vendor_shipping, $order_id, $vendor_id, $product_id, $commission_rule );
						$shipping_tax        = apply_filters( 'wcfmmmp_commission_shipping_tax', ( $shipping_tax - $refunded_shipping_tax ), $vendor_shipping, $order_id, $vendor_id, $product_id, $commission_rule );
						
						// Commission Rule on Shipping Cost - by default false
						if( apply_filters( 'wcfmmp_is_allow_commission_on_shipping', false ) ) {
							$shipping_cost = $this->wcfmmp_generate_commission_cost( $shipping_cost, $commission_rule );
							$shipping_tax  = $this->wcfmmp_generate_commission_cost( $shipping_tax, $commission_rule );
						}
						
						if( $get_shipping = $WCFMmp->wcfmmp_vendor->is_vendor_get_shipping( $vendor_id ) ) {
							$grosse_total 		+= (float) $gross_shipping_cost;
							$total_commission += (float) $shipping_cost;
						}
						$gross_sales_total  += (float) $gross_shipping_cost;
						
						// Tax commission calculation
						$gross_tax_cost = $line_item->get_total_tax();
						if ( wc_tax_enabled() ) {
							$order_taxes         = $order->get_taxes();
							$tax_data = $item->get_taxes();
							if ( ! empty( $tax_data ) ) {
								foreach ( $order_taxes as $tax_item ) {
									$tax_item_id         = $tax_item['rate_id'];
									$refunded_total_tax += $order->get_tax_refunded_for_item( $order_item_id, $tax_item_id );
								}
							}
						}
						$tax_cost       = apply_filters( 'wcfmmmp_commission_tax_cost', ( $line_item->get_total_tax() - $refunded_total_tax ), $commission_amount, $order_id, $vendor_id, $product_id, $commission_rule );
						
						// Commission Rule on Tax Cost - by default false
						if( apply_filters( 'wcfmmp_is_allow_commission_on_tax', false ) ) {
							$tax_cost = $this->wcfmmp_generate_commission_cost( $tax_cost, $commission_rule );
						}
						
						if( $WCFMmp->wcfmmp_vendor->is_vendor_get_tax( $vendor_id ) ) {
							$grosse_total 		+= (float) $gross_tax_cost;
							$total_commission += (float) $tax_cost;
							if( $get_shipping ) {
								$grosse_total 		+= (float) $gross_shipping_tax;
								$total_commission += (float) $shipping_tax;
							}
						}
						$gross_sales_total  += (float) $gross_tax_cost;
						$gross_sales_total  += (float) $gross_shipping_tax;
						
						// Purchase Price
						$purchase_price = get_post_meta( $product_id, '_purchase_price', true );
						if( !$purchase_price ) $purchase_price = $product->get_price();
						
						// Withdrawal Charges Calculation
						if( !$is_auto_withdrawal ) {
							$withdraw_charges = $WCFMmp->wcfmmp_withdraw->calculate_withdrawal_charges( $total_commission, $vendor_id );
						}
						
						$wpdb->query(
										$wpdb->prepare(
											"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_orders` 
													( vendor_id
													, order_id
													, customer_id
													, payment_method
													, product_id
													, variation_id
													, quantity
													, product_price
													, purchase_price
													, item_id
													, item_type
													, item_sub_total
													, item_total
													, shipping
													, tax
													, shipping_tax_amount
													, commission_amount
													, discount_amount
													, discount_type
													, other_amount
													, other_amount_type
													, refunded_amount
													, withdraw_charges
													, total_commission
													, order_status
													, shipping_status 
													, is_withdrawable
													, is_auto_withdrawal
													, is_partially_refunded
													, refund_status
													, created
													) VALUES ( %d
													, %d
													, %d
													, %s
													, %d
													, %d 
													, %d
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %s
													, %d
													, %d
													, %d
													, %s
													, %s
													) ON DUPLICATE KEY UPDATE `item_id` = %d"
											, $vendor_id
											, $order_id
											, $customer_id
											, $payment_method
											, $product_id
											, $variation_id
											, $line_item->get_quantity()
											, $product->get_price()
											, $purchase_price
											, $order_item_id
											, $line_item->get_type()
											, $line_item->get_subtotal()
											, $line_item->get_total()
											, $shipping_cost
											, $tax_cost
											, $shipping_tax
											, $commission_amount
											, $discount_amount
											, $discount_type
											, $other_amount
											, $other_amount_type
											, ( $refunded_amount + $refunded_total_tax + $refunded_shipping_amount + $refunded_shipping_tax )
											, $withdraw_charges
											, $total_commission
											, $order_status
											, $shipping_status 
											, $is_withdrawable
											, $is_auto_withdrawal
											, $is_partially_refunded
											, $refund_status
											, date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) )
											, $order_item_id
							)
						);
						$commission_id = $wpdb->insert_id;
						
						// Update Commission Metas
						$this->wcfmmp_update_commission_meta( $commission_id, 'currency', $order->get_currency() );
						$this->wcfmmp_update_commission_meta( $commission_id, 'gross_total', $grosse_total );
						$this->wcfmmp_update_commission_meta( $commission_id, 'gross_sales_total', $gross_sales_total );
						$this->wcfmmp_update_commission_meta( $commission_id, 'gross_shipping_cost', $gross_shipping_cost );
						$this->wcfmmp_update_commission_meta( $commission_id, 'gross_shipping_tax', $gross_shipping_tax );
						$this->wcfmmp_update_commission_meta( $commission_id, 'gross_tax_cost', $gross_tax_cost );
						$this->wcfmmp_update_commission_meta( $commission_id, 'commission_rule', serialize( $commission_rule ) );
						
						do_action( 'wcfmmp_order_item_processed', $commission_id, $order_id, $order, $vendor_id, $product_id, $order_item_id, $grosse_total, $total_commission, $is_auto_withdrawal );
					}
					
					// Updating Order Item meta processed
					wc_update_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed', $commission_id );
					$wcfmmp_order_processed = true;
				}
			}
		}
		
		if( $wcfmmp_order_processed ) {
			update_post_meta( $order_id, '_wcfmmp_order_processed', 'yes' );
			
			$wcfmmp_order_email_triggered = get_post_meta( $order_id, '_wcfmmp_order_email_triggered', true );
			if( !$wcfmmp_order_email_triggered ) {
				$store_new_order_email_allowed_order_status = get_wcfm_store_new_order_email_allowed_order_status();
				$current_order_status = 'wc-'.$order_status;
				if( isset( $store_new_order_email_allowed_order_status[$current_order_status] ) ) {
					$wcfmmp_email = WC()->mailer()->emails['WCFMmp_Email_Store_new_order'];
					if( $wcfmmp_email ) {
						$wcfmmp_email->trigger( $order_id );
						update_post_meta( $order_id, '_wcfmmp_order_email_triggered', 'yes' );
					}
				}
			}
			
			do_action( 'wcfmmp_order_processed', $order_id, $is_auto_withdrawal );
		}
            
		return;
	}
	
	/**
	 * Update Commission metas
	 */
	public function wcfmmp_update_commission_meta( $commission_id, $key, $value ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_orders_meta` 
									( order_commission_id
									, `key`
									, `value`
									) VALUES ( %d
									, %s
									, %s
									)"
							, $commission_id
							, $key
							, $value
			)
		);
		$commission_meta_id = $wpdb->insert_id;
		return $commission_meta_id;
	}
	
	/**
	 * Get Commission metas
	 */
	public function wcfmmp_get_commission_meta( $commission_id, $key ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$commission_meta = $wpdb->get_var( 
						$wpdb->prepare(
							"SELECT `value` FROM `{$wpdb->prefix}wcfm_marketplace_orders_meta` 
							     WHERE 
							     `order_commission_id` = %d
									  AND `key` = %s
									"
							, $commission_id
							, $key
			)
		);
		return $commission_meta;
	}
	
	/**
	 * New Order check on WC New Order
	 */
	function wcfmmp_new_order_check( $order, $updated_props ) {
		if( !$order || !is_a($order , 'WC_Order') ) return;
		
		$created_via = $order->get_created_via();
		if( !$created_via || !in_array( $created_via, apply_filters( 'wcfmmp_new_order_check_created_via', array( 'wepos' ) ) ) ) {
			if( defined( 'WCFM_MANUAL_ORDER' ) ) return;
			if( defined( 'WC_BOOKINGS_VERSION' ) ) return;
			if( defined( 'WC_APPOINTMENTS_VERSION' ) ) return;
			if( defined( 'WC_POS_VERSION' ) ) return;
		}
		$order_id = $order->get_id();
		if( $order_id ) {
			if ( get_post_meta( $order_id, '_wcfmmp_order_processed', true ) ) return;
			$order_posted = get_post( $order_id );
			$this->wcfmmp_checkout_order_processed( $order_id, $order_posted, $order );
		}
	}
	
	/**
	 * WC POS New Order Check 
	 */
	function wcfmmp_pos_order_check( $payment_details, $order ) {
		if( !$order || !is_a($order , 'WC_Order') ) return;
		$order_id = $order->get_id();
		if( $order_id ) {
			wcfm_log( "POS Order: #" . $order_id );
			if ( get_post_meta( $order_id, '_wcfmmp_order_processed', true ) ) return;
			$order_posted = get_post( $order_id );
			$this->wcfmmp_checkout_order_processed( $order_id, $order_posted, $order );
		}
	}
	
	/**
	 * Marketplace Order for WC Subscription Renewal Order
	 */
	function wcfmmp_renewal_order_processed( $renewal_order, $subscription ) {
		global $WCFM, $WCFMmp, $wpdb;
		wcfm_log( "RENEWAL ORDER ::" . $renewal_order->get_id() );
		if( $renewal_order ) {
			$order_id = $renewal_order->get_id();
			$order_posted = get_post( $order_id );
			delete_post_meta( $order_id, '_wcfmmp_order_processed' );
			$this->wcfmmp_checkout_order_processed( $order_id, $order_posted, $renewal_order );
			$WCFM->wcfm_notification->wcfm_message_on_new_order( $order_id, true );
		}
		
		return $renewal_order;
	}
	
	/**
	 * Marketplace Order Status update on WC Order status change
	 */
	function wcfmmp_order_status_changed( $order_id, $status_from, $status_to, $order ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$withdrawal_auto_complate_order_status = apply_filters( 'wcfmmp_withdrawal_auto_complate_order_status', array( 'processing', 'completed' ) );
		$withdrawal_auto_cancel_order_status   = apply_filters( 'wcfmmp_withdrawal_auto_cancel_order_status', array( 'cancelled', 'failed', 'refunded' ) );
		$commission_trashed_order_status       = apply_filters( 'wcfmmp_commission_trashed_order_status', array( 'cancelled', 'failed' ) );
		
		// Update Commission Order status by Main Order Status
		if( apply_filters( 'wcfm_is_allow_status_update_by_main_order_status', true ) ) {
			$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('commission_status' => $status_to, 'order_status' => $status_to), array('order_id' => $order_id), array('%s', '%s'), array('%d'));
		}
		
		// Withdrawal Threshold check by Order Completed date 
		if( apply_filters( 'wcfm_is_allow_withdrwal_check_by_order_complete_date', false ) && ( $status_to == 'completed' ) ) {
			$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array( 'created' => date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ), array('order_id' => $order_id), array('%s'), array('%d'));
		}
		
		// Fetch commission ids for this order
		$sql = 'SELECT ID, is_auto_withdrawal, vendor_id  FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
		$sql .= ' WHERE 1=1';
		$sql .= " AND `order_id` = " . $order_id;
		$commissions = $wpdb->get_results( $sql );
		
		if( !empty( $commissions ) ) {
			foreach( $commissions as $commission ) {
				// Update commission ledger status
				$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $commission->ID, $status_to );
				
				// Update auto withdrawal complated
				if( in_array( $status_to, $withdrawal_auto_complate_order_status ) && $commission->is_auto_withdrawal && apply_filters( 'wcfm_is_pref_withdrawal', true ) ) {
					$WCFMmp->wcfmmp_withdraw->wcfmmp_withdraw_status_update_by_commission( $commission->ID, 'completed' );
				}
				
				// Update auto withdrawal Status cancelled
				if( in_array( $status_to, $withdrawal_auto_cancel_order_status ) ) {
					if( apply_filters( 'wcfm_is_pref_withdrawal', true ) && $commission->is_auto_withdrawal ) {
						$WCFMmp->wcfmmp_withdraw->wcfmmp_reverse_withdraw_status_update_by_commission( $commission->ID, 'cancelled' );
					}
					if( apply_filters( 'wcfm_is_pref_withdrawal', true ) ) {
						$WCFMmp->wcfmmp_withdraw->wcfmmp_withdraw_status_update_by_commission( $commission->ID, 'cancelled' );
					}
					if( apply_filters( 'wcfm_is_pref_refund', true ) ) {
						$WCFMmp->wcfmmp_refund->wcfmmp_refund_status_update_by_commission( $commission->ID, 'cancelled' );
					}
				}
				
				do_action( 'wcfmmp_order_status_'.$status_to, $order_id, $commission->ID, $order );
			}
		}
		
		// Vendor Notification
		if( !wcfm_is_vendor() && apply_filters( 'wcfm_is_allow_status_update_by_main_order_status', true ) ) {
			$wcfmmp_order_email_triggered = get_post_meta( $order_id, '_wcfmmp_order_email_triggered', true );
			if( $wcfmmp_order_email_triggered ) {
				if( !empty( $commissions ) ) {
					$processed_vendors = array();
					foreach( $commissions as $commission ) {
						$vendor_id = $commission->vendor_id;
						if( !empty( $processed_vendors ) && in_array( $vendor_id, $processed_vendors ) ) continue;
						$processed_vendors[$vendor_id] = $vendor_id;
						if( $vendor_id ) {
							$wcfm_messages = sprintf( __( '<b>%s</b> order status updated to <b>%s</b>', 'wc-multivendor-marketplace' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order_id . '</a>', $WCFMmp->wcfmmp_vendor->wcfmmp_vendor_order_status_name( $status_to ) );
							$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'status-update' );
						}
					}
				}
			}
		}
		
		// Trashed Commission Order
		if( apply_filters( 'wcfm_is_allow_trashed_cancelled_orders', true ) ) {
			if( in_array( $status_to, $commission_trashed_order_status ) ) {
				$this->wcfmmp_commission_order_trash( $order_id );
			} else {
			  $this->wcfmmp_commission_order_untrash( $order_id );
			}
		}
		do_action( 'wcfmmp_order_status_updated', $order_id, $status_from, $status_to, $order );
	}
	
	/**
	 * Commission withdrawal Update on complete
	 */
	function wcfmmp_commission_withdrawal_id_update( $withdrawal_id, $commission_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$withdrawal_id ) return;
		if( !$commission_id ) return;
		
		// Set Withdrawal ID at Vendor Orders table
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('withdrawal_id' => $withdrawal_id, 'withdraw_status' => 'completed', 'commission_paid_date' => date('Y-m-d H:i:s', current_time( 'timestamp', 0 ))), array('ID' => $commission_id), array('%d', '%s', '%s'), array('%d'));
	}
	
	/**
	 * Generate commission for an item cost
	 */
	public function wcfmmp_generate_commission_cost( $item_price, $commission_rule, $quantity = 1 ) {
		
		if( !$item_price ) return 0;
		
		$item_commission = 0;
		if( $commission_rule && is_array( $commission_rule ) ) {
			switch( $commission_rule['mode'] ) {
				case 'percent':
					$item_commission = $item_price * ((float) $commission_rule['percent']/100);
				break;
				
				case 'fixed':
					if( apply_filters( 'wcfmmp_is_allow_commission_fixed_per_unit', true ) ) {
						$item_commission = (float) $commission_rule['fixed'] * $quantity;
					} else {
						$item_commission = (float) $commission_rule['fixed'];
					}
				break;
				
				case 'percent_fixed':
					$item_commission  = (float) $item_price * ((float) $commission_rule['percent']/100);
					if( apply_filters( 'wcfmmp_is_allow_commission_fixed_per_unit', true ) ) {
						$item_commission += (float) $commission_rule['fixed'] * $quantity;
					} else {
						$item_commission += (float) $commission_rule['fixed'];
					}
				break;
			}
			
			// Negative commission value By Pass
			if( (float) $item_price < (float) $item_commission ) {
				$item_commission = $item_price;
			}
			
			$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
			if( $admin_fee_mode ) {
				$item_commission = (float) $item_price - (float) $item_commission;
			}
		}
		//wcfm_log( "Item Commission:: " . $item_commission );
		
		return $item_commission;
	}
	
	/**
	 * Generate commission for an Order Item
	 */
	public function wcfmmp_get_order_item_commission( $order_id, $vendor_id, $product_id, $variation_id = 0, $item_price, $quantity, $commission_rule = '' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$item_price = apply_filters( 'wcfmmp_order_item_price', $item_price, $product_id, $variation_id, $quantity, $vendor_id, $order_id );
		
		$quantity = apply_filters( 'wcfmmp_order_item_quantity', $quantity, $product_id, $variation_id, $item_price, $vendor_id, $order_id );
		
		if( !$commission_rule )
			$commission_rule = $WCFMmp->wcfmmp_product->wcfmmp_get_product_commission_rule( $product_id, $variation_id, $vendor_id, $item_price, $quantity, $order_id );
		
		$item_commission = $this->wcfmmp_generate_commission_cost( $item_price, $commission_rule, $quantity );
		
		return apply_filters( 'wcfmmp_order_item_commission', $item_commission, $vendor_id, $product_id, $variation_id, $item_price, $quantity, $commission_rule, $order_id );
	}
	
	/**
	 * Generate Commission Rule by Vendor Sales
	 */
	public function wcfmmp_get_commission_rule_by_sales_rule( $vendor_id, $vendor_commission_sales_rules ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$vendor_id ) return false;
		
		$commission_rule = array( 'rule' => 'by_sales', 'mode' => 'fixed', 'percent' => 0, 'fixed' => 0 );
		if( empty( $vendor_commission_sales_rules ) ) return $commission_rule;
		
		$vendor_gross_sales = $WCFM->wcfm_vendor_support->wcfm_get_gross_sales_by_vendor( $vendor_id, 'all' );
		
		$matched_rule_price = 0;
		foreach( $vendor_commission_sales_rules as $vendor_commission_sales_rule ) {
			$rule_price = $vendor_commission_sales_rule['sales'];
			$rule = $vendor_commission_sales_rule['rule'];
			
			if( ( $rule == 'upto' ) && ( (float)$vendor_gross_sales <= (float)$rule_price ) && ( !$matched_rule_price || ( (float)$rule_price <= (float)$matched_rule_price ) ) ) {
				$matched_rule_price = $rule_price;
				$commission_rule = array( 'mode' => $vendor_commission_sales_rule['type'], 'percent' => $vendor_commission_sales_rule['commission'], 'fixed' => isset( $vendor_commission_sales_rule['commission_fixed'] ) ? $vendor_commission_sales_rule['commission_fixed'] : $vendor_commission_sales_rule['commission'] );
			} elseif( ( $rule == 'greater' ) && ( (float)$vendor_gross_sales > (float)$rule_price ) && ( !$matched_rule_price || ( (float)$rule_price >= (float)$matched_rule_price ) ) ) {
				$matched_rule_price = $rule_price;
				$commission_rule = array( 'mode' => $vendor_commission_sales_rule['type'], 'percent' => $vendor_commission_sales_rule['commission'], 'fixed' => isset( $vendor_commission_sales_rule['commission_fixed'] ) ? $vendor_commission_sales_rule['commission_fixed'] : $vendor_commission_sales_rule['commission'] );
			}
		}
		return apply_filters( 'wcfmmp_commission_rule_by_sales_rule', $commission_rule, $vendor_id, $vendor_commission_sales_rules );
	}
	
	/**
	 * Generate Commission Rule by Product Price
	 */
	public function wcfmmp_get_commission_rule_by_product_rule( $product_id, $item_price, $quantity, $vendor_commission_product_rules = array() ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$product_id ) return false;
		
		$commission_rule = array( 'rule' => 'by_products', 'mode' => 'fixed', 'percent' => 0, 'fixed' => 0 );
		if( empty( $vendor_commission_product_rules ) ) return $commission_rule;
		
		if( !$item_price ) {
			$product = wc_get_product( $product_id );
			$item_price = (float)$product->get_price() * (int)$quantity;
		}
		
		$matched_rule_price = 0;
		foreach( $vendor_commission_product_rules as $vendor_commission_product_rule ) {
			$rule_price = $vendor_commission_product_rule['cost'];
			$rule = $vendor_commission_product_rule['rule'];
			
			if( ( $rule == 'upto' ) && ( (float) $item_price <= (float)$rule_price ) && ( !$matched_rule_price || ( (float)$rule_price <= (float)$matched_rule_price ) ) ) {
				$matched_rule_price = $rule_price;
				$commission_rule = array( 'mode' => $vendor_commission_product_rule['type'], 'percent' => $vendor_commission_product_rule['commission'], 'fixed' => isset( $vendor_commission_product_rule['commission_fixed'] ) ? $vendor_commission_product_rule['commission_fixed'] : $vendor_commission_product_rule['commission'] );
			} elseif( ( $rule == 'greater' ) && ( (float) $item_price > (float)$rule_price ) && ( !$matched_rule_price || ( (float)$rule_price >= (float)$matched_rule_price ) ) ) {
				$matched_rule_price = $rule_price;
				$commission_rule = array( 'mode' => $vendor_commission_product_rule['type'], 'percent' => $vendor_commission_product_rule['commission'], 'fixed' => isset( $vendor_commission_product_rule['commission_fixed'] ) ? $vendor_commission_product_rule['commission_fixed'] : $vendor_commission_product_rule['commission'] );
			}
		}
		return apply_filters( 'wcfmmp_commission_rule_by_product_rule', $commission_rule, $product_id, $item_price, $quantity, $vendor_commission_sales_rules );
	}
	
	/**
	 * Generate Commission Rule by Product Purchase Quantity
	 */
	public function wcfmmp_get_commission_rule_by_quantity_rule( $product_id, $item_price, $quantity, $vendor_commission_quantity_rules = array() ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$product_id ) return false;
		
		$commission_rule = array( 'rule' => 'by_quantity', 'mode' => 'fixed', 'percent' => 0, 'fixed' => 0 );
		if( empty( $vendor_commission_quantity_rules ) ) return $commission_rule;
		
		$matched_rule_quantity = 0;
		foreach( $vendor_commission_quantity_rules as $vendor_commission_quantity_rule ) {
			$rule_quantity = $vendor_commission_quantity_rule['quantity'];
			$rule = $vendor_commission_quantity_rule['rule'];
			
			if( ( $rule == 'upto' ) && ( (float) $quantity <= (float)$rule_quantity ) && ( !$matched_rule_quantity || ( (float)$rule_quantity <= (float)$matched_rule_quantity ) ) ) {
				$matched_rule_quantity = $rule_quantity;
				$commission_rule = array( 'mode' => $vendor_commission_quantity_rule['type'], 'percent' => $vendor_commission_quantity_rule['commission'], 'fixed' => isset( $vendor_commission_quantity_rule['commission_fixed'] ) ? $vendor_commission_quantity_rule['commission_fixed'] : 0 );
			} elseif( ( $rule == 'greater' ) && ( (float) $quantity > (float)$rule_quantity ) && ( !$matched_rule_quantity || ( (float)$rule_quantity >= (float)$matched_rule_quantity ) ) ) {
				$matched_rule_quantity = $rule_quantity;
				$commission_rule = array( 'mode' => $vendor_commission_quantity_rule['type'], 'percent' => $vendor_commission_quantity_rule['commission'], 'fixed' => isset( $vendor_commission_quantity_rule['commission_fixed'] ) ? $vendor_commission_quantity_rule['commission_fixed'] : 0 );
			}
		}
		return apply_filters( 'wcfmmp_commission_rule_by_quantity_rule', $commission_rule, $product_id, $item_price, $quantity, $vendor_commission_sales_rules );
	}
	
	/**
	 * Commission Order item refresh on Order Item Refund - WC Order Action
	 */
	function wcfmmp_commission_order_item_refund( $order_id, $refund_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if ( did_action( 'wp_ajax_woocommerce_refund_line_items' ) ) {
			// Reset WCFMmp Comission Orders
			delete_post_meta( $order_id, '_wcfmmp_order_processed' );
			delete_post_meta( $order_id, '_wcfm_store_invoices' );
			$this->wcfmmp_commission_order_reset( $order_id );
			
			$order = wc_get_order( $order_id );
			
			$items = $order->get_items( 'line_item' );
			if( !empty( $items ) ) {
				foreach( $items as $order_item_id => $item ) {
					$order_item_processed = wc_get_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed', true );
					if( $order_item_processed ) {
						wc_delete_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed' );
					}
				}
			}
			
			$order_posted = get_post( $order_id );
			do_action( 'wcfm_manual_order_processed', $order_id, $order_posted, $order );
		}
	}
	
	/**
	 * Comission order item create on New Item added to the order - WC Order Action
	 */
	function wcfmmp_commission_order_item_add( $added_items, $order ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		delete_post_meta( $order->get_id(), '_wcfmmp_order_processed' );
		
		$order_posted = get_post( $order->get_id() );
		do_action( 'wcfm_manual_order_processed', $order->get_id(), $order_posted, $order );
	}
	
	/**
	 * Commission Order item refresh on Order Item edit - WC Order Action
	 */
	function wcfmmp_commission_order_item_edit( $order_id, $items ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if ( did_action( 'wp_ajax_woocommerce_save_order_items' ) ) {
			// Reset WCFMmp Comission Orders
			delete_post_meta( $order_id, '_wcfmmp_order_processed' );
			delete_post_meta( $order_id, '_wcfm_store_invoices' );
			$this->wcfmmp_commission_order_reset( $order_id );
			
			if( !empty( $items ) ) {
				foreach ( $items['order_item_id'] as $item_id ) {
					$order_item_processed = wc_get_order_item_meta( $item_id, '_wcfmmp_order_item_processed', true );
					if( $order_item_processed ) {
						wc_delete_order_item_meta( $item_id, '_wcfmmp_order_item_processed' );
					}
				}
			}
			
			$order_posted = get_post( $order_id );
			do_action( 'wcfm_manual_order_processed', $order_id, $order_posted, wc_get_order( $order_id ) );
		}
	}
	
	/**
	 * Commission Order Delete on Order Item Delete - WC Order Action
	 */
	function wcfmmp_commission_order_item_delete( $item_id ) {
		global $wpdb;
		
		$marketplace_orders = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_orders WHERE `item_id` = %d", $item_id ) );
		foreach( $marketplace_orders as $marketplace_order ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_order->ID ) );
			
			// Withdrawals
			$marketplace_withdrawals = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_withdraw_request WHERE commission_ids = %s", $marketplace_order->ID ) );
			foreach( $marketplace_withdrawals as $marketplace_withdrawal ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_withdrawal->ID ) );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_withdraw_request WHERE commission_ids = %s", $marketplace_order->ID ) );
			
			// Reverse Withdrawals
			$marketplace_reveerse_withdrawals = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_reverse_withdrawal WHERE commission_id = %s", $marketplace_order->ID ) );
			foreach( $marketplace_reveerse_withdrawals as $marketplace_reveerse_withdrawal ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_reveerse_withdrawal->ID ) );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_reverse_withdrawal WHERE commission_id = %s", $marketplace_order->ID ) );
			
			// Refund Requests
			$marketplace_refunds = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_refund_request WHERE commission_id = %d", $marketplace_order->ID ) );
			foreach( $marketplace_refunds as $marketplace_refund ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_refund->ID ) );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_refund_request WHERE commission_id = %d", $marketplace_order->ID ) );
		}
		
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE `item_id` = %d", $item_id ) );
	}
	
	/**
	 * Commission Order Un Trash on Order Retrive
	 */
	function wcfmmp_commission_order_untrash( $order_id ) {
		global $wpdb;
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('is_trashed' => 0), array('order_id' => $order_id), array('%d'), array('%d'));
	}
	
	/**
	 * Commission Order Trash on Order Trashed
	 */
	function wcfmmp_commission_order_trash( $order_id ) {
		global $wpdb;
		
		if ( in_array( get_post_type( $order_id ), wc_get_order_types(), true ) ) {
			$order = wc_get_order( $order_id );
			if ( is_a( $order , 'WC_Order' ) ) {
				$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('is_trashed' => 1), array('order_id' => $order_id), array('%d'), array('%d'));
				$this->wcfmmp_commission_order_reset( $order_id, false );
			}
		}
	}
	
	/**
	 * Commission Order Delete on Order Delete
	 */
	function wcfmmp_commission_order_delete( $order_id ) {
		global $wpdb;
		
		if ( in_array( get_post_type( $order_id ), wc_get_order_types(), true ) ) {
			$order = wc_get_order( $order_id );
			if ( is_a( $order , 'WC_Order' ) ) {
				$this->wcfmmp_commission_order_reset( $order_id );
			}
		}
	}
	
	/**
	 * Commission Order Reset
	 */
	public function wcfmmp_commission_order_reset( $order_id, $commission_order = true ) {
		global $wpdb;
		
		// Commission Orders
		if( $commission_order ) {
			$marketplace_orders = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_orders WHERE order_id = %d", $order_id ) );
			foreach( $marketplace_orders as $marketplace_order ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_order->ID ) );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE order_id = %d", $order_id ) );
		}
		
		// Withdrawals
		$marketplace_withdrawals = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_withdraw_request WHERE order_ids = %s", $order_id ) );
		foreach( $marketplace_withdrawals as $marketplace_withdrawal ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_withdrawal->ID ) );
		}
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_withdraw_request WHERE order_ids = %s", $order_id ) );
		
		// Reverse Withdrawals
		$marketplace_reveerse_withdrawals = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_reverse_withdrawal WHERE order_id = %s", $order_id ) );
		foreach( $marketplace_reveerse_withdrawals as $marketplace_reveerse_withdrawal ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_reveerse_withdrawal->ID ) );
		}
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_reverse_withdrawal WHERE order_id = %s", $order_id ) );
		
		// Refund Requests
		$marketplace_refunds = $wpdb->get_results(  $wpdb->prepare( "SELECT ID from {$wpdb->prefix}wcfm_marketplace_refund_request WHERE order_id = %d", $order_id ) );
		foreach( $marketplace_refunds as $marketplace_refund ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_vendor_ledger WHERE reference_id = %d", $marketplace_refund->ID ) );
		}
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcfm_marketplace_refund_request WHERE order_id = %d", $order_id ) );
	}
}