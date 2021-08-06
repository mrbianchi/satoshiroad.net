<?php
/**
 * WCFM plugin view
 *
 * WCfM Refund popup View
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/views/refund
 * @version   1.0.0
 */
 
global $wp, $WCFM, $WCFMmp, $_POST, $wpdb;

$order_id      = sanitize_text_field( $_POST['order_id'] );
$order_id = str_replace( '#', '', $order_id );

if( !$order_id ) return;

$item_id = 0;
if( isset( $_POST['item_id'] ) ) {
	$item_id       = sanitize_text_field( $_POST['item_id'] );
}

$commission_id = 0;
if( isset( $_POST['commission_id'] ) ) {
	$commission_id = sanitize_text_field( $_POST['commission_id'] );
}


$order                  = wc_get_order( $order_id );
$line_items             = $order->get_items( 'line_item' );
$line_items             = apply_filters( 'wcfm_valid_line_items', $line_items, $order_id );
$product_items          = array();
foreach ( $line_items as $order_item_id => $item ) {
	
	$sql = 'SELECT ID, withdraw_status, vendor_id, refund_status, is_partially_refunded, is_refunded FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
	$sql .= ' WHERE 1=1';
	$sql .= " AND `item_id` = " . $order_item_id;
	//$sql .= " AND (`is_refunded` = 1 OR `is_partially_refunded` = 1)";
	//$sql .= " AND `refund_status` in ('completed', 'requested')";
	$commissions = $wpdb->get_results( $sql );
	if( !empty( $commissions ) ) {
		foreach( $commissions as $commission ) {
			if( ( $commission->is_refunded != 1 ) && ( $commission->refund_status != 'requested' ) && ( ( $commission->is_partially_refunded != 1 ) || ( ( $commission->is_partially_refunded == 1 ) && ( $commission->refund_status == 'completed' ) ) ) ) {
				if ( $refunded_amount = $order->get_total_refunded_for_item( $order_item_id ) ) {
					$item_total = $item->get_total() - $refunded_amount;
				} else {
					$item_total = $item->get_total();
				}
				$product_items[$order_item_id] = $item->get_name() . ' (' . $item_total . ' ' . $order->get_currency() . ')';
			}
		}
	}
}

$wcfm_refund_product_attr = array( 'style' => 'width: 95%;' );
if( $item_id ) {
	$wcfm_refund_product_attr = array( 'style' => 'width: 95%;', 'readonly' => true );
}

$wcfm_refund_request_class = '';
if( !wcfm_is_vendor() ) {
	$wcfm_refund_request_class = ' wcfm_custom_hide';
}
?>

