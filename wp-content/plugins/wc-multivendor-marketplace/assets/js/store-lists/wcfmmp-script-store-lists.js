jQuery(document).ready(function($) {
		
	// Set Bootstrap full width class
	if( $('#wcfmmp-stores-lists').parent().hasClass('col-md-8') ) {
		$('#wcfmmp-stores-lists').parent().removeClass('col-md-8');
		$('#wcfmmp-stores-lists').parent().addClass('col-md-12');
	}
	if( $('#wcfmmp-stores-lists').parent().hasClass('col-md-9') ) {
		$('#wcfmmp-stores-lists').parent().removeClass('col-md-9');
		$('#wcfmmp-stores-lists').parent().addClass('col-md-12');
	}
	if( $('#wcfmmp-stores-lists').parent().hasClass('col-sm-8') ) {
		$('#wcfmmp-stores-lists').parent().removeClass('col-sm-8');
		$('#wcfmmp-stores-lists').parent().addClass('col-md-12');
	}
	if( $('#wcfmmp-stores-lists').parent().hasClass('col-sm-9') ) {
		$('#wcfmmp-stores-lists').parent().removeClass('col-sm-9');
		$('#wcfmmp-stores-lists').parent().addClass('col-md-12');
	}
	$('#wcfmmp-stores-lists').parent().removeClass('col-sm-push-3');
	$('#wcfmmp-stores-lists').parent().removeClass('col-sm-push-4');
	$('#wcfmmp-stores-lists').parent().removeClass('col-md-push-3');
	$('#wcfmmp-stores-lists').parent().removeClass('col-md-push-4');
	
	// Store Sidebar
	if( $('.left_sidebar').length > 0 ) {
		if( $(window).width() > 768 ) {
			$left_sidebar_height = $('.left_sidebar').outerHeight();
			$right_side_height = $('.right_side').outerHeight();
			if( $left_sidebar_height < $right_side_height ) {
				$('.left_sidebar').css( 'height', $right_side_height + 50 );
			}
		}
	}
		
	// Store Box Height Set
	function storeBoxHeightManage() {
		var store_list_footer_height = 280;
		if( $('.wcfmmp-single-store').hasClass('coloum-2') ) {
			$('.wcfmmp-single-store .store-footer').each(function() {
				if( $(this).outerHeight() > store_list_footer_height ) {
					store_list_footer_height = $(this).outerHeight();
				}
			});
			$('.wcfmmp-single-store .store-footer').css( 'height', store_list_footer_height );
		}
		
		$('.wcfmmp-store-lists-sorting #wcfmmp_store_orderby').on('change', function() {
		  $(this).parent().submit();
		});
	}
	setTimeout(function() { storeBoxHeightManage(); }, 200 );
	
	if( $("#wcfmmp_store_country").length > 0 ) {
		$("#wcfmmp_store_country").select2({
			allowClear:  true,
			placeholder: wcfmmp_store_list_messages.choose_location + ' ...'
		});
	}
	
	if( $("#wcfmmp_store_category").length > 0 ) {
		$("#wcfmmp_store_category").select2({
			allowClear:  true,
			placeholder: wcfmmp_store_list_messages.choose_category + ' ...'
		});
	}
		
		
	var form = $('.wcfmmp-store-search-form');
	var xhr;
	var timer = null;
	
	function refreshStoreList() {
	 data = {
			search_term             : $('#search').val(),
			wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
			action                  : 'wcfmmp_stores_list_search',
			pagination_base         : form.find('#pagination_base').val(),
			paged                   : form.find('#wcfm_paged').val(),
			per_row                 : $per_row,
			per_page                : $per_page,
			includes                : $includes,
			excludes                : $excludes,
			orderby                 : $('#wcfmmp_store_orderby').val(),
			has_orderby             : $has_orderby,
			has_product             : $has_product,
			sidebar                 : $sidebar,
			theme                   : $theme,
			search_data             : jQuery('.wcfmmp-store-search-form').serialize(),
			_wpnonce                : form.find('#nonce').val()
		};

		if (timer) {
			clearTimeout(timer);
		}

		if ( xhr ) {
			xhr.abort();
		}

		timer = setTimeout(function() {
			$('.wcfmmp-stores-listing').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			xhr = $.post(wcfm_params .ajax_url, data, function(response) {
				if (response.success) {
					$('.wcfmmp-stores-listing').unblock();

					var data = response.data;
					$('#wcfmmp-stores-wrap').html( $(data).find( '.wcfmmp-stores-content' ) );
					fetchMarkers();
					initEnquiryButton();
					setTimeout(function() { storeBoxHeightManage(); }, 200 );
				}
			});
		}, 500);
	}
	
	$wcfm_anr_loaded = false;
	function initEnquiryButton() {
		$('.wcfm_catalog_enquiry').each(function() {
			$(this).off('click').on('click', function(event) {
				event.preventDefault();
				
				$store   = $(this).data('store');
				$product = $(this).data('product');
				
				$.colorbox( { inline:true, href: "#enquiry_form_wrapper", width: $popup_width,
					onComplete:function() {
						
						$('#wcfm_enquiry_form').find('#enquiry_vendor_id').val($store);
						$('#wcfm_enquiry_form').find('#enquiry_product_id').val($product);
						
						if( jQuery('.anr_captcha_field').length > 0 ) {
							if (typeof grecaptcha != "undefined") {
								if( $wcfm_anr_loaded ) {
									grecaptcha.reset();
								} else {
									wcfm_anr_onloadCallback();
								}
								$wcfm_anr_loaded = true;
							}
						}
						
					}
				});
			});
		});
	}
	
	if( $('.wcfmmp-store-search-form').length > 0 ) {
		
		if( wcfmmp_store_list_options.is_geolocate ) {
			refreshStoreList();
		}
		
		form.on('keyup', '.wcfm-search-field', function() {
			refreshStoreList();
		} );

		form.on('keyup', '#search', function() {
			refreshStoreList();
		} );
		
		$('.wcfm-search-field').on('input',function(e){
			refreshStoreList();
		});

		$('#search').on('input',function(e){
			refreshStoreList();
		});
		
		// Category Filter
		form.on('change', '#wcfmmp_store_category', function() {
			refreshStoreList();
		} );
		
		// Country Filter
		//form.on('change', '#wcfmmp_store_country', function() {
		$( document.body ).on( 'wcfm_store_list_country_changed', function( event ) {
			refreshStoreList();
		} );
		
		// State Filter
		form.on('change', '#wcfmmp_store_state', function() {
			refreshStoreList();
		} );
		
		// State Filter
	  form.on('keyup', '#wcfmmp_store_state', function() {
			refreshStoreList();
		} );
		
		// Store Radius Search
		if( $('#wcfmmp_radius_addr').length > 0 ) {
			var wcfmmp_radius_addr_input = document.getElementById("wcfmmp_radius_addr");
			var awcfmmp_radius_addr_autocomplete = new google.maps.places.Autocomplete(wcfmmp_radius_addr_input);
			awcfmmp_radius_addr_autocomplete.addListener("place_changed", function() {
				var place = awcfmmp_radius_addr_autocomplete.getPlace();
				$('#wcfmmp_radius_lat').val(place.geometry.location.lat());
				$('#wcfmmp_radius_lng').val(place.geometry.location.lng());
				refreshStoreList();
			});
			
			$('#wcfmmp_radius_range').on('input', function() {
				$('.wcfmmp_radius_range_cur').html(this.value+'km');
				$('.wcfmmp_radius_range_cur').css( 'left', ((this.value/100)*$('.wcfm_radius_slidecontainer').outerWidth())-(15/2)+'px' );
				$wcfmmp_radius_lat = $('#wcfmmp_radius_lat').val();
				if( $wcfmmp_radius_lat ) {
					setTimeout(function() {refreshStoreList();}, 100);
				}
			});
			$('.wcfmmp_radius_range_cur').css( 'left', ((10/100)*$('.wcfm_radius_slidecontainer').outerWidth())-(15/2)+'px' );
		}
	}
	
	// Store List Filter Country -> State Dropdowns
	var wcfmmp_cs_filter_wrapper = $( '.wcfmmp-store-search-form' );
	var input_csd_state = '';
	var csd_selected_state = '';
	var wcfmmo_cs_filter_select = {
		init: function () {
			wcfmmp_cs_filter_wrapper.on( 'change', 'select#wcfmmp_store_country', this.state_select );
			//jQuery('select#wcfmmp_store_country').change();
		},
		state_select: function () {
			var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
					states = $.parseJSON( states_json ),
					$statebox = $( '#wcfmmp_store_state' ),
					value = $statebox.val(),
					country = $( this ).val(),
					$state_required = $statebox.data('required');

			if ( states[ country ] ) {

					if ( $.isEmptyObject( states[ country ] ) ) {

						if ( $statebox.is( 'select' ) ) {
							if( typeof $state_required != 'undefined') {
								$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
							} else {
								$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
							}
						}

						if( value ) {
							$( '#wcfmmp_store_state' ).val( value );
						} else {
							$( '#wcfmmp_store_state' ).val( '' );
						}

					} else {
							input_csd_state = '';

							var options = '',
									state = states[ country ];

							for ( var index in state ) {
									if ( state.hasOwnProperty( index ) ) {
											if ( csd_selected_state ) {
													if ( csd_selected_state == index ) {
															var selected_value = 'selected="selected"';
													} else {
															var selected_value = '';
													}
											}
											options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
									}
							}

							if ( $statebox.is( 'select' ) ) {
									$( 'select#wcfmmp_store_state' ).html( '<option value="">' + wcfmmp_store_list_messages.choose_state + ' ...</option>' + options );
							}
							if ( $statebox.is( 'input' ) ) {
								if( typeof $state_required != 'undefined') {
									$( 'input#wcfmmp_store_state' ).replaceWith( '<select class="wcfm-select wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state"></select>' );
								} else {
									$( 'input#wcfmmp_store_state' ).replaceWith( '<select class="wcfm-select wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state"></select>' );
								}
								$( 'select#wcfmmp_store_state' ).html( '<option value="">' + wcfmmp_store_list_messages.choose_state + ' ...</option>' + options );
							}
							//$( '#wcmarketplace_address_state' ).removeClass( 'wcmarketplace-hide' );
							//$( 'div#wcmarketplace-states-box' ).slideDown();

					}
			} else {
				if ( $statebox.is( 'select' ) ) {
					if( typeof $state_required != 'undefined') {
						$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
					} else {
						$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
					}
				}
				$( '#wcfmmp_store_state' ).val(input_csd_state);

				if ( $( '#wcfmmp_store_state' ).val() == 'N/A' ){
					$( '#wcfmmp_store_state' ).val('');
				}
				//$( '#wcmarketplace_address_state' ).removeClass( 'wcmarketplace-hide' );
				//$( 'div#wcmarketplace-states-box' ).slideDown();
			}
			
			$( document.body ).trigger( 'wcfm_store_list_country_changed' );
		}
	}
	
	wcfmmo_cs_filter_select.init();
	
	function fetchMarkers() {
		if( $('.wcfmmp-store-list-map').length > 0 ) {
			reloadMarkers();
			
			var data = {
				search_term             : $('.wcfmmp-store-search').val(),
				wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
				wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
				wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
				action                  : 'wcfmmp_stores_list_map_markers',
				pagination_base         : form.find('#pagination_base').val(),
				paged                   : form.find('#wcfm_paged').val(),
				per_row                 : $per_row,
				per_page                : $per_page,
				includes                : $includes,
				excludes                : $excludes,
				has_product             : $has_product,
				has_orderby             : $has_orderby,
				sidebar                 : $sidebar,
				theme                   : $theme,
				search_data             : jQuery('.wcfmmp-store-search-form').serialize(),
			};
			
			xhr = $.post(wcfm_params.ajax_url, data, function(response) {
				if (response.success) {
					var locations = response.data;
					setMarkers( $.parseJSON(locations) );
				}
			});
		}
	}
	
	// Store List Map
	if( $('.wcfmmp-store-list-map').length > 0 ) {
		$('.wcfmmp-store-list-map').css( 'height', $('.wcfmmp-store-list-map').outerWidth()/2);
		
		var markers = [];
		var store_list_map = '';
		
		function setMarkers(locations) {
			var latlngbounds = new google.maps.LatLngBounds();
			var infowindow = new google.maps.InfoWindow();
				
			$.each(locations, function( i, beach ) {
				var myLatLng = new google.maps.LatLng(beach.lat, beach.lang);
				latlngbounds.extend(myLatLng);
				var marker = new google.maps.Marker({
						position: myLatLng,
						map: store_list_map,
						animation: google.maps.Animation.DROP,
						title: beach.name,
						icon: beach.icon,
						zIndex: i 
				});
				
				var infoWindowContent = beach.info_window_content;
				
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
						infowindow.setContent(infoWindowContent);
						infowindow.open(store_list_map, marker);
					}
				})(marker, i));
				
				store_list_map.setCenter(marker.getPosition());

				// Push marker to markers array                                   
				markers.push(marker);
			});
			if( $auto_zoom && locations.length > 0 ) {
			  store_list_map.fitBounds(latlngbounds);
			}
		}
		
		function reloadMarkers() {
			for( var i = 0; i < markers.length; i++ ) {
				markers[i].setMap(null);
			}
			markers = [];
		}
		
		var mapOptions = {
        zoom: $map_zoom,
        center: new google.maps.LatLng(wcfmmp_store_list_options.default_lat,wcfmmp_store_list_options.default_lng,13),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    store_list_map = new google.maps.Map(document.getElementById('wcfmmp-store-list-map'), mapOptions);
    fetchMarkers();
	}
});