jQuery(document).ready(function($) {
	$('#vendor_sold_by_template').change(function() {
		$vendor_sold_by_template = $(this).val();
		$('.vendor_sold_by_type').addClass('wcfm_ele_hide');
		$('.vendor_sold_by_type_'+$vendor_sold_by_template).removeClass('wcfm_ele_hide');
	}).change();
		
	$('#vendor_commission_mode').change(function() {
		$vendor_commission_mode = $(this).val();
		$('.commission_mode_field').addClass('wcfm_ele_hide');
		$('.commission_mode_'+$vendor_commission_mode).removeClass('wcfm_ele_hide');
		resetCollapsHeight($('#vendor_commission_mode').parent());
	}).change();
	
	function addVariationCommissionProperty() {
		$('.var_commission_mode').each(function() {
			$(this).change(function() {
				$vendor_commission_mode = $(this).val();
				$(this).parent().find('.var_commission_mode_field').addClass('wcfm_custom_hide');
				$(this).parent().find('.var_commission_mode_'+$vendor_commission_mode).removeClass('wcfm_custom_hide');
				resetCollapsHeight($('#variations'));
			}).change();
		});
	}
	addVariationCommissionProperty();
	
	$('#withdrawal_mode').change(function() {
		$withdrawal_mode = $(this).val();
		if( $withdrawal_mode == 'by_order_status' ) {
			$('.auto_withdrawal_order_status').removeClass('wcfm_custom_hide');
			$('.manual_withdrawal_ele').addClass('wcfm_custom_hide');
			$('.withdrawal_threshold_ele').addClass('wcfm_custom_hide');
			$('.schedule_withdrawal_threshold_ele').addClass('wcfm_custom_hide');
		} else if( $withdrawal_mode == 'by_manual' ) {
			$('.auto_withdrawal_order_status').addClass('wcfm_custom_hide');
			$('.schedule_withdrawal_threshold_ele').addClass('wcfm_custom_hide');
			$('.manual_withdrawal_ele').removeClass('wcfm_custom_hide');
			$('.withdrawal_threshold_ele').removeClass('wcfm_custom_hide');
		} else if( $withdrawal_mode == 'by_schedule' ) {
			$('.auto_withdrawal_order_status').addClass('wcfm_custom_hide');
			$('.manual_withdrawal_ele').removeClass('wcfm_custom_hide');
			$('.schedule_withdrawal_threshold_ele').removeClass('wcfm_custom_hide');
			$('.withdrawal_threshold_ele').removeClass('wcfm_custom_hide');
		}
	}).change();
	
	$('#withdrawal_reverse').change(function() {
		if( $(this).is(':checked') ) {
			$('.reverse_withdrawal_ele').removeClass('wcfm_custom_hide');
		} else {
			$('.reverse_withdrawal_ele').addClass('wcfm_custom_hide');
		}
	}).change();
	
	$('#withdrawal_payment_methods').find('.payment_options').each(function() {
		$(this).change(function() {
			$payment_option = $(this).val();
			if( $(this).is(':checked') ) {
				$('.withdrawal_mode_'+$payment_option).removeClass('wcfm_ele_hide');
			} else {
				$('.withdrawal_mode_'+$payment_option).addClass('wcfm_ele_hide');
			}
		}).change();
	});
	
	$('#withdrawal_test_mode').change(function() {
		if( $(this).is(':checked') ) {
			$('.withdrawal_mode_live').addClass('wcfm_custom_hide');
			$('.withdrawal_mode_test').removeClass('wcfm_custom_hide');
		} else {
			$('.withdrawal_mode_live').removeClass('wcfm_custom_hide');
			$('.withdrawal_mode_test').addClass('wcfm_custom_hide');
		}
	}).change();
	
	$('#withdrawal_charge_type').change(function() {
		$withdrawal_charge_type = $(this).val();
		if( $withdrawal_charge_type == 'no' ) {
			$('.withdraw_charge_block').addClass('wcfm_custom_hide');
		} else {
			$('.withdraw_charge_block').removeClass('wcfm_custom_hide');
			$('.withdraw_charge_field').addClass('wcfm_ele_hide');
			$('.withdraw_charge_'+$withdrawal_charge_type).removeClass('wcfm_ele_hide');
		}
	}).change();
	
	// Gateway specific charge option
	$('#withdrawal_payment_methods').find('.payment_options').each(function() {
		$(this).change(function() {
			$payment_option = $(this).val();
			if( $(this).is(':checked') ) {
				$('.withdraw_charge_'+$payment_option).removeClass('wcfm_ele_hide');
			} else {
				$('.withdraw_charge_'+$payment_option).addClass('wcfm_ele_hide');
			}
		}).change();
	});
	
	// Vendor Payment Method Specific charge Option
	$('#payment_mode').change(function() {
		$vendor_payment_mode = $(this).val();
		$('.withdraw_charge_block').addClass('wcfm_block_hide');
		$('.withdraw_charge_'+$vendor_payment_mode).removeClass('wcfm_block_hide');
		resetCollapsHeight($('#vendor_withdrawal_mode').parent());
	}).change();
	
	$('#vendor_withdrawal_mode').change(function() {
		$vendor_withdrawal_mode = $(this).val();
		$('.withdrawal_mode_field').addClass('wcfm_ele_hide');
		$('.withdrawal_mode_'+$vendor_withdrawal_mode).removeClass('wcfm_ele_hide');
		if( $vendor_withdrawal_mode != 'global' ) {
			$('#withdrawal_charge_type').change();
		}
		resetCollapsHeight($('#vendor_withdrawal_mode').parent());
	}).change();
	
	// Store Shipping Setting Options
	$('#enable_store_shipping').click(function() {
	  if( $(this).is(':checked') ) {
	  	$('.wcfm_store_shipping_fields').removeClass('wcfm_ele_hide');
	  	resetCollapsHeight($('#wcfm_settings_form_shipping_bu_country_expander'));
	  } else {
	  	$('.wcfm_store_shipping_fields').addClass('wcfm_ele_hide');
	  }
	});
	if( !$('#enable_store_shipping').is(':checked') ) {
		$('.wcfm_store_shipping_fields').addClass('wcfm_ele_hide');
	}
	
	// Store Style Settings Reset to Default
	if( $('#wcfm_store_color_setting_reset_button').length > 0 ) {
		$('#wcfm_store_color_setting_reset_button').click(function(event) {
			event.preventDefault();
			$.each(wcfm_store_color_setting_options, function( wcfm_store_color_setting_option, wcfm_store_color_setting_option_values ) {
				//$('#' + wcfm_color_setting_option_values.name).val( wcfm_color_setting_option_values.default );	
				$('#' + wcfm_store_color_setting_option_values.name).iris( 'color', wcfm_store_color_setting_option_values.default );
			} );
			$('#wcfm_settings_save_button').click();
		});
	}
	
	// WCfM Marketplace banner settings options
	if( $('#banner_type').length > 0 ) {
		$('#banner_type').change(function() {
			$banner_type = $(this).val();
			$('.banner_type_field').hide();
			$('.banner_type_' + $banner_type).show();
			$('input[type="text"].banner_type_upload').hide();
		}).change();
	}
	
	// SMS Verification
	if( $('#wcfmvm_sms_verification').length > 0 ) {
		$('#wcfmvm_sms_verification').click(function() {
		  if( $(this).is(':checked') ) {
		  	$('#phone').attr( 'checked', true );
		  }
		});
	}
});



