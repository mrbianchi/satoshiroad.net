<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Refund Form Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/controllers/refund
 * @version   1.0.0
 */

class WCFMmp_Refund_Requests_Form_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMmp;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMmp, $wpdb;
		
		$wcfm_refund_tab_form_data = array();
	  parse_str($_POST['wcfm_refund_requests_form'], $wcfm_refund_tab_form_data);
	  
	  $wcfm_refund_messages = get_wcfm_refund_requests_messages();
	  $has_error = false;
	  
	  // Google reCaptcha support
	  if ( function_exists( 'gglcptch_init' ) ) {
			if(isset($wcfm_refund_tab_form_data['g-recaptcha-response']) && !empty($wcfm_refund_tab_form_data['g-recaptcha-response'])) {
				$_POST['g-recaptcha-response'] = $wcfm_refund_tab_form_data['g-recaptcha-response'];
			}
			$check_result = apply_filters( 'gglcptch_verify_recaptcha', true, 'string', 'wcfm_refund_request_form' );
			if ( true === $check_result ) {
					/* do necessary action */
			} else { 
				echo '{"status": false, "message": "' . $check_result . '"}';
				die;
			}
		} elseif ( class_exists( 'anr_captcha_class' ) && function_exists( 'anr_captcha_form_field' ) ) {
			$check_result = anr_verify_captcha( $wcfm_refund_tab_form_data['g-recaptcha-response'] );
			if ( true === $check_result ) {
					/* do necessary action */
			} else { 
				echo '{"status": false, "message": "' . __( 'Captcha failed, please try again.', 'wc-frontend-manager' ) . '"}';
				die;
			}
		}
	  
	  if(isset($wcfm_refund_tab_form_data['wcfm_refund_reason']) && !empty($wcfm_refund_tab_form_data['wcfm_refund_reason'])) {
	  	
	  	$refund_reason    = wcfm_stripe_newline( $wcfm_refund_tab_form_data['wcfm_refund_reason'] );
	  	$refund_reason    = esc_sql( $refund_reason );
	  	$order_id         = absint( $wcfm_refund_tab_form_data['wcfm_refund_order_id'] );
	  	$commission_id    = absint( $wcfm_refund_tab_form_data['wcfm_refund_commission_id'] );
	  	$refund_item_id   = absint( $wcfm_refund_tab_form_data['wcfm_refund_product'] );
	  	$refund_request   = $wcfm_refund_tab_form_data['wcfm_refund_request'];
	  	$refunded_amount  = $wcfm_refund_tab_form_data['wcfm_refund_request_amount'];
	  	$refund_status    = 'pending';
	  	
	  	$product_id       = 0;
	  	$vendor_id        = 0;
	  	$item_total       = 0;
	  	$old_refunds      = 0;
	  	
	  	$sql = 'SELECT ID, product_id, vendor_id, item_total, refunded_amount FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
			$sql .= ' WHERE 1=1';
			$sql .= " AND `order_id` = " . $order_id;
			$sql .= " AND `item_id`  = " . $refund_item_id;
			$commissions = $wpdb->get_results( $sql );
			if( !empty( $commissions ) ) {
				foreach( $commissions as $commission ) {
					$commission_id    = $commission->ID;
					$product_id       = $commission->product_id;
					$vendor_id        = $commission->vendor_id;
					$item_total       = $commission->item_total;
					$old_refunds      = $commission->refunded_amount;
				}
			}
			
			$order                = wc_get_order( $order_id );
			
			if( !$vendor_id ) {
				$line_item  = new WC_Order_Item_Product( $refund_item_id );
				$item_total = $line_item->get_total();
			}
			
			if( $refund_request == 'full' ) {
				$refunded_amount = $item_total - (float)$old_refunds;
			} elseif( (float)$refunded_amount > ((float)$item_total - (float)$old_refunds) ) {
				echo '{"status": false, "message": "' . __('Refund request amount more than item value.', 'wc-multivendor-marketplace') . '"}';
				die;
			}
			
			$refund_request_id = $WCFMmp->wcfmmp_refund->wcfmmp_refund_processed( $vendor_id, $order_id, $commission_id, $refund_item_id, $refund_reason, $refunded_amount, $refund_request );
	  	
			if( $refund_request_id && !is_wp_error( $refund_request_id ) ) {
				// Update Commissions Table Refund Status
				$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('refund_status' => 'requested'), array('ID' => $commission_id), array('%s'), array('%d'));
				
				$refund_auto_approve = isset( $WCFMmp->wcfmmp_refund_options['refund_auto_approve'] ) ? $WCFMmp->wcfmmp_refund_options['refund_auto_approve'] : 'no';
				if( ( $refund_auto_approve == 'yes' ) && $vendor_id && wcfm_is_vendor() ) {
					
					// Update refund status
					$refund_update_status = $WCFMmp->wcfmmp_refund->wcfmmp_refund_status_update_by_refund( $refund_request_id );
					
					if( $refund_update_status ) {
						// Admin Notification
						$wcfm_messages = sprintf( __( 'Refund <b>%s</b> has been processed for Order <b>%s</b> item <b>%s</b> by <b>%s</b>', 'wc-multivendor-marketplace' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . add_query_arg( 'request_id', $refund_request_id, wcfm_refund_requests_url() ) . '">#' . $refund_request_id . '</a>', '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url( $order_id ) . '">#' . $order->get_order_number() . '</a>', get_the_title( $product_id ), $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $vendor_id ) );
						$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'refund-request' );
						
						// Order Note
						$is_customer_note = apply_filters( 'wcfm_is_allow_refund_update_note_for_customer', '1' );
						add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
						$comment_id = $order->add_order_note( strip_tags($wcfm_messages), $is_customer_note );
						add_comment_meta( $comment_id, '_vendor_id', $vendor_id );
						remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
						
						do_action( 'wcfmmp_refund_request_approved', $refund_request_id );
						
						echo '{"status": true, "message": "' . __('Refund requests successfully processed.', 'wc-multivendor-marketplace') . ' #' . $refund_request_id . '"}';
					} else {
						echo '{"status": false, "message": "' . __('Refund processing failed, please contact site admin.', 'wc-multivendor-marketplace') . ' #' . $refund_request_id . '"}';
					}
				} else {
					// Admin Notification
					$wcfm_messages = sprintf( __( 'Refund Request <b>%s</b> received for Order <b>%s</b> item <b>%s</b>', 'wc-multivendor-marketplace' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . add_query_arg( 'request_id', $refund_request_id, wcfm_refund_requests_url() ) . '">#' . $refund_request_id . '</a>', '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url( $order_id ) . '">#' . $order->get_order_number() . '</a>', get_the_title( $product_id ) );
					$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'refund-request' );
					
					// Send Vendor Notification
					if( $vendor_id && !wcfm_is_vendor() ) {
						$is_allow_refund = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'refund-request' );
						if( $is_allow_refund && apply_filters( 'wcfm_is_allow_refund_vendor_notification', true ) ) {
							$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'refund-request' );
						}
					}
					
					// Order Note
					$is_customer_note = apply_filters( 'wcfm_is_allow_refund_request_note_for_customer', '1' );
					$comment_id = $order->add_order_note( strip_tags($wcfm_messages), $is_customer_note );
					
					echo '{"status": true, "message": "' . $wcfm_refund_messages['refund_requests_saved'] . ' #' . $refund_request_id . '"}';
				}
				
				do_action( 'wcfm_after_refund_request',  $refund_request_id, $order_id, $commission_id, $refund_item_id, $vendor_id, $refund_reason );
				
			} else {
				echo '{"status": false, "message": "' . $wcfm_refund_messages['refund_requests_failed'] . '"}';
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_refund_messages['no_refund_reason'] . '"}';
		}
		
		die;
	}
}