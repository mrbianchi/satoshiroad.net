jQuery(function( $ ) {
  $( document.body ).on( 'wcfm-date-range-refreshed', function() {
		$('input[name="start_date"]').val($filter_date_form);
		$('input[name="end_date"]').val($filter_date_to);
		$('input[name="end_date"]').parent().submit();
	});
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			var data = {
				action                : 'sales_by_vendor_change_url',
				vendor_manager_change : $('#dropdown_vendor').val()
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					if($response_json.redirect) {
						window.location = $response_json.redirect;
					}
				}
			});
		}).select2( $wcfm_vendor_select_args );
	}	
});
