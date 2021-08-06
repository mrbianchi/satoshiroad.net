<?php
/**
 * WCFM Marketplace plugin core
 *
 * Plugin Frontend Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Frontend {
	
	public function __construct() {
		global $WCFM, $WCFMmp;
		
		$vendor_sold_by_position = isset( $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by_position'] ) ? $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by_position'] : 'below_atc';
		
		// Show Product Sold By Label
		if( $vendor_sold_by_position == 'bellow_price' ) {
			add_action( 'woocommerce_single_product_summary',	array( &$this, 'wcfmmp_sold_by_single_product' ), 15 );
		} elseif( $vendor_sold_by_position == 'bellow_sc' ) {
			add_action( 'woocommerce_single_product_summary',	array( &$this, 'wcfmmp_sold_by_single_product' ), 25 );
		} else {
			add_action( 'woocommerce_product_meta_start',	array( &$this, 'wcfmmp_sold_by_single_product' ), 50 );
		}
		
		// YiTH Quick Product View Sold By
		if( ( $vendor_sold_by_position == 'bellow_price' ) || ( $vendor_sold_by_position == 'bellow_sc' ) ) {
			add_action( 'yith_wcqv_product_summary',	array( &$this, 'wcfmmp_sold_by_single_product' ), 35 );
		}
		
		// Flatsome Quick Product View Sold by
		add_action( 'woocommerce_single_product_lightbox_summary', array( &$this, 'wcfmmp_sold_by_single_product' ), 35 );
		
		$is_look_hook_defined = false;
		
		// Martfury Theme Compatibility
		if( function_exists( 'martfury_is_vendor_page' ) ) {
			$is_look_hook_defined = true;
			add_action('woocommerce_after_shop_loop_item_title', array( $this, 'wcfmmp_sold_by_product' ), 140 );
			add_action( 'martfury_woo_after_shop_loop_item_title', array( $this, 'wcfmmp_sold_by_product' ), 20 );
		}
		
		// ReHUB Theme Compatibility
		if( function_exists( 'rehub_option' ) ) {
			$is_look_hook_defined = true;
			/*if (rehub_option('woo_design') == 'list') {
				add_action('rh_woo_button_loop', array( $this, 'wcfmmp_sold_by_product' ), 50 );
			} elseif (rehub_option('woo_design') == 'grid') {
				add_action('rehub_vendor_show_action', array( $this, 'wcfmmp_sold_by_product' ), 50 );
			} elseif (rehub_option('woo_design') == 'gridtwo') {
				add_action('rehub_after_compact_grid_figure', array( $this, 'wcfmmp_sold_by_product' ), 50 );
			} else {
				add_action('woocommerce_after_shop_loop_item_title', array( $this, 'wcfmmp_sold_by_product' ), 50 );
			}*/
		} 
		
		// Ocean WP Theme Compatibility
		if( function_exists( 'oceanwp_woo_product_elements_positioning' ) ) {
			$is_look_hook_defined = true;
			add_action('ocean_before_archive_product_add_to_cart_inner', array( $this, 'wcfmmp_sold_by_product' ), 50 );
		}
		
		// SW WooCommerce Compatibility
		if( function_exists( 'sw_woocommerce_construct' ) ) {
			$is_look_hook_defined = true;
			add_action('woocommerce_after_shop_loop_item', array( $this, 'wcfmmp_sold_by_product' ), 50 );
			add_action('sw_custom_mobile', array( $this, 'wcfmmp_sold_by_product' ), 50 );
		}
		
		// Show Product Sold By in Loop
		if( !$is_look_hook_defined ) {
			add_action('woocommerce_after_shop_loop_item_title', array( $this, 'wcfmmp_sold_by_product' ), 50 );
		}
		
		// Show Product Sold By in Cart
		add_filter('woocommerce_get_item_data', array( &$this, 'wcfmmp_sold_by_cart' ), 50, 2 );
		
		// WC Products Short Code Store Attribute Compatibility
		add_filter( 'shortcode_atts_products', array( $this, 'wcfmmp_shortcode_atts_products' ), 50, 4 );
		add_filter( 'shortcode_atts_sale_products', array( $this, 'wcfmmp_shortcode_atts_products' ), 50, 4 );
		add_filter( 'shortcode_atts_recent_products', array( $this, 'wcfmmp_shortcode_atts_products' ), 50, 4 );
		add_filter( 'shortcode_atts_featured_products', array( $this, 'wcfmmp_shortcode_atts_products' ), 50, 4 );
		add_filter( 'shortcode_atts_top_rated_products', array( $this, 'wcfmmp_shortcode_atts_products' ), 50, 4 );
		add_filter( 'shortcode_atts_best_selling_products', array( $this, 'wcfmmp_shortcode_atts_products' ), 50, 4 );
		add_filter( 'woocommerce_shortcode_products_query', array( $this, 'wcfmmp_woocommerce_shortcode_products_query' ), 50, 3 );
		
		// Store Related Product Rule
		add_filter( 'woocommerce_product_related_posts_query', array( &$this, 'wcfmmp_store_related_products' ), 99, 2 );
		
		// Store Order Next-Previous Link
		if( apply_filters( 'wcfm_is_allow_header_store_order_related_orders', false ) ) { 
			add_action( 'begin_wcfm_orders_details', array( &$this, 'wcfmmp_store_order_related_orders' ),500, 1 );
		}
		add_action( 'end_wcfm_orders_details', array( &$this, 'wcfmmp_store_order_related_orders' ),500, 1 );
		
		// My Account Vendor Registration URL
		add_action( 'woocommerce_register_form_end', array( &$this, 'wcfmmp_become_vendor_link' ) );
		add_action( 'woocommerce_after_my_account', array( &$this, 'wcfmmp_become_vendor_link' ) );
		
		// My Account Dashboard Menu
		add_filter( 'woocommerce_account_menu_items', array( &$this, 'wcfm_dashboard_my_account_menu_items' ), 210 );
		add_filter( 'woocommerce_get_endpoint_url', array( &$this,  'wcfm_dashboard_my_account_endpoint_redirect'), 10, 4 );
		
		// Membership Commission Rules
		add_filter( 'membership_manager_fields_commission', array( &$this, 'wcfmmp_membership_manager_fields_commission' ) );
		
		// GEO Location Disable
		add_filter( 'wcfmmp_is_allow_store_list_by_user_location', array( &$this, 'wcfmmp_is_allow_geo_locate' ) );
		
		// Store Default Logo
		add_filter( 'wcfmmp_store_default_logo', array( &$this, 'wcfmmp_store_default_logo' ) );
		
		// WCFM Store Page Body Class
		add_filter('body_class', array( &$this, 'wcfm_store_body_classes' ) );	
		
		// WCFM Store Page Title
 		//add_filter( 'the_title', array( &$this, 'wcfm_store_page_title' ) );
		
		//enqueue scripts
		add_action( 'wp_enqueue_scripts', array( &$this, 'wcfmmp_scripts' ), 20 );
		
		//enqueue styles
		add_action( 'wp_enqueue_scripts', array( &$this, 'wcfmmp_styles' ), 20 );
		
	}
	
	/**
	 * Show Sold by at Single Product Page
	 */
	public static function wcfmmp_sold_by_single_product() {
		global $WCFM, $WCFMmp, $product;
		
		if( !apply_filters( 'wcfmmp_is_allow_single_product_sold_by', true ) ) return;
		
		if( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$product_id = $product->get_id();
			
			$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
			
			$vendor_sold_by_template = $WCFMmp->wcfmmp_vendor->get_vendor_sold_by_template();
			
			if( $vendor_sold_by_template == 'tab' ) {
				
			} elseif( ( $vendor_sold_by_template == 'advanced' ) && !defined('DOING_AJAX') ) {
				$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-advanced.php', array( 'product_id' => $product_id, 'vendor_id' => $vendor_id ) );
			} else {
				$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-simple.php', array( 'product_id' => $product_id, 'vendor_id' => $vendor_id ) );
			}
		}
	}
	
	/**
	 * Show Sold by as Tab at Single Product Page
	 */
	public static function wcfmmp_sold_by_tab_single_product() {
		global $WCFM, $WCFMmp, $product;
		
		if( !apply_filters( 'wcfmmp_is_allow_single_product_sold_by', true ) ) return;
		
		if( !$product ) return;
		
		if( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$product_id = $product->get_id();
			$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
			$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-tab.php', array( 'product_id' => $product_id, 'vendor_id' => $vendor_id ) );
		}
	}
	
	/**
	 * Show sold by at Product Page
	 */
	public static function wcfmmp_sold_by_product() {
		global $WCFM, $WCFMmp, $product;
		
		if ( wcfm_is_store_page() ) return;
		if( !$product ) return;
		
		if( !apply_filters( 'wcfmmp_is_allow_archive_product_sold_by', true ) ) return;
		
		if( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$product_id = $product->get_id();
			
			$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
			
			if( apply_filters( 'wcfmmp_is_allow_archive_sold_by_advanced', false ) ) { 
				$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-advanced.php', array( 'product_id' => $product_id, 'vendor_id' => $vendor_id ) );
			} else {
				$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-simple.php', array( 'product_id' => $product_id, 'vendor_id' => $vendor_id ) );
			}
		}
	}
	
	/**
	 * Show sold by at Cart Page
	 */
	public function wcfmmp_sold_by_cart( $cart_item_meta = array(), $cart_item ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfmmp_is_allow_cart_sold_by', true ) ) return;
		
		if( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$product_id = $cart_item['product_id'];
			$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
			if( $vendor_id ) {
				if( apply_filters( 'wcfmmp_is_allow_sold_by', true, $vendor_id ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'sold_by' ) ) {
					// Check is store Online
					$is_store_offline = get_user_meta( $vendor_id, '_wcfm_store_offline', true );
					if ( !$is_store_offline ) {
						$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint($vendor_id) );
						
						if( apply_filters( 'wcfmmp_is_allow_sold_by_linked', true ) ) {
							$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($vendor_id) );
						} else {
							$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint($vendor_id) );
						}
						
						do_action('before_wcfmmp_sold_by_label_cart_page', $vendor_id );
						if( !is_array( $cart_item_meta ) ) $cart_item_meta = (array) $cart_item_meta;
						$cart_item_meta = array_merge( $cart_item_meta, array( array( 'name' => $sold_by_text, 'value' => $store_name ) ) );
						do_action('after_wcfmmp_sold_by_label_cart_page', $vendor_id );
					}
				}
			}
		}
		return $cart_item_meta;
	}
	
	/**
	 * WC Products short code "store" attribute support added
	 */
	function wcfmmp_shortcode_atts_products( $attributes, $pairs, $atts, $shortcode ) {
		if ( array_key_exists( 'store', $atts ) ) {
			$attributes['store'] = $atts['store'];
		}
		return $attributes;
	}
	
	/**
	 * WC Products short codde store filter
	 */
	function wcfmmp_woocommerce_shortcode_products_query( $query_args, $attributes, $type = 'products' ) {
		if( isset( $attributes['store'] ) ) {
			$store = absint( $attributes['store'] );
			if( $store )
				$query_args['author'] = $store;
		}
		return $query_args;
	}
	
	/**
	 * Store related product rule
	 */
	function wcfmmp_store_related_products( $query, $product_id ) {
		global $WCFM, $WCFMmp;
		
		$store_related_products   =  isset( $WCFMmp->wcfmmp_marketplace_options['store_related_products'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_related_products'] : 'default';
		if ( 'store' == $store_related_products ) {
			$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
			if ( $vendor_id ) {
				$query['where'] .= ' AND p.post_author = ' . $vendor_id;
			}
		}
		return $query;
	}
	
	/**
	 * Store order related orders
	 */
	function wcfmmp_store_order_related_orders( $order_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !apply_filters( 'wcfm_is_allow_store_order_related_orders', true ) ) return; 
		
		if( wcfm_is_vendor() ) {
			$sql = 'SELECT ID FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
			$sql .= ' WHERE 1=1';
			$sql .= " AND `vendor_id` = {$WCFMmp->vendor_id}";
			$sql .= " AND `order_id` = {$order_id}";
			
			$commission_id = 0;
			$commissions = $wpdb->get_results( $sql );
			if( !empty( $commissions ) ) {
				foreach( $commissions as $commission ) {
					$commission_id = $commission->ID;
				}
			}
			
			if( $commission_id ) {
				$allowed_status      = get_wcfm_marketplace_active_withdrwal_order_status_in_comma();
				$allowed_status      = apply_filters( 'wcfmp_order_list_allowed_status', $allowed_status ); 
				
				$sql = 'SELECT order_id FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
				$sql .= ' WHERE 1=1';
				$sql .= " AND `vendor_id` = {$WCFMmp->vendor_id}";
				$sql .= " AND `order_id` != {$order_id}";
				if( apply_filters( 'wcfmmp_is_allow_order_status_filter', false ) ) {
					$sql .= " AND commission.order_status IN ({$allowed_status})";
				}
				$sql .= ' AND `is_trashed` = 0';
				
				$next_sql = $sql . " AND ID = (select min(ID) from {$wpdb->prefix}wcfm_marketplace_orders where ID > {$commission_id})";
				$next_orders = $wpdb->get_results( $next_sql );
				
				echo '<div class="wcfm-clearfix"></div><br />';
				if( !empty( $next_orders ) ) {
					foreach( $next_orders as $next_order ) {
						$next_order_id = $next_order->order_id;
						if( $next_order_id ) {
							echo '<a href="' . get_wcfm_view_order_url($next_order_id) . '" class="wcfm_submit_button wcfm_store_next_order">' . __( 'Next', 'wc-frontend-manager' ) . ' >></a>';
						}
					}
				}
				
				$pre_sql = $sql . " AND ID = (select max(ID) from {$wpdb->prefix}wcfm_marketplace_orders where ID < {$commission_id})";
				$pre_orders = $wpdb->get_results( $pre_sql );
				
				if( !empty( $pre_orders ) ) {
					foreach( $pre_orders as $pre_order ) {
						$pre_order_id = $pre_order->order_id;
						if( $pre_order_id ) {
							echo '<a href="' . get_wcfm_view_order_url($pre_order_id) . '" class="wcfm_submit_button wcfm_store_previous_order" style="float:left;"><< ' . __( 'Previous', 'wc-frontend-manager' ) . '</a>';
						}
					}
				}
				echo '<div class="wcfm-clearfix"></div><br />';
			}
		}
	}
	
	/**
	 * WC Registration Become Vendor link
	 */
	function wcfmmp_become_vendor_link() {
		global $WCFM, $WCFMmp;
		
		if( apply_filters( 'wcfm_is_allow_my_account_become_vendor', true ) && WCFMmp_Dependencies::wcfmvm_plugin_active_check() ) {
			if( wcfm_is_allowed_membership() && !wcfm_has_membership() && !wcfm_is_vendor() ) {
				echo '<div class="wcfmmp_become_vendor_link">';
				$wcfm_memberships = get_wcfm_memberships();
				if( !empty( $wcfm_memberships ) && apply_filters( 'wcfm_is_allow_my_account_membership_subscribe', true ) ) {
					echo '<a href="' . apply_filters( 'wcfm_change_membership_url', get_wcfm_membership_url() ) . '">' . apply_filters( 'wcfm_become_vendor_label', __( 'Become a Vendor', 'wc-multivendor-marketplace' ) ) . '</a>';
				} else {
					echo '<a href="' . get_wcfm_registration_page() . '">' . apply_filters( 'wcfm_become_vendor_label', __( 'Become a Vendor', 'wc-multivendor-marketplace' ) ) . '</a>';
				}
				echo '</div>';
			}
		}
	}
	
	/**
	 * WC My Account Dashboard Link
	 */
	function wcfm_dashboard_my_account_menu_items( $items ) {
		global $WCFM, $WCFMmp;
		
		if( wcfm_is_vendor() ) {
			$dashboard_page_title = __( 'Store Manager', 'wc-multivendor-marketplace' );
			$pages = get_option("wcfm_page_options");
			if( isset($pages['wc_frontend_manager_page_id']) && $pages['wc_frontend_manager_page_id'] ) {
				$dashboard_page_title = get_the_title( $pages['wc_frontend_manager_page_id'] );
			}
			$dashboard_page_title = apply_filters( 'wcfmmp_wcmy_dashboard_page_title', $dashboard_page_title ); 
			
			$items = array_slice($items, 0, count($items) - 2, true) +
																		array(
																					"wcfm-store-manager" => __( $dashboard_page_title, 'wc-multivendor-marketplace' )
																					) +
																		array_slice($items, count($items) - 2, count($items) - 1, true) ;
		}
																	
		return $items;
	}
	
	function wcfm_dashboard_my_account_endpoint_redirect( $url, $endpoint, $value, $permalink ) {
		if( $endpoint == 'wcfm-store-manager')
      $url = get_wcfm_url();
    return $url;
	}
	
	/**
	 * Membership commission rules 
	 */
	function wcfmmp_membership_manager_fields_commission( $commission_fileds ) {
		global $WCFM, $WCFMmp, $wp;
		
		$membership_id = 0;
		if( isset( $wp->query_vars['wcfm-memberships-manage'] ) && !empty( $wp->query_vars['wcfm-memberships-manage'] ) ) {
			$membership_id = absint( $wp->query_vars['wcfm-memberships-manage'] );
		}
		
		// Commission
		$wcfm_commission_options = get_option( 'wcfm_commission_options', array() );
		$wcfm_commission_for = isset( $wcfm_commission_options['commission_for'] ) ? $wcfm_commission_options['commission_for'] : 'vendor';
		
		$wcfm_commission_types = get_wcfm_marketplace_commission_types();
		$wcfm_commission_types = array_merge( array( 'global' => __( 'By Global Rule', 'wc-multivendor-marketplace' ) ), $wcfm_commission_types );
		
		$vendor_commission_mode = 'global';
		$vendor_commission_fixed = '';
		$vendor_commission_percent = 90;
		$vendor_commission_by_sales = array();
		$vendor_commission_by_products = array();
		$vendor_get_shipping = 'yes';
		$vendor_get_tax = 'yes';
		$vendor_coupon_deduct = 'yes';
		$admin_coupon_deduct = 'yes';
		
		if( $membership_id ) {
			$commission = (array) get_post_meta( $membership_id, 'commission', true );
			$vendor_commission_mode        = isset( $commission['commission_mode'] ) ? $commission['commission_mode'] : 'global';
			$vendor_commission_fixed       = isset( $commission['commission_fixed'] ) ? $commission['commission_fixed'] : '';
			$vendor_commission_percent     = isset( $commission['commission_percent'] ) ? $commission['commission_percent'] : '90';
			$vendor_commission_by_sales    = isset( $commission['commission_by_sales'] ) ? $commission['commission_by_sales'] : array();
			$vendor_commission_by_products = isset( $commission['commission_by_products'] ) ? $commission['commission_by_products'] : array();
			$vendor_commission_by_quantity = isset( $commission['commission_by_quantity'] ) ? $commission['commission_by_quantity'] : array();
			$vendor_get_shipping           = isset( $commission['get_shipping'] ) ? $commission['get_shipping'] : '';
			$vendor_get_tax                = isset( $commission['get_tax'] ) ? $commission['get_tax'] : '';
			$vendor_coupon_deduct          = isset( $commission['coupon_deduct'] ) ? $commission['coupon_deduct'] : '';
			$admin_coupon_deduct           = isset( $commission['admin_coupon_deduct'] ) ? $commission['admin_coupon_deduct'] : '';
		}
		
		$commission_fileds = apply_filters( 'wcfm_marketplace_settings_fields_membership_commission', array(
			                                                                            "wcfm_commission_for" => array('label' => __('Commission For', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'vendor' => __( 'Vendor', 'wc-multivendor-marketplace' ), 'admin' => __( 'Admin', 'wc-multivendor-marketplace' ) ), 'attributes' => array( 'disabled' => true, 'style' => 'border: 0px !important;font-weight:600;color:#00897b;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_commission_for, 'hints' => __( 'Always applicable as per global rule.', 'wc-multivendor-marketplace' ) ),
					                                                                        "vendor_commission_mode" => array('label' => __('Commission Mode', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_mode]', 'type' => 'select', 'options' => $wcfm_commission_types, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_commission_mode ),
					                                                                        "vendor_commission_percent" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_percent]', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_percent commission_mode_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele commission_mode_field commission_mode_percent commission_mode_percent_fixed', 'value' => $vendor_commission_percent, 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        "vendor_commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'commission[commission_fixed]', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_fixed commission_mode_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele commission_mode_field commission_mode_fixed commission_mode_percent_fixed', 'value' => $vendor_commission_fixed, 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        "vendor_commission_by_sales" => array('label' => __('Commission By Sales Rule(s)', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_by_sales]', 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_sales', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_sales', 'desc_class' => 'commission_mode_field commission_mode_by_sales instructions', 'value' => $vendor_commission_by_sales, 'desc' => sprintf( __( 'Commission rules depending upon vendors total sales. e.g 50&#37; commission when sales < %s1000, 75&#37; commission when sales > %s1000 but < %s2000 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
					                                                                        																			"sales" => array('label' => __('Sales', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '1', 'step' => '0.1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
					                                                                        "vendor_commission_by_products" => array('label' => __('Commission By Product Price', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_by_products]', 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_products', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_products', 'desc_class' => 'commission_mode_field commission_mode_by_products instructions', 'value' => $vendor_commission_by_products, 'desc' => sprintf( __( 'Commission rules depending upon product price. e.g 80&#37; commission when product cost < %s1000, %s100 fixed commission when product cost > %s1000 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
					                                                                        																			"cost" => array('label' => __('Product Cost', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
					                                                                        "vendor_commission_by_quantity" => array('label' => __('Commission By Purchase Quantity', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_by_quantity]', 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_quantity', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_quantity', 'desc_class' => 'commission_mode_field commission_mode_by_quantity instructions', 'value' => $vendor_commission_by_quantity, 'desc' => sprintf( __( 'Commission rules depending upon purchased product quantity. e.g 80&#37; commission when purchase quantity 2, 80&#37; commission when purchase quantity > 2 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ) ),  'options' => array( 
					                                                                        																			"quantity" => array('label' => __('Purchase Quantity', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '1', 'step' => '1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcfm-text wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
																																									"vendor_get_shipping" => array('label' => __('Shipping cost goes to vendor?', 'wc-multivendor-marketplace'), 'name' => 'commission[get_shipping]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'value' => 'yes', 'dfvalue' => $vendor_get_shipping ),
																																									"vendor_get_tax" => array('label' => __('Tax goes to vendor?', 'wc-multivendor-marketplace'), 'name' => 'commission[get_tax]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'value' => 'yes', 'dfvalue' => $vendor_get_tax ),
																																									"vendor_coupon_deduct" => array('label' => __('Commission after consider Vendor Coupon?', 'wc-multivendor-marketplace'), 'name' => 'commission[coupon_deduct]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'value' => 'yes', 'dfvalue' => $vendor_coupon_deduct, 'hints' => __( 'Generate vendor commission after deduct Vendor Coupon discounts.', 'wc-multivendor-marketplace' ) ),
																																									"admin_coupon_deduct" => array('label' => __('Commission after consider Admin Coupon?', 'wc-multivendor-marketplace'), 'name' => 'commission[admin_coupon_deduct]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products commission_mode_by_quantity', 'value' => 'yes', 'dfvalue' => $admin_coupon_deduct, 'hints' => __( 'Generate vendor commission after deduct Admin Coupon discounts.', 'wc-multivendor-marketplace' ) ),
																																									), $membership_id );
		
		return $commission_fileds;
	}
	
	/**
	 * WCFM GEO Locate
	 */
	function wcfmmp_is_allow_geo_locate( $is_allow ) {
		global $WCFMmp;
		$enable_wcfm_geo_locate = isset( $WCFMmp->wcfmmp_marketplace_options['enable_wcfm_geo_locate'] ) ? $WCFMmp->wcfmmp_marketplace_options['enable_wcfm_geo_locate'] : 'no';
		if( $enable_wcfm_geo_locate == 'yes' ) return true;
		return false;
	}
	
	/**
	 * WCFM Default Store Logo
	 */
	function wcfmmp_store_default_logo( $default_logo ) {
		global $WCFMmp;
		$default_logo = !empty( $WCFMmp->wcfmmp_marketplace_options['store_default_logo'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_default_logo'] : $default_logo;
		return $default_logo;
	}
	
	/**
	 * WCFM Store Page Body Class
	 */
	function wcfm_store_body_classes($classes) {
		if( wcfm_is_store_page() ) {
			$classes[] = 'wcfm-store-page';
			$classes[] = 'wcfmmp-store-page';
			$classes[] = 'tax-dc_vendor_shop';
			
			// Martfury Compatibility
			if ( function_exists( 'martfury_is_vendor_page' ) && martfury_is_vendor_page() ) {
			$shop_view = isset( $_COOKIE['shop_view'] ) ? $_COOKIE['shop_view'] : martfury_get_option( 'catalog_view_12' );
			$classes[] = 'shop-view-' . $shop_view;
			}
		
		} elseif( wcfmmp_is_stores_list_page() ) {
			$classes[] = 'wcfm-store-list-page';
			$classes[] = 'wcfmmp-store-list-page';
		}
		return $classes;
	}
	
	/**
	 * WCFM Store Page Title
	 */
	function wcfm_store_page_title( $title ) {
		global $WCFM, $WCFM_Query, $wp_query;
		if( ! is_null( $wp_query ) && !is_admin() && is_main_query() && in_the_loop() && wcfmmp_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
				$title = get_user_meta( $store_user->ID, 'wcfmmp_store_name', true );
				if( !$title ) $title = $store_user->display_name;
			}
		}
		return $title;
	}
	
	/**
	 * WCFMmp Store JS
	 */
	function wcfmmp_scripts() {
 		global $WCFM, $WCFMmp, $wp, $WCFM_Query;
 		
 		if( isset( $_REQUEST['fl_builder'] ) ) return;
 		
 		if( wcfmmp_is_store_page() ) {
 			$WCFM->library->load_blockui_lib();
 			$WCFM->library->load_datepicker_lib();
 			
			// Store JS
			wp_enqueue_script( 'wcfmmp_store_js', $WCFMmp->library->js_lib_url_min . 'store/wcfmmp-script-store.js', array('jquery' ), $WCFMmp->version, true );
			
			$scheme  = is_ssl() ? 'https' : 'http';
			$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
			if ( $api_key ) {
				wp_enqueue_script( 'wcfm-store-google-maps', $scheme . '://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places' );
			}
			
			$wcfm_reviews_messages = get_wcfm_reviews_messages();
			wp_localize_script( 'wcfmmp_store_js', 'wcfm_reviews_messages', $wcfm_reviews_messages );
			
			wp_localize_script( 'wcfmmp_store_js', 'wcfm_slider_banner_delay', array( "delay" => apply_filters( 'wcfmmp_slider_banner_delay', 4000 ) ) );
		}
		
		if( wcfmmp_is_stores_list_page() || wcfmmp_is_stores_map_page() ) {
			$WCFM->library->load_select2_lib();
			wp_enqueue_script( 'wc-country-select' );
			
			$scheme  = is_ssl() ? 'https' : 'http';
			$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
			if ( $api_key ) {
				wp_enqueue_script( 'wcfm-store-google-maps', $scheme . '://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places' );
			}
			
			wp_enqueue_script( 'wcfmmp_store_list_js', $WCFMmp->library->js_lib_url_min . 'store-lists/wcfmmp-script-store-lists.js', array('jquery' ), $WCFMmp->version, true );
			wp_localize_script( 'wcfmmp_store_list_js', 'wcfmmp_store_list_messages', array( 'choose_category' => __( 'Choose Category', 'wc-multivendor-marketplace' ), 'choose_location' => __( 'Choose Location', 'wc-multivendor-marketplace' ), 'choose_state' => __( 'Choose State', 'wc-multivendor-marketplace' ) ) );
			wp_localize_script( 'wcfmmp_store_list_js', 'wcfmmp_store_list_options', array( 'is_geolocate' => apply_filters( 'wcfmmp_is_allow_store_list_by_user_location', true ), 'default_lat' => apply_filters( 'wcfmmp_map_default_lat', 30.0599153 ), 'default_lng' => apply_filters( 'wcfmmp_map_default_lng', 31.2620199 ) ) );
		}
		
		if( apply_filters( 'wcfm_is_allow_store_shipping_countries', false ) ) {
      if(is_cart()) {
        wp_enqueue_script( 'wcfmmp_cart_js', $WCFMmp->library->js_lib_url_min . 'cart/wcfmmp-script-cart.js', array('jquery' ), $WCFMmp->version, true );
      }
      if(is_checkout()) {
        wp_enqueue_script( 'wcfmmp_checkout_js', $WCFMmp->library->js_lib_url_min . 'checkout/wcfmmp-script-checkout.js', array('jquery' ), $WCFMmp->version, true );
      }
    }
 	}
 	
 	/**
 	 * WCFMmp Core CSS
 	 */
 	function wcfmmp_styles() {
 		global $WCFM, $WCFMmp, $wp, $WCFM_Query;
 		
 		if( isset( $_REQUEST['fl_builder'] ) ) return;
 		
 		if( is_product() || ( apply_filters( 'wcfmmp_is_allow_archive_sold_by_advanced', false ) && ( is_shop() || is_product_category() ) ) ) {
			wp_enqueue_style( 'wcfmmp_product_css',  $WCFMmp->library->css_lib_url_min . 'store/wcfmmp-style-product.css', array(), $WCFMmp->version );
		}
 		
 		if( wcfmmp_is_store_page() ) {
			// Store CSS
			if( apply_filters( 'wcfmmp_is_allow_legacy_header', false ) ) {
				wp_enqueue_style( 'wcfmmp_store_css',  $WCFMmp->library->css_lib_url_min . 'store/legacy/wcfmmp-style-store.css', array(), $WCFMmp->version );
			} else {
				wp_enqueue_style( 'wcfmmp_store_css',  $WCFMmp->library->css_lib_url_min . 'store/wcfmmp-style-store.css', array(), $WCFMmp->version );
			}

			// RTL CSS
      if( is_rtl() ) {
      	if( apply_filters( 'wcfmmp_is_allow_legacy_header', false ) ) {
         wp_enqueue_style( 'wcfmmp_store_rtl_css',  $WCFMmp->library->css_lib_url_min . 'store/legacy/wcfmmp-style-store-rtl.css', array('wcfmmp_store_css'), $WCFMmp->version );
        } else {
        	wp_enqueue_style( 'wcfmmp_store_rtl_css',  $WCFMmp->library->css_lib_url_min . 'store/wcfmmp-style-store-rtl.css', array('wcfmmp_store_css'), $WCFMmp->version );
        }
      }
			
			// Store Responsive CSS
			if( apply_filters( 'wcfmmp_is_allow_legacy_header', false ) ) {
			 wp_enqueue_style( 'wcfmmp_store_responsive_css',  $WCFMmp->library->css_lib_url_min . 'store/legacy/wcfmmp-style-store-responsive.css', array(), $WCFMmp->version );
			} else {
				wp_enqueue_style( 'wcfmmp_store_responsive_css',  $WCFMmp->library->css_lib_url_min . 'store/wcfmmp-style-store-responsive.css', array(), $WCFMmp->version );
			}
		}
		
		if( wcfmmp_is_stores_list_page() || is_singular( 'wcfm_vendor_groups' ) || wcfmmp_is_stores_map_page() ) {
			wp_enqueue_style( 'wcfmmp_store_list_css',  $WCFMmp->library->css_lib_url_min . 'store-lists/wcfmmp-style-stores-list.css', array(), $WCFMmp->version );
			
			if( is_rtl() ) {
        wp_enqueue_style( 'wcfmmp_store_list_rtl_css',  $WCFMmp->library->css_lib_url_min . 'store-lists/wcfmmp-style-stores-list-rtl.css', array('wcfmmp_store_list_css'), $WCFMmp->version );
      }
		}
		
		if( wcfmmp_is_store_page() || wcfmmp_is_stores_list_page() || is_singular( 'wcfm_vendor_groups' ) ) {
			$upload_dir      = wp_upload_dir();
			
			// WCFMmp Custom CSS
			$wcfmmp_style_custom = get_option( 'wcfmmp_style_custom' );
			if( $wcfmmp_style_custom && file_exists( trailingslashit( $upload_dir['basedir'] ) . 'wcfm/' . $wcfmmp_style_custom ) ) {
				wp_enqueue_style( 'wcfmmp_style_custom',  trailingslashit( $upload_dir['baseurl'] ) . 'wcfm/' . $wcfmmp_style_custom, array( 'wcfmmp_store_css' ), $WCFMmp->version );
				wp_enqueue_style( 'wcfmmp_store_list_style_custom',  trailingslashit( $upload_dir['baseurl'] ) . 'wcfm/' . $wcfmmp_style_custom, array( 'wcfmmp_store_list_css' ), $WCFMmp->version );
			}
	  }
 	}
	
}