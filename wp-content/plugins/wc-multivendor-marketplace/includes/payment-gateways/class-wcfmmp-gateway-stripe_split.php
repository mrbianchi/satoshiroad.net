<?php

if (!defined('ABSPATH')) {
   return;
}

use Stripe\Stripe as Stripe;
use Stripe\Customer as Stripe_Customer;
use Stripe\Charge as Stripe_Charge;
use Stripe\Transfer as Stripe_Transfer;
use Stripe\Token as Stripe_Token;

class WCFMmp_Gateway_Stripe_Split extends WC_Payment_Gateway {

	public  $customer;
	private $charge;
	private $vendor_disconnected;
	
	public  $gateway_title;
	public  $payment_gateway;
	public  $message = array();
	
	private $client_id;
	private $client_secret;
	private $published_key;
	
	private $is_testmode = false;
	private $payout_mode = 'true';
	
	private $reciver_email;
	
	private $api_endpoint;
	private $token_endpoint;
	private $access_token;
	private $token_type;

	public function __construct() {
		global $WCFM, $WCFMmp;

		$this->id = 'stripe_split';
		$this->has_fields = false;
		$this->method_title = __('Marketplace Stripe Split Pay', 'wc-multivendor-marketplace');
		$this->vendor_disconnected = false;
		$this->supports           = array(
			'products',
			'subscriptions',
		);
		$this->init_form_fields();
		$this->init_settings();

		$withdrawal_test_mode = isset( $WCFMmp->wcfmmp_withdrawal_options['test_mode'] ) ? 'yes' : 'no';

		$this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_client_id'] : '';
		$this->published_key = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_published_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_published_key'] : '';
		$this->secret_key = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_secret_key'] : '';
		
		if ( $withdrawal_test_mode == 'yes') {
			$this->is_testmode = true;
			$this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_test_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_test_client_id'] : '';
			$this->published_key = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_test_published_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_test_published_key'] : '';
			$this->secret_key = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_test_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_test_secret_key'] : '';
		}
		
		$this->title        = __('Credit Card (Stripe)', 'wc-multivendor-marketplace');
		$this->description  = __('Pay with your credit card via Stripe.', 'wc-multivendor-marketplace');
		$this->charge_type  = isset( $WCFMmp->wcfmmp_withdrawal_options['stripe_split_pay_mode'] ) ? $WCFMmp->wcfmmp_withdrawal_options['stripe_split_pay_mode'] : 'direct_charges';
		$this->debug        = false; //$this->is_testmode;
		
		if( !class_exists("Stripe\Stripe") ) {
			require_once( $WCFMmp->plugin_path . 'includes/Stripe/init.php' );
		}
		
		// Init Access Token
		$this->init_stripe_access_token();
		
		// Register WC Payment gateway
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_stripe_split_pay_gateway' ) );
		
		// Process Refund
		add_action( 'wcfmmp_refund_status_completed', array( &$this, 'wcfmmp_stripe_split_process_refund' ), 50, 3 );
		
		// De-register WCFMmp Auto-withdrawal Gateway
		add_filter( 'wcfm_marketplace_disallow_active_order_payment_methods', array( $this, 'wcfmmp_auto_withdrawal_stipe_pay' ), 750 );

		add_action( 'wp_enqueue_scripts', array( &$this, 'wcfmmp_stripe_split_scripts' ) );
	}
	
	public function add_stripe_split_pay_gateway($methods) {
		$methods[] = 'WCFMmp_Gateway_Stripe_Split';
		return $methods;
	}
	
	public function wcfmmp_auto_withdrawal_stipe_pay( $auto_withdrawal_methods ) {
		if( isset( $auto_withdrawal_methods['stripe_split'] ) )
			unset( $auto_withdrawal_methods['stripe_split'] );
		return $auto_withdrawal_methods;
	}
	
	/**
	 * Init Stripe access token.
	 *
	 * @access public
	 */
	public function init_stripe_access_token() {
		if ($this->secret_key == "") {
			add_action( 'admin_notices', array( &$this, 'stripe_access_token_error') );
			//wcfm_log('Stripe secret_key is not set. Kindly set that from WCFM Dashboard => Settings => Withdrawal Setting');
		} else {
			// Stripe initialize
			Stripe::setApiKey($this->secret_key);
		}
	}

	public function get_icon() {
		global $WCFM, $WCFMmp;

		$imgaes_url = $WCFMmp->plugin_url . 'assets/images/';

		return apply_filters( 'wcfmmp_stripe_split_pay_icons', '<br /><img src="' . $imgaes_url . 'gateway/visa.svg" class="stripe-visa-icon stripe-icon" alt="Visa" />' .
						'<img src="' . $imgaes_url . 'gateway/amex.svg" class="stripe-amex-icon stripe-icon" alt="American Express" />' .
						'<img src="' . $imgaes_url . 'gateway/mastercard.svg" class="stripe-mastercard-icon stripe-icon" alt="Mastercard" />' .
						'<img src="' . $imgaes_url . 'gateway/discover.svg" class="stripe-discover-icon stripe-icon" alt="Discover" />' .
						'<img src="' . $imgaes_url . 'gateway/diners.svg" class="stripe-diners-icon stripe-icon" alt="Diners" />' .
						'<img src="' . $imgaes_url . 'gateway/jcb.svg" class="stripe-jcb-icon stripe-icon" alt="JCB" />'
		);
	}