// Shipping by Country and weight
(function($){
		
	$('#enable_marketplace_shipping').click(function() {
	  if( $(this).is(':checked') ) {
	  	$('.wcfm_store_shipping_country_fields').removeClass('wcfm_ele_hide');
	  	resetCollapsHeight($('#wcfm_settings_form_shipping_bu_country_expander'));
	  } else {
	  	$('.wcfm_store_shipping_country_fields').addClass('wcfm_ele_hide');
	  }
	});
	if( !$('#enable_marketplace_shipping').is(':checked') ) {
		$('.wcfm_store_shipping_country_fields').addClass('wcfm_ele_hide');
	}
	
	$('#enable_marketplace_shipping_by_weight').click(function() {
	  if( $(this).is(':checked') ) {
	  	$('.wcfm_store_shipping_weight_fields').removeClass('wcfm_ele_hide');
	  	resetCollapsHeight($('#wcfm_settings_form_shipping_bu_country_expander'));
	  } else {
	  	$('.wcfm_store_shipping_weight_fields').addClass('wcfm_ele_hide');
	  }
	});
	if( !$('#enable_marketplace_shipping_by_weight').is(':checked') ) {
		$('.wcfm_store_shipping_weight_fields').addClass('wcfm_ele_hide');
	}
	
	function setStateBoxforCountry( countryBox ) {
		var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
				states = $.parseJSON( states_json ),
				country = countryBox.val();

		if ( states[ country ] ) {
			if ( $.isEmptyObject( states[ country ] ) ) {
				countryBox.parent().find('.wcfmmp_state_to_select').each(function() {
					$statebox = $(this);
					$statebox_id = $statebox.attr('id');
					$statebox_name = $statebox.attr('name');
					$statebox_val = $statebox.val();
					if( $statebox_val === null ) $statebox_val = '';
					$statebox_dataname = $statebox.data('name');
					
					if ( $statebox.is( 'select' ) ) {
						$statebox.replaceWith( '<input type="text" name="'+$statebox_name+'" id="'+$statebox_id+'" data-name="'+$statebox_dataname+'" value="'+$statebox_val+'" class="wcfm-text wcfmmp_state_to_select multi_input_block_element" />' );
					}
				});
			} else {
				input_selected_state = '';
				var options = '',
						state = states[ country ];

				countryBox.parent().find('.wcfmmp_state_to_select').each(function() {
					$statebox = $(this);
					$statebox_id = $statebox.attr('id');
					$statebox_name = $statebox.attr('name');
					$statebox_val = $statebox.val();
					if( $statebox_val === null ) $statebox_val = '';
					$statebox_dataname = $statebox.data('name');
					
					for ( var index in state ) {
						if ( state.hasOwnProperty( index ) ) {
							if ( $statebox_val ) {
								if ( $statebox_val == index ) {
									var selected_value = 'selected="selected"';
								} else {
									var selected_value = '';
								}
							}
							options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
						}
					}
					
					if ( $statebox.is( 'select' ) ) {
						$statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option><optgroup label="-------------------------------------"><option value="everywhere">'+wcfm_dashboard_messages.everywhere+'</option></optgroup><optgroup label="-------------------------------------">' + options + '</optgroup>' );
					}
					if ( $statebox.is( 'input' ) ) {
						$statebox.replaceWith( '<select name="'+$statebox_name+'" id="'+$statebox_id+'" data-name="'+$statebox_dataname+'" class="wcfm-select wcfmmp_state_to_select multi_input_block_element"></select>' );
						$statebox = $('#'+$statebox_id);
						$statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option><optgroup label="-------------------------------------"><option value="everywhere">'+wcfm_dashboard_messages.everywhere+'</option></optgroup><optgroup label="-------------------------------------">' + options + '</optgroup>' );
					}
					$statebox.val( $statebox_val );
				});
			}
		} else {
			countryBox.parent().find('.wcfmmp_state_to_select').each(function() {
				$statebox = $(this);
				$statebox_id = $statebox.attr('id');
				$statebox_name = $statebox.attr('name');
				$statebox_val = $statebox.val();
				if( $statebox_val === null ) $statebox_val = '';
				$statebox_dataname = $statebox.data('name');
				
				if ( $statebox.is( 'select' ) ) {
					$statebox.replaceWith( '<input type="text" name="'+$statebox_name+'" id="'+$statebox_id+'" data-name="'+$statebox_dataname+'" value="'+$statebox_val+'" class="wcfm-text wcfmmp_state_to_select multi_input_block_element" />' );
				}
			});
		}
		
		if( country == 'everywhere' ) {
			countryBox.parent().find('.wcfmmp_shipping_state_rates_label').addClass('wcfm_custom_hide');
			countryBox.parent().find('.multi_input_holder').addClass('wcfm_custom_hide');
		} else {
			countryBox.parent().find('.wcfmmp_shipping_state_rates_label').removeClass('wcfm_custom_hide');
			countryBox.parent().find('.multi_input_holder').removeClass('wcfm_custom_hide');
		}
	}
	
	$('.wcfmmp_country_to_select').each(function() {
	  $(this).change(function() {
	    setStateBoxforCountry( $(this) );
	  }).change();
	});
	
	setTimeout(function() {
    
		$('#wcfmmp_shipping_rates').children('.multi_input_block').children('.add_multi_input_block').click(function() {
      
			$('#wcfmmp_shipping_rates').children('.multi_input_block:last').find('.wcfmmp_country_to_select').select2();
			$('#wcfmmp_shipping_rates').children('.multi_input_block:last').find('.wcfmmp_country_to_select').change(function() {
				setStateBoxforCountry( $(this) );
			}).change();
			
		});
		
		$('#wcfmmp_shipping_rates').find('.multi_input_block').children('.add_multi_input_block').click(function() {
			resetCollapsHeight($('#wcfm_settings_form_shipping_bu_country_expander'));
		});
    
    $('#wcfmmp_shipping_rates_by_weight').children('.multi_input_block').children('.add_multi_input_block').click(function() {
      //alert('aa');
      //console.log($('#wcfmmp_shipping_rates_by_weight').children('.multi_input_block:last').find('.wcfmmp_weightwise_country_to'));
      $('#wcfmmp_shipping_rates_by_weight').children('.multi_input_block:last').find('.wcfmmp_weightwise_country_to_select').select2();
    });
    
    $('#wcfmmp_shipping_rates_by_weight').find('.multi_input_block').children('.add_multi_input_block').click(function() {
      resetCollapsHeight($('#wcfm_settings_form_shipping_bu_country_expander'));
    });
    
	}, 2000 );
  
})(jQuery);