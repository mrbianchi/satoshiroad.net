<?php
/**
 * WCFM plugin core
 *
 * Third Party Plugin Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.2.2
 */
 
class WCFM_ThirdParty_Support {

	public function __construct() {
		global $WCFM;
		
    // WCFM Query Var Filter
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_thirdparty_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_thirdparty_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_thirdparty_init' ), 20 );
		
		// WCFM Third Party Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( $this, 'wcfm_thirdparty_endpoints_slug' ) );
    
    // WCFM Menu Filter
    add_filter( 'wcfm_menus', array( &$this, 'wcfm_thirdparty_menus' ), 100 );
    
    // WCFM Thirdparty Product Type
		add_filter( 'wcfm_product_types', array( &$this, 'wcfm_thirdparty_product_types' ), 50 );
    
    // Third Party Product Type Capability
		add_filter( 'wcfm_capability_settings_fields_product_types', array( &$this, 'wcfmcap_product_types' ), 50, 3 );
		
		if( apply_filters( 'wcfm_is_allow_listings', true ) && apply_filters( 'wcfm_is_allow_products_for_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				if( wcfm_is_allow_wcfm() && apply_filters( 'wcfm_is_allow_manage_listings_wcfm_wrapper', true ) ) {
					if( !WCFM_Dependencies::wcfm_products_mylistings_active_check() ) {
						add_filter( 'the_content', array( &$this, 'wcfm_add_listing_page' ), 20 );
						add_filter( 'wcfm_current_endpoint', array( $this, 'wcfm_add_listing_endpoint' ) );
						add_action( 'wp_enqueue_scripts', array( $this, 'wcfm_add_listing_enqueue_scripts' ) );
					}
				}
			}
		}
    