	public function wcfmmp_stripe_split_scripts() {
		global $WCFM, $WCFMmp, $woocommerce;
		
		if( function_exists( 'is_checkout' ) && is_checkout() ) {
			$vendors = array();
			
			// Generating Vendors List
			$items = $woocommerce->cart->get_cart();
			foreach ($items as $item) {
				if (isset($item['product_id'])) {
					$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $item['product_id'] );
					if ( $vendor_id && !in_array( $vendor_id, $vendors ) ) {
						$vendors[] = $vendor_id;
					}
				}
			}
			
			$script_path = $WCFMmp->plugin_url . 'assets/';
			$script_path = str_replace(array('http:', 'https:'), '', $script_path);
	
			wp_enqueue_script( 'stripe', 'https://js.stripe.com/v3/', '', '3.0', true );
			wp_enqueue_script( 'wcfmmp_stripe_split_pay', $script_path . 'js/gateway/stripe.js', array('jquery-payment', 'stripe'), $WCFMmp->version, true );
	
			$wcfmmp_stripe_split_pay_params['key']              = $this->published_key;
			$wcfmmp_stripe_split_pay_params['elements_options'] = apply_filters( 'wcfmmp_stripe_split_pay_elements_options', array());
			$wcfmmp_stripe_split_pay_params['is_checkout']      = ( is_checkout() && empty($_GET['pay_for_order']) ) ? 'yes' : 'no';
			$wcfmmp_stripe_split_pay_params['ajaxurl']          = WC_AJAX::get_endpoint('%%endpoint%%');
			$wcfmmp_stripe_split_pay_params['stripe_nonce']     = wp_create_nonce('_wcfmmp_stripe_split_pay_nonce');
			$wcfmmp_stripe_split_pay_params['no_of_vendor']     = count($vendors);
	
			wp_localize_script( 'wcfmmp_stripe_split_pay', 'wcfmmp_stripe_split_pay_params', apply_filters( 'wcfmmp_stripe_split_pay_params', $wcfmmp_stripe_split_pay_params ) );
	
			wp_enqueue_style( 'wcfmmp_stripe_split_pay_css', $script_path . 'css/gateway/stripe.css', array(), $WCFMmp->version, 'all' );
		}
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		$description = $this->description;
		if ($description) {
			if ($this->is_testmode) {
				/* translators: link to Stripe testing page */
				$description .= ' ' . sprintf(__('TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date or check the <a href="%s" target="_blank">Testing Stripe documentation</a> for more card numbers.', 'woocommerce-gateway-stripe'), 'https://stripe.com/docs/testing');
				$description = trim($description);
			}

			echo apply_filters( 'wcfmmp_stripe_split_pay_description', wpautop(wp_kses_post($description)), $this->id);
		}
		ob_start();
		?>
		<fieldset id="wc-<?php echo esc_attr($this->id); ?>-cc-form" class="wcfmmp-credit-card-form wc-payment-form" style="background:transparent;">
		  <?php do_action('wcfmmp_stripe_split_pay_credit_card_form_start', $this->id); ?>

			<label for="card-element">
	      <?php esc_html_e('Credit or debit card', 'woocommerce-gateway-stripe'); ?>
			</label>

			<div class="form-row form-row-wide">
				<label for="wcfmmp-stripe-split-pay-card-element"><?php esc_html_e('Card Number', 'woocommerce-gateway-stripe'); ?> <span class="required">*</span></label>
				<div class="wcfmmp-stripe-split-pay-card-group">
					<div id="wcfmmp-stripe-split-pay-card-element" class="wc-wcfmmp-stripe-split-pay-elements-field">
							<!-- a Stripe Element will be inserted here. -->
					</div>
					<i class="stripe-credit-card-brand stripe-card-brand" alt="Credit Card"></i>
				</div>
			</div>

			<div class="form-row form-row-first">
				<label for="wcfmmp-stripe-split-pay-exp-element"><?php esc_html_e('Expiry Date', 'woocommerce-gateway-stripe'); ?> <span class="required">*</span></label>

				<div id="wcfmmp-stripe-split-pay-exp-element" class="wc-wcfmmp-stripe-split-pay-elements-field">
						<!-- a Stripe Element will be inserted here. -->
				</div>
			</div>

			<div class="form-row form-row-last">
				<label for="wcfmmp-stripe-split-pay-cvc-element"><?php esc_html_e('Card Code (CVC)', 'woocommerce-gateway-stripe'); ?> <span class="required">*</span></label>
				<div id="wcfmmp-stripe-split-pay-cvc-element" class="wc-wcfmmp-stripe-split-pay-elements-field">
						<!-- a Stripe Element will be inserted here. -->
				</div>
			</div>
			<div class="clear"></div>

			<!-- Used to display form errors -->
			<div class="wcfmmp-stripe-split-pay-source-errors" role="alert"></div>
			
			<?php do_action('wcfmmp_stripe_split_pay_credit_card_form_end', $this->id); ?>
			