<div class="wcfm-clearfix"></div><br />
<div id="wcfm_refund_form_wrapper">
	<form action="" method="post" id="wcfm_refund_requests_form" class="refund-form wcfm_popup_wrapper" novalidate="">
		<div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Refund Request', 'wc-multivendor-marketplace' ); ?></h2></div>
		
		<?php if( !empty( $product_items ) ) { ?>
			<p class="wcfm-refund-form-product wcfm_popup_label">
				<label for="wcfm_refund_product"><strong><?php _e( 'Product', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></strong></label> 
			</p>
			<?php $WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_refund_fields_product', array( "wcfm_refund_product" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title', 'options' => $product_items , 'value' => $item_id ) ) ) ); ?>
			
			<p class="wcfm-refund-form-request wcfm_popup_label <?php echo $wcfm_refund_request_class; ?>">
				<label for="wcfm_refund_request"><strong><?php _e( 'Refund Requests', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></strong></label> 
			</p>
			<?php $WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_refund_fields_request', array( "wcfm_refund_request" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele wcfm_popup_input'.$wcfm_refund_request_class, 'label_class' => 'wcfm_title', 'options' => array( 'full' => __( 'Full Refund', 'wc-multivendor-marketplace' ), 'partial' => __( 'Partial Refund', 'wc-multivendor-marketplace' ) ) ) ) ) ); ?>
			
			<p class="wcfm-refund-form-request-amount wcfm_popup_label">
				<label for="wcfm_refund_request_amount"><strong><?php _e( 'Refund Amount', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></strong></label> 
			</p>
			<?php $WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_refund_fields_amount', array( "wcfm_refund_request_amount" => array( 'type' => 'number', 'attributes' => array( 'min' => '1', 'step' => '1' ), 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm-refund-form-request-amount wcfm_popup_input', 'label_class' => 'wcfm_title', 'value' => '1' ) ) ) ); ?>
		
			<p class="wcfm-refund-form-reason wcfm_popup_label">
				<label for="comment"><strong><?php _e( 'Refund Requests Reason', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></strong></label>
			</p>
			<textarea id="wcfm_refund_reason" name="wcfm_refund_reason" class="wcfm_popup_input wcfm_popup_textarea"></textarea>
			
			<?php if ( function_exists( 'gglcptch_init' ) ) { ?>
			<div class="wcfm_clearfix"></div>
			<div class="wcfm_gglcptch_wrapper" style="float:right;">
				<?php echo apply_filters( 'gglcptch_display_recaptcha', '', 'wcfm_refund_request_form' ); ?>
			</div>
		<?php } elseif ( class_exists( 'anr_captcha_class' ) && function_exists( 'anr_captcha_form_field' ) ) { ?>
			<div class="wcfm_clearfix"></div>
			<div class="wcfm_gglcptch_wrapper" style="float:right;">
				<div class="anr_captcha_field"><div id="anr_captcha_field_9999"></div></div>
							
				<?php
					$site_key = trim( anr_get_option( 'site_key' ) );
					$theme    = anr_get_option( 'theme', 'light' );
					$size     = anr_get_option( 'size', 'normal' );
					$language = trim( anr_get_option( 'language' ) );
		
						$lang = '';
					if ( $language ) {
						$lang = "&hl=$language";
					}
		
				?>
				<script type="text/javascript">
					var wcfm_refund_anr_onloadCallback = function() {
						var anr_obj = {
						'sitekey' : '<?php echo esc_js( $site_key ); ?>',
						'size' : '<?php echo esc_js( $size ); ?>',
					};
					<?php
					if ( 'invisible' == $size ) {
						wp_enqueue_script( 'jquery' );
						?>
						anr_obj.badge = '<?php echo esc_js( anr_get_option( 'badge', 'bottomright' ) ); ?>';
					<?php } else { ?>
						anr_obj.theme = '<?php echo esc_js( $theme ); ?>';
					<?php } ?>
				
						var anr_captcha9999;
						
						<?php if ( 'invisible' == $size ) { ?>
							var anr_form9999 = jQuery('#anr_captcha_field_9999').closest('form')[0];
							anr_obj.callback = function(){ anr_form9999.submit(); };
							anr_obj["expired-callback"] = function(){ grecaptcha.reset(anr_captcha9999); };
							
							anr_form9999.onsubmit = function(evt){
								evt.preventDefault();
								//grecaptcha.reset(anr_captcha9999);
								grecaptcha.execute(anr_captcha9999);
							};
						<?php } ?>
						anr_captcha_9999 = grecaptcha.render('anr_captcha_field_9999', anr_obj );
					};
				</script>
				<script src="https://www.google.com/recaptcha/api.js?render=explicit<?php echo esc_js( $lang ); ?>"
					async defer>
				</script>
			</div>
		<?php } ?>
			<div class="wcfm_clearfix"></div>
			<div class="wcfm-message" tabindex="-1"></div>
			<div class="wcfm_clearfix"></div><br />
			
			<p class="form-submit">
				<input name="submit" type="submit" id="wcfm_refund_requests_submit_button" class="submit wcfm_popup_button" value="<?php _e( 'Submit', 'wc-multivendor-marketplace' ); ?>"> 
				<input type="hidden" name="wcfm_refund_order_id" value="<?php echo $order_id; ?>" id="wcfm_refund_order_id">
				<input type="hidden" name="wcfm_refund_commission_id" value="<?php echo $commission_id; ?>" id="wcfm_refund_commission_id">
			</p>	
		<?php } else { ?>
			<div><?php _e( 'This order\'s item(s) are already requested for refund!', 'wc-multivendor-marketplace' ); ?></div>
		<?php } ?>
	</form>
</div>
<div class="wcfm-clearfix"></div>