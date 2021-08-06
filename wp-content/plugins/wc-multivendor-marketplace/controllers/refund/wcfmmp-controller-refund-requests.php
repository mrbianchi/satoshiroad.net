<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WCfM Marketplace Refund Requests Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/controllers/refund
 * @version   1.0.0
 */

class WCFMmp_Refund_Requests_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMmp;
		
		$length = sanitize_text_field( $_POST['length'] );
		$offset = sanitize_text_field( $_POST['start'] );
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$transaction_id = ! empty( $_POST['transaction_id'] ) ? sanitize_text_field( $_POST['transaction_id'] ) : '';
		
		$refund_vendor_filter = '';
		if ( ! empty( $_POST['refund_vendor'] ) ) {
			$refund_vendor = esc_sql( $_POST['refund_vendor'] );
			$refund_vendor_filter = " AND commission.`vendor_id` = {$refund_vendor}";
		}
		
		$status_filter = 'requested';
    if( isset($_POST['status_type']) ) {
    	$status_filter = sanitize_text_field( $_POST['status_type'] );
    }
    if( $status_filter ) {
    	$status_filter = " AND commission.refund_status = '" . $status_filter . "'";
    }

		$sql = 'SELECT COUNT(commission.ID) FROM ' . $wpdb->prefix . 'wcfm_marketplace_refund_request AS commission';
		$sql .= ' WHERE 1=1';
		if( $transaction_id ) $sql .= " AND commission.ID = $transaction_id";
		$sql .= $status_filter;
		$sql .= $refund_vendor_filter;
		
		$filtered_refund_requests_count = $wpdb->get_var( $sql );
		if( !$filtered_refund_requests_count ) $filtered_refund_requests_count = 0;

		$sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_refund_request AS commission';
		$sql .= ' WHERE 1=1';
		if( $transaction_id ) $sql .= " AND commission.ID = $transaction_id";
		$sql .= $status_filter;
		$sql .= $refund_vendor_filter;
		$sql .= " ORDER BY `{$the_orderby}` {$the_order}";
		$sql .= " LIMIT {$length}";
		$sql .= " OFFSET {$offset}";
		
		$wcfm_refund_requests_array = $wpdb->get_results( $sql );
		
		if ( WC()->payment_gateways() ) {
			$payment_gateways = WC()->payment_gateways->payment_gateways();
		} else {
			$payment_gateways = array();
		}
		
		// Generate Payments JSON
		$wcfm_payments_json = '';
		$wcfm_payments_json = '{
															"draw": ' . sanitize_text_field( $_POST['draw'] ) . ',
															"recordsTotal": ' . $filtered_refund_requests_count . ',
															"recordsFiltered": ' . $filtered_refund_requests_count . ',
															"data": ';
		if(!empty($wcfm_refund_requests_array)) {
			$index = 0;
			$wcfm_refund_requests_json_arr = array();
			foreach( $wcfm_refund_requests_array as $wcfm_refund_request_single ) {
				
				// Status
				if( $wcfm_refund_request_single->refund_status == 'completed' ) {
					$wcfm_refund_requests_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-completed text_tip" data-tip="' . __( 'Refund Completed', 'wc-multivendor-marketplace') . '"></span>';
				} elseif( $wcfm_refund_request_single->refund_status == 'cancelled' ) {
					$wcfm_refund_requests_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-cancelled text_tip" data-tip="' . __( 'Refund Cancelled', 'wc-multivendor-marketplace') . '"></span>';
				} else {
					$wcfm_refund_requests_json_arr[$index][] =  '<input name="refunds[]" value="' . $wcfm_refund_request_single->ID . '" class="wcfm-checkbox select_refund_requests" type="checkbox" >';
				}
				
				// Request ID
				$wcfm_refund_requests_json_arr[$index][] = '<span class="wcfm_dashboard_item_title"># ' . $wcfm_refund_request_single->ID . '</span>';
				
				// Order ID
				$wcfm_refund_requests_json_arr[$index][] =  '<a target="_blank" href="' . get_wcfm_view_order_url( $wcfm_refund_request_single->order_id ) . '" class="wcfm_dashboard_item_title transaction_order_id">#'.  $wcfm_refund_request_single->order_id . '</a>';
				
				// Store
				if( $wcfm_refund_request_single->vendor_id ) {
					$wcfm_refund_requests_json_arr[$index][] = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($wcfm_refund_request_single->vendor_id) );
				} else {
					$wcfm_refund_requests_json_arr[$index][] = '&ndash;';
				}
				
				// Amount
				$wcfm_refund_requests_json_arr[$index][] = wc_price($wcfm_refund_request_single->refunded_amount);
				
				// Mode
				if( $wcfm_refund_request_single->is_partially_refunded ) {
					$wcfm_refund_requests_json_arr[$index][] = __( 'Partial Refund', 'wc-multivendor-marketplace' );
				} else {
					$wcfm_refund_requests_json_arr[$index][] = __( 'Full Refund', 'wc-multivendor-marketplace' );
				}
				
				// Reason
				$wcfm_refund_requests_json_arr[$index][] = $wcfm_refund_request_single->refund_reason;
				
				// Date
				$wcfm_refund_requests_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $wcfm_refund_request_single->created ) );
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_refund_requests_json_arr) ) $wcfm_payments_json .= json_encode($wcfm_refund_requests_json_arr);
		else $wcfm_payments_json .= '[]';
		$wcfm_payments_json .= '
													}';
													
		echo $wcfm_payments_json;
	}
}