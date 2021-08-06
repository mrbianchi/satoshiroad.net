$wcfm_refund_submited = false;

jQuery(document).ready(function($) {
	$refund_form_show = false;
	
	// Vendor Refund
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfmmp_order_refund_request').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				$order_id = $(this).data('order');
				initRefundPopup( $(this), $order_id, true );
			});
		});
	});
	
	// Customer Refund
	$('.wcfm-refund-action').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			$order_id = $(this).attr('href');
			initRefundPopup( $(this), $order_id, false );
		});
	});
	
	function initRefundPopup( $refund_ele, $order_id, $is_refresh ) {
		$item_id = $refund_ele.data('item');
		$commission_id = $refund_ele.data('commission');
		
		var data = {
			action        : 'wcfmmp_refund_requests_form_html',
			item_id       : $item_id,
			order_id      : $order_id,
			commission_id : $commission_id
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, width: $popup_width,
					onComplete:function() {
						
						if( jQuery('.anr_captcha_field').length > 0 ) {
							if (typeof grecaptcha != "undefined") {
								wcfm_refund_anr_onloadCallback();
							}
						}
						
						jQuery('#wcfm_refund_request').change(function() {
							$wcfm_refund_request = $(this).val();
							if( $wcfm_refund_request == 'full' ) {
								$('.wcfm-refund-form-request-amount').addClass('wcfm_custom_hide');
							} else {
								$('.wcfm-refund-form-request-amount').removeClass('wcfm_custom_hide');
							}
						}).change();
				
						// Intialize Quick Update Action
						jQuery('#wcfm_refund_requests_submit_button').click(function(event) {
							event.preventDefault();
							jQuery('#wcfm_refund_requests_form').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							if( jQuery('#wcfm_refund_reason').val().length == 0 ) {
								//alert(wcfm_refund_manage_messages.no_query);
								jQuery('#wcfm_refund_requests_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_refund_requests_messages.no_refund_reason).addClass('wcfm-error').slideDown();
								jQuery('#wcfm_refund_requests_form').unblock();
							} else {
								var data = {
									action                     : 'wcfm_ajax_controller',
									controller                 : 'wcfm-refund-requests-form', 
									wcfm_refund_requests_form  : jQuery('#wcfm_refund_requests_form').serialize()
								}	
								jQuery.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										jQueryresponse_json = jQuery.parseJSON(response);
										jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										wcfm_notification_sound.play();
										if(jQueryresponse_json.status) {
											jQuery('#wcfm_refund_requests_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
											jQuery('#wcfm_refund_requests_submit_button').hide();
											if( $is_refresh ) {
												$wcfm_orders_table.ajax.reload();
											} else {
												window.location = window.location.href; 
											}
											setTimeout(function() {
												jQuery.colorbox.remove();
											}, 2000);
										} else {
											jQuery('#wcfm_refund_requests_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
										}
										if( $('.wcfm_gglcptch_wrapper').length > 0 ) {
											if (typeof grecaptcha != "undefined") {
												grecaptcha.reset();
											}
										}
										jQuery('#wcfm_refund_requests_form').unblock();
									}
								} );
							}
							return false;
						});
					}
				});
			}
		});
	}
	
	$('.add_refund').click(function() {
		if( $refund_form_show ) {
			$('.refund_form_wrapper_hide').slideUp( "slow" );
			$refund_form_show = false;
		} else {
			$('.refund_form_wrapper_hide').slideDown( "slow" );
			$refund_form_show = true;
		}
	});
	
	// Submit Refund
	$('#wcfm_refund_requests_submit_button').click(function(event) {
	  event.preventDefault();
	  $wcfm_refund_submited = false;
	  wcfm_refund_requests_form_submit($(this).parent().parent());
	});
	
});
	
	
function wcfm_refund_requests_form_validate($refund_form) {
	$is_valid = true;
	jQuery('.wcfm-message').html('').removeClass('wcfm-success').removeClass('wcfm-error').slideUp();
	var wcfm_refund_reason = jQuery.trim($refund_form.find('#wcfm_refund_reason').val());
	if(wcfm_refund_reason.length == 0) {
		$is_valid = false;
		$refund_form.find('.wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_refund_requests_messages.no_refund_reason).addClass('wcfm-error').slideDown();
	}
	return $is_valid;
}

function wcfm_refund_requests_form_submit($refund_form) {
	
	// Validations
	$is_valid = wcfm_refund_requests_form_validate($refund_form);
	
	if($is_valid) {
		$refund_form.block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		
		var data = {
			action                   : 'wcfm_ajax_controller',
			controller               : 'wcfm-refund-tab',
			wcfm_refund_tab_form    : $refund_form.serialize(),
			status                   : 'submit'
		}	
		jQuery.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$response_json = jQuery.parseJSON(response);
				if($response_json.status) {
					wcfm_notification_sound.play();
					$refund_form.find('.wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" );
					setTimeout(function() {
						jQuery('.refund_form_wrapper_hide').slideUp( "slow" );
						$refund_form_show = false;
						$refund_form.find('#wcfm_refund_reason').val('');
					}, 2000 );
					$wcfm_refund_submited = true;
				} else {
					$refund_form.find('.wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
				}
				$refund_form.unblock();
			}
		});
	}
}