    // WC Paid Listing Support - 2.3.4
    if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
				// WC Paid Listing Product options
				add_filter( 'wcfm_product_manage_fields_pricing', array( &$this, 'wcfm_wcpl_product_manage_fields_pricing' ), 50, 2 );
			}
		}
		
		// WC Rental & Booking Support - 2.3.8
    if( $wcfm_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
				// WC Rental Product options
				add_filter( 'after_wcfm_products_manage_general', array( &$this, 'wcfm_wcrental_product_manage_fields' ), 80, 2 );
			}
		}
		
		// YITH AuctionsFree Support - 3.0.4
    if( $wcfm_allow_auction = apply_filters( 'wcfm_is_allow_auction', true ) ) {
			if( WCFM_Dependencies::wcfm_yith_auction_free_active_check() ) {
				// YITH Auction Product options
				add_filter( 'after_wcfm_products_manage_general', array( &$this, 'wcfm_yithauction_free_product_manage_fields' ), 70, 2 );
			}
		}
		
		// Geo my WP Support - 3.2.4
    if( $wcfm_allow_geo_my_wp = apply_filters( 'wcfm_is_allow_geo_my_wp', true ) ) {
			if( WCFM_Dependencies::wcfm_geo_my_wp_plugin_active_check() ) {
				// GEO my WP Product Location options
				add_action( 'end_wcfm_products_manage', array( &$this, 'wcfm_geomywp_products_manage_views' ), 100 );
			}
		}
		
		// WooCommerce Product Voucher Support - 3.4.7
		if( apply_filters( 'wcfm_is_allow_wc_product_voucher', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_product_voucher_plugin_active_check() ) {
				add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcfm_wc_product_voucher_product_manage_fields_general' ), 30, 3 );
				add_filter( 'wcfm_product_manage_fields_pricing', array( &$this, 'wcfm_wc_product_voucher_product_manage_fields_pricing' ), 50, 3 );
			}
		}
		
		// Woocommerce Germanized Support - 3.3.2
    if( apply_filters( 'wcfm_is_allow_woocommerce_germanized', true ) ) {
			if( WCFM_Dependencies::wcfm_woocommerce_germanized_plugin_active_check() ) {
				// Woocommerce Germanized Product Pricing & Shipping options
				add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcfm_woocommerce_germanized_product_manage_fields_general' ), 40, 3 );
				add_filter( 'wcfm_product_manage_fields_content', array( &$this, 'wcfm_woocommerce_germanized_product_manage_fields_content' ), 50, 3 );
				add_filter( 'wcfm_product_manage_fields_pricing', array( &$this, 'wcfm_woocommerce_germanized_product_manage_fields_pricing' ), 50, 3 );
				add_filter( 'wcfm_product_manage_fields_shipping', array( &$this, 'wcfm_woocommerce_germanized_product_manage_fields_shipping' ), 50, 2 );
				
				// Woocommerce Germanized Variations Pricing & Shipping options
				add_filter( 'wcfm_product_manage_fields_variations', array( &$this, 'wcfm_woocommerce_germanized_product_manage_fields_variations' ), 100, 4 );
				add_filter( 'wcfm_variation_edit_data', array( &$this, 'wcfm_woocommerce_germanized_product_data_variations' ), 100, 3 );
			}
		}
		
		// WC Epeken Support - 4.1.0
    if( apply_filters( 'wcfm_is_allow_epeken', true ) ) {
			if( WCFM_Dependencies::wcfm_epeken_plugin_active_check() ) {
				// WC Epeken Product options
				add_filter( 'end_wcfm_products_manage', array( &$this, 'wcfm_wcepeken_product_manage_views' ), 150 );
				
				// WC Epeken User Settings
				add_action( 'end_wcfm_vendor_settings', array( &$this, 'wcfm_wcepeken_settings_views' ), 150 );
				add_action( 'wcfm_vendor_settings_update', array( &$this, 'wcfm_wcepeken_vendor_settings_update' ), 150, 2 );
			}
		}
		
		// WooCommerce Schedular - 5.0.7
    if( apply_filters( 'wcfm_is_allow_wdm_scheduler', true ) ) {
			if( WCFM_Dependencies::wcfm_wdm_scheduler_active_check() ) {
				//add_filter( 'end_wcfm_products_manage', array( &$this, 'wcfm_wdm_scheduler_product_manage_views' ), 160 );
			}
		}
		
		// WooCommerce Product Schedular - 6.1.4
    if( apply_filters( 'wcfm_is_allow_wc_product_scheduler', true ) ) {
			//if( WCFM_Dependencies::wcfm_wc_product_scheduler_active_check() ) {
				//add_filter( 'end_wcfm_products_manage', array( &$this, 'wcfm_wc_product_scheduler_product_manage_views' ), 160 );
			//}
		}
		
		// Product Manage Third Party Plugins View
    add_action( 'end_wcfm_products_manage', array( &$this, 'wcfm_thirdparty_products_manage_views' ), 100 );
	}
	
	
	/**
   * WCFM Third Party Query Var
   */
  function wcfm_thirdparty_query_vars( $query_vars ) {
  	
  	// WP Job Manager Support
  	if( $wcfm_allow_listings = apply_filters( 'wcfm_is_allow_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
				$query_listing_vars = array(
					'wcfm-listings'       => ! empty( $wcfm_modified_endpoints['wcfm-listings'] ) ? $wcfm_modified_endpoints['wcfm-listings'] : 'listings',
				);
				     
				// WP Job Manager Applications Support
				if ( WCFM_Dependencies::wcfm_wp_job_manager_applications_plugin_active_check() ) {
					//$query_listing_vars['wcfm-applications'] = ! empty( $wcfm_modified_endpoints['wcfm-applications'] ) ? $wcfm_modified_endpoints['wcfm-applications'] : 'applications';
				}
		
				$query_vars = array_merge( $query_vars, $query_listing_vars );
			} else {
				if( get_option( 'wcfm_updated_end_point_wc_listings' ) ) {
					delete_option( 'wcfm_updated_end_point_wc_listings' );
				}
			}
		}
		
		return $query_vars;
  }
  
  /**
   * WCFM Third Party End Point Title
   */
  function wcfm_thirdparty_endpoint_title( $title, $endpoint ) {
  	
  	switch ( $endpoint ) {
  		case 'wcfm-listings' :
				$title = __( 'Listings Dashboard', 'wc-frontend-manager' );
			break;
			
			case 'wcfm-applications' :
				$title = __( 'Applications Dashboard', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WCFM Third Party Endpoint Intialize
   */
  function wcfm_thirdparty_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_listings' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_listings', 1 );
		}
  }
  
  /**
	 * Thirdparty Endpoiint Edit
	 */
	function wcfm_thirdparty_endpoints_slug( $endpoints ) {
		
		// Listings
		if( $wcfm_allow_listings = apply_filters( 'wcfm_is_allow_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$listings_endpoints = array(
															'wcfm-listings'  		   => 'listings',
															);
				if ( WCFM_Dependencies::wcfm_wp_job_manager_applications_plugin_active_check() ) {
					//$listings_endpoints['wcfm-applications'] = 'applications';
				}
				$endpoints = array_merge( $endpoints, $listings_endpoints );
			}
		}
		
		return $endpoints;
	}
	
	/**
	 * WCFM Third Party Plugins Menus
	 */
	function wcfm_thirdparty_menus( $menus ) {
  	global $WCFM;
  	
  	// WP Job Manager Support
  	if( $wcfm_allow_listings = apply_filters( 'wcfm_is_allow_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$jobs_dashboard = get_permalink( get_option( 'job_manager_job_dashboard_page_id' ) );
				$post_a_job = get_permalink ( get_option( 'job_manager_submit_job_form_page_id' ) );
				if( $jobs_dashboard && $post_a_job ) {
					$menus = array_slice($menus, 0, 3, true) +
															array( 'wcfm-listings' => array(  'label'       => __( 'Listings', 'wc-frontend-manager' ),
																																 'url'        => get_wcfm_listings_url(),
																																 'icon'       => 'briefcase',
																																 'priority'   => 10
																																) )	 +
																array_slice($menus, 3, count($menus) - 3, true) ;
						
				  // WP Job Manager Applications Support
					if ( WCFM_Dependencies::wcfm_wp_job_manager_applications_plugin_active_check() ) {
						/*$menus = array_slice($menus, 0, 4, true) +
															array( 'wcfm-applications' => array(  'label'     => __( 'Applications', 'wc-frontend-manager' ),
																																	 'url'        => get_wcfm_applications_url(),
																																	 'icon'       => 'user-tie',
																																	 'priority'   => 11
																																	) )	 +
																array_slice($menus, 4, count($menus) - 4, true) ;*/
					}
				}
			}
		}
		
  	return $menus;
  }
  
  /**
   * WCFM Third Party Product Type
   */
  function wcfm_thirdparty_product_types( $pro_types ) {
  	global $WCFM;
  	
  	// WC Paid Listing Support - 2.3.4
    if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
				$pro_types['job_package'] = __( 'Listing Package', 'wp-job-manager-wc-paid-listings' );
			}
		}
		
		// WC Rental & Booking Support - 2.3.8
    if( $wcfm_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
				$pro_types['redq_rental'] = __( 'Rental Product', 'wc-frontend-manager' );
			}
		}
		
		// YiTH Auctions Free - 3.0.4
  	if( $wcfm_allow_auction = apply_filters( 'wcfm_is_allow_auction', true ) ) {
			if( WCFM_Dependencies::wcfm_yith_auction_free_active_check() ) {
				$pro_types['auction'] = __( 'Auction', 'wc-frontend-manager' );
			}
		}
  	
  	return $pro_types;
  }
  
  /**
	 * WCFM Capability Product Types
	 */
	function wcfmcap_product_types( $product_types, $handler = 'wcfm_capability_options', $wcfm_capability_options = array() ) {
		global $WCFM, $WCFMu;
		
		if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
			$job_package = ( isset( $wcfm_capability_options['job_package'] ) ) ? $wcfm_capability_options['job_package'] : 'no';
		
			$product_types["job_package"] = array('label' => __('Listing Package', 'wc-frontend-manager') , 'name' => $handler . '[job_package]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $job_package);
		}
		
		if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
			$rental = ( isset( $wcfm_capability_options['rental'] ) ) ? $wcfm_capability_options['rental'] : 'no';
			
			$product_types["rental"] = array('label' => __('Rental', 'wc-frontend-manager') , 'name' => $handler . '[rental]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $rental);
		}
		
		if( WCFM_Dependencies::wcfm_yith_auction_free_active_check() ) {
			$auction = ( isset( $wcfm_capability_options['auction'] ) ) ? $wcfm_capability_options['auction'] : 'no';
		
			$product_types["auction"] = array('label' => __('Auction', 'wc-frontend-manager') , 'name' => $handler . '[auction]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $auction);
		}
		
		return $product_types;
	}
	
	function wcfm_add_listing_page( $content ) {
		global $post, $WCFM;
		
		$job_dashboard_page = get_option( 'job_manager_job_dashboard_page_id' );
		$add_listings_page = get_option( 'job_manager_submit_job_form_page_id' );
		$post_a_job = get_permalink ( $add_listings_page );
		if( ( $add_listings_page && is_object( $post ) && ( $add_listings_page == $post->ID ) ) || ( $job_dashboard_page && is_object( $post ) && ( $job_dashboard_page == $post->ID ) && isset( $_GET['action'] ) && ( in_array( $_GET['action'], array( 'edit', 'mark_filled', 'mark_not_filled' ) ) ) ) ) {
			ob_start();
			echo '<div id="wcfm-main-contentainer">';
			echo  '<div id="wcfm-content">';
			$WCFM->template->get_template( 'wcfm-view-menu.php' );
			echo '<div class="collapse wcfm-collapse" id="wcfm_listing_job_submit">';
			echo '<div class="wcfm-page-headig">';
			echo '<span class="wcfmfa fa-chalkboard"></span>';
			echo '<span class="wcfm-page-heading-text">' . __( 'Manage Listings', 'wc-frontend-manager' ) . '</span>';
			$WCFM->template->get_template( 'wcfm-view-header-panels.php' );
			echo '</div>';
			echo '<div class="wcfm-collapse-content">';
			echo '<div class="wcfm-container wcfm-top-element-container">';
			if( isset( $_GET['action'] ) && ( $_GET['action'] == 'edit' ) ) {
				echo '<h2>' . __( 'Edit Listing', 'wc-frontend-manager' ) . '</h2>';
			} else if( isset( $_GET['action'] ) && ( in_array( $_GET['action'], array( 'mark_filled', 'mark_not_filled' ) ) ) ) {
				echo '<h2>' . __( 'Manage Listing', 'wc-frontend-manager' ) . '</h2>';
			} else {
				echo '<h2>' . __( 'Add Listing', 'wc-frontend-manager' ) . '</h2>';
			}
			if( apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				echo '<a target="_blank" class="wcfm_wp_admin_view text_tip" href="' . admin_url('edit.php?post_type=job_listing') . '" data-tip="' . __( 'WP Admin View', 'wc-frontend-manager' ) . '"><span class="fab fa-wordpress"></span></a>';
			}
			if( isset( $_GET['action'] ) && ( $_GET['action'] == 'edit' ) ) {
				echo '<a id="add_new_listing_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_listings_url().'" data-tip="' . __('Manage Listings', 'wc-frontend-manager') . '"><span class="wcfmfa fa-briefcase"></span></a>';
				echo '<a id="add_new_listing_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.$post_a_job.'" data-tip="' . __('Add New Listing', 'wc-frontend-manager') . '"><span class="wcfmfa fa-briefcase"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			} else {
				echo '<a id="add_new_listing_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_listings_url().'" data-tip="' . __('Manage Listings', 'wc-frontend-manager') . '"><span class="wcfmfa fa-briefcase"></span><span class="text">' . __( 'Listings', 'wc-frontend-manager') . '</span></a>';
			}
			echo '<div class="wcfm-clearfix"></div></div><div class="wcfm-clearfix"></div></br>';
			echo '<div class="wcfm-container wcfm_listing_job_fields_container">';
			$content = ob_get_clean() . $content . '</div></div></div></div></div>';
		}
		
		return $content;
	}
	
	/**
	 * Add/Edit listing WCfM End point set
	 */
	function wcfm_add_listing_endpoint( $current_endpoint ) {
		global $WCFM, $WCFMu, $post, $_GET;
		
		$job_dashboard_page = get_option( 'job_manager_job_dashboard_page_id' );
		$add_listings_page = get_option( 'job_manager_submit_job_form_page_id' );
		if( ( $add_listings_page && is_object( $post ) && ( $add_listings_page == $post->ID ) ) || ( $job_dashboard_page && is_object( $post ) && ( $job_dashboard_page == $post->ID ) && isset( $_GET['action'] ) && ( in_array( $_GET['action'], array( 'edit', 'mark_filled', 'mark_not_filled' ) ) ) ) ) {
			$current_endpoint = 'wcfm-listings-manage';
		}
		return $current_endpoint;
	}
	
	/**
	 * Listings - WCfM Dashboard wrapper Script - 4.2.3
	 */
	function wcfm_add_listing_enqueue_scripts() {
		global $WCFM, $post, $_GET;
		
		$job_dashboard_page = get_option( 'job_manager_job_dashboard_page_id' );
		$add_listings_page = get_option( 'job_manager_submit_job_form_page_id' );
		if( ( $add_listings_page && is_object( $post ) && ( $add_listings_page == $post->ID ) ) || ( $job_dashboard_page && is_object( $post ) && ( $job_dashboard_page == $post->ID ) && isset( $_GET['action'] ) && ( in_array( $_GET['action'], array( 'edit', 'mark_filled', 'mark_not_filled' ) ) ) ) ) {
			
			if( !WCFM_Dependencies::wcfm_products_listings_active_check() && !WCFM_Dependencies::wcfm_products_mylistings_active_check() ) {
				$WCFM->library->load_scripts( 'wcfm-profile' );
				
				// Load Styles
				$WCFM->library->load_styles( 'wcfm-profile' );
				wp_enqueue_style( 'wcfm_add_listings_css', $WCFM->library->css_lib_url . 'listings/wcfm-style-listings-manage.css', array(), $WCFM->version );
			}
		}
	}
	
  /**
	 * WC Paid Listing Product General options
	 */
	function wcfm_wcpl_product_manage_fields_pricing( $general_fields, $product_id ) {
		global $WCFM;
		
		$_job_listing_package_subscription_type        = '';
		$_job_listing_limit     = '';
		$_job_listing_duration       = '';
		$_job_listing_featured = 'no';
		
		if( $product_id ) {
			$_job_listing_package_subscription_type        = get_post_meta( $product_id, '_job_listing_package_subscription_type', true );
			$_job_listing_limit     = get_post_meta( $product_id, '_job_listing_limit', true );
			$_job_listing_duration       = get_post_meta( $product_id, '_job_listing_duration', true );
			$_job_listing_featured = get_post_meta( $product_id, '_job_listing_featured', true );
		}
		
		$pos_counter = 4;
		if( WCFM_Dependencies::wcfmu_plugin_active_check() ) $pos_counter = 6;
		
		$job_listing_package_fields = array( 
																				"_job_listing_package_subscription_type" => array( 'label' => __('Subscription Type', 'wp-job-manager-wc-paid-listings' ), 'type' => 'select', 'options' => array( 'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-job-manager-wc-paid-listings' ), 'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-job-manager-wc-paid-listings' ) ), 'class' => 'wcfm-select wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'hints' => __( 'Choose how subscriptions affect this package', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_package_subscription_type ),
																				"_job_listing_limit" => array( 'label' => __('Job listing limit', 'wp-job-manager-wc-paid-listings' ), 'placeholder' => __( 'Unlimited', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'attributes' => array( 'min'   => '', 'step' 	=> '1' ), 'hints' => __( 'The number of job listings a user can post with this package.', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_limit ),
																				"_job_listing_duration" => array( 'label' => __('Job listing duration', 'wp-job-manager-wc-paid-listings' ), 'placeholder' => 0, 'type' => 'number', 'class' => 'wcfm-text wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'attributes' => array( 'min'   => '', 'step' 	=> '1' ), 'hints' => __( 'The number of days that the job listing will be active.', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_duration ),
																				"_job_listing_featured" => array( 'label' => __('Feature Listings?', 'wp-job-manager-wc-paid-listings' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title checkbox_title wcfm_ele job_package', 'hints' => __( 'Feature this job listing - it will be styled differently and sticky.', 'wp-job-manager-wc-paid-listings' ), 'value' => 'yes', 'dfvalue' => $_job_listing_featured ),
																				);
		$general_fields = array_merge( $general_fields, $job_listing_package_fields );
		
		return $general_fields;
	}
	
  /**
	 * WC Rental Product General options
	 */
	function wcfm_wcrental_product_manage_fields( $product_id = 0, $product_type ) {
		global $WCFM;
		
		$pricing_type = '';
		$hourly_price = '';
		$general_price = '';
		
		$redq_rental_availability = array();
		
		if( $product_id ) {
			$pricing_type = get_post_meta( $product_id, 'pricing_type', true );
			$hourly_price = get_post_meta( $product_id, 'hourly_price', true );
			$general_price = get_post_meta( $product_id, 'general_price', true );
			
			$redq_rental_availability = (array) get_post_meta( $product_id, 'redq_rental_availability', true );
		}
		
		
		?>
		
		<div class="page_collapsible products_manage_redq_rental redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_head"><label class="wcfmfa fa-cab"></label><?php _e('Rental', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container redq_rental non-variable-subscription">
			<div id="wcfm_products_manage_form_redq_rental_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields', array( 
					"pricing_type" => array( 'label' => __('Set Price Type', 'wc-frontend-manager') , 'type' => 'select', 'options' => apply_filters( 'wcfm_redq_rental_pricing_options', array( 'general_pricing' => __( 'General Pricing', 'wc-frontend-manager' ) ) ), 'class' => 'wcfm-select wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $pricing_type, 'hints' => __( 'Choose a price type - this controls the schema.', 'wc-frontend-manager' ) ),
					"hourly_price" => array( 'label' => __('Hourly Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $hourly_price, 'hints' => __( 'Hourly price will be applicabe if booking or rental days min 1day', 'wc-frontend-manager' ), 'placeholder' => __( 'Enter price here', 'wc-frontend-manager' ) ),
					"general_price" => array( 'label' => __('General Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele rentel_pricing rental_general_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_general_pricing redq_rental', 'value' => $general_price, 'placeholder' => __( 'Enter price here', 'wc-frontend-manager' ) ),
					) ) );
				?>
			</div>
		</div>
		
		<div class="page_collapsible products_manage_redq_rental_availabillity redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_availabillity_head"><label class="wcfmfa fa-clock"></label><?php _e('Availability', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container redq_rental non-variable-subscription">
			<div id="wcfm_products_manage_form_redq_rental_availabillity_expander" class="wcfm-content">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
				"redq_rental_availability" =>   array('label' => __('Product Availabilities', 'wc-frontend-manager') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'desc' => __( 'Please select the date range to be disabled for the product.', 'wc-frontend-manager' ), 'desc_class' => 'avail_rules_desc', 'value' => $redq_rental_availability, 'options' => array(
											"type" => array('label' => __('Type', 'wc-frontend-manager'), 'type' => 'select', 'options' => array( 'custom_date' => __( 'Custom Date', 'wc-frontend-manager' )), 'class' => 'wcfm-select wcfm_ele avail_range_type redq_rental', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label redq_rental' ),
											"from" => array('label' => __('From', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
											"to" => array('label' => __('To', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
											"rentable" => array('label' => __('Bookable', 'wc-frontend-manager'), 'type' => 'select', 'options' => array( 'no' => __('NO', 'wc-frontend-manager') ), 'class' => 'wcfm-select wcfm_ele avail_rules_ele avail_rules_text redq_rental', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label' ),
											)	)
				) );
			?>
		</div>
	</div>
	<?php	
	}
	
	/**
	 * YITH Auction Free Product General options
	 * @since 3.0.4
	 */
	function wcfm_yithauction_free_product_manage_fields( $product_id = 0, $product_type ) {
		global $WCFM;
		
		$_yith_auction_for = '';
		$_yith_auction_to = '';
		
		if( $product_id ) {
			$_yith_auction_for = get_post_meta( $product_id, '_yith_auction_for', true );
			$_yith_auction_to = get_post_meta( $product_id, '_yith_auction_to', true );
			
			if( $_yith_auction_for ) $_yith_auction_for = get_date_from_gmt( date( 'Y-m-d H:i:s', absint( $_yith_auction_for ) ) );
			if( $_yith_auction_to ) $_yith_auction_to = get_date_from_gmt( date( 'Y-m-d H:i:s', absint( $_yith_auction_to ) ) );
		}
		
		?>
		<div class="page_collapsible products_manage_yithauction_free auction non-variable-subscription" id="wcfm_products_manage_form_auction_head"><label class="wcfmfa fa-gavel"></label><?php _e('Auction', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container auction non-variable-subscription">
			<div id="wcfm_products_manage_form_yithauction_free_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
					"_yith_auction_for" => array( 'label' => __('Auction Date From', 'wc-frontend-manager') , 'type' => 'text', 'placeholder' => 'YYYY-MM-DD hh:mm:ss', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_for, 'attributes' => array( "pattern" => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" ) ),
					"_yith_auction_to" => array( 'label' => __('Auction Date To', 'wc-frontend-manager') , 'type' => 'text', 'placeholder' => 'YYYY-MM-DD hh:mm:ss', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_to, 'attributes' => array( "pattern" => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" ) ),
					) );
				?>
			</div>
		</div>
		<?php
	}
	
	/**
   * Product Manage GEO my WP views
   */
	function wcfm_geomywp_products_manage_views( ) {
		global $WCFM;
	  
	  $WCFM->template->get_template( 'products-manager/wcfm-view-geomywp-products-manage.php' );
	}
	
	/**
   * Product Manage Woocommerce Product Voucher General Fields - General
   */
	function wcfm_wc_product_voucher_product_manage_fields_general( $general_fields, $product_id, $product_type ) {
		global $WCFM;
		
		$_has_voucher = '';
		
		// Voucher options
		if( $product_id ) {
			$_has_voucher = ( get_post_meta( $product_id, '_has_voucher', true ) == 'yes' ) ? 'yes' : '';
		}
		
		$general_fields = array_slice($general_fields, 0, 1, true) + 
													array(
														"_has_voucher" => array( 'desc' => __( 'Has Voucher', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele wcfm_half_ele_checkbox simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'desc_class' => 'wcfm_title wcfm_ele downloadable_ele_title checkbox_title simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => 'yes', 'dfvalue' => $_has_voucher),
														) +
											array_slice($general_fields, 1, count($general_fields) - 1, true) ;
		
		return $general_fields;
	}
	
	/**
   * Product Manage Woocommerce Product Voucher Template Fields - General
   */
	function wcfm_wc_product_voucher_product_manage_fields_pricing( $pricing_fields, $product_id, $product_type ) {
		global $WCFM;
		
		$_voucher_template_id = '';
		
		// Voucher options
		if( $product_id ) {
			$_voucher_template_id = get_post_meta( $product_id, '_voucher_template_id', true );
		}
		
		// Fetching Voucher Templates
		$args = array(  'posts_per_page'   => -1,
										'offset'           => 0,
										'post_type'        => 'wc_voucher_template',
										'post_status'      => array('any'),
										'suppress_filters' => 0 
									);
		$wc_voucher_templates = get_posts( $args ); 
		$wc_voucher_templates_array = array( '' => __( '-- Choose Template --', 'wc-frontend-manager' ) );
		foreach ( $wc_voucher_templates as $wc_voucher_template ) {
			$wc_voucher_templates_array[$wc_voucher_template->ID] = $wc_voucher_template->post_title;
		}
		
		$wcfm_voucher_template_fields = array(
																				"_voucher_template_id" => array( 'label' => __('Voucher Template', 'wc-frontend-manager'), 'type' => 'select', 'options' => $wc_voucher_templates_array, 'class' => 'wcfm-select wcfm_ele simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'label_class' => 'wcfm_title simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => $_voucher_template_id, 'hints' => __( 'Select a voucher template to make this into a voucher product.', 'wc-frontend-manager') )
																				);
		
		$pricing_fields = array_merge( $pricing_fields, $wcfm_voucher_template_fields );
		
		return $pricing_fields;
	}
	
	/**
   * Product Manage Woocommerce Germanized Fields - General
   */
	function wcfm_woocommerce_germanized_product_manage_fields_general( $general_fields, $product_id, $product_type ) {
		global $WCFM;
		
		// Servive options
		$_service = ( get_post_meta( $product_id, '_service', true) == 'yes' ) ? 'yes' : '';
		$_differential_taxation = ( get_post_meta( $product_id, '_differential_taxation', true) == 'yes' ) ? 'yes' : '';
		if( $product_type != 'simple' ) $_service = '';
		
		$general_fields = array_slice($general_fields, 0, 1, true) + 
													array(
														"_service" => array( 'desc' => __( 'Service', 'woocommerce-germanized') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele wcfm_half_ele_checkbox simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'desc_class' => 'wcfm_title wcfm_ele virtual_ele_title checkbox_title simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => 'yes', 'dfvalue' => $_service),
														"_differential_taxation" => array( 'desc' => __( 'Diff. Taxation', 'woocommerce-germanized') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external groupd wcfm_half_ele_checkbox simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'desc_class' => 'wcfm_title wcfm_ele simple variable external groupd downloadable_ele_title checkbox_title simple non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => 'yes', 'dfvalue' => $_differential_taxation),
														) +
											array_slice($general_fields, 1, count($general_fields) - 1, true) ;
		
		return $general_fields;
	}
	
	/**
   * Product Manage Woocommerce Germanized Fields - Content
   */
	function wcfm_woocommerce_germanized_product_manage_fields_content( $content_fields, $product_id, $product_type ) {
		global $WCFM, $WCFMu;
		
		$_mini_desc = '';
		
		if( $product_id ) {
			$_product = wc_get_product( $product_id );
			$wc_gzd_product = wc_gzd_get_gzd_product( $_product );
			$_mini_desc = $wc_gzd_product->mini_desc;
		}
		
		$woocommerce_germanized_content_fields =  array(
																				"_mini_desc" => array('label' => __( 'Optional Mini Description', 'woocommerce-germanized' ), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'This content will be shown as short product description within checkout and emails.', 'woocommerce-germanized' ), 'value' => $_mini_desc ),
																				);
		
		$content_fields = array_merge( $woocommerce_germanized_content_fields, $content_fields );
		
		return $content_fields;
	}
	
	/**
   * Product Manage Woocommerce Germanized Fields - Pricing
   */
	function wcfm_woocommerce_germanized_product_manage_fields_pricing( $pricing_fields, $product_id, $product_type ) {
		global $WCFM, $WCFMu;
		
		$_sale_price_label = '';
		$_sale_price_regular_label = '';
		$_unit = '';
		$_unit_product = '';
		$_unit_base = '';
		$_unit_price_auto = 'no';
		$_unit_price_regular = '';
		$_unit_price_sale = '';
		
		if( $product_id ) {
			$_product = wc_get_product( $product_id );
			$wc_gzd_product = wc_gzd_get_gzd_product( $_product );
			$_sale_price_label = $wc_gzd_product->sale_price_label;
			$_sale_price_regular_label = $wc_gzd_product->sale_price_regular_label;
			$_unit = $wc_gzd_product->unit;
			$_unit_product = $wc_gzd_product->get_unit_products();
			$_unit_base = $wc_gzd_product->unit_base;
			$_unit_price_auto = get_post_meta( $product_id, '_unit_price_auto', true ) ? get_post_meta( $product_id, '_unit_price_auto', true ) : 'no';
			$_unit_price_regular = $wc_gzd_product->get_unit_regular_price();
			$_unit_price_sale = $wc_gzd_product->get_unit_sale_price();
		}
		
		$woocommerce_germanized_pricing_fields =  array(
																				"_sale_price_label" => array('label' => __( 'Sale Label', 'woocommerce-germanized' ), 'type' => 'select', 'options' => array_merge( array( "-1" => __( 'Select Price Label', 'woocommerce-germanized' ) ), WC_germanized()->price_labels->get_labels() ), 'class' => 'wcfm-select wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'If the product is on sale you may want to show a price label right before outputting the old price to inform the customer.', 'woocommerce-germanized' ), 'value' => $_sale_price_label),
																				"_sale_price_regular_label" => array('label' => __( 'Sale Regular Label', 'woocommerce-germanized' ), 'type' => 'select', 'options' => array_merge( array( "-1" => __( 'Select Price Label', 'woocommerce-germanized' ) ), WC_germanized()->price_labels->get_labels() ), 'class' => 'wcfm-select wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'If the product is on sale you may want to show a price label right before outputting the new price to inform the customer.', 'woocommerce-germanized' ), 'value' => $_sale_price_regular_label),
																				"_unit" => array('label' => __( 'Unit', 'woocommerce-germanized' ), 'type' => 'select', 'options' => array_merge( array( "-1" => __( 'Select unit', 'woocommerce-germanized' ) ), WC_germanized()->units->get_units() ), 'class' => 'wcfm-select wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Needed if selling on a per unit basis', 'woocommerce-germanized' ), 'value' => $_unit ),
																				"_unit_product" => array('label' => __( 'Product Units', 'woocommerce-germanized' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Number of units included per default product price. Example: 1000 ml.', 'woocommerce-germanized' ), 'value' => $_unit_product ),
																				"_unit_base" => array('label' => __( 'Base Price Units', 'woocommerce-germanized' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Base price units. Example base price: 0,99 € / 100 ml. Insert 100 as base price unit amount.', 'woocommerce-germanized' ), 'value' => $_unit_base ),
																				"_unit_price_auto" => array('label' => __( 'Calculation', 'woocommerce-germanized' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele checkbox_title wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Calculate base prices automatically.', 'woocommerce-germanized' ), 'dfvalue' => $_unit_price_auto, 'value' => 'yes' ),
																				"_unit_price_regular" => array('label' => __( 'Regular Base Price', 'woocommerce-germanized' ) . ' (' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'value' => $_unit_price_regular),
																				"_unit_price_sale" => array('label' => __( 'Sale Base Price', 'woocommerce-germanized' ) . ' (' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'value' => $_unit_price_sale),
																				);
		
		$pricing_fields = array_merge( $pricing_fields, $woocommerce_germanized_pricing_fields );
		
		return $pricing_fields;
	}
	
	/**
   * Product Manage Woocommerce Germanized Fields - Shipping
   */
	function wcfm_woocommerce_germanized_product_manage_fields_shipping( $shipping_fields, $product_id ) {
		global $WCFM, $WCFMu;
		
		$delivery_time = '';
		$_free_shipping = '';
		
		if( $product_id ) {
			$_product = wc_get_product( $product_id );
			$wc_gzd_product = wc_gzd_get_gzd_product( $_product );
			$_free_shipping = $wc_gzd_product->free_shipping;
			$delivery_time = $wc_gzd_product->delivery_time->slug;
		}
		
		$delivery_time_list = array( "" => __( 'Select Delivery Time', 'wc-frontend-manager' ) );
		$terms = get_terms( 'product_delivery_time', array( 'hide_empty' => false ) );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term )
				$delivery_time_list[ $term->slug ] = $term->name;
		}
		
		$woocommerce_germanized_shipping_fields =  array(
																				"delivery_time" => array('label' => __( 'Delivery Time', 'woocommerce-germanized' ), 'type' => 'select', 'options' => $delivery_time_list, 'class' => 'wcfm-select wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'value' => $delivery_time),
																				"_free_shipping" => array('label' => __( 'Free shipping?', 'woocommerce-germanized' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele checkbox_title wcfm_title simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'This option disables the "plus shipping costs" notice on product page', 'woocommerce-germanized' ), 'value' => 'yes', 'dfvalue' => $_free_shipping),
																				);
		
		$shipping_fields = array_merge( $shipping_fields, $woocommerce_germanized_shipping_fields );
		
		return $shipping_fields;
	}
	
	/**
   * Product Manage Woocommerce Germanized Fields - Variation
   */
	function wcfm_woocommerce_germanized_product_manage_fields_variations( $variation_fileds, $variations, $variation_shipping_option_array, $variation_tax_classes_options ) {
		global $WCFM, $WCFMu;
		
		$delivery_time_list = array( "" => __( 'Select Delivery Time', 'wc-frontend-manager' ) );
		$terms = get_terms( 'product_delivery_time', array( 'hide_empty' => false ) );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term )
				$delivery_time_list[ $term->slug ] = $term->name;
		}
		
		$woocommerce_germanized_fields =  array(
																				"_sale_price_label" => array('label' => __( 'Sale Label', 'woocommerce-germanized' ), 'type' => 'select', 'options' => array_merge( array( "-1" => __( 'Select Price Label', 'woocommerce-germanized' ) ), WC_germanized()->price_labels->get_labels() ), 'class' => 'wcfm-select wcfm_ele simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'If the product is on sale you may want to show a price label right before outputting the old price to inform the customer.', 'woocommerce-germanized' ) ),
																				"_sale_price_regular_label" => array('label' => __( 'Sale Regular Label', 'woocommerce-germanized' ), 'type' => 'select', 'options' => array_merge( array( "-1" => __( 'Select Price Label', 'woocommerce-germanized' ) ), WC_germanized()->price_labels->get_labels() ), 'class' => 'wcfm-select wcfm_ele simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'If the product is on sale you may want to show a price label right before outputting the new price to inform the customer.', 'woocommerce-germanized' ) ),
																				"_unit" => array('label' => __( 'Unit', 'woocommerce-germanized' ), 'type' => 'select', 'options' => array_merge( array( "-1" => __( 'Select unit', 'woocommerce-germanized' ) ), WC_germanized()->units->get_units() ), 'class' => 'wcfm-select wcfm_ele simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Needed if selling on a per unit basis', 'woocommerce-germanized' ) ),
																				"_unit_product" => array('label' => __( 'Product Units', 'woocommerce-germanized' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Number of units included per default product price. Example: 1000 ml.', 'woocommerce-germanized' ) ),
																				"_unit_base" => array('label' => __( 'Base Price Units', 'woocommerce-germanized' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'Base price units. Example base price: 0,99 € / 100 ml. Insert 100 as base price unit amount.', 'woocommerce-germanized' ) ),
																				"_unit_price_regular" => array('label' => __( 'Regular Base Price', 'woocommerce-germanized' ) . ' (' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking'),
																				"_unit_price_sale" => array('label' => __( 'Sale Base Price', 'woocommerce-germanized' ) . ' (' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking' ),
																				"delivery_time" => array('label' => __( 'Delivery Time', 'woocommerce-germanized' ), 'type' => 'select', 'options' => $delivery_time_list, 'class' => 'wcfm-select wcfm_ele variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking' ),
																				"_service" => array( 'label' => __( 'Service', 'woocommerce-germanized') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele variable non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'label_class' => 'wcfm_title wcfm_ele variable checkbox_title non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => 'yes' ),
																				"_mini_desc" => array('label' => __( 'Optional Mini Description', 'woocommerce-germanized' ), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title wcfm_full_ele variable non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'hints' => __( 'This content will be shown as short product description within checkout and emails.', 'woocommerce-germanized' ) ),
																				);
		
		$variation_fileds = array_merge( $variation_fileds, $woocommerce_germanized_fields );
		
		return $variation_fileds;
	}
	
	/**
   * Product Manage Woocommerce Germanized Fields - Variation Data
   */
	function wcfm_woocommerce_germanized_product_data_variations( $variations, $variation_id, $variation_id_key ) {
		global $WCFM, $WCFMu;
		
		if( $variation_id ) {
			$_product = wc_get_product( $variation_id );
			$wc_gzd_product = wc_gzd_get_gzd_product( $_product );
			
			$variations[$variation_id_key]['_service'] = ( get_post_meta( $variation_id, '_service', true) == 'yes' ) ? 'yes' : '';
		  $variations[$variation_id_key]['_sale_price_label'] = $wc_gzd_product->sale_price_label;
			$variations[$variation_id_key]['_sale_price_regular_label'] = $wc_gzd_product->sale_price_regular_label;
			$variations[$variation_id_key]['_unit'] = $wc_gzd_product->unit;
			$variations[$variation_id_key]['_unit_product'] = $wc_gzd_product->get_unit_products();
			$variations[$variation_id_key]['_unit_base'] = $wc_gzd_product->unit_base;
			$variations[$variation_id_key]['_unit_price_regular'] = $wc_gzd_product->get_unit_regular_price();
			$variations[$variation_id_key]['_unit_price_sale'] = $wc_gzd_product->get_unit_sale_price();
			$variations[$variation_id_key]['delivery_time'] = $wc_gzd_product->delivery_time;
			$variations[$variation_id_key]['_mini_desc'] = $wc_gzd_product->mini_desc;
		}
		
		return $variations;
	}
	
	/**
	 * Product manage Epeken Plugins View
	 */
	function wcfm_wcepeken_product_manage_views() {
		global $WCFM;
	  $WCFM->template->get_template( 'products-manager/wcfm-view-epeken-products-manage.php' );
	}
	
	/**
	 * Product manage WDM Scheduler Plugins View
	 */
	function wcfm_wdm_scheduler_product_manage_views() {
		global $wp, $WCFM;
		
		$product_id = 0;
		if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
			$product_id = $wp->query_vars['wcfm-products-manage'];
		}
		?>
		<div class="page_collapsible products_manage_scheduler simple variable nonvirtual booking" id="wcfm_products_manage_form_scheduler_head"><label class="wcfmfa fa-clock"></label><?php _e('Scheduler Config', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container simple variable nonvirtual booking">
			<div id="wcfm_products_manage_form_scheduler_expander" class="wcfm-content">
			  <?php
				$scheduler_admin = new SchedulerAdmin();
				$scheduler_admin->wdmStartEndDate( $product_id );
				?>
			</div>
		</div>
		<?php
		wdmCpbEnqueueScripts( 'post-new.php' );
	}
	
	/**
	 * Product Manager WC Product Scheduler Plugins View
	 */
	function wcfm_wc_product_scheduler_product_manage_views() {
		global $wp, $WCFM;
		
		$product_id = 0;
		
		$status = 0;
		
		$start_date = '';
		$st_hh = '';
		$st_mn = '';
		
		$end_date = '';
		$end_hh = '';
		$end_mn = '';
		
		$countdown = 0;
		
		if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
			$product_id = absint( $wp->query_vars['wcfm-products-manage'] );
			
			if( $product_id ) {
				$status      = get_post_meta( $product_id, 'wpas_schedule_sale_status', true );   
				$start_time  = get_post_meta( $product_id, 'wpas_schedule_sale_st_time', true );   
				$end_time    = get_post_meta( $product_id, 'wpas_schedule_sale_end_time', true );   
				$mode        = get_post_meta( $product_id, 'wpas_schedule_sale_mode', true );   
				$countdown   = get_post_meta( $product_id, 'wpas_schedule_sale_countdown', true );  
				
				if( !empty($start_time) ) {
					$start_date = date('Y-m-d', $start_time);
					$st_mm      = date('m', $start_time);
					$st_dd      = date('d', $start_time);
					$st_hh      = date('H', $start_time);
					$st_mn      = date('i', $start_time);
				}
				
				if( isset($end_time) &!empty($end_time) ) {
					$end_date  = date('Y-m-d', $end_time);
					$end_mm    = date('m', $end_time);
					$end_dd    = date('d', $end_time);
					$end_hh    = date('H', $end_time);
					$end_mn    = date('i', $end_time);
				}
			}
		}
		?>
		<div class="page_collapsible products_manage_wc_product_scheduler simple variable external booking" id="wcfm_products_manage_form_wc_product_scheduler_head"><label class="wcfmfa fa-clock"></label><?php _e('Availability Scheduler', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container simple variable external booking">
			<div id="wcfm_products_manage_form_wc_product_scheduler_expander" class="wcfm-content">
			  <?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wc_product_scheduler_fields', array(  
						
						"wpas_select_status" => array('label' => __( 'Status', 'wc-frontend-manager' ) , 'type' => 'select', 'options' => array( '0' => __( 'Disable', 'wc-frontend-manager'), '1' => __( 'Enable', 'wc-frontend-manager' ) ), 'class' => 'wcfm-select wcfm_ele simple variable external booking', 'label_class' => 'wcfm_title simple variable external booking', 'value' => $status ),
						
						"wpas_st_date" => array( 'label' => __( 'Start Time', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => __('From', 'wc-frontend-manager') . '... YYYY-DD-MM', 'custom-attributes' => array( 'date_format' => 'yy-mm-dd' ), 'class' => 'wcfm-text wcfm_ele wcfm_small_ele wcfm_datepicker simple variable external booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external booking', 'value' => $start_date ),
						"wpas_st_hh" => array( 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_small_ele wcfm_non_negative_input simple variable external booking', 'label_class' => 'wcfm_title simple variable external booking', 'attributes' => array( 'min' => 0, 'max' => 12, 'step' => 1 ), 'value' => $st_hh ),
						"wpas_st_mn" => array( 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_small_ele wcfm_non_negative_input simple variable external booking', 'label_class' => 'wcfm_title simple variable external booking', 'attributes' => array( 'min' => 0, 'max' => 60, 'step' => 1 ), 'value' => $end_mn ),
						
						"wpas_end_date" => array( 'label' => __( 'End Time', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => __('Upto', 'wc-frontend-manager') . '... YYYY-DD-MM', 'custom-attributes' => array( 'date_format' => 'yy-mm-dd' ), 'class' => 'wcfm-text wcfm_ele wcfm_small_ele wcfm_datepicker simple variable external booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external booking', 'value' => $end_date ),
						"wpas_end_hh" => array( 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_small_ele wcfm_non_negative_input simple variable external booking', 'label_class' => 'wcfm_title simple variable external booking', 'attributes' => array( 'min' => 0, 'max' => 12, 'step' => 1 ), 'value' => $end_hh ),
						"wpas_end_mn" => array( 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_small_ele wcfm_non_negative_input simple variable external booking', 'label_class' => 'wcfm_title simple variable external booking', 'attributes' => array( 'min' => 0, 'max' => 60, 'step' => 1 ), 'value' => $end_mn ),
						
						"wpas_enable_countdown" => array('label' => __( 'CountDown', 'wc-frontend-manager' ) , 'type' => 'select', 'options' => array( '0' => __( 'Disable', 'wc-frontend-manager'), '1' => __( 'Enable', 'wc-frontend-manager' ) ), 'class' => 'wcfm-select wcfm_ele simple variable external booking', 'label_class' => 'wcfm_title simple variable external booking', 'value' => $countdown ),
						
						
																															), $product_id ) );
				?>
				<div class="wcfm-clearfix"></div><br />
				<p class="description instructions"><?php _e( 'Note: Start time and End time will be on GMT, Current GMT time is', 'wc-frontend-manager' );  echo ': ' .  date( "Y-m-d @ H:i", time() ); ?></p>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Vendor Settings Epeken Plugins View
	 */
	function wcfm_wcepeken_settings_views( $user_id ) {
		global $WCFM;
		
		$vendor_data_asal_kota = get_user_meta(intval($user_id), 'vendor_data_kota_asal', true);
		$vendor_jne_reg = get_user_meta(intval($user_id), 'vendor_jne_reg', true);
		$vendor_jne_oke = get_user_meta(intval($user_id), 'vendor_jne_oke', true);
		$vendor_jne_yes = get_user_meta(intval($user_id), 'vendor_jne_yes', true);
		$vendor_tiki_reg = get_user_meta(intval($user_id), 'vendor_tiki_reg', true);
		$vendor_tiki_eco = get_user_meta(intval($user_id), 'vendor_tiki_eco', true);
		$vendor_tiki_ons = get_user_meta(intval($user_id), 'vendor_tiki_ons', true);
		$vendor_pos_kilat_khusus = get_user_meta(intval($user_id), 'vendor_pos_kilat_khusus', true);
		$vendor_pos_express_next_day = get_user_meta(intval($user_id), 'vendor_pos_express_next_day', true);
		$vendor_pos_valuable_goods = get_user_meta(intval($user_id), 'vendor_pos_valuable_goods', true);
		$vendor_jnt_ez = get_user_meta(intval($user_id), 'vendor_jnt_ez', true);
		$vendor_sicepat_reg = get_user_meta(intval($user_id), 'vendor_sicepat_reg', true);
		$vendor_sicepat_best = get_user_meta(intval($user_id), 'vendor_sicepat_best', true);
		$vendor_wahana = get_user_meta(intval($user_id), 'vendor_wahana', true);
		
		?>
		<div class="page_collapsible" id="wcfm_settings_form_epeken_head">
			<label class="wcfmfa fa-truck"></label>
			<?php _e('Shipping', 'wc-frontend-manager'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_epeken_expander" class="wcfm-content">
				
				<h2><?php _e('Shipment Origin Information', 'wc-frontend-manager'); ?></h2>
				<table class="form-table">
				 <tr>
					 <th>
						Kota Asal Pengiriman
					 </th>
					 <td>
						<select name="vendor_data_asal_kota" id="vendor_data_asal_kota" style="width: 50%">
							<?php
							$license = get_option('epeken_wcjne_license_key');     
							$origins = epeken_get_valid_origin($license);
							$origins = json_decode($origins,true);
							$origins = $origins["validorigin"];
							?>		
							<option value="0">None</option>
							<?php
							foreach($origins as $origin) {
								$idx=$origin['origin_code'];
								?>
								<option value=<?php echo '"'.$idx.'"'; if($vendor_data_asal_kota === $idx){echo ' selected';}?>><?php echo $origin["kota_kabupaten"]; ?></option>
						  <?php
							}
						  ?>
						</select>
						<script type='text/javascript'>
							jQuery(document).ready(function($){
									$('#vendor_data_asal_kota').select2();
							});
						</script>
					 </td>
				 </tr>
				 <tr>
					 <th>
					 Expedisi/Kurir Yang Diaktifkan
					 </th>
					 <td>
						<table>
							<tr>
								<td style="width: 33%">
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_jne_reg" <?php if($vendor_jne_reg === 'on') echo " checked"; ?>> JNE REG</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_jne_oke" <?php if($vendor_jne_oke === 'on') echo " checked"; ?>> JNE OKE</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_jne_yes" <?php if($vendor_jne_yes === 'on') echo " checked"; ?>> JNE YES</input><br>
								</td>
								<td style="width: 33%">
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_tiki_reg" <?php if($vendor_tiki_reg === 'on') echo " checked"; ?>> TIKI REG</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_tiki_eco" <?php if($vendor_tiki_eco === 'on') echo " checked"; ?>> TIKI ECO</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_tiki_ons" <?php if($vendor_tiki_ons === 'on') echo " checked"; ?>> TIKI ONS</input><br>
								</td>
								<td style="width: 33%">
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_pos_kilat_khusus" <?php if($vendor_pos_kilat_khusus === 'on') echo " checked"; ?>> POS KILAT KHUSUS</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_pos_express_next_day" <?php if($vendor_pos_express_next_day === 'on') echo " checked"; ?>> POS Express Next Day</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_pos_valuable_goods" <?php if($vendor_pos_valuable_goods === 'on') echo " checked"; ?>> POS Valuable Goods</input><br>
								</td>
								</tr>
								<tr>
								<td style="width: 33%">
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_jnt_ez" <?php if($vendor_jnt_ez === 'on') echo " checked"; ?>> J&T EZ</input><br>
								</td>
								<td style="width: 33%">
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_sicepat_reg" <?php if($vendor_sicepat_reg === 'on') echo " checked"; ?>> SICEPAT REG</input><br>
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_sicepat_best" <?php if($vendor_sicepat_best === 'on') echo " checked"; ?>> SICEPAT BEST</input><br>
								</td>
								<td style="width: 33%">
								 <input type="checkbox" class="wcfm-checkbox" style="margin-right: 5%;" name="vendor_wahana" <?php if($vendor_wahana === 'on') echo " checked"; ?>> Wahana </input><br>
								</td>
							</tr>
						</table>
					 </td>
				 </tr>
				</table>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Vendor Settings Epeken Plugins Data Update
	 */
	function wcfm_wcepeken_vendor_settings_update( $user_id, $wcfm_settings_form ) {
		
		$vendor_data_asal_kota = (empty($wcfm_settings_form['vendor_data_asal_kota'] )) ? '' : $wcfm_settings_form['vendor_data_asal_kota']; // code kota asal
		$vendor_jne_reg = (empty($wcfm_settings_form['vendor_jne_reg'])) ? '' : $wcfm_settings_form['vendor_jne_reg'];
		$vendor_jne_oke = (empty($wcfm_settings_form['vendor_jne_oke'])) ? '' : $wcfm_settings_form['vendor_jne_oke'];
		$vendor_jne_yes = (empty($wcfm_settings_form['vendor_jne_yes'])) ? '' : $wcfm_settings_form['vendor_jne_yes'];
		$vendor_tiki_reg = (empty($wcfm_settings_form['vendor_tiki_reg'])) ? '' : $wcfm_settings_form['vendor_tiki_reg'];
		$vendor_tiki_eco = (empty($wcfm_settings_form['vendor_tiki_eco'])) ? '' : $wcfm_settings_form['vendor_tiki_eco'];
		$vendor_tiki_ons = (empty($wcfm_settings_form['vendor_tiki_ons'])) ? '' : $wcfm_settings_form['vendor_tiki_ons'];
		$vendor_pos_kilat_khusus = (empty($wcfm_settings_form['vendor_pos_kilat_khusus'])) ? '' : $wcfm_settings_form['vendor_pos_kilat_khusus'];
		$vendor_pos_express_next_day = (empty($wcfm_settings_form['vendor_pos_express_next_day'])) ? '' : $wcfm_settings_form['vendor_pos_express_next_day'];
		$vendor_pos_valuable_goods = (empty($wcfm_settings_form['vendor_pos_valuable_goods'])) ? '' : $wcfm_settings_form['vendor_pos_valuable_goods'];
		$vendor_jnt_ez = (empty($wcfm_settings_form['vendor_jnt_ez'])) ? '' : $wcfm_settings_form['vendor_jnt_ez'];
		$vendor_sicepat_reg = (empty($wcfm_settings_form['vendor_sicepat_reg'])) ? '' : $wcfm_settings_form['vendor_sicepat_reg'];
		$vendor_sicepat_best = (empty($wcfm_settings_form['vendor_sicepat_best'])) ? '' : $wcfm_settings_form['vendor_sicepat_best'];
		$vendor_wahana = (empty($wcfm_settings_form['vendor_wahana'])) ? '' : $wcfm_settings_form['vendor_wahana'];
		
		update_user_meta( $user_id, 'vendor_data_kota_asal', $vendor_data_asal_kota);
		update_user_meta( $user_id, 'vendor_jne_reg', $vendor_jne_reg);
		update_user_meta( $user_id, 'vendor_jne_oke', $vendor_jne_oke);
		update_user_meta( $user_id, 'vendor_jne_yes', $vendor_jne_yes);
		update_user_meta( $user_id, 'vendor_tiki_reg', $vendor_tiki_reg);
		update_user_meta( $user_id, 'vendor_tiki_eco', $vendor_tiki_eco);
		update_user_meta( $user_id, 'vendor_tiki_ons', $vendor_tiki_ons);
		update_user_meta( $user_id, 'vendor_pos_kilat_khusus', $vendor_pos_kilat_khusus);
		update_user_meta( $user_id, 'vendor_pos_express_next_day', $vendor_pos_express_next_day);
		update_user_meta( $user_id, 'vendor_pos_valuable_goods', $vendor_pos_valuable_goods);
		update_user_meta( $user_id, 'vendor_jnt_ez', $vendor_jnt_ez);
		update_user_meta( $user_id, 'vendor_sicepat_reg', $vendor_sicepat_reg);
		update_user_meta( $user_id, 'vendor_sicepat_best', $vendor_sicepat_best);
		update_user_meta( $user_id, 'vendor_wahana', $vendor_wahana);
	}
		
	
	/**
   * Product Manage Third Party Plugins views
   */
  function wcfm_thirdparty_products_manage_views( ) {
		global $WCFM;
	  $WCFM->template->get_template( 'products-manager/wcfm-view-thirdparty-products-manage.php' );
	}
}