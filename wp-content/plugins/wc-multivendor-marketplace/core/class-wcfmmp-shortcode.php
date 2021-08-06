<?php
/**
 * WCFM Marketplace plugin core
 *
 * Plugin shortcode
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Shortcode {

	public $list_product;

	public function __construct() {
		
		// WCFM Markeptlace Store List
		add_shortcode('wcfm_stores', array(&$this, 'wcfmmp_stores_shortcode'));
		
		// WCFM Markeptlace Store List Map
		add_shortcode('wcfm_stores_map', array(&$this, 'wcfmmp_stores_map_shortcode'));
		
		// WCFM Marketplace Store Carousel
		add_shortcode('wcfm_stores_carousel', array(&$this, 'wcfmmp_stores_carousel_shortcode'));
		
		// WCFM Markeplace Store Info
		add_shortcode('wcfm_store_info', array(&$this, 'wcfmmp_store_info_shortcode'));
		
		// WCFM Markeplace Store Hours
		add_shortcode('wcfm_store_hours', array(&$this, 'wcfmmp_store_hours_shortcode'));
		
		// WCFM Markeplace More Offers
		add_shortcode('wcfm_more_offers', array(&$this, 'wcfmmp_more_offers_shortcode'));
		
		// WCFM Marketplace Store FB Widget
		add_shortcode('wcfm_store_fb_feed', array(&$this, 'wcfmmp_store_fb_widget_shortcode'));
		
		// WCFM Marketplace Store Twitter Widget
		add_shortcode('wcfm_store_twitter_feed', array(&$this, 'wcfmmp_store_twitter_widget_shortcode'));
		
		// WCFM Marketplace Store Instagram Widget
		//add_shortcode('wcfm_store_instagram_feed', array(&$this, 'wcfmmp_store_instagram_widget_shortcode'));
	}

	/**
	 * WCFM Stores Short Code
	 */
	public function wcfmmp_stores_shortcode( $atts ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $includes;
		$WCFM->nocache();
		
		$defaults = array(
				'per_page'           => 12,
				'sidebar'            => 'yes',
				'has_orderby'        => 'yes',
				'orderby'            => 'newness_asc',
				'filter'             => 'yes',
				'search'             => 'yes',
				'category'           => 'yes',
				'country'            => 'yes',
				'state'              => 'yes',
				'radius'             => 'no',
				'map'                => 'yes',
				'map_zoom'           => 5,
				'auto_zoom'          => 'yes',
				'per_row'            => 2,
				'includes'           => '',
				'excludes'           => '',
				'exclude_membership' => '',
				'search_category'    => '',
				'has_product'        => '',
				'theme'              => 'classic'
		);

		$attr   = shortcode_atts( apply_filters( 'wcfmmp_stores_default_args', $defaults ), $atts );
		$paged  = max( 1, get_query_var( 'paged' ) );
		$length = apply_filters( 'wcfmmp_stores_per_page', $attr['per_page'] );
		$offset = ( $paged - 1 ) * $length;
		
		$search_country = '';
		$search_state   = '';
		
		// GEO Locate Support
		if( apply_filters( 'wcfmmp_is_allow_store_list_by_user_location', true ) ) {
			if( is_user_logged_in() && !$search_country ) {
				$user_location = get_user_meta( get_current_user_id(), 'wcfm_user_location', true );
				if( $user_location ) {
					$search_country = $user_location['country'];
					$search_state   = $user_location['state'];
				}
			}
					
			if( apply_filters( 'wcfm_is_allow_wc_geolocate', true ) && class_exists( 'WC_Geolocation' ) && !$search_country ) {
				$user_location = WC_Geolocation::geolocate_ip();
				$search_country = $user_location['country'];
				$search_state   = $user_location['state'];
			}
		}

		$orderby          = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : apply_filters( 'wcfmmp_stores_default_orderby', $attr['orderby'] );
		$search_term      = isset( $_GET['wcfmmp_store_search'] ) ? sanitize_text_field( $_GET['wcfmmp_store_search'] ) : '';
		$search_category  = isset( $_GET['wcfmmp_store_category'] ) ? sanitize_text_field( $_GET['wcfmmp_store_category'] ) : $attr['search_category'];
		
		$search_category  = apply_filters( 'wcfmmp_stores_default_search_category', $search_category );
		$has_product      = apply_filters( 'wcfmmp_stores_list_has_product', $attr['has_product'] );
		
		$search_data     = array();
		if( isset( $_POST['search_data'] ) ) {
			parse_str($_POST['search_data'], $search_data);
		} elseif( isset( $_GET['orderby'] ) ) {
			$search_data = $_GET;
		} else {
			$search_data['orderby'] = $orderby;
		}
		
		// Exclude Membership
		$exclude_members = array();
		$exclude_membership = isset( $attr['exclude_membership'] ) ? sanitize_text_field( $attr['exclude_membership'] ) : '';
		if( $exclude_membership ) $exclude_membership = explode(",", $exclude_membership);
		if( !empty( $exclude_membership ) && is_array( $exclude_membership ) ) {
			foreach( $exclude_membership as $wcfm_membership ) {
				$membership_users = (array) get_post_meta( $wcfm_membership, 'membership_users', true );
				$exclude_members  = array_merge( $exclude_members, $membership_users );
			}
		}
		if( $exclude_members ) $exclude_members = implode(",", $exclude_members);
		else $exclude_members = '';
		
		// Excluded Stores from List
		$excludes = !empty( $attr['excludes'] ) ? sanitize_text_field( $attr['excludes'] ) : $exclude_members;
		$search_data['excludes'] = $excludes;
		
		// Include Store List
		$includes = !empty( $attr['includes'] ) ? sanitize_text_field( $attr['includes'] ) : '';
		if( $includes ) $includes = explode(",", $includes);
		else $includes = array();
		
		// Radious Search
		$radius = wc_string_to_bool( $attr['radius'] );
		$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
		if ( !$api_key ) $radius = false;
		if( $radius ) {
			$attr['country'] = 'no';
			$attr['state'] = 'no';
		}
			

		$stores = $WCFMmp->wcfmmp_vendor->wcfmmp_search_vendor_list( true, $offset, $length, $search_term, $search_category, $search_data, $has_product, $includes );

		$template_args = apply_filters( 'wcfmmp_stores_args', array(
				'stores'          => $stores,
				'limit'           => $length,
				'offset'          => $offset,
				'includes'        => $includes,
				'excludes'        => $excludes,
				'paged'           => $paged,
				'image_size'      => 'full',
				'has_product'     => $has_product,
				'has_orderby'     => $attr['has_orderby'],
				'orderby'         => $orderby,
				'sidebar'         => wc_string_to_bool( $attr['sidebar'] ),
				'filter'          => wc_string_to_bool( $attr['filter'] ),
				'search'          => wc_string_to_bool( $attr['search'] ),
				'category'        => wc_string_to_bool( $attr['category'] ),
				'country'         => wc_string_to_bool( $attr['country'] ),
				'state'           => wc_string_to_bool( $attr['state'] ),
				'radius'          => $radius,
				'map'             => wc_string_to_bool( $attr['map'] ),   
				'map_zoom'        => $attr['map_zoom'],
				'auto_zoom'       => $attr['auto_zoom'],
				'per_row'         => $attr['per_row'],
				'theme'           => $attr['theme'],
				'search_category' => $search_category
		) );
		
		ob_start();
		$WCFMmp->template->get_template( 'store-lists/wcfmmp-view-store-lists.php', $template_args );
		return ob_get_clean();
	}
	
		/**
	 * WCFM Stores Map Short Code
	 */
	public function wcfmmp_stores_map_shortcode( $atts ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query;
		$WCFM->nocache();
		
		$defaults = array(
				'filter'             => 'yes',
				'search'             => 'yes',
				'category'           => 'yes',
				'country'            => 'yes',
				'state'              => 'yes',
				'radius'             => 'no',
				'map'                => 'yes',
				'map_zoom'           => 5,
				'auto_zoom'          => 'yes',
				'limit'              => 10,
				'per_row'            => 2,
				'includes'           => '',
				'excludes'           => '',
				'exclude_membership' => '',
				'search_category'    => '',
				'has_product'        => '',
		);
		
		$attr   = shortcode_atts( apply_filters( 'wcfmmp_stores_map_default_args', $defaults ), $atts );

		$filter           = isset( $attr['filter'] ) ? wc_string_to_bool( $attr['filter'] ) : '';
		$search           = isset( $attr['search'] ) ? wc_string_to_bool( $attr['search'] ) : '';
		$category         = isset( $attr['category'] ) ? wc_string_to_bool( $attr['category'] ) : '';
		$country          = isset( $attr['country'] ) ? wc_string_to_bool( $attr['country'] ) : '';
		$state            = isset( $attr['state'] ) ? wc_string_to_bool( $attr['state'] ) : '';
		
		$has_product      = apply_filters( 'wcfmmp_stores_list_has_product', $attr['has_product'] );
		
		$search_query     = isset( $_GET['wcfmmp_store_search'] ) ? sanitize_text_field( $_GET['wcfmmp_store_search'] ) : '';
		$search_category  = isset( $_GET['wcfmmp_store_category'] ) ? sanitize_text_field( $_GET['wcfmmp_store_category'] ) : '';
		$search_country   = isset( $_GET['wcfmmp_store_country'] ) ? sanitize_text_field( $_GET['wcfmmp_store_country'] ) : '';
		$search_state     = isset( $_GET['wcfmmp_store_state'] ) ? sanitize_text_field( $_GET['wcfmmp_store_state'] ) : '';
		
		// Exclude Membership
		$exclude_members = array();
		$exclude_membership = isset( $attr['exclude_membership'] ) ? sanitize_text_field( $attr['exclude_membership'] ) : '';
		if( $exclude_membership ) $exclude_membership = explode(",", $exclude_membership);
		if( !empty( $exclude_membership ) && is_array( $exclude_membership ) ) {
			foreach( $exclude_membership as $wcfm_membership ) {
				$membership_users = (array) get_post_meta( $wcfm_membership, 'membership_users', true );
				$exclude_members  = array_merge( $exclude_members, $membership_users );
			}
		}
		if( $exclude_members ) $exclude_members = implode(",", $exclude_members);
		else $exclude_members = '';
		
		// Excluded Stores from List
		$excludes = !empty( $attr['excludes'] ) ? sanitize_text_field( $attr['excludes'] ) : $exclude_members;
		$search_data['excludes'] = $excludes;
		
		// Include Store List
		$includes = !empty( $attr['includes'] ) ? sanitize_text_field( $attr['includes'] ) : '';
		if( $includes ) $includes = explode(",", $includes);
		else $includes = array();
		
		$pagination_base = '';
		
		// Radious Search
		$radius = wc_string_to_bool( $attr['radius'] );
		$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
		if ( !$api_key ) $radius = false;
		if( $radius ) {
			$country = false;
			$state = false;
		}
			

		$template_args = apply_filters( 'wcfmmp_stores_map_args', array(
				'excludes'        => $excludes,
				'filter'          => $attr['filter'],
				'search'          => $search,
				'category'        => $category,
				'country'         => $country,
				'state'           => $state,
				'radius'          => $radius,
				'search_query'    => $search_query,
				'search_category' => $search_category,
				'search_country'  => $search_country,
				'search_state'    => $search_state,
				'pagination_base' => $pagination_base,
				'has_product'     => $has_product,
				'includes'        => $includes,
				'per_row'         => $attr['per_row'],
				'limit'           => $attr['limit'],
				'map'             => 'yes',
				'map_zoom'        => $attr['map_zoom'],
				'auto_zoom'       => $attr['auto_zoom'],
				'has_orderby'     => 'no',
				'sidebar'         => 'no',
				'theme'           => 'simple'
		) );
		
		ob_start();
		echo '<div id="wcfmmp-stores-map-wrapper">';
		$WCFMmp->template->get_template( 'store-lists/wcfmmp-view-store-lists-map.php', $template_args );
		if( $filter ) {
			if( $search || $radius || $category || $country || $state ) { $WCFMmp->template->get_template( 'store-lists/wcfmmp-view-store-lists-search-form.php', $template_args ); }
		}
		echo '</div>';
		echo '<div class="wcfm-clearfix"></div>';
		return ob_get_clean();
	}
	
	
	/**
	 * WCFM Stores Carousal Short Code
	 */
	public function wcfmmp_stores_carousel_shortcode( $atts ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $includes;
		$WCFM->nocache();
		
		$defaults = array(
				'per_page'           => 20,
				'category'           => 'yes',
				'country'            => 'yes',
				'state'              => 'yes',
				'per_row'            => 5,
				'includes'           => '',
				'excludes'           => '',
				'exclude_membership' => '',
				'search_category'    => '',
				'has_product'        => '',
				'theme'              => 'classic'
		);

		$attr   = shortcode_atts( apply_filters( 'wcfmmp_stores_carousel_default_args', $defaults ), $atts );
		$paged  = max( 1, get_query_var( 'paged' ) );
		$length = apply_filters( 'wcfmmp_stores_per_page', $attr['per_page'] );
		$offset = ( $paged - 1 ) * $length;
		
		$search_country = '';
		$search_state   = '';
		
		// GEO Locate Support
		if( apply_filters( 'wcfmmp_is_allow_store_list_by_user_location', true ) ) {
			if( is_user_logged_in() && !$search_country ) {
				$user_location = get_user_meta( get_current_user_id(), 'wcfm_user_location', true );
				if( $user_location ) {
					$search_country = $user_location['country'];
					$search_state   = $user_location['state'];
				}
			}
					
			if( apply_filters( 'wcfm_is_allow_wc_geolocate', true ) && class_exists( 'WC_Geolocation' ) && !$search_country ) {
				$user_location = WC_Geolocation::geolocate_ip();
				$search_country = $user_location['country'];
				$search_state   = $user_location['state'];
			}
		}

		$search_term      = isset( $_GET['wcfmmp_store_search'] ) ? sanitize_text_field( $_GET['wcfmmp_store_search'] ) : '';
		$search_category  = isset( $_GET['wcfmmp_store_category'] ) ? sanitize_text_field( $_GET['wcfmmp_store_category'] ) : $attr['search_category'];
		
		$search_category  = apply_filters( 'wcfmmp_stores_default_search_category', $search_category );
		$has_product      = apply_filters( 'wcfmmp_stores_list_has_product', $attr['has_product'] );
		
		$search_data     = array();
		if( isset( $_POST['search_data'] ) ) {
			parse_str($_POST['search_data'], $search_data);
		} elseif( isset( $_GET['orderby'] ) ) {
			$search_data = $_GET;
		}
		
		// Exclude Membership
		$exclude_members = array();
		$exclude_membership = isset( $attr['exclude_membership'] ) ? sanitize_text_field( $attr['exclude_membership'] ) : '';
		if( $exclude_membership ) $exclude_membership = explode(",", $exclude_membership);
		if( !empty( $exclude_membership ) && is_array( $exclude_membership ) ) {
			foreach( $exclude_membership as $wcfm_membership ) {
				$membership_users = (array) get_post_meta( $wcfm_membership, 'membership_users', true );
				$exclude_members  = array_merge( $exclude_members, $membership_users );
			}
		}
		if( $exclude_members ) $exclude_members = implode(",", $exclude_members);
		else $exclude_members = '';
		
		// Excluded Stores from List
		$excludes = !empty( $attr['excludes'] ) ? sanitize_text_field( $attr['excludes'] ) : $exclude_members;
		$search_data['excludes'] = $excludes;
		
		// Include Store List
		$includes = !empty( $attr['includes'] ) ? sanitize_text_field( $attr['includes'] ) : '';
		if( $includes ) $includes = explode(",", $includes);
		else $includes = array();
			

		$stores = $WCFMmp->wcfmmp_vendor->wcfmmp_search_vendor_list( true, $offset, $length, $search_term, $search_category, $search_data, $has_product, $includes );

		$template_args = apply_filters( 'wcfmmp_stores_args', array(
				'stores'          => $stores,
				'limit'           => $length,
				'offset'          => $offset,
				'includes'        => $includes,
				'excludes'        => $excludes,
				'has_product'     => $has_product,
				'category'        => wc_string_to_bool( $attr['category'] ),
				'country'         => wc_string_to_bool( $attr['country'] ),
				'state'           => wc_string_to_bool( $attr['state'] ),
				'per_row'         => $attr['per_row'],
				'theme'           => $attr['theme'],
				'search_category' => $search_category
		) );
		
		ob_start();
		$WCFMmp->template->get_template( 'store-lists/wcfmmp-view-store-lists-carousel.php', $template_args );
		return ob_get_clean();
	}
	
	/**
	 * WCFM Marketplace Store Info Shortcode
	 */
	public function wcfmmp_store_info_shortcode( $attr ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
		
		$store_id = '';
		if ( isset( $attr['id'] ) && !empty( $attr['id'] ) ) { $store_id = absint($attr['id']); }
		
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		}
		
		if( is_product() ) {
			$store_id = $post->post_author;
		}
		
		$data_info = '';
		if ( isset( $attr['data'] ) && !empty( $attr['data'] ) ) { $data_info = $attr['data']; }
		
		if( !$store_id ) return;
		if( !$data_info ) return;
		
		$is_store_offline = get_user_meta( $store_id, '_wcfm_store_offline', true );
		if ( $is_store_offline ) {
			return;
		}
		
		$store_user  = wcfmmp_get_store( $store_id );
		$store_info  = $store_user->get_shop_info();
		
		$content = '<div class="wcfmmp_store_info wcfmmp_store_info_' . $data_info . '">';
		
		switch( $data_info ) {
			case 'store_name':
				$content .= $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( $store_id );
				break;
				
			case 'store_url':
				$content .=  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $store_id );
				break;
				
			case 'store_address':
				$content .= $store_user->get_address_string();
				break;
				
		  case 'store_email':
				$content .= $store_user->get_email();
				break;
			
			case 'store_phone':
				$content .=  $store_user->get_phone();
				break;
				
			case 'store_gravatar':
				$content .=  '<img src="' . $store_user->get_avatar() . '" />';
				break;
				
			case 'store_banner':
				$content .=  '<img src="' . $store_user->get_banner() . '" />';
				break;
				
			case 'store_support':
				$content .=  $store_user->get_customer_support_details();
				break;
				
		  case 'store_social':
		  	ob_start();
		  	$WCFMmp->template->get_template( 'store/wcfmmp-view-store-social.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
				$content .=  ob_get_clean();
				if( !wcfmmp_is_store_page() && !is_product() ) {
					wp_enqueue_style( 'wcfmmp_product_css',  $WCFMmp->library->css_lib_url . 'store/wcfmmp-style-product.css', array(), $WCFMmp->version );
				}
				break;
				
			case 'store_location':
				$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
				$store_lat    = isset( $store_info['store_lat'] ) ? esc_attr( $store_info['store_lat'] ) : 0;
				$store_lng    = isset( $store_info['store_lng'] ) ? esc_attr( $store_info['store_lng'] ) : 0;
	
				if ( !empty( $api_key ) && !empty( $store_lat ) && !empty( $store_lng ) ) {
					ob_start();
					$WCFMmp->template->get_template( 'store/widgets/wcfmmp-view-store-location.php', array( 
																												 'store_user' => $store_user, 
																												 'store_info' => $store_info,
																												 'store_lat'  => $store_lat,
																												 'store_lng'  => $store_lng,
																												 'map_id'     => 'wcfm_sold_by_widget_map_'.rand(10,100)
																												 ) );
					$content .=  ob_get_clean();
					
					if( !wcfmmp_is_store_page() ) {
						wp_enqueue_style( 'wcfmmp_product_css',  $WCFMmp->library->css_lib_url . 'store/wcfmmp-style-product.css', array(), $WCFMmp->version );
						wp_enqueue_script( 'wcfmmp_store_js', $WCFMmp->library->js_lib_url . 'store/wcfmmp-script-store.js', array('jquery' ), $WCFMmp->version, true );
						$scheme  = is_ssl() ? 'https' : 'http';
						wp_enqueue_script( 'wcfm-store-google-maps', $scheme . '://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places' );
					}
				}
				break;
				
			case 'store_rating':
				ob_start();
				$store_user->show_star_rating();
				$content .=  ob_get_clean();
				break;
				
			case 'register_on':
				$data_value = get_user_meta( $store_id, 'wcfm_register_on', true );
				$content .=  date( wc_date_format(), $data_value );
				break;
				
			default:
				$data_value = get_user_meta( $store_id, $data_info, true );
				$content .=  apply_filters( 'wcfmmp_additional_store_info', $data_value, $data_info, $store_id );
			  break;
		}
		
		$content .= '</div>';
		
		return $content;
		
	}
	
	/**
	 * WCFM Marketplace Store Hours Shortcode
	 */
	public function wcfmmp_store_hours_shortcode( $attr ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
		
		if( !apply_filters( 'wcfm_is_pref_store_hours', true ) ) return;
		
		$store_id = '';
		if ( isset( $attr['id'] ) && !empty( $attr['id'] ) ) { $store_id = absint($attr['id']); }
		
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		}
		
		if( is_product() ) {
			$store_id = $post->post_author;
		}
		
		if( !$store_id ) return;
		
		$is_store_offline = get_user_meta( $store_id, '_wcfm_store_offline', true );
		if ( $is_store_offline ) {
			return;
		}
		
		if( !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'store_hours' ) ) return;
		
		$store_user  = wcfmmp_get_store( $store_id );
		
		$wcfm_vendor_store_hours = get_user_meta( $store_id, 'wcfm_vendor_store_hours', true );
		if( !$wcfm_vendor_store_hours ) {
			update_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', 'yes' );
			return;
		}
		
		$wcfm_store_hours_enable = isset( $wcfm_vendor_store_hours['enable'] ) ? 'yes' : 'no';
		if( $wcfm_store_hours_enable != 'yes' ) return;
		
		$wcfm_store_hours_off_days  = isset( $wcfm_vendor_store_hours['off_days'] ) ? $wcfm_vendor_store_hours['off_days'] : array();
		$wcfm_store_hours_day_times = isset( $wcfm_vendor_store_hours['day_times'] ) ? $wcfm_vendor_store_hours['day_times'] : array();
		if( empty( $wcfm_store_hours_day_times ) ) return;
		
		// Old Store Hours Migrating
		$wcfm_vendor_store_hours_migrated = get_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', true );
		if( !empty( array_filter( $wcfm_vendor_store_hours ) ) && !$wcfm_vendor_store_hours_migrated ) {
			$wcfm_store_hours_mon_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[0]['start'] ) ? $wcfm_store_hours_day_times[0]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[0]['end'] ) ? $wcfm_store_hours_day_times[0]['end'] : '' ) );
			$wcfm_store_hours_tue_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[1]['start'] ) ? $wcfm_store_hours_day_times[1]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[1]['end'] ) ? $wcfm_store_hours_day_times[1]['end'] : '' ) );
			$wcfm_store_hours_wed_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[2]['start'] ) ? $wcfm_store_hours_day_times[2]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[2]['end'] ) ? $wcfm_store_hours_day_times[2]['end'] : '' ) );
			$wcfm_store_hours_thu_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[3]['start'] ) ? $wcfm_store_hours_day_times[3]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[3]['end'] ) ? $wcfm_store_hours_day_times[3]['end'] : '' ) );
			$wcfm_store_hours_fri_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[4]['start'] ) ? $wcfm_store_hours_day_times[4]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[4]['end'] ) ? $wcfm_store_hours_day_times[4]['end'] : '' ) );
			$wcfm_store_hours_sat_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[5]['start'] ) ? $wcfm_store_hours_day_times[5]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[5]['end'] ) ? $wcfm_store_hours_day_times[5]['end'] : '' ) );
			$wcfm_store_hours_sun_times = array( 0 => array( 'start' => isset( $wcfm_store_hours_day_times[6]['start'] ) ? $wcfm_store_hours_day_times[6]['start'] : '', 'end' => isset( $wcfm_store_hours_day_times[6]['end'] ) ? $wcfm_store_hours_day_times[6]['end'] : '' ) );
			
			$wcfm_store_hours_day_times = array( 0 => $wcfm_store_hours_mon_times,
																					 1 => $wcfm_store_hours_tue_times,
																					 2 => $wcfm_store_hours_wed_times,
																					 3 => $wcfm_store_hours_thu_times,
																					 4 => $wcfm_store_hours_fri_times,
																					 5 => $wcfm_store_hours_sat_times,
																					 6 => $wcfm_store_hours_sun_times
																					);
			
			$wcfm_vendor_store_hours['day_times'] = $wcfm_store_hours_day_times;
			update_user_meta( $store_id, 'wcfm_vendor_store_hours', $wcfm_vendor_store_hours );
			update_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', 'yes' );
		} else {
			update_user_meta( $store_id, 'wcfm_vendor_store_hours_migrated', 'yes' );
		}
			
		
		$weekdays = array( 0 => __( 'Monday', 'wc-multivendor-marketplace' ), 1 => __( 'Tuesday', 'wc-multivendor-marketplace' ), 2 => __( 'Wednesday', 'wc-multivendor-marketplace' ), 3 => __( 'Thursday', 'wc-multivendor-marketplace' ), 4 => __( 'Friday', 'wc-multivendor-marketplace' ), 5 => __( 'Saturday', 'wc-multivendor-marketplace' ), 6 => __( 'Sunday', 'wc-multivendor-marketplace') );
		
		$content = '<div class="wcfmmp_store_hours">';
		$content .= '<span class="wcfmmp-store-hours widget-title"><span class="wcfmfa fa-clock"></span>&nbsp;' . apply_filters( 'wcfm_store_hours_label', __( 'Store Hours', 'wc-multivendor-marketplace' ) ) . '</span><div class="wcfm_clearfix"></div>';
		
		ob_start();
		$WCFMmp->template->get_template( 'store/widgets/wcfmmp-view-store-hours.php', array( 
			                                             'wcfm_store_hours_day_times' => $wcfm_store_hours_day_times, 
			                                             'wcfm_store_hours_off_days'  => $wcfm_store_hours_off_days,
			                                             'weekdays' => $weekdays,
			                                             'store_id' => $store_id,
			                                             ) );
		$content .= ob_get_clean();
		
		$content .= '</div>';
		
		return $content;
	}
	
	/**
	 * WCFM Marketplace Store Hours Shortcode
	 */
	public function wcfmmp_more_offers_shortcode( $attr ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
		
		if( !apply_filters( 'wcfm_is_pref_product_multivendor', true ) ) return;
		
		if( !is_product() ) return;
		
		//$content = '<div class="wcfmmp_more_offers">';
		//$content .= '<span class="wcfmmp-more-offers widget-title"><span class="wcfmfa fa-clock"></span>&nbsp;' . apply_filters( 'wcfm_store_hours_label', __( 'Store Hours', 'wc-multivendor-marketplace' ) ) . '</span><div class="wcfm_clearfix"></div>';
		
		ob_start();
		$WCFMmp->template->get_template( 'product_multivendor/wcfmmp-view-more-offers.php' );
		$content .= ob_get_clean();
		
		//$content .= '</div>';
		
		return $content;
	}
	
	/**
	 * WCFM Marketplace Store FB Widget Shortcode
	 */
	public function wcfmmp_store_fb_widget_shortcode( $attr ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
		
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		}
		
		if( is_product() ) {
			$store_id = $post->post_author;
		}
		
		if( !$store_id ) return;
		
		$store_user   = wcfmmp_get_store( $store_id );
		$store_info   = $store_user->get_shop_info();
		
		if( !isset( $store_info['social']['fb'] ) || empty( $store_info['social']['fb'] ) ) return;
		
		$facebook_url = wcfmmp_generate_social_url( $store_info['social']['fb'], 'facebook' );
		
		if( !$facebook_url ) return;
		
		$custom_css = apply_filters( 'wcfmmp_fb_widget_custom_style', '' );
		
		$content = '';
		
		ob_start();
		
		echo '<div class="wcfm_store_fb_widget" style="margin-bottom:50px;">';
		
		echo '<div id="fb-root"></div>';
    echo '<div class="fb-page" data-href="' . $facebook_url . '" 
                               data-width="400" 
                               data-height="500" 
                               data-small-header="true" 
                               data-adapt-container-width="true" 
                               data-hide-cover="false" 
                               data-show-facepile="false" 
                               data-show-posts="true" 
                               style="' . $custom_css . '"></div>';
		
		echo '</div>';
		
		$content .= ob_get_clean();
		
		wp_register_script( 'wcfm_store_fb_widget_script', $WCFMmp->library->js_lib_url_min . 'store/wcfmmp-script-fb-widget.js', array('jquery') );
    wp_enqueue_script( 'wcfm_store_fb_widget_script' );
    $local_variables = array( 'app_id' => '503595753002055', 'select_lng' => 'en_US' );
    wp_localize_script( 'wcfm_store_fb_widget_script', 'wcfm_store_fb_widget_vars', $local_variables );
		
		return $content;
	}
	
	/**
	 * WCFM Marketplace Store Twitter Widget Shortcode
	 */
	public function wcfmmp_store_twitter_widget_shortcode( $attr ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
		
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		}
		
		if( is_product() ) {
			$store_id = $post->post_author;
		}
		
		if( !$store_id ) return;
		
		$store_user   = wcfmmp_get_store( $store_id );
		$store_info   = $store_user->get_shop_info();
		
		if( !isset( $store_info['social']['twitter'] ) || empty( $store_info['social']['twitter'] ) ) return;
		
		$twitter_url = wcfmmp_generate_social_url( $store_info['social']['twitter'], 'twitter' );
		
		if( !$twitter_url ) return;
		
		$custom_css = apply_filters( 'wcfmmp_twitter_widget_custom_style', '' );
		
		$oembed_params = array( 'omit_script' => 1, 'lang' => 'en', 'maxwidth' => 300, 'maxheight' => 400, 'chrome' => '', 'url' => $twitter_url );
		
		$QUEUE_HANDLE = 'twitter-wjs';
		$URI = 'https://platform.twitter.com/widgets.js';
		$script = 'window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src=' . wp_json_encode( $URI ) . ';fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script",' . wp_json_encode( $QUEUE_HANDLE ) . '));';
		
		$content = '';
		
		ob_start();
		
		echo '<div class="wcfm_store_twitter_widget" style="margin-bottom:50px;">';
		
    echo '<div class=" wcfmmp_twitter twitter-timeline-profile">
          <a class="twitter-timeline" data-lang="en" 
                                      data-width="100%" 
                                      data-height="400" 
                                      href="' . $twitter_url . '?ref_src=twsrc%5Etfw">Recent Tweets</a></div>';
                                      
		echo '<script>' . $script . '</script>';
		
		echo '</div>';
		
		$content .= ob_get_clean();
		
		return $content;
	}
	
	/**
	 * WCFM Marketplace Store Instagram Widget Shortcode
	 */
	public function wcfmmp_store_instagram_widget_shortcode( $attr ) {
		global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
		
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		}
		
		if( is_product() ) {
			$store_id = $post->post_author;
		}
		
		if( !$store_id ) return;
		
		$store_user   = wcfmmp_get_store( $store_id );
		$store_info   = $store_user->get_shop_info();
		
		if( !isset( $store_info['social']['instagram'] ) || empty( $store_info['social']['instagram'] ) ) return;
		
		$instagram_url = wcfmmp_generate_social_url( $store_info['social']['instagram'], 'instagram' );
		
		if( !$instagram_url ) return;
		
		ob_start();
		
		echo '<div class="wcfm_store_instagram_widget" style="margin-bottom:50px;">';
		
    
		
		echo '</div>';
		
		$content .= ob_get_clean();
		
		return $content;
	}
	
	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	public function load_class($class_name = '') {
		global $WCFM;
		if ('' != $class_name && '' != $WCFM->token) {
			require_once ( $WCFM->plugin_path . 'includes/shortcodes/class-' . esc_attr($WCFM->token) . '-shortcode-' . esc_attr($class_name) . '.php' );
		}
	}

}
?>