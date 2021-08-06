jQuery(document).ready(function($) {
	$('.wcfm_product_multivendor').click(function(event) {
	  event.preventDefault();
	  $('.wcfm_product_multivendor').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		
		var data = {
			action        : 'wcfmmp_product_multivendor_clone',
			product_id    : $('.wcfm_product_multivendor').data('product_id'),
		}	
		jQuery.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$response_json = jQuery.parseJSON(response);
				wcfm_notification_sound.play();
				if($response_json.redirect) {
					window.location = $response_json.redirect;
				} else {
					
				}
			}
		});
	});
});