			<div class="wcfm-clearfix"></div>
		</fieldset>
		<?php
		ob_end_flush();
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id
	 *
	 * @return array
	 */
	public function process_payment($order_id) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$order = wc_get_order($order_id);
		$error_message = __('An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'wc-multivendor-marketplace');

		//$WCFMmp->wcfmmp_commission->wcfmmp_checkout_order_processed( $order_id );
		
		$user = wp_get_current_user();
		$this->prepare_customer_obj( $user, $_POST );
		
		// Update userdata
		if (isset($this->customer->id)) {
			update_user_meta(get_current_user_id(), 'wcfmmp_stripe_split_pay_customer_id', $this->customer->id);
		}
		
		$wcfmmp_stripe_split_pay_list = $this->wcfmmp_stripe_split_pay_list( $order, $_POST );
		
		$vendor_gross_sales = 0;
		$total_gross_sales  = $wcfmmp_stripe_split_pay_list['total_amount'];
		
		$card_charged = false;
		$all_success = array();

		
		switch ($this->charge_type) {
			case 'direct_charges':
				
				if(isset($wcfmmp_stripe_split_pay_list['distribution_list']) && is_array($wcfmmp_stripe_split_pay_list['distribution_list']) && count($wcfmmp_stripe_split_pay_list['distribution_list']) > 0) {
					$i = 0;
					foreach($wcfmmp_stripe_split_pay_list['distribution_list'] as $vendor_id => $distribution_info) {
						$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint($vendor_id) );
						
						// Directly Charge the Customer and collect the application_fee
						try {
							$charge_data = array(
												"amount"          => $this->get_stripe_amount($distribution_info['gross_sales']),
												"currency"        => $wcfmmp_stripe_split_pay_list['currency'],
												"source"          => $wcfmmp_stripe_split_pay_list['stripe_token'][$i],
												"application_fee" => $this->get_stripe_amount($distribution_info['gross_sales'] - $distribution_info['commission']),
												"description"     => $wcfmmp_stripe_split_pay_list['description'],
											);
							$vendor_stripe_account = array(
																							"stripe_account" => $distribution_info['destination']
																						 );
							$charge_data = apply_filters('wcfmmp_stripe_split_pay_create_direct_charges', $charge_data);
							$i++;
							
							if ($this->debug)
								wcfm_log("Stripe Charge Data before Processing: " . serialize($charge_data));
							
							$this->charge = Stripe_Charge::create($charge_data, $vendor_stripe_account);
							
							if ($this->debug)
								wcfm_log("Stripe Charge Data after Processing: " . serialize($this->charge));
					
							if (isset($this->charge['failure_message']) && empty($this->charge['failure_message'])) {
								if (isset($this->charge->id)) {
									$order->update_meta_data('wcfmmp_stripe_split_pay_charge_id_'.$vendor_id, $this->charge->id);
									$order->update_meta_data('wcfmmp_stripe_split_pay_charge_type_'.$vendor_id, $this->charge_type);
									$order->payment_complete();
																																							
									// Create vendor withdrawal Instance
									$commission_id_list = $wpdb->get_col("SELECT ID FROM `{$wpdb->prefix}wcfm_marketplace_orders` WHERE order_id =" . $order_id . " AND vendor_id = " . $vendor_id);
									
									$withdrawal_id = $WCFMmp->wcfmmp_withdraw->wcfmmp_withdrawal_processed( $vendor_id, $order_id, implode( ',', $commission_id_list ), 'stripe_split', $distribution_info['gross_sales'], $distribution_info['commission'], 0, 'pending', 'by_split_pay', 0 );
									
									// Wwithdrawal Processing
									$WCFMmp->wcfmmp_withdraw->wcfmmp_withdraw_status_update_by_withdrawal( $withdrawal_id, 'completed', __( 'Stripe Split Pay', 'wc-multivendor-marketplace' ) );
									
									// Withdrawal Meta
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'withdraw_amount', $distribution_info['commission'] );
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'currency', $order->get_currency() );
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'transaction_id', $this->charge->id );
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'transaction_type', $this->charge_type );
									
									do_action( 'wcfmmp_withdrawal_request_approved', $withdrawal_id );
									
									wcfmmp_log( sprintf( '#%s - %s payment processing complete via %s for order %s. Amount: %s', sprintf( '%06u', $withdrawal_id ), $store_name, 'Stripe Split Pay', $order_id, $distribution_info['commission'] . ' ' . $order->get_currency() ), 'info' );
								}
								$card_charged = true;
								$all_success[$vendor_id] = "true";
								$vendor_gross_sales += $distribution_info['gross_sales'];
							} else {
								wcfm_log( $store_name . " Stripe Charge Error: " . $this->charge['failure_message']);
								wc_add_notice(__("Stripe Charge Error: ", 'wc-multivendor-marketplace') . $this->charge['failure_message'], 'error');
								$all_success[$vendor_id] = "false";
								return false;
							}
						} catch (Exception $ex) {
							wcfm_log( $store_name . " Stripe Split Pay Error: " . $ex->getMessage());
							wc_add_notice(__("Stripe Split Pay Error: ", 'wc-multivendor-marketplace') . $ex->getMessage(), 'error');
							$this->delete_vendor_commission($order_id);
							//$stripe_cust = Stripe_Customer::retrieve($this->customer->id);
							//$stripe_cust->delete();
							$all_success[$vendor_id] = "false";
							return array(
								'result' => 'fail',
							);
						}
					}
				}
				
				// Remaining Amount Pay to Admin
				$remaining_sales_amount = $total_gross_sales - $vendor_gross_sales;
				if( $remaining_sales_amount ) {
					try {
						$charge_data = array(
																"amount"         => $this->get_stripe_amount( $remaining_sales_amount ),
																"currency"       => $wcfmmp_stripe_split_pay_list['currency'],
																"source"         => $wcfmmp_stripe_split_pay_list['stripe_source'],
																"description"    => sprintf( __( 'Payment for Order #%s', 'wc-multivendor-marketplace' ), $order_id ),
																);
						$this->charge = Stripe_Charge::create($charge_data);
						
						if (isset($this->charge['failure_message']) && empty($this->charge['failure_message'])) {
							if (isset($this->charge->id)) {
								$order->update_meta_data('wcfmmp_stripe_split_pay_charge_id_admin', $this->charge->id);
								$order->update_meta_data('wcfmmp_stripe_split_pay_charge_type_admin', $this->charge_type);
							}
							wcfm_log( "Stripe Remaining Amount Paid for Order #" . $order_id . " => " . $remaining_sales_amount );
						} else {
							wcfm_log( "Stripe Remaining Pay Error: " . $this->charge['failure_message'] );
							$all_success[0] = "false";
							return false;
						}
					} catch (Exception $ex) {
						wcfm_log( "Stripe Split Pay Remaining Pay Error: " . $ex->getMessage() );
						$all_success[0] = "false";
					}
				}
			break;
			
			case 'destination_charges':
				if(isset($wcfmmp_stripe_split_pay_list['distribution_list']) && is_array($wcfmmp_stripe_split_pay_list['distribution_list']) && count($wcfmmp_stripe_split_pay_list['distribution_list']) > 0) {
					$i = 0;
					foreach($wcfmmp_stripe_split_pay_list['distribution_list'] as $vendor_id => $distribution_info ) {
						$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint($vendor_id) );
						
						// Directly Charge the Customer and collect the application_fee
						try {
							$charge_data = array(
												"amount"       => $this->get_stripe_amount($distribution_info['gross_sales']),
												"currency"     => $wcfmmp_stripe_split_pay_list['currency'],
												"source"       => $wcfmmp_stripe_split_pay_list['stripe_token'][$i],
												"destination"  => array(
													"amount"     => $this->get_stripe_amount($distribution_info['commission']),
													"account"    => $distribution_info['destination'],
												),
												"description"    => $wcfmmp_stripe_split_pay_list['description'],
											);
							$charge_data = apply_filters('wcfmmp_stripe_split_pay_destination_charges', $charge_data);
							$i++;
							
							if ($this->debug)
								wcfm_log("Stripe Charge Data before Processing: " . serialize($charge_data));
							
							$this->charge = Stripe_Charge::create($charge_data);
							
							if ($this->debug)
								wcfm_log("Stripe Charge Data after Processing: " . serialize($this->charge));
							
							if (isset($this->charge['failure_message']) && empty($this->charge['failure_message'])) {
								if (isset($this->charge->id)) {
									$order->update_meta_data('wcfmmp_stripe_split_pay_charge_id_'.$vendor_id, $this->charge->id);
									$order->update_meta_data('wcfmmp_stripe_split_pay_charge_type_'.$vendor_id, $this->charge_type);
									$order->payment_complete();
																																							
									// Create vendor withdrawal Instance
									$commission_id_list = $wpdb->get_col("SELECT ID FROM `{$wpdb->prefix}wcfm_marketplace_orders` WHERE order_id =" . $order_id . " AND vendor_id = " . $vendor_id);
									
									$withdrawal_id = $WCFMmp->wcfmmp_withdraw->wcfmmp_withdrawal_processed( $vendor_id, $order_id, implode( ',', $commission_id_list ), 'stripe_split', $distribution_info['gross_sales'], $distribution_info['commission'], 0, 'pending', 'by_split_pay', 0 );
									
									// Withdrawal Processing
									$WCFMmp->wcfmmp_withdraw->wcfmmp_withdraw_status_update_by_withdrawal( $withdrawal_id, 'completed', __( 'Stripe Split Pay', 'wc-multivendor-marketplace' ) );
									
									// Withdrawal Meta
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'withdraw_amount', $distribution_info['commission'] );
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'currency', $order->get_currency() );
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'transaction_id', $this->charge->id );
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'transaction_type', $this->charge_type );
									
									do_action( 'wcfmmp_withdrawal_request_approved', $withdrawal_id );
									
									wcfmmp_log( sprintf( '#%s - %s payment processing complete via %s for order %s. Amount: %s', sprintf( '%06u', $withdrawal_id ), $store_name, 'Stripe Split Pay', $order_id, $distribution_info['commission'] . ' ' . $order->get_currency() ), 'info' );
								}
								$card_charged = true;
								$all_success[$vendor_id] = "true";
								$vendor_gross_sales += $distribution_info['gross_sales'];
							} else {
								wcfm_log( $store_name . " Stripe Charge Error: " . $this->charge['failure_message']);
								wc_add_notice(__("Stripe Charge Error: ", 'wc-multivendor-marketplace') . $this->charge['failure_message'], 'error');
								$all_success[$vendor_id] = "false";
								return false;
							}
						} catch (Exception $ex) {
							wcfm_log( $store_name . " Stripe Split Pay Error: " . $ex->getMessage());
							wc_add_notice(__("Stripe Split Pay Error: ", 'wc-multivendor-marketplace') . $ex->getMessage(), 'error');
							$this->delete_vendor_commission($order_id);
							//$stripe_cust = Stripe_Customer::retrieve($this->customer->id);
							//$stripe_cust->delete();
							$all_success[$vendor_id] = "false";
							return array(
								'result' => 'fail',
							);
						}
					}
				}
				
				// Remaining Amount Pay to Admin
				$remaining_sales_amount = $total_gross_sales - $vendor_gross_sales;
				if( $remaining_sales_amount ) {
					try {
						$charge_data = array(
																"amount"         =>  $this->get_stripe_amount( $remaining_sales_amount ),
																"currency"       => $wcfmmp_stripe_split_pay_list['currency'],
																"source"         => $wcfmmp_stripe_split_pay_list['stripe_source'],
																"description"    => sprintf( __( 'Payment for Order #%s', 'wc-multivendor-marketplace' ), $order_id ),
																);
						$this->charge = Stripe_Charge::create($charge_data);
						
						if (isset($this->charge['failure_message']) && empty($this->charge['failure_message'])) {
							if (isset($this->charge->id)) {
								$order->update_meta_data('wcfmmp_stripe_split_pay_charge_id_admin', $this->charge->id);
								$order->update_meta_data('wcfmmp_stripe_split_pay_charge_type_admin', $this->charge_type);
							}
							wcfm_log( "Stripe Remaining Amount Paid for Order #" . $order_id . " => " . $remaining_sales_amount );
						} else {
							wcfm_log( "Stripe Remaining Pay Error: " . $this->charge['failure_message'] );
							$all_success[0] = "false";
							return false;
						}
					} catch (Exception $ex) {
						wcfm_log( "Stripe Split Pay Remaining Pay Error: " . $ex->getMessage() );
						$all_success[0] = "false";
					}
				}
			break;
		
			case 'transfers_charges':
				try {
					$charge_data = array(
															"amount"         =>  $this->get_stripe_amount( $wcfmmp_stripe_split_pay_list['total_amount'] ),
															"currency"       => $wcfmmp_stripe_split_pay_list['currency'],
															"source"         => $wcfmmp_stripe_split_pay_list['stripe_source'],
															"transfer_group" => $wcfmmp_stripe_split_pay_list['transfer_group'],
															"description"    => $wcfmmp_stripe_split_pay_list['description'],
															);
					$charge_data = apply_filters( 'wcfmmp_stripe_split_pay_create_destination_charges', $charge_data, $_POST );
					
					if ($this->debug)
						wcfm_log("Stripe Charge Data before Processing: " . serialize($charge_data));
					
					$this->charge = Stripe_Charge::create($charge_data);
					
					if ($this->debug)
						wcfm_log("Stripe Charge Data after Processing: " . serialize($this->charge));
					
					if (isset($this->charge['failure_message']) && empty($this->charge['failure_message'])) {
						if (isset($this->charge->id)) {
							$order->update_meta_data( 'wcfmmp_stripe_split_pay_charge_id_'.$vendor_id, $this->charge->id );
							$order->update_meta_data( 'wcfmmp_stripe_split_pay_charge_type_'.$vendor_id, $this->charge_type );
							$order->payment_complete();
						}
						$card_charged = true;
					} else {
						wcfm_log( $store_name . " Stripe Charge Error: " . $this->charge['failure_message'] );
						wc_add_notice( __("Stripe Charge Error: ", 'wc-multivendor-marketplace') . $this->charge['failure_message'], 'error' );
						$all_success[$vendor_id] = "false";
						return false;
					}
				} catch (Exception $ex) {
					wcfm_log( $store_name . " Stripe Split Pay Error: " . $ex->getMessage() );
					wc_add_notice( __("Stripe Split Pay Error: ", 'wc-multivendor-marketplace') . $ex->getMessage(), 'error' );
					$this->delete_vendor_commission($order_id);
					//$stripe_cust = Stripe_Customer::retrieve( $this->customer->id );
					//$stripe_cust->delete();
					$all_success[$vendor_id] = "false";
					return array(
						'result' => 'fail',
					);
				}
					
				
				if($card_charged && isset( $wcfmmp_stripe_split_pay_list['distribution_list'] ) && is_array( $wcfmmp_stripe_split_pay_list['distribution_list'] ) && count( $wcfmmp_stripe_split_pay_list['distribution_list'] ) > 0 ) {
					foreach( $wcfmmp_stripe_split_pay_list['distribution_list'] as $vendor_id => $distribution_info ) {
						$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint($vendor_id) );
						
						// Dristribute among vendors
						$source_transaction = apply_filters( 'wcfmmp_stripe_split_pay_source_transaction_enabled', true, $vendor_id );
						try {
							$transfer_data = array(
																			"destination" => $distribution_info['destination'],
																			"description" => $wcfmmp_stripe_split_pay_list['transfer_group'],
																			);
							if($source_transaction) $transfer_data['source_transaction'] = $this->charge->id;
							$transfer_data = apply_filters('wcfmmp_stripe_split_pay_create_transfer', $transfer_data, $_POST);
							
							if ($this->debug)
								wcfm_log("Before creating transfer with Stripe. Stripe Transfer Data: " . serialize($transfer_data));
							
							$commission_id_list = $wpdb->get_col("SELECT ID FROM `{$wpdb->prefix}wcfm_marketplace_orders` WHERE order_id =" . $order_id . " AND vendor_id = " . $vendor_id);
							
							// Creating Withdrawal Instance
							$withdrawal_id = $WCFMmp->wcfmmp_withdraw->wcfmmp_withdrawal_processed( $vendor_id, $order_id, implode( ',', $commission_id_list ), 'stripe_split', $distribution_info['gross_sales'], $distribution_info['commission'], 0, 'pending', 'by_split_pay', 0 );
							
							// Processing 
							$transfer_response = $WCFMmp->wcfmmp_gateways->payment_gateways['stripe']->process_payment( $withdrawal_id, $vendor_id, $distribution_info['commission'], 0, 'auto', $transfer_data );
							
							// Update withdrawal status
							if ($transfer_response) {
								if( isset( $transfer_response['status'] ) && $transfer_response['status'] ) {
									$all_success[$vendor_id] = "true";
									
									// Withdrawal Processing
									$WCFMmp->wcfmmp_withdraw->wcfmmp_withdraw_status_update_by_withdrawal( $withdrawal_id, 'completed', __( 'Stripe Split Pay', 'wc-multivendor-marketplace' ) );
									
									// Withdrawal Meta
									$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $withdrawal_id, 'transaction_type', $this->charge_type );
									
									do_action( 'wcfmmp_withdrawal_request_approved', $withdrawal_id );
									
									wcfmmp_log( sprintf( '#%s - %s payment processing complete via %s for order %s. Amount: %s', sprintf( '%06u', $withdrawal_id ), $store_name, 'Stripe Split Pay', $order_id, $distribution_info['commission'] . ' ' . $order->get_currency() ), 'info' );
								} else {
									$all_success[$vendor_id] = "false";
									foreach ($transfer_response as $message) {
										wcfmmp_log( sprintf( '#%s - %s payment processing failed: %s', sprintf( '%06u', $withdrawal_id ), $store_name, $message['message'] ), 'error' );
									}
								}
							} else {
								wcfmmp_log( sprintf( '#%s - %s payment processing failed: %s', sprintf( '%06u', $withdrawal_id ), $store_name, __('Something went wrong please try again later.', 'wc-multivendor-marketplace') ), 'error' );
							}
							
							
							if ($this->debug)
								wcfm_log("After creating transfer with Stripe. Stripe Transfer Response: " . serialize($transfer_response));
						
						} catch (Exception $ex) {
							wcfm_log( $store_name . " Error creating transfer record with Stripe: " . $ex->getMessage());
							wc_add_notice(__("Error creating transfer record with Stripe: ", 'wc-multivendor-marketplace') . $ex->getMessage(), 'error');
							$all_success[$vendor_id] = "false";
							return array(
								'result' => 'fail',
							);
						}
					}
				}
			break;
			default:
		}

		//if ((is_array($all_success) && in_array( "false", $all_success )) || $this->vendor_disconnected) {
		if( is_array( $all_success ) && in_array( "false", $all_success ) ) {
			$order->update_status( apply_filters('wcfmmp_stripe_split_pay_failed_order_status', 'failed') );
			
			wc_add_notice( __("Stripe Payment Error", 'wc-multivendor-marketplace'), 'error' );
			
			return array(
				'result' => 'fail',
				'redirect' => $this->get_return_url($order)
			);
		} else {
			$order->update_status( apply_filters('wcfmmp_stripe_split_pay_completed_order_status', 'processing') );
			
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url($order)
			);
		}
		
		// Set Cart Empty
		// WC()->cart->empty_cart();
	}

	/**
	 * Generate payment arguments for Stripe Split Pay.
	 *
	 * @param  WC_Order $order Order data.
	 *
	 * @return array  Stripe Split Pay payment arguments.
	 */
	protected function wcfmmp_stripe_split_pay_list( $order, $postData ) {
		global $WCFM, $WCFMmp;
		$args = array();

		$split_payers = array();
		$total_vendor_commission = 0;
		$vendor_wise_gross_sales = $this->vendor_wise_gross_sales($order);
		
		if( $vendor_wise_gross_sales && is_array($vendor_wise_gross_sales) ) {
			foreach( $vendor_wise_gross_sales as $vendor_id => $distribution_info ) {
				$vendor_payment_method = $WCFMmp->wcfmmp_vendor->get_vendor_payment_method( $vendor_id );
				if( ($vendor_payment_method == 'stripe') && apply_filters( 'wcfmmp_is_allow_vendor_stripe_split_pay', true, $vendor_id ) ) {
					$vendor_connected = get_user_meta( $vendor_id, 'vendor_connected', true );
					$vendor_stripe_user_id = get_user_meta( $vendor_id, 'stripe_user_id', true );
					if( $vendor_connected && $vendor_stripe_user_id ) {
						$vendor_order_amount = $this->vendor_commission( $vendor_id, $order->get_id(), $order );
						$vendor_commission = round( $vendor_order_amount, 2 );
						//wcfm_log( "Stripe Split Pay:: #" . $order->get_id() . " => " . $vendor_id . " => " . $vendor_commission );
						if( $vendor_commission > 0 ) {
							$split_payers[$vendor_id] = array(
								'destination' => $vendor_stripe_user_id,
								'commission'  => $vendor_commission,
							);
							if( isset( $vendor_wise_gross_sales[$vendor_id] ) ) $split_payers[$vendor_id]['gross_sales'] = $vendor_wise_gross_sales[$vendor_id];
						}
						$total_vendor_commission += $vendor_commission;
					} else {
						$this->vendor_disconnected = true;
					}
				}
			}
		}

		$args = array(
			'total_amount'   => number_format($order->get_total(), 2, '.', ''),
			'stripe_source'  => $postData['stripe_source'],
			'stripe_token'   => $postData['stripe_token'],
			'currency'       => $order->get_currency(),
			'transfer_group' => __('Split Pay for Order #', 'wc-multivendor-marketplace') . $order->get_order_number(),
			'description'    => __('Payment for Order #', 'wc-multivendor-marketplace') . $order->get_order_number()
		);

		$args['distribution_list'] = $split_payers;
		
		$args = apply_filters( 'wcfmmp_stripe_split_pay_payment_args', $args, $order );

		return $args;
	}
    
	public function vendor_wise_gross_sales($order) {
		global $WCFM, $WCFMmp;
		
		$vendor_wise_gross_sales = array();
		if(!$order) {
			return $vendor_wise_gross_sales;
		}
		
		$items = $order->get_items('line_item');
		foreach ($items as $order_item_id => $item) {
			$line_item = new WC_Order_Item_Product($item);
			$product_id = $line_item->get_product_id();
			if ($product_id) {
				$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
				if( $vendor_id ) {
					$line_item_total = $line_item->get_total() + $line_item->get_total_tax();

					if(isset($vendor_wise_gross_sales[$vendor_id])) $vendor_wise_gross_sales[$vendor_id] = $vendor_wise_gross_sales[$vendor_id] + $line_item_total;
					else $vendor_wise_gross_sales[$vendor_id] = $line_item_total;
				}
			}
		}
		
		$shipping_items = $order->get_items('shipping');
		foreach ($shipping_items as $shipping_item_id => $shipping_item) {
			$order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
			$shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
			if($shipping_vendor_id > 0) {
				$shipping_item_total = $order_item_shipping->get_total() + $order_item_shipping->get_total_tax();
				
				if(isset($vendor_wise_gross_sales[$shipping_vendor_id])) $vendor_wise_gross_sales[$shipping_vendor_id] = $vendor_wise_gross_sales[$shipping_vendor_id] + $shipping_item_total;
				else $vendor_wise_gross_sales[$shipping_vendor_id] = $shipping_item_total;
			}
		}
	
		return $vendor_wise_gross_sales;
	}
	
	public function vendor_commission( $vendor_id, $order_id, $order ) {
		global $WCFM, $WCFMmp;
		
		$commission_amount = 0;
		
		$items = $order->get_items('line_item');
		foreach ($items as $order_item_id => $item) {
			$line_item = new WC_Order_Item_Product($item);
			$product_id = $line_item->get_product_id();
			if ($product_id) {
				$pvendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
				if( $pvendor_id && ( $pvendor_id == $vendor_id ) ) {
					if( $WCFMmp->wcfmmp_vendor->is_vendor_deduct_discount( $vendor_id, $order_id ) ) {
						$commission_rule   = $WCFMmp->wcfmmp_product->wcfmmp_get_product_commission_rule( $product_id, $line_item->get_variation_id(), $vendor_id, $line_item->get_total(), $line_item->get_quantity(), $order_id );
						$commission_amount += $WCFMmp->wcfmmp_commission->wcfmmp_get_order_item_commission( $order_id, $vendor_id, $product_id, $line_item->get_variation_id(), $line_item->get_total(), $line_item->get_quantity(), $commission_rule );
					} else {
						$commission_rule   = $WCFMmp->wcfmmp_product->wcfmmp_get_product_commission_rule( $product_id, $line_item->get_variation_id(), $vendor_id, $line_item->get_subtotal(), $line_item->get_quantity(), $order_id );
						$commission_amount += $WCFMmp->wcfmmp_commission->wcfmmp_get_order_item_commission( $order_id, $vendor_id, $product_id, $line_item->get_variation_id(), $line_item->get_subtotal(), $line_item->get_quantity(), $commission_rule );
					}
					if( $WCFMmp->wcfmmp_vendor->is_vendor_get_tax( $vendor_id ) ) {
						$tax_cost       = apply_filters( 'wcfmmmp_commission_tax_cost', $line_item->get_total_tax(), $commission_amount, $order_id, $vendor_id, $product_id, $commission_rule );
						
						if( apply_filters( 'wcfmmp_is_allow_commission_on_tax', false ) ) {
							$tax_cost = $WCFMmp->wcfmmp_commission->wcfmmp_generate_commission_cost( $tax_cost, $commission_rule );
						}
						
						$commission_amount = $commission_amount + $tax_cost;
					}
				}
			}
		}
		
		if( $get_shipping = $WCFMmp->wcfmmp_vendor->is_vendor_get_shipping( $vendor_id ) ) {
			$shipping_items = $order->get_items('shipping');
			foreach ($shipping_items as $shipping_item_id => $shipping_item) {
				$order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
				$shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
				if( $shipping_vendor_id > 0 && ( $shipping_vendor_id == $vendor_id ) ) {
					
					$shipping_cost       = apply_filters( 'wcfmmmp_commission_shipping_cost', $order_item_shipping->get_total(), $shipping_items, $order_id, $vendor_id, $product_id, $commission_rule );
					$shipping_tax        = $order_item_shipping->get_total_tax();
					
					// Commission Rule on Shipping Cost - by default false
					if( apply_filters( 'wcfmmp_is_allow_commission_on_shipping', false ) ) {
						$shipping_cost = $WCFMmp->wcfmmp_commission->wcfmmp_generate_commission_cost( $shipping_cost, $commission_rule );
						$shipping_tax  = $WCFMmp->wcfmmp_commission->wcfmmp_generate_commission_cost( $shipping_tax, $commission_rule );
					}
					
					$commission_amount = $commission_amount + $shipping_cost;
					if( $WCFMmp->wcfmmp_vendor->is_vendor_get_tax( $vendor_id ) ) {
						$commission_amount += $shipping_tax;
					}
					
				}
			}
		}
		
		return $commission_amount;
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' => __('Enable/Disable', 'wc-multivendor-marketplace'),
				'type' => 'checkbox',
				'default' => 'yes'
			)
		);
	}

	protected function delete_vendor_commission($order_id) {
		global $wpdb;
		
		$wpdb->delete( $wpdb->prefix . 'wcfm_marketplace_orders', array( 'order_id' => $order_id ), array( '%d' ) );
		delete_post_meta( $order_id, '_wcfmmp_order_processed' );
	}

	/**
	 * Prepare & assign stripe customer object
	 *
	 * @since 1.0.0
	 * @param object $user
	 * @param array $postData
	 *
	 * @throws Exception When card was not added or for and invalid card.
	 * @return object
	 */
	public function prepare_customer_obj($user, $postData) {
		// Create stripe customer
		try {
			$customer_data = apply_filters('wcfmmp_stripe_split_pay_customer_data', array(
					"email" => $user->user_email,
							), $user, $postData);
			if ($this->debug)
			  wcfm_log("Before creating customer record with Stripe. Stripe Data: " . serialize($customer_data));
			$this->customer = Stripe_Customer::create($customer_data);
		} catch (Exception $ex) {
			wcfm_log("Error creating customer record with Stripe: " . $ex->getMessage());
			wc_add_notice(__("Error creating customer record with Stripe: ", 'wc-multivendor-marketplace') . $ex->getMessage(), 'error');
			return false;
		}
	}
	
	/**
	 * Stripe Split Charges Refund
	 */
	public function wcfmmp_stripe_split_process_refund( $refund_id, $order_id, $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( $WCFMmp->refund_processed ) return;
		
		if( !$refund_id ) return;
		if( !$order_id ) return;
		if( !$vendor_id ) return;
		
		$order = wc_get_order( $order_id );
		if( !is_a( $order, 'WC_Order' ) ) return;
		
		if( $order->get_payment_method() != 'stripe_split' ) return;
		
		$vendor_token     = get_user_meta( $vendor_id, 'access_token', true );
		if( !$vendor_token ) return;
		
		
    $vendor_charge_id = $order->get_meta( "wcfmmp_stripe_split_pay_charge_id_{$vendor_id}" );
    if( !$vendor_charge_id ) return;
    
    $split_pay_refund_id = $order->get_meta( "wcfmmp_stripe_split_pay_refund_id_{$refund_id}" );
    if( $split_pay_refund_id ) return;
    
    $is_split_pay_refund_processed = $order->get_meta( "wcfmmp_stripe_split_pay_refund_processed_{$refund_id}" );
    if( $is_split_pay_refund_processed ) return;
    
    $WCFMmp->refund_processed = true;
    
    $sql = "SELECT item_id, commission_id, vendor_id, order_id, is_partially_refunded, refunded_amount, refund_reason FROM {$wpdb->prefix}wcfm_marketplace_refund_request";
		$sql .= " WHERE 1=1";
		$sql .= " AND ID = {$refund_id}";
		$refund_infos = $wpdb->get_results( $sql );
		if( !empty( $refund_infos ) ) {
			foreach( $refund_infos as $refund_info ) {
				$refunded_amount       = (float) $refund_info->refunded_amount;
				$refund_reason         = $refund_info->refund_reason;
				$is_partially_refunded = $refund_info->is_partially_refunded;
				
				$refund_application_fee = true;
				if( $is_partially_refunded ) $refund_application_fee = false;
				$refund_application_fee = apply_filters( 'wcfm_is_allow_stripe_refund_application_fee', $refund_application_fee );
				
				\Stripe\Stripe::setApiKey( $this->secret_key );

        try {
            $refund = \Stripe\Refund::create( [
                'charge'                 => $vendor_charge_id,
                'amount'                 => $this->get_stripe_amount( $refunded_amount ),
                'reason'                 => 'requested_by_customer',
                'refund_application_fee' => $refund_application_fee
            ], $vendor_token );
        } catch( Exception $e ) {
           wcfm_log( "Stripe Split Pay refund error: " . $e->getMessage() );
        }
        
        if ( $refund->id ) {
        	wcfm_log( "Stripe Split Pay refund successful for #{$refund_id}. Stripe refund ID => " . $refund->id );
        	$order->update_meta_data( 'wcfmmp_stripe_split_pay_refund_id_'.$refund_id, $refund->id );
        	$order->add_order_note( sprintf( __( 'Refund Processed Via Stripe ( Refund ID: #%s )', 'wc-multivendor-marketplace' ), $refund_id ) );
        } else {
        	wcfm_log( "Stripe Split Pay refund failed #" . $refund_id );
        }
			}
		}
	}

	public function get_stripe_amount($total, $currency = '', $reverse = false) {
		if (!$currency) {
			$currency = get_woocommerce_currency();
		}

		if (in_array(strtolower($currency), $this->no_decimal_currencies())) {
			return absint($total);
		} else {
			if ($reverse) {
				return absint(wc_format_decimal(( (float) $total / 100), wc_get_price_decimals())); // actual.
			} else {
				return absint(wc_format_decimal(( (float) $total * 100), wc_get_price_decimals())); // In cents.
			}
		}
	}

	/**
	 * List of currencies supported by Stripe that has no decimals.
	 *
	 * @return array $currencies
	 */
	public function no_decimal_currencies() {
		return apply_filters('wcfmmp_stripe_split_pay_no_decimal_currencies', array(
				'bif', // Burundian Franc
				'djf', // Djiboutian Franc
				'jpy', // Japanese Yen
				'krw', // South Korean Won
				'pyg', // Paraguayan GuaranÃ­
				'vnd', // Vietnamese Ä�á»“ng
				'xaf', // Central African Cfa Franc
				'xpf', // Cfp Franc
				'clp', // Chilean Peso
				'gnf', // Guinean Franc
				'kmf', // Comorian Franc
				'mga', // Malagasy Ariary
				'rwf', // Rwandan Franc
				'vuv', // Vanuatu Vatu
				'xof', // West African Cfa Franc
		));
	}

	public function stripe_access_token_error() { ?>
		<div id="message tes" class="error">
				<p><?php printf( __( "<strong>Stripe Gateway is disabled.</strong> Please re-check %swithdrawal setting panel%s. This occurs mostly due to absence of Stripe Secret Key", 'wc-multivendor-marketplace' ), '<a href="'.get_wcfm_settings_url().'#wcfm_settings_form_withdrawal_head">', '</a>' ); ?></p>
		</div>
	<?php }